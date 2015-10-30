<?php
/**
 * @package johnpancoast/data-validator
 * @copyright (c) 2015 John Pancoast
 * @author John Pancoast <johnpancoaster@gmail.com>
 * @license MIT
 */

namespace Pancoast\DataValidator;

/**
 * Contract for a migration model
 *
 * @author John Pancoast <johnpancoaster@gmail.com>
 */
interface ModelInterface
{
    /**
     * Get model's fields
     *
     * @param FieldInterface[] Array of {@see self::FieldInterface}
     * @return array
     */
    public function getFieldDefinitions();

    /**
     * Set all field's values
     *
     * @param mixed $values
     * @return $this
     */
    public function setValues($values);

    /**
     * Set field value
     *
     * @param $field
     * @param $value
     * @return $this
     */
    public function setValue($field, $value);

    /**
     * Get field value
     *
     * @param $field
     * @return mixed
     */
    public function getValue($field);

    /**
     * Get field values
     *
     * @return array
     */
    public function getValues();

    /**
     * Get field
     *
     * @param $field
     * @return FieldInterface
     */
    public function getField($field);

    /**
     * Get field objects
     *
     * @return array
     */
    public function getFields();

    /**
     * Is this a valid field
     *
     * @param $field
     * @return bool
     */
    public function isValidField($field);

    /**
     * Validate field
     *
     * @throws ValidationException If error occurred
     */
    public function validate();
}