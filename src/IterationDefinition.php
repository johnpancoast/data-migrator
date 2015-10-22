<?php
/**
 * @package www.mymedicalforum.com
 * @copyright (c) 2015 Mindgruve
 * @author John Pancoast <jpancoast@mindgruve.com>
 */

namespace Pancoast\DataMigrator;

/**
 * Iteration definition
 *
 * @author John Pancoast <jpancoast@mindgruve.com>
 */
class IterationDefinition implements IterationDefinitionInterface
{
    /**
     * @var int Count of current iteration
     */
    private $iterationCount;

    /**
     * @var bool Do we continue with this iteration
     */
    private $continueIterating;

    /**
     * @var array Collection of iterations to skip with the iteration number to skip as array key
     */
    private $skippedIterations;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->iterationCount = 0;
        $this->skippedIterations = [];
        $this->continueIterating = true;
    }

    /**
     * @inheritDoc
     */
    public function getIterationCount()
    {
        return $this->iterationCount;
    }

    /**
     * @inheritDoc
     */
    public function incrementIteration()
    {
        $this->iterationCount++;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function decrementIteration()
    {
        $this->iterationCount--;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function continueIterating()
    {
        $this->continueIterating = true;
    }

    /**
     * @inheritDoc
     */
    public function stopIterating()
    {
        $this->continueIterating = false;
    }

    /**
     * @inheritDoc
     */
    public function isContinuingIteration()
    {
        return $this->continueIterating;
    }

    /**
     * @inheritDoc
     */
    public function skipIteration($skippedIteration = null)
    {
        if ($skippedIteration !== null && !is_int($skippedIteration)) {
            throw new \LogicException('The skipped iteration must be an integer');
        }

        $this->skippedIterations[$skippedIteration ?: $this->iterationCount] = true;
    }

    /**
     * @inheritDoc
     */
    public function isSkippedIteration()
    {
        return (isset($this->skippedIterations[$this->iterationCount]) && $this->skippedIterations[$this->iterationCount]);
    }
}