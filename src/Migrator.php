<?php
/**
 * @package johnpancoast/model-validator
 * @copyright (c) 2015 John Pancoast
 * @author John Pancoast <johnpancoaster@gmail.com>
 * @license MIT
 */

namespace Pancoast\ModelValidator;

use Pancoast\ModelValidator\Exception\FieldViolationException;
use Pancoast\ModelValidator\Exception\SkippableModelIterationException;
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

        $this->iterationDefinition = new IterationDefinition();
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

        if ($this->iterationDefinition->isIterating()) {
            foreach ($this->data as $iterationInput) {
                $this->iterationDefinition->incrementIteration();

                if (!$this->iterationDefinition->isIterating() || $this->iterationDefinition->isSkippedIteration()) {
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
        // output that gets handled in user model
        $iterationOutput = [];

        try {
            // create input for this iteration
            $iterationInput = $this->createIterationInput($iterationInput);

            // check if this iteration should still run after createIterationInput() call above.
            if ($this->iterationDefinition->isSkippedIteration() || !$this->iterationDefinition->isIterating()) {
                return;
            }

            // begin iteration
            $this->beginIteration($iterationInput, $iterationOutput);

            // check if iteration should still run after we began iteration
            if ($this->iterationDefinition->isSkippedIteration() || !$this->iterationDefinition->isIterating()) {
                return;
            }

            // create output for this iteration
            $iterationOutput = $this->createIterationOutput($iterationInput, $iterationOutput);

            // check if iteration should still run after creating output
            if ($this->iterationDefinition->isSkippedIteration() || !$this->iterationDefinition->isIterating()) {
                return;
            }

        // Skippable exceptions are caught, saved, then ignored.
        // All others are thrown up.
        } catch (SkippableModelIterationException $e) {
            $this->iterationExceptions[$this->iterationCount] = $e;
        }

        // must be called *after* we catch skippable model iteration exceptions above so that we can attempt
        // to end the iteration nicely in skippable situations.
        $this->endIteration($iterationOutput);
    }

    /**
     * Create iteration input
     *
     * @param $iterationInput
     * @return mixed Iteration input
     */
    private function createIterationInput($iterationInput)
    {
        // attempt to set the input for this iteration, let model handle exception otherwise
        try {
            return $this->model->createIterationInput($this->iterationDefinition, $iterationInput);
        } catch (\Exception $e) {
            $this->model->handleIterationException($this->iterationDefinition, $e);
        }
    }

    /**
     * Begin iteration
     *
     * @param mixed $iterationInput
     * @param mixed $iterationOutput
     */
    private function beginIteration(&$iterationInput, &$iterationOutput)
    {
        // attempt to begin iteration, let model handle exception otherwise
        try {
            $this->model->beginIteration($this->iterationDefinition, $iterationInput, $iterationOutput);
        } catch (\Exception $e) {
            $this->model->handleIterationException($this->iterationDefinition, $e);
        }
    }

    /**
     * Create iteration output
     *
     * @param $iterationInput
     * @param $iterationOutput
     * @return mixed Iteration output
     */
    private function createIterationOutput($iterationInput, $iterationOutput)
    {
        // collection of field exceptions for this iteration
        $fieldViolationList = [];

        foreach ($this->model->getFields() as $field) {
            $name = $field->getName();

            // set value of this field using extraction method.
            // if empty use default.
            $fieldValue = $field->extractValue($iterationInput);
            $iterationOutput[$name] = !empty($fieldValue) ? $fieldValue : $field->getDefaultValue();

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
            try {
                $this->model->handleIterationConstraintViolations($this->iterationDefinition, $fieldViolationList);
            } catch (\Exception $e) {
                $this->model->handleIterationException($this->iterationDefinition, $e);
            }
        }

        return $iterationOutput;
    }

    /**
     * End iteration
     *
     * @param $iterationOutput
     */
    private function endIteration($iterationOutput)
    {
        try {
            $this->model->endIteration($this->iterationDefinition, $iterationOutput);
        } catch (SkippableModelIterationException $e) {
            $this->iterationExceptions[$this->iterationCount] = $e;
        }
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