<?php
/**
 * @package johnpancoast/data-validator
 * @copyright (c) 2015 John Pancoast
 * @author John Pancoast <johnpancoaster@gmail.com>
 * @license MIT
 */

namespace Pancoast\DataValidator\Field;

use Pancoast\DataValidator\AbstractField;

/**
 * A model field allowing a passed in array index to be used in field value extraction
 *
 * @author John Pancoast <johnpancoaster@gmail.com>
 */
class ArrayIndexField extends AbstractField
{
    /**
     * @var int Position of this field in an iteration of input data
     */
    private $fieldIndex;

    /**
     * @inheritDoc
     * @param int $fieldIndex Position of this field in an iteration of input data
     */
    public function __construct($name, array $constraints, $fieldIndex, $defaultValue = '')
    {
        parent::__construct($name, $constraints, $defaultValue);
        $this->fieldIndex = $fieldIndex;
    }

    /**
     * @inheritDoc
     */
    public function extractValue($iterationInput)
    {
        return isset($iterationInput[$this->fieldIndex]) ? $iterationInput[$this->fieldIndex] : '';
    }
}