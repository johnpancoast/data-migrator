<?php
/**
 * @package shideon/data-mover
 * @copyright (c) 2015 John Pancoast
 * @author John Pancoast <johnpancoaster@gmail.com>
 * @license MIT
 */

namespace Shideon\DataMover\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Shideon\DataMover\Exception\FieldViolationException
 *
 * @author John Pancoast <johnpancoaster@gmail.com>
 */
class FieldViolationException extends \Exception
{
    /**
     * @var string
     */
    private $fieldName;

    /**
     * Build exceptions
     *
     * @param $fieldName Field name that failed
     * @param $message Message
     * @return FieldViolationException
     */
    public static function build($fieldName, $message)
    {
        $e = new self($message);
        $e->fieldName = $fieldName;
        return $e;
    }

    /**
     * Get field name
     * @return string
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }
}