<?php

namespace Neimheadh\SonataAnnotationBundle\Annotation;

use Psr\Log\InvalidArgumentException;
use ReflectionException;
use ReflectionProperty;

/**
 * Trait for annotation having only one array property.
 */
trait ArrayAnnotationTrait
{

    /**
     * Annotation property name.
     *
     * @var string
     */
    private string $property;

    /**
     * Annotation property value.
     *
     * @var mixed
     */
    private $value;

    /**
     * Set the annotation array property.
     *
     * @param string $name Property name.
     * @param        $value
     *
     * @return void
     * @throws ReflectionException
     */
    private function setArrayProperty(string $name, $value): void
    {
        $this->property = $name;
        $this->__set($name, $value['value'] ?? $value[$name] ?? $value);
    }


    /**
     * Set class property.
     *
     * @param string $name  Property name.
     * @param mixed  $value Value.
     *
     * @return void
     */
    public function __set(string $name, $value)
    {
        if ($name !== $this->property) {
            throw new InvalidArgumentException(
                sprintf(
                    'Unknown property "%s".',
                    $name
                )
            );
        }

        if (is_array($value)) {
            $this->value = $value;
        } else {
            $this->value = is_null($value) ? [] : [$value];
        }
    }

    /**
     * Get class property.
     *
     * @param string $name Property name.
     *
     * @return mixed
     */
    public function __get(string $name)
    {
        if ($name !== $this->property) {
            throw new InvalidArgumentException(
                sprintf(
                    'Unknown property "%s".',
                    $name
                )
            );
        }

        return $this->value;
    }

}