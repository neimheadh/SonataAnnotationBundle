<?php

declare(strict_types=1);

namespace Neimheadh\SonataAnnotationBundle\Annotation;

/**
 * Access control annotation.
 *
 * Allow you to control permission per role.
 *
 * @Annotation
 * @Target("CLASS")
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
final class Access implements AnnotationInterface
{
    /**
     * Allowed role.
     *
     * @var string
     */
    public string $role;

    /**
     * Allowed permissions.
     *
     * @var array
     */
    public array $permissions = [];
}
