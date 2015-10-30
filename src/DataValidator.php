<?php
/**
 * @package johnpancoast/data-validator
 * @copyright (c) 2015 John Pancoast
 * @author John Pancoast <johnpancoaster@gmail.com>
 * @license MIT
 */

namespace Pancoast\DataValidator;

use Pancoast\DataValidator\Exception\FieldViolationException;
use Pancoast\DataValidator\Exception\SkippableModelIterationException;
use Pancoast\DataValidator\Exception\ValidationException;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator;

/**
 * Data validator
 *
 * This is the core public interface to interact with
 *
 * @author John Pancoast <johnpancoaster@gmail.com>
 */
class DataValidator implements DataValidatorInterface
{
    /**
     * @var DataModelInterface
     */
    protected $model;

    /**
     * @var Validator
     */
    protected $validator;

    /**
     * Constructor
     * @param DataModelInterface $model|null
     */
    public function __construct(DataModelInterface $model = null, $values = null)
    {
        if ($model) {
            $this->setModel($model);
        }

        if ($values) {
            $this->setValues($values);
        }
    }

    /**
     * @inheritDoc
     */
    public function setModel(DataModelInterface $model)
    {
        $this->model = $model;
    }

    /**
     * @inheritDoc
     */
    public function setValue($field, $value)
    {
        $this->checkHasModel();
        $this->model->setValue($field, $value);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getValue($field)
    {
        $this->checkHasModel();
        return $this->model->getValue($field);
    }

    /**
     * @inheritDoc
     */
    public function getValues()
    {
        $this->checkHasModel();
        return $this->model->getValues();
    }

    /**
     * @inheritDoc
     */
    public function setValues($values)
    {
        $this->checkHasModel();
        $this->model->setValues($values);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getField($field)
    {
        $this->checkHasModel();
        return $this->model->getField($field);
    }

    /**
     * @inheritDoc
     */
    public function getFields()
    {
        return $this->model->getFieldDefinitions();
    }

    /**
     * @inheritDoc
     */
    public function isValidField($field)
    {
        $this->checkHasModel();
        return $this->model->isValidField($field);
    }

    /**
     * @inheritDoc
     */
    public function validate()
    {
        $this->checkHasModel();
        $this->model->validate();
    }

    /**
     * Check that a model has been set
     */
    private function checkHasModel()
    {
        if (!$this->model) {
            throw new \LogicException('Must have model to define validation');
        }
    }
}