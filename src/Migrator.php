<?php
/**
 * @package johnpancoast/data-migrator
 * @copyright (c) 2015 John Pancoast
 * @author John Pancoast <johnpancoaster@gmail.com>
 * @license MIT
 */

namespace Pancoast\DataMigrator;

use Pancoast\DataMigrator\Exception\FieldViolationException;
use Pancoast\DataMigrator\Exception\SkippableModelIterationException;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator;

/**
 * Pancoast\DataMigrator\Migrator
 *
 * @author John Pancoast <johnpancoaster@gmail.com>
 */
class Migrator implements MigratorInterface
{
    /**
     * @var ModelInterface
     */
    protected $model;

    /**
     * @var \Iterator
     */
    protected $data;

    /**
     * @var array
     */
    protected $validationErrors = [];

    /**
     * @var Validator
     */
    protected $validator;

    /**
     * @var \Exception[] Array of exceptions for an iteration
     */
    protected $iterationExceptions = [];

    /**
     * @var int Current iteration count
     */
    protected $iterationCount = 0;

    /**
     * Constructor
     * @param ModelInterface $model
     * @param \Iterator $data
     */
    public function __construct(ModelInterface $model, \Iterator $data)
    {
        $this->setModel($model);
        $this->setData($data);
    }

    /**
     * @inheritDoc
     */
    public function setModel(ModelInterface $model)
    {
        $this->model = $model;
    }

    /**
     * @inheritDoc
     */
    public function setData(\Iterator $data)
    {
        $this->data = $data;
    }

    /**
     * @inheritDoc
     */
    public function run()
    {
        if (!$this->model) {
            throw new \LogicException('Must have model before running');
        }

        if (!$this->data) {
            throw new \LogicException('Must have data to iterate before running');
        }

        if (!$this->validator) {
            $this->validator = Validation::createValidatorBuilder()
                ->addMethodMapping('loadValidatorMetadata')
                ->getValidator();
        }

        $this->model->begin();

        foreach ($this->data as $iterationInput) {
            $this->handleIteration($iterationInput);
        }

        $this->model->end();
    }

    /**
     * @inheritDoc
     */
    protected function handleIteration($iterationInput)
    {
        try {
            $this->iterationCount ++;

            // input that gets passed to fields in user model
            $iterationInput = $this->model->createIterationInput($this->iterationCount, $iterationInput);

            // output that gets handled in user model
            $iterationOutput = [];

            // collection of field exceptions for this iteration
            $fieldViolationList = [];

            $this->model->beginIteration($this->iterationCount, $iterationInput, $iterationOutput);

            foreach ($this->model->getFields() as $field) {
                $name = $field->getName();

                // set value of output field by extracting using field
                $iterationOutput[$name] = $field->extractValue($iterationInput);

                $violations = $this->validator->validateValue(
                    $iterationOutput[$name],
                    $field->getConstraints()
                );

                if (count($violations) > 0) {
                    $fieldErrorMessages = [];

                    foreach ($violations as $violation) {
                        $fieldErrorMessages[] = $violation->getMessage();
                    }

                    $fieldViolationList[] = FieldViolationException::build(
                        $name,
                        implode(', ', $fieldErrorMessages)
                    );
                }
            }

            if (!empty($fieldViolationList)) {
                $this->model->handleIterationConstraintViolations($this->iterationCount, $fieldViolationList);
            }

        // Skippable exceptions are caught, saved, then ignored.
        // All others are thrown up.
        } catch (SkippableModelIterationException $e) {
            $this->iterationExceptions[$this->iterationCount] = $e;
        }

        $this->model->endIteration($this->iterationCount, $iterationOutput);
    }

    /**
     * Get iteration exceptions
     * @return \Exception[]
     */
    public function getIterationExceptions()
    {
        return $this->iterationExceptions;
    }
}