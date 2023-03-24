<?php

namespace Neimheadh\SonataAnnotationBundle\Annotation;

use ReflectionException;
use ReflectionMethod;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Sonata admin annotation.
 */
abstract class AbstractAnnotation implements AnnotationInterface
{

    /**
     * Initialize annotation.
     *
     * @param array $values Annotation value list.
     *
     * @return void
     * @throws ReflectionException
     */
    protected function initAnnotation(
        array $values
    ): void {
        $accessor = PropertyAccess::createPropertyAccessor();

        $method = new ReflectionMethod($this, '__construct');
        $params = $method->getParameters();

        if (array_keys($values) === ['value']) {
            $values = is_array(
                $values['value']
            ) ? $values['value'] : [$values['value']];

            foreach ($values as $i => $value) {
                $accessor->setValue($this, $params[$i]->getName(), $value);
            }
        } else {
            foreach ($values as $arg => $value) {
                $accessor->setValue($this, $arg, $value);
            }
        }
    }

}