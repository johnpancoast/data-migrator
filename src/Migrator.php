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
 * Data migrator
 *
 * This is the core public interface to interact with
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
     * @var IterationDefinition
     */
    private $iterationDefinition;

    /**
     * Constructor
     * @param ModelInterface $model|null
     * @param \Iterator $data|null
     */
    public function __construct(ModelInterface $model = null, \Iterator $data = null)
    {
        if ($model) {
            $this->setModel($model);
        }

        if ($data) {
            $this->setData($data);
        }

        $this->iterationDefinition = new IterationDefinition(0, true);
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

        if ($this->iterationDefinition->isContinuingIteration()) {
            foreach ($this->data as $iterationInput) {
                $this->iterationDefinition->incrementIteration();

                if (!$this->iterationDefinition->isContinuingIteration() || $this->iterationDefinition->isSkippedIteration()) {
                    continue;
                }

                $this->handleIteration($iterationInput);
            }
        }

        $this->model->end();
    }

    /**
     * @inheritDoc
     */
    protected function handleIteration($iterationInput)
    {
        // input that gets passed to fields in user model
        $iterationInput = $this->model->createIterationInput($this->iterationDefinition, $iterationInput);

        // output that gets handled in user model
        $iterationOutput = [];

        // collection of field exceptions for this iteration
        $fieldViolationList = [];

        // check if this iteration should still run after model createIterationInput() call above.
        if ($this->iterationDefinition->isSkippedIteration() || !$this->iterationDefinition->isContinuingIteration()) {
            return;
        }

        try {

            $this->model->beginIteration($this->iterationDefinition, $iterationInput, $iterationOutput);

            // check if iteration should still run after model iteration handling began
            if ($this->iterationDefinition->isSkippedIteration() || !$this->iterationDefinition->isContinuingIteration()) {
                return;
            }

            foreach ($this->model->getFields() as $field) {
                $name = $field->getName();

                // set value of output field by extracting using field
                $iterationOutput[$name] = $field->extractValue($iterationInput);

                $violations = $this->validator->validateValue(
                    $iterationOutput[$name],
                    $field->getConstraints()
                );

                if (count($violations) > 0) {
                    $field->handleConstraintViolations($violations);

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

                // check if iteration should still run after model handled constraint violation
                if ($this->iterationDefinition->isSkippedIteration() || !$this->iterationDefinition->isContinuingIteration()) {
                    return;
                }
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