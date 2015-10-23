<?php
/**
 * @package www.mymedicalforum.com
 * @copyright (c) 2015 Mindgruve
 * @author John Pancoast <jpancoast@mindgruve.com>
 */

namespace Pancoast\DataMigrator;

/**
 * Contract for an iteration definition which defines one iteration
 *
 * @author John Pancoast <jpancoast@mindgruve.com>
 */
interface IterationDefinitionInterface
{
    /**
     * Get the count of the current iteration
     *
     * @return int
     */
    public function getIterationCount();

    /**
     * Increment iteration count
     *
     * @return mixed
     */
    public function incrementIteration();

    /**
     * Decrement iteration count
     *
     * @return mixed
     */
    public function decrementIteration();

    /**
     * Continue iterating
     *
     * @return $this
     */
    public function continueIterating();

    /**
     * Stop iterating
     *
     * @return mixed
     */
    public function stopIterating();

    /**
     * Are we still iterating
     *
     * @return bool
     */
    public function isIterating();

    /**
     * Skip the current iteration or the iteration count of the passed param
     *
     * Your implementation should not leave the iteration definition in a state that will not continue iterating
     * after the iteration has been skipped.
     *
     * @param int|null $skippedIteration
     * @return mixed
     */
    public function skipIteration($skippedIteration = null);

    /**
     * Is this an iteration to skip
     *
     * @return bool
     */
    public function isSkippedIteration();
}