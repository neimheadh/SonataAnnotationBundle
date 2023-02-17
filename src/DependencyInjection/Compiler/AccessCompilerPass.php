<?php

declare(strict_types=1);

namespace KunicMarko\SonataAnnotationBundle\DependencyInjection\Compiler;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use InvalidArgumentException;
use KunicMarko\SonataAnnotationBundle\Annotation\Access;
use KunicMarko\SonataAnnotationBundle\Exception\MissingAnnotationArgumentException;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Bundle compiler pass.
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
final class AccessCompilerPass implements CompilerPassInterface
{

    use FindClassTrait;

    /**
     * {@inheritDoc}
     *
     * @throws ReflectionException
     */
    public function process(ContainerBuilder $container): void
    {
        /**
         * @var AnnotationReader $annotationReader
         */
        $annotationReader = $container->get('annotation_reader');
        $roles = $container->getParameter('security.role_hierarchy.roles');
        $services = $container->findTaggedServiceIds('sonata.admin');

        foreach ($services as $id => $tag) {
            $class = $this->getClass($container, $id);

            if ($permissions = $this->getRoles(
                $annotationReader,
                new ReflectionClass($class),
                $this->getRolePrefix($id)
            )) {
                $roles = array_merge_recursive($roles, $permissions);
            }
        }

        $container->setParameter('security.role_hierarchy.roles', $roles);
    }

    /**
     * Get service role name.
     *
     * @param string $serviceId Service name.
     *
     * @return string
     */
    private function getRolePrefix(string $serviceId): string
    {
        return 'ROLE_' . str_replace('.', '_', strtoupper($serviceId)) . '_';
    }

    /**
     * Get the list of permissions associated roles.
     *
     * @param AnnotationReader $annotationReader Doctrine annotation reader.
     * @param ReflectionClass  $class            Service class.
     * @param string           $prefix           Service role name (permission
     *                                           roles prefix).
     *
     * @return array
     */
    private function getRoles(
        AnnotationReader $annotationReader,
        ReflectionClass $class,
        string $prefix
    ): array {
        $roles = [];
        $annotations = $annotationReader->getClassAnnotations($class);

        foreach ($annotations as $annotation) {
            if (!$annotation instanceof Access) {
                continue;
            }

            if (!isset($annotation->role)) {
                throw new MissingAnnotationArgumentException(
                    $annotation,
                    'role',
                    $class
                );
            }

            $roles[$annotation->role] = array_map(
                function (string $permission) use ($prefix) {
                    return $prefix . strtoupper($permission);
                },
                $annotation->permissions
            );
        }

        return $roles;
    }

}
