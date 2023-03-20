<?php

declare(strict_types=1);

namespace Neimheadh\SonataAnnotationBundle\Annotation;

use Attribute;

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
#[Attribute(Attribute::TARGET_CLASS)]
final class RemoveRoute implements RouteAnnotationInterface
{
    /**
     * Removed route name.
     *
     * @var string
     */
    public string $name;
}
