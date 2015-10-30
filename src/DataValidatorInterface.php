<?php
/**
 * @package johnpancoast/data-validator
 * @copyright (c) 2015 John Pancoast
 * @author John Pancoast <johnpancoaster@gmail.com>
 * @license MIT
 */

namespace Pancoast\DataValidator;

/**
 * Contract for migrator
 *
 * This is the core public interface to interact with
 *
 * @author John Pancoast <johnpancoaster@gmail.com>
 */
interface DataValidatorInterface
{
    /**
     * Set model
     *
     * A model defines the fields used in a data transfer
     *
     * @param DataModelInterface $model
     */
    public function setModel(DataModelInterface $model);

    /**
     * Set all field's values
     *
     * @param mixed $values
     * @return $this
     */
    public function setValues($values);

    /**
     * Get values
     *
     * @return array
     */
    public function getValues();

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
     * Get field
     *
     * @param $field
     * @return FieldInterface
     */
    public function getField($field);

    /**
     * Get fields
     * @return FieldInterface[] Traversable fields
     */
    public function getFields();

    /**
     * Core logic
     */
    public function validate();
}