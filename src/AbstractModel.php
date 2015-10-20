<?php
/**
 * @package johnpancoast/data-migrator
 * @copyright (c) 2015 John Pancoast
 * @author John Pancoast <johnpancoaster@gmail.com>
 * @license MIT
 */

namespace Pancoast\DataMigrator;

use Pancoast\DataMigrator\Exception\HaltableModelIterationException;

/**
 * Abstract migration model
 *
 * @author John Pancoast <johnpancoaster@gmail.com>
 */
abstract class AbstractModel implements ModelInterface
{
    /**
     * @inheritDoc
     */
    abstract public function createIterationInput($iterationCount, $iterationData);

    /**
     * @inheritDoc
     * @see ModelInterface::begin()
     */
    public function begin()
    {
        // nothing to do by default. override at will.
    }

    /**
     * @inheritDoc
     * @see ModelInterface::end()
     */
    public function end()
    {
        // nothing to do by default. override at will.
    }

    /**
     * @inheritDoc
     * @see ModelInterface::beginIteration()
     */
    public function beginIteration($iterationCount, &$iterationInput, &$iterationOutput)
    {
        // nothing to do by default. override at will.
    }

    /**
     * @inheritDoc
     * @see ModelInterface::endIteration()
     */
    public function endIteration($iterationCount, $iterationOutput)
    {
        // nothing to do by default. override at will.
    }

    /**
     * @inheritDoc
     */
    public function handleIterationConstraintViolations($iteration, array $violationList)
    {
        $messages = [];

        foreach ($violationList as $violation) {
            $messages[] = sprintf('[%s] - %s', $violation->getFieldName(), $violation->getMessage());
        }

        throw new HaltableModelIterationException(
            sprintf(
                'The following field errors occurred in iteration %s: %s',
                $iteration,
                implode(', ', $messages)
            )
        );
    }
}