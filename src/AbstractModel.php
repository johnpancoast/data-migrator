<?php
/**
 * @package johnpancoast/data-validator
 * @copyright (c) 2015 John Pancoast
 * @author John Pancoast <johnpancoaster@gmail.com>
 * @license MIT
 */

namespace Pancoast\DataValidator;

use Pancoast\DataValidator\Exception\HaltableModelIterationException;
use Pancoast\DataValidator\Exception\IterationConstraintViolationException;

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
    abstract public function getFields();

    /**
     * @inheritDoc
     */
    public function createIterationInput(IterationDefinitionInterface $iterationDefinition, $iterationData)
    {
        return $iterationData;
    }

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
    public function beginIteration(IterationDefinitionInterface $iterationDefinition, &$iterationInput, &$iterationOutput)
    {
        // nothing to do by default. override at will.
    }

    /**
     * @inheritDoc
     * @see ModelInterface::endIteration()
     */
    public function endIteration(IterationDefinitionInterface $iterationDefinition, $iterationOutput)
    {
        // nothing to do by default. override at will.
    }

    /**
     * @inheritDoc
     */
    public function handleIterationConstraintViolations(IterationDefinitionInterface $iterationDefinition, array $violationList)
    {
        $messages = [];

        foreach ($violationList as $violation) {
            $messages[] = sprintf('[%s] - %s', $violation->getFieldName(), $violation->getMessage());
        }

        throw new IterationConstraintViolationException(implode(', ', $messages));
    }

    /**
     * @inheritDoc
     */
    public function handleIterationException(IterationDefinitionInterface $iterationDefinition, \Exception $exception)
    {
        throw new HaltableModelIterationException(
            sprintf(
                'Validation failed for iteration %s',
                $iterationDefinition->getIterationCount()
            ),
            0,
            $exception
        );
    }
}