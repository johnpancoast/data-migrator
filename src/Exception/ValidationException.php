<?php
/**
 * @package spamroast/data-validator
 * @copyright (c) 2015 John Pancoast
 * @author John Pancoast <johnpancoaster@gmail.com>
 * @license MIT
 */

namespace Pancoast\DataValidator\Exception;

use Pancoast\DataValidator\FieldInterface;

/**
 * Pancoast\DataValidator\Exception\ValidationException
 *
 * @author John Pancoast <johnpancoaster@gmail.com>
 */
class ValidationException extends \Exception
{
    /**
     * @var FieldInterface
     */
    private $field;

    /**
     * Build exception
     *
     * @param FieldInterface $field
     * @param $message
     * @return static
     */
    public static function build(FieldInterface $field, $message)
    {
        $self = new self($message);
        $self->field = $field;
        return $self;
    }

    /**
     * Get field
     *
     * @return FieldInterface
     */
    public function getField()
    {
        return $this->field;
    }
}