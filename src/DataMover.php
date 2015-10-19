<?php
/**
 * @package shideon/data-mover
 * @copyright (c) 2015 John Pancoast
 * @author John Pancoast <johnpancoaster@gmail.com>
 * @license MIT
 */

namespace Shideon\DataMover;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator;

/**
 * Shideon\DataMover\DataMover
 *
 * @author John Pancoast <johnpancoaster@gmail.com>
 */
class DataMover implements DataMoverInterface
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

        foreach ($this->data as $iterationInput) {
            $this->handleIteration($iterationInput);
        }
    }

    /**
     * @inheritDoc
     */
    protected function handleIteration($iterationInput)
    {
        $iterationInput = $this->model->createIterationInput($iterationInput);
        $iterationOutput = [];

        $this->model->beginIteration($iterationInput, $iterationOutput);

        foreach ($this->model->getFields() as $field) {
            $name = $field->getName();

            // set value of output field by extracting using field
            $iterationOutput[$name] = $field->extractValue($iterationInput);

            $errors = $this->validator->validateValue(
                $iterationOutput[$name],
                $field->getConstraints()
            );

            if (count($errors) > 0) {
                $this->validationErrors[$name] = $errors;
            }

            // @todo determine how validation errors are handled
        }

        $this->model->endIteration($iterationOutput);
    }
}