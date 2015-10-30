<?php
/**
 * @package johnpancoast/data-validator
 * @copyright (c) 2015 John Pancoast
 * @author John Pancoast <johnpancoaster@gmail.com>
 * @license MIT
 */

namespace Pancoast\DataValidator\Field;

use Pancoast\DataValidator\AbstractField;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * A field allowing you to specify a property path
 *
 * @author John Pancoast <johnpancoaster@gmail.com>
 * @see https://github.com/symfony/property-access
 */
class PropertyField extends AbstractField
{
    /**
     * @var int Position of this field in an iteration of input data
     */
    private $fieldIndex;

    /**
     * @var PropertyAccessor
     */
    private $propertyAccessor;

    /**
     * @inheritDoc
     * @param int $fieldIndex Position of this field in an iteration of input data
     */
    public function __construct($name, array $constraints, $fieldIndex, $defaultValue = '')
    {
        parent::__construct($name, $constraints, $defaultValue);
        $this->fieldIndex = $fieldIndex;
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * @inheritDoc
     */
    public function extractValue($values)
    {
        if (is_int($this->fieldIndex)) {
            $str = sprintf('[%s]', $this->fieldIndex);
        } elseif (is_string($this->fieldIndex)) {
            $str = $this->fieldIndex;
        } else {
            throw new \LogicException('Property path must be a string or int');
        }

        return $this->propertyAccessor->getValue($values, $str);
    }
}