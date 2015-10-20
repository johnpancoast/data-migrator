<?php
/**
 * @package shideon/data-mover
 * @copyright (c) 2015 John Pancoast
 * @author John Pancoast <johnpancoaster@gmail.com>
 * @license MIT
 */

namespace Shideon\DataMigrator\Field;

use Shideon\DataMigrator\AbstractField;

/**
 * Shideon\DataMigrator\Field\CallableField
 *
 * @author John Pancoast <johnpancoaster@gmail.com>
 */
class CallableField extends AbstractField
{
    /**
     * Callable for handling logic to retrieve data for this field from
     * an iteration of input data
     *
     * This callable should share the same signature as {@see Shideon\DataMigrator\FieldInterface::extractValue()}.
     *
     *    @#param mixed $iterationInput One iteration of input to pull the specific value from
     *    @#return mixed The value to be assigned to the field
     *
     * @see FieldInterface::extractValue()
     * @var callable
     */
    private $handler;

    /**
     * Constructor
     * @param $name
     * @param array $constraints
     * @param callable $handler
     */
    public function __construct($name, array $constraints, callable $handler)
    {
        parent::__construct($name, $constraints);
        $this->handler = $handler;
    }

    /**
     * @inheritDoc
     */
    public function extractValue($iterationInput)
    {
        return call_user_func($this->handler, $iterationInput);
    }
}