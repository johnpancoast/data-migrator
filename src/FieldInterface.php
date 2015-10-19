<?php
/**
 * @package shideon/data-mover
 * @copyright (c) 2015 John Pancoast
 * @author John Pancoast <johnpancoaster@gmail.com>
 * @license MIT
 */

namespace Shideon\DataMover;

/**
 * Shideon\DataMover\FieldInterface
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
     * @return Symfony\Component\Validator\Constraint[]
     */
    public function getConstraints();

    /**
     * @param Symfony\Component\Validator\Constraint[] $constraints An array of symfony validator constraints
     */
    public function setConstraints(array $constraints);

    /**
     * Given an input iteration, get the value for this field
     * @param $inputIteration
     * @return mixed
     */
    public function extractValue($inputIteration);
}