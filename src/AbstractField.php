<?php
/**
 * @package johnpancoast/data-validator
 * @copyright (c) 2015 John Pancoast
 * @author John Pancoast <johnpancoaster@gmail.com>
 * @license MIT
 */

namespace Pancoast\DataValidator;

use Pancoast\DataValidator\Exception\FieldViolationException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Abstract migration model field
 *
 * @author John Pancoast <johnpancoaster@gmail.com>
 */
abstract class AbstractField implements FieldInterface
{
    /**
     * Field name
     * @var string
     */
    protected $name;

    /**
     * @var \Symfony\Component\Validator\Constraint[] $constraints An array of symfony validator constraints
     */
    protected $constraints = [];

    /**
     * @var mixed
     */
    protected $defaultValue = '';

    /**
     * @inheritDoc
     */
    abstract public function extractValue($iterationInput);

    /**
     * Constructor
     *
     * @see class property docblocks
     *
     * @param $name
     * @param array $constraints
     * @param mixed $defaultValue Default value if value is empty
     */
    public function __construct($name, array $constraints, $defaultValue = '')
    {
        $this->setName($name);
        $this->setConstraints($constraints);
        $this->setDefaultValue($defaultValue);
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getConstraints()
    {
        return $this->constraints;
    }

    /**
     * @inheritDoc
     */
    public function setConstraints(array $constraints)
    {
        $this->constraints = $constraints;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setDefaultValue($defaultValue = '')
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @inheritDoc
     */
    public function handleConstraintViolations(ConstraintViolationListInterface $violations)
    {
        // Our lib does nothing for a failed field at this layer. Override at will but if throwing exceptions,
        // see internals at {@see Migrator::handleIteration()}.
    }
}