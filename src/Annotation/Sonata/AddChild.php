<?php

declare(strict_types=1);

namespace Neimheadh\SonataAnnotationBundle\Annotation\Sonata;

use Attribute;
use Neimheadh\SonataAnnotationBundle\Annotation\AbstractAnnotation;
use ReflectionException;

/**
 * Add child annotation.
 *
 * Add child admin to current admin class or admin annotated model.
 *
 * @Annotation
 * @Target("CLASS")
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class AddChild extends AbstractAnnotation
{

    /**
     * Child model class.
     *
     * @var string|null
     */
    public ?string $class = null;

    /**
     * Reverse field.
     *
     * @var string|null
     */
    public ?string $field = null;

    /**
     * @param string|array|null $class Model class or annotation parameters.
     * @param string|null       $field Reverse field.
     *
     * @throws ReflectionException
     */
    public function __construct(
        $class = null,
        ?string $field = null
    ) {
        $this->field = $field;

        if (is_array($class)) {
            $this->initAnnotation($class);
        } else {
            $this->class = $class;
        }
    }

}
