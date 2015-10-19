<?php
/**
 * @package shideon/data-mover
 * @copyright (c) 2015 John Pancoast
 * @author John Pancoast <johnpancoaster@gmail.com>
 * @license MIT
 */

namespace Shideon\DataMover;

/**
 * Shideon\DataMover\ModelInterface
 *
 * @author John Pancoast <johnpancoaster@gmail.com>
 */
interface ModelInterface
{
    /**
     * Get model's fields
     *
     * @param FieldInterface[] Array of {@see self::FieldInterface}
     * @return mixed
     */
    public function getFields();

    /**
     * Given one iteration of input, create and return an input iteration data structure of your choice
     *
     * This method will be run first by the data mover to create the iteration input the model's logic
     * expects.
     *
     * @param mixed $iterationData
     * @return mixed
     */
    public function createIterationInput($iterationData);

    /**
     * Logic to run at the beginning of an iteration
     *
     * This method is called at the beginning of an iteration after {@see self::createIterationInput()} and
     * before field validation.
     *
     * Note that the parameters are passed in by reference and can be changed, however,
     * $iterationOutput will *always* be overwritten by internal logic handling field input
     * so take notice of the object properties you're setting.
     *
     * @param $iterationInput Input data for this iteration created by {@see self::createIterationInput()}
     * @param $iterationOutput Output data for this iteration
     */
    public function beginIteration(&$iterationInput, &$iterationOutput);

    /**
     * Logic to run at the end of an iteration
     *
     * This is run after field validation has succeeded. The $iterationOutput object will be an
     * instance of \StdClass with public properties matching those defined in {@see self::getFields()}
     * This might be the method where you save your final output data to a database for example.
     *
     * @param $iterationOutput Iteration output after field input and validation have occurred
     */
    public function endIteration($iterationOutput);
}