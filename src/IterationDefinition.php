<?php
/**
 * @package www.mymedicalforum.com
 * @copyright (c) 2015 Mindgruve
 * @author John Pancoast <jpancoast@mindgruve.com>
 */

namespace Pancoast\DataValidator;

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
    private $iterationCount = 0;

    /**
     * @var bool Are we currently iterating
     */
    private $isIterating = true;

    /**
     * @var array Collection of iterations to skip with the iteration number to skip as array key and bool as value
     * defining whether to skip.
     */
    private $skippedIterations = [];

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
        $this->isIterating = true;
    }

    /**
     * @inheritDoc
     */
    public function stopIterating()
    {
        $this->isIterating = false;
    }

    /**
     * @inheritDoc
     */
    public function isIterating()
    {
        return $this->isIterating;
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