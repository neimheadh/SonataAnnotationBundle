<?php

declare(strict_types=1);

namespace KunicMarko\SonataAnnotationBundle\Annotation;

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
final class RemoveRoute implements AnnotationInterface
{
    /**
     * Removed route name.
     *
     * @var string
     */
    public string $name;
}
