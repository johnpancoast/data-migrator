<?php
/**
 * @package spamroast/data-migrator
 * @copyright (c) 2015 John Pancoast
 * @author John Pancoast <johnpancoaster@gmail.com>
 * @license MIT
 */

namespace SpamRoast\DataMigrator;

use SpamRoast\DataMigrator\Exception\FieldViolationException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * SpamRoast\DataMigrator\AbstractField
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
     */
    public function __construct($name, array $constraints)
    {
        $this->setName($name);
        $this->setConstraints($constraints);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return \Symfony\Component\Validator\Constraint[]
     */
    public function getConstraints()
    {
        return $this->constraints;
    }

    /**
     * @param \Symfony\Component\Validator\Constraint[] $constraints
     * @return $this
     */
    public function setConstraints(array $constraints)
    {
        $this->constraints = $constraints;
        return $this;
    }

    /**
     * @inheritDoc
     * @throws FieldViolationException
     */
    public function handleConstraintViolations(ConstraintViolationListInterface $violations)
    {
        // Our lib does nothing for a failed field at this layer. Override at will but if throwing exceptions,
        // see internals at {@see DataMigrator::handleIteration()}.
    }
}