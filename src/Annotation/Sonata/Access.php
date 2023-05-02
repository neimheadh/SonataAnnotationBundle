<?php

declare(strict_types=1);

namespace Neimheadh\SonataAnnotationBundle\Annotation\Sonata;

use Attribute;
use Neimheadh\SonataAnnotationBundle\Annotation\AbstractAnnotation;
use ReflectionException;

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
#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final class Access extends AbstractAnnotation
{

    /**
     * Allowed role.
     *
     * @var string|null
     */
    public ?string $role = null;

    /**
     * Allowed permissions.
     *
     * @var array
     */
    public array $permissions = [];

    /**
     * @param array|string|null $role        Allowed role or annotation
     *                                       parameters.
     * @param array             $permissions Allowed permissions.
     *
     * @throws ReflectionException
     */
    public function __construct(
        $role = null,
        array $permissions = []
    ) {
        $this->permissions = $permissions;

        if (is_array($role)) {
            $this->initAnnotation($role);
        } else {
            $this->role = $role;
        }
    }

}
