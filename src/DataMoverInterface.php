<?php
/**
 * @package shideon/data-mover
 * @copyright (c) 2015 John Pancoast
 * @author John Pancoast <johnpancoaster@gmail.com>
 * @license MIT
 */

namespace Shideon\DataMover;

/**
 * Shideon\DataMover\DataMoverInterface
 *
 * @author John Pancoast <johnpancoaster@gmail.com>
 */
interface DataMoverInterface
{
    /**
     * Set model
     *
     * A model defines the fields used in a data transfer
     *
     * @param ModelInterface $model
     */
    public function setModel(ModelInterface $model);

    /**
     * Set data to iterate over
     * @param \Iterator $data
     * @return $this
     */
    public function setData(\Iterator $data);

    /**
     * Core logic
     */
    public function run();
}