<?php
/**
 * @package shideon/data-mover
 * @copyright (c) 2015 John Pancoast
 * @author John Pancoast <johnpancoaster@gmail.com>
 * @license MIT
 */

namespace Shideon\DataMover;

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
        $iterationOutput = new \StdClass();

        $this->model->beginIteration($iterationInput, $iterationOutput);

        foreach ($this->model->getFields() as $field) {
            $iterationOutput->{$field->getName()} = $field->extractValue($iterationInput);
        }

        // @todo validation

        $this->model->endIteration($iterationOutput);
    }
}