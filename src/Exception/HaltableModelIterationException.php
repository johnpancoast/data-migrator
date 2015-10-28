<?php
/**
 * @package johnpancoast/data-validator
 * @copyright (c) 2015 John Pancoast
 * @author John Pancoast <johnpancoaster@gmail.com>
 * @license MIT
 */

namespace Pancoast\DataValidator\Exception;

/**
 * A model iteration exception that will cause iterating to stop
 *
 * @author John Pancoast <johnpancoaster@gmail.com>
 */
class HaltableModelIterationException extends \Exception
{
}