<?php
/**
 * @package johnpancoast/data-validator
 * @copyright (c) 2015 John Pancoast
 * @author John Pancoast <johnpancoaster@gmail.com>
 * @license MIT
 */

namespace Pancoast\DataValidator;

use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Contract for a migration model field
 *
 * @author John Pancoast <johnpancoaster@gmail.com>
 */
interface FieldInterface
{
    /**
     * Get field name
     * @return string
     */
    public function getName();

    /**
     * Set field name
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * @return \Symfony\Component\Validator\Constraint[] An array of symfony validator constraints
     */
    public function getConstraints();

    /**
     * @param \Symfony\Component\Validator\Constraint[] $constraints An array of symfony validator constraints
     */
    public function setConstraints(array $constraints);

    /**
     * Given one iteration of input, get the value for this field
     *
     * @param mixed $iterationInput One iteration of input to pull the specific value from
     * @return mixed The value to be assigned to the field
     */
    public function extractValue($iterationInput);

    /**
     * Default value if field is empty
     *
     * @param string $defaultValue
     * @return $this
     */
    public function setDefaultValue($defaultValue = '');

    /**
     * Get default value
     * @return mixed
     */
    public function getDefaultValue();

    /**
     * Handle constraint violations
     *
     * @param ConstraintViolationListInterface $violations
     */
    public function handleConstraintViolations(ConstraintViolationListInterface $violations);
}