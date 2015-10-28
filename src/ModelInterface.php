<?php
/**
 * @package johnpancoast/model-validator
 * @copyright (c) 2015 John Pancoast
 * @author John Pancoast <johnpancoaster@gmail.com>
 * @license MIT
 */

namespace Pancoast\ModelValidator;

/**
 * Contract for a migration model
 *
 * @author John Pancoast <johnpancoaster@gmail.com>
 */
interface ModelInterface
{
    /**
     * Get model's fields
     *
     * @param FieldInterface[] Array of {@see self::FieldInterface}
     * @return array
     */
    public function getFields();

    /**
     * Logic to be run before iterating over input data
     *
     * This is the first method to be run re: iterating input data
     */
    public function begin();

    /**
     * Given one iteration of input, create and return an input iteration data structure of your choice
     *
     * This method is called after {@see self::begin()} and before {@see::self::beginIteration()} to define
     * the input data for this iteration.
     *
     * @param IterationDefinitionInterface $iterationDefinition The definition of this iteration
     * @param mixed $iterationData Data to create iteration input from
     * @return
     */
    public function createIterationInput(IterationDefinitionInterface $iterationDefinition, $iterationData);

    /**
     * Logic to run at the beginning of an iteration
     *
     * This method is called at the beginning of an iteration after {@see self::createIterationInput()} and
     * before the iteration output has been created.
     *
     * Note that the parameters are passed in by reference and can be changed, however,
     * $iterationOutput will *always* be overwritten by internal logic handling field input
     * so take notice of the object properties you're setting.
     *
     * @param IterationDefinitionInterface $iterationDefinition The definition of this iteration
     * @param mixed $iterationInput Input data for this iteration created by {@see self::createIterationInput()}
     * @param array $iterationOutput Output data for this iteration
     * @return
     */
    public function beginIteration(IterationDefinitionInterface $iterationDefinition, &$iterationInput, &$iterationOutput);

    /**
     * Logic to run at the end of an iteration
     *
     * This is run after field validation has succeeded. $iterationOutput will be an array with values
     * matching those defined in {@see self::getFields()}. This method is useful for handling the end
     * of each iteration. Adding something to a transaction is an examples of something you might do here.
     *
     * @param IterationDefinitionInterface $iterationDefinition The definition of this iteration
     * @param $iterationOutput Iteration output after field input and validation have occurred
     * @return
     */
    public function endIteration(IterationDefinitionInterface $iterationDefinition, $iterationOutput);

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
     * @param IterationDefinitionInterface $iterationDefinition The definition of this iteration
     * @param FieldViolationException[] An array of {@see FieldViolationException}.
     * @return
     */
    public function handleIterationConstraintViolations(IterationDefinitionInterface $iterationDefinition, array $violationList);

    /**
     * Handle an exception encountered in an iteration
     *
     * @param IterationDefinitionInterface $iterationDefinition
     * @param \Exception $exception
     * @return mixed
     */
    public function handleIterationException(IterationDefinitionInterface $iterationDefinition, \Exception $exception);
}