<?php

declare(strict_types=1);

namespace KunicMarko\SonataAnnotationBundle\Annotation;

/**
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
    public ?string $role = null;

    /**
     * Allowed permissions.
     *
     * @var array
     */
    public array $permissions = [];

    public function getRole(): string
    {
        if ($this->role) {
            return $this->role;
        }

        throw new \InvalidArgumentException(
            sprintf(
                'Argument "role" is mandatory in "%s" annotation.',
                self::class
            )
        );
    }
}
