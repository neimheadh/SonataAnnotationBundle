<?php

declare(strict_types=1);

namespace KunicMarko\SonataAnnotationBundle\Reader;

use KunicMarko\SonataAnnotationBundle\Annotation\AddRoute;
use KunicMarko\SonataAnnotationBundle\Annotation\RemoveRoute;
use KunicMarko\SonataAnnotationBundle\Annotation\RouteAnnotationInterface;
use ReflectionClass;

/**
 * Route configuration reader.
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
final class RouteReader extends AbstractReader
{

    /**
     * Get admin routes.
     *
     * @param ReflectionClass $class Entity class.
     *
     * @return array<array<string, object>> Route name => route annotations,
     *                             added route key 0, removed routes key 1.
     */
    public function getRoutes(ReflectionClass $class): array
    {
        $addRoutes = [];
        $removeRoutes = [];

        foreach (
            $this->getClassAnnotations(
                $class,
                RouteAnnotationInterface::class
            ) as $annotation
        ) {
            if ($annotation instanceof AddRoute) {
                $addRoutes[$annotation->name] = $annotation;
                continue;
            }

            if ($annotation instanceof RemoveRoute) {
                $removeRoutes[$annotation->name] = $annotation;
            }
        }

        return [$addRoutes, $removeRoutes];
    }

}
