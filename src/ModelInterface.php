<?php
/**
 * @package shideon/data-mover
 * @copyright (c) 2015 John Pancoast
 * @author John Pancoast <johnpancoaster@gmail.com>
 * @license MIT
 */

namespace SpamRoast\DataMigrator;

/**
 * SpamRoast\DataMigrator\ModelInterface
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
     * Logic to be run before iterating over input data
     *
     * This is the first method to be run re: iterating input data
     *
     * @return mixed
     */
    public function begin();

    /**
     * Given one iteration of input, create and return an input iteration data structure of your choice
     *
     * This method is called after {@see self::begin()} and before {@see::self::beginIteration()} to define
     * the input data for this iteration.
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
     * @param mixed $iterationInput Input data for this iteration created by {@see self::createIterationInput()}
     * @param array $iterationOutput Output data for this iteration
     */
    public function beginIteration(&$iterationInput, &$iterationOutput);

    /**
     * Logic to run at the end of an iteration
     *
     * This is run after field validation has succeeded. $iterationOutput will be an array with values
     * matching those defined in {@see self::getFields()}. This method is useful for handling the end
     * of each iteration. Ppersisting an object or adding something to a transaction are examples of
     * things you might do here.
     *
     * @param $iterationOutput Iteration output after field input and validation have occurred
     */
    public function endIteration($iterationOutput);

    /**
     * Logic to run after all iterations
     *
     * Writing your transaction or doing some post cleanup are examples of things you might do here.
     */
    public function end();

    /**
     * Handle constrain violations for an iteration
     *
     * This method can throw certain exceptions to effect the behavior of future iterations. See @#throws
     * docs below.
     *
     * @param int $iteration Iteration number where the exception occurred
     * @param FieldViolationException[] An array of {@see FieldViolationException}.
     * @throws HaltableModelIterationException Which will halt iterating in the migrator
     * @throws SilentModelIterationException Which will let iterating continue in the migrator
     * @return void
     */
    public function handleIterationConstraintViolations($iteration, array $violationList);
}