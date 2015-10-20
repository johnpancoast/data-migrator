<?php
/**
 * @package johnpancoast/data-migrator
 * @copyright (c) 2015 John Pancoast
 * @author John Pancoast <johnpancoaster@gmail.com>
 * @license MIT
 */

namespace Pancoast\DataMigrator\Field;

use Pancoast\DataMigrator\AbstractField;

/**
 * Pancoast\DataMigrator\Field\ArrayIndexField
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
     * @param int $fieldPosition Position of this field in an iteration of input data
     */
    public function __construct($name, array $constraints, $fieldIndex)
    {
        parent::__construct($name, $constraints);
        $this->fieldIndex = $fieldIndex;
    }

    /**
     * @inheritDoc
     */
    public function extractValue($iterationInput)
    {
        return $iterationInput[$this->fieldIndex];
    }
}