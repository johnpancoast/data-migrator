<?php
/**
 * @package johnpancoast/data-validator
 * @copyright (c) 2015 John Pancoast
 * @author John Pancoast <johnpancoaster@gmail.com>
 * @license MIT
 */

namespace Pancoast\DataValidator;

use Pancoast\DataValidator\Exception\HaltableModelIterationException;
use Pancoast\DataValidator\Exception\IterationConstraintViolationException;
use Pancoast\DataValidator\Exception\UnknownFieldException;
use Pancoast\DataValidator\Exception\ValidationException;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Validator\Validation;

/**
 * Abstract data model
 *
 * @author John Pancoast <johnpancoaster@gmail.com>
 */
abstract class AbstractDataModel implements DataModelInterface
{
    /**
     * @var FieldInterface[] Traversable of field objects
     */
    private $fields;

    /**
     * @var Validator
     */
    private $validator;

    /**
     * @var PropertyAccessor
     */
    private $accessor;

    /**
     * @inheritDoc
     */
    abstract public function getFieldDefinitions();

    /**
     * Construct
     */
    public function __construct()
    {
        $this->accessor = PropertyAccess::createPropertyAccessor();
        $this->fields = $this->getFieldDefinitions();
    }

    /**
     * @inheritDoc
     */
    public function setValues($values)
    {
        foreach ($this->fields as $f) {
            $f->setValue($f->extractValue($values));
        }
    }

    /**
     * @inheritDoc
     */
    public function setValue($field, $value)
    {
        $this->checkIsValidField($field);

        $this->fields[$field]->setValue($value);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getValue($field)
    {
        $this->checkIsValidField($field);

        return $this->fields[$field]->getValue($field);
    }

    /**
     * @inheritDoc
     */
    public function getValues()
    {
        $return = [];

        foreach ($this->fields as $field) {
            $return[$field->getName()] = $field->getValue();
        }

        return $return;
    }

    /**
     * @inheritDoc
     */
    public function getField($field)
    {
        $this->checkIsValidField($field);

        return $this->fields[$field];
    }

    /**
     * @inheritDoc
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @inheritDoc
     */
    public function isValidField($field)
    {
        return isset($this->fields[$field]);
    }

    /**
     * Checks if field is valid and throws if not
     *
     * @param $field
     * @throws UnknownFieldException If field not valid
     */
    public function checkIsValidField($field)
    {
        if (!$this->isValidField($field)) {
            throw new UnknownFieldException(sprintf('Unknown field "%s"', $field));
        }
    }

    /**
     * @inheritDoc
     */
    public function validate()
    {
        if (!$this->validator) {
            $this->validator = Validation::createValidatorBuilder()
                ->addMethodMapping('loadValidatorMetadata')
                ->getValidator();
        }

        foreach ($this->getFields() as $field) {
            $violations = $this->validator->validateValue(
                $field->getValue(),
                $field->getConstraints()
            );

            if (count($violations) > 0) {
                $field->handleConstraintViolations($violations);

                $fieldErrorMessages = [];

                foreach ($violations as $violation) {
                    $fieldErrorMessages[] = $violation->getMessage();
                }

                throw ValidationException::build($field, implode(', ', $fieldErrorMessages));
            }
        }
    }
}
