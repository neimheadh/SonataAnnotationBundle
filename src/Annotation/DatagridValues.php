<?php

declare(strict_types=1);

namespace KunicMarko\SonataAnnotationBundle\Annotation;

/**
 * Datagrid values annotation.
 *
 * @Annotation
 * @Target("CLASS")
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
final class DatagridValues implements AnnotationInterface
{
    /**
     * Datagrid select values.
     *
     * @var array
     */
    public array $values = [];
}
