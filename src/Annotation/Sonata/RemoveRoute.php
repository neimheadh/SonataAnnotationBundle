<?php

declare(strict_types=1);

namespace Neimheadh\SonataAnnotationBundle\Annotation\Sonata;

use Attribute;
use Neimheadh\SonataAnnotationBundle\Annotation\AbstractAnnotation;
use Neimheadh\SonataAnnotationBundle\Annotation\RouteAnnotationInterface;
use ReflectionException;

/**
 * Remove route annotation.
 *
 * Allows you to remove a default route from your admin.
 *
 * @Annotation
 * @Target("CLASS")
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final class RemoveRoute extends AbstractAnnotation implements
    RouteAnnotationInterface
{

    /**
     * Removed route name.
     *
     * @var string|null
     */
    public ?string $name = null;

    /**
     * @param string|array|null $name Removed route name or annotation
     *                                parameters.
     *
     * @throws ReflectionException
     */
    public function __construct(
        $name = null
    ) {
        if (is_array($name)) {
            $this->initAnnotation($name);
        } else {
            $this->name = $name;
        }
    }

}
