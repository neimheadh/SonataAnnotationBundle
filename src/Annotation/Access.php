<?php

declare(strict_types=1);

namespace Neimheadh\SonataAnnotationBundle\Annotation;

use Attribute;

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
#[Attribute(Attribute::TARGET_CLASS)]
final class Access implements AnnotationInterface
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
     */
    public function __construct(
        $role = null,
        array $permissions = []
    ) {
        $this->permissions = $permissions;

        if (is_array($role)) {
            foreach ($role as $name => $value) {
                $this->$name = $value;
            }
        } else {
            $this->role = $role;
        }
    }

}
