<?php
/**
 * @package shideon/data-mover
 * @copyright (c) 2015 John Pancoast
 * @author John Pancoast <johnpancoaster@gmail.com>
 * @license MIT
 */

namespace Shideon\DataMover\Field;

use Shideon\DataMover\AbstractField;

/**
 * Shideon\DataMover\Field\CallableField
 *
 * @author John Pancoast <johnpancoaster@gmail.com>
 */
class CallableField extends AbstractField
{
    /**
     * Callable for handling logic to retrieve data for this field from
     * an input object.
     *
     * $handler has the following signature
     *
     *    @#param mixed $inputIteration One iteration of input
     *    @#return mixed The value to be assigned to the field being added
     *
     * @see FieldInterface
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
    public function extractValue($inputIteration)
    {
        return call_user_func($this->handler, $inputIteration);
    }
}