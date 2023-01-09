<?php

declare(strict_types=1);

namespace KunicMarko\SonataAnnotationBundle\DependencyInjection\Compiler;

use Doctrine\Common\Annotations\Reader;
use KunicMarko\SonataAnnotationBundle\Annotation\Access;
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
     * @var Reader|null
     */
    private ?Reader $annotationReader = null;

    /**
     * {@inheritDoc}
     *
     * @throws ReflectionException
     */
    public function process(ContainerBuilder $container): void
    {
        $this->annotationReader = $container->get('annotation_reader');
        $roles = $container->getParameter('security.role_hierarchy.roles');
        $services = $container->findTaggedServiceIds('sonata.admin');

        foreach ($services as $id => $tag) {
            $class = $this->getClass($container, $id);

            if ($permissions = $this->getRoles(
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
     * @param ReflectionClass $class  Service class.
     * @param string          $prefix Service role name (permission roles
     *                                prefix).
     *
     * @return array
     */
    private function getRoles(ReflectionClass $class, string $prefix): array
    {
        $roles = [];
        $annotations = $this->annotationReader->getClassAnnotations($class);

        foreach ($annotations as $annotation) {
            if (!$annotation instanceof Access) {
                continue;
            }

            $roles[$annotation->getRole()] = array_map(
              function (string $permission) use ($prefix) {
                  return $prefix . strtoupper($permission);
              },
              $annotation->permissions
            );
        }

        return $roles;
    }

}
