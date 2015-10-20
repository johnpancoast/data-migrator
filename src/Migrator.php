<?php
/**
 * @package shideon/data-mover
 * @copyright (c) 2015 John Pancoast
 * @author John Pancoast <johnpancoaster@gmail.com>
 * @license MIT
 */

namespace Shideon\DataMigrator;

use Shideon\DataMigrator\Exception\FieldViolationException;
use Shideon\DataMigrator\Exception\SkippableModelIterationException;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator;

/**
 * Shideon\DataMigrator\Migrator
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
     * @var int Current iteration numbe
     */
    protected $currentIteration = 0;

    /**
     * Constructor
     * @param ModelInterface|null $model
     * @param \Iterator|null $data
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
        $this->currentIteration ++;

        try {
            // collection of field exceptions for this iteration
            $fieldViolationList = [];

            $iterationInput = $this->model->createIterationInput($iterationInput);
            $iterationOutput = [];

            $this->model->beginIteration($iterationInput, $iterationOutput);

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

            $this->model->handleIterationConstraintViolations($this->currentIteration, $fieldViolationList);

        // Skippable exceptions are caught, saved, then ignored.
        // All others are thrown up.
        } catch (SkippableModelIterationException $e) {
            $this->iterationExceptions[$this->currentIteration] = $e;
        }

        $this->model->endIteration($iterationOutput);
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