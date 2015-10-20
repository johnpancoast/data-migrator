<?php
/**
 * @package johnpancoast/data-migrator
 * @copyright (c) 2015 John Pancoast
 * @author John Pancoast <johnpancoaster@gmail.com>
 * @license MIT
 */

namespace Pancoast\DataMigrator\Exception;

/**
 * A model iteration exception that will allow iterating to continue
 *
 * @author John Pancoast <johnpancoaster@gmail.com>
 */
class SkippableModelIterationException extends \Exception
{
}