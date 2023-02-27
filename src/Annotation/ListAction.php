<?php

declare(strict_types=1);

namespace KunicMarko\SonataAnnotationBundle\Annotation;

/**
 * List action annotation.
 *
 * Allows you to configure your list page actions.
 *
 * @Annotation
 * @Target("CLASS")
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
final class ListAction implements AnnotationInterface
{
    /**
     * Action name.
     *
     * @var string
     */
    public string $name;

    /**
     * Action options.
     *
     * @var array
     */
    public array $options = [];
}
