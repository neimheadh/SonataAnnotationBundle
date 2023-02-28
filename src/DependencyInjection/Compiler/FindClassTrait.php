<?php

declare(strict_types=1);

namespace Neimheadh\SonataAnnotationBundle\DependencyInjection\Compiler;

use LogicException;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Find class function trait.
 *
 * @internal
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
trait FindClassTrait
{

    /**
     * Get the class associated to the given admin service id.
     *
     * @param ContainerBuilder $container Kernel container.
     * @param string           $id        Admin service id.
     *
     * @return string|null
     */
    private function getClass(ContainerBuilder $container, string $id): ?string
    {
        $definition = $container->getDefinition($id);

        //Entity can be a class or a parameter
        $tag = current($definition->getTag('sonata.admin'));
        $class = $tag['model_class'] ?? '';

        if ($class[0] !== '%') {
            return $class;
        }

        if ($container->hasParameter($class = trim($class, '%'))) {
            return $container->getParameter($class);
        }

        throw new LogicException(sprintf(
            'Service "%s" has a parameter "%s" as an argument but it is not found.',
            $id,
            $class
        ));
    }
}
