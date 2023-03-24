<?php

declare(strict_types=1);

namespace Neimheadh\SonataAnnotationBundle\DependencyInjection\Compiler;

use Composer\Autoload\ClassLoader;
use Exception;
use LogicException;
use Neimheadh\SonataAnnotationBundle\Annotation\Sonata\Admin;
use Neimheadh\SonataAnnotationBundle\AnnotationReader;
use Neimheadh\SonataAnnotationBundle\DependencyInjection\SonataAnnotationExtension;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

use function class_exists;

/**
 * Auto-registering Sonata annotated admin services compiler.
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
final class AutoRegisterCompilerPass implements CompilerPassInterface
{

    private const DEFAULT_SERVICE_PREFIX = 'app.admin.';

    /**
     * {@inheritDoc}
     *
     * @throws Exception
     */
    public function process(ContainerBuilder $container): void
    {
        $annotationReader = new AnnotationReader();

        $files = $this->findFiles(
            $container->getParameter(
                SonataAnnotationExtension::PARAM_ENTITY_NAMESPACE
            )
        );

        foreach ($files as $file) {
            if (!($className = $this->getFullyQualifiedClassName($file))) {
                continue;
            }

            if (!class_exists($className)) {
                continue;
            }

            $class = new ReflectionClass($className);
            if (!($annotation = $annotationReader->getClassAnnotation(
                $class,
                Admin::class
            ))) {
                continue;
            }

            $definition = new Definition(
                $annotation->admin,
                [
                    new Reference('sonata.annotation.reader.action_button'),
                    new Reference('sonata.annotation.reader.datagrid'),
                    new Reference('sonata.annotation.reader.datagrid_values'),
                    new Reference('sonata.annotation.reader.dashboard_action'),
                    new Reference('sonata.annotation.reader.export'),
                    new Reference('sonata.annotation.reader.form'),
                    new Reference('sonata.annotation.reader.list'),
                    new Reference('sonata.annotation.reader.route'),
                    new Reference('sonata.annotation.reader.show'),
                ]
            );

            $definition->addTag(
                'sonata.admin',
                array_merge(
                    $this->getAdminAnnotationOptions(
                        $container,
                        $class,
                        $annotation
                    ),
                    ['model_class' => $className],
                )
            );


            $container->setDefinition(
                $annotation->serviceId ?: $this->getServiceId($file),
                $definition
            );
        }
    }

    /**
     * List PHP files in given directory.
     *
     * @param string[] $namespaces Entity namespaces.
     *
     * @return iterable
     */
    private function findFiles(array $namespaces): iterable
    {
        $files = [];

        foreach ($namespaces as $namespace) {
            foreach ($this->findPsr4Directories($namespace) as $directory) {
                $files = array_merge(
                    $files,
                    array_values(
                        iterator_to_array(
                            Finder::create()
                                ->in($directory)
                                ->files()
                                ->name('*.php')
                                ->getIterator()
                        )
                    )
                );
            }
        }

        return $files;
    }

    /**
     * Get the PSR4 directory for the given namespace.
     *
     * @param string $namespace The namespace.
     *
     * @return iterable
     */
    private function findPsr4Directories(string $namespace): iterable
    {
        $loader = current(ClassLoader::getRegisteredLoaders());
        $psr4 = $loader->getPrefixesPsr4();
        $entitiesPsr4 = '';

        foreach ($psr4 as $ns => $dirs) {
            if (substr($namespace, 0, strlen($ns)) === $ns
                && strlen($ns) > strlen($entitiesPsr4)
            ) {
                $entitiesPsr4 = $ns;
            }
        }

        if ($entitiesPsr4 === '') {
            throw new LogicException(
                sprintf(
                    'Cannot find PS4 for namespace %s',
                    $namespace
                )
            );
        }

        return array_filter(
            array_map(
                fn(string $dir) => $dir . str_replace(
                        '\\',
                        '/',
                        substr($namespace, strlen($entitiesPsr4) - 1)
                    ),
                $psr4[$entitiesPsr4]
            ),
            fn(string $dir) => is_dir($dir)
        );
    }

    /**
     * Get admin annotation options with default option set.
     *
     * @param ContainerBuilder $container Container builder.
     * @param ReflectionClass  $class Annotated class.
     * @param Admin            $annotation Admin annotation.
     *
     * @return array
     */
    private function getAdminAnnotationOptions(
        ContainerBuilder $container,
        ReflectionClass $class,
        Admin $annotation
    ): array {
        $options = $annotation->getTagOptions();
        $options['label'] = $options['label'] ?: $class->getShortName();

        if (
            $options['group'] === null
            && $container->getParameter(
                SonataAnnotationExtension::PARAM_MENU_USE_NAMESPACE
            )
        ) {
            $current = $class->getNamespaceName();

            foreach (
                $container->getParameter(
                    SonataAnnotationExtension::PARAM_ENTITY_NAMESPACE
                ) as $namespace
            ) {
                if (substr($current, 0, strlen($namespace)) === $namespace) {
                    $options['group'] = str_replace(
                        '\\',
                        ' ',
                        substr($current, strlen($namespace))
                    );
                    break;
                }
            }
        }

        return $options;
    }

    /**
     * Get the given file associated full class name.
     *
     * @param SplFileInfo $file PHP class file.
     *
     * @return string|null
     */
    private function getFullyQualifiedClassName(SplFileInfo $file): ?string
    {
        if (!($namespace = $this->getNamespace($file->getPathname()))) {
            return null;
        }

        return $namespace . '\\' . $this->getClassName($file->getFilename());
    }

    /**
     * Get the given file associated namespace.
     *
     * @param string $filePath PHP class file path.
     *
     * @return string|null
     */
    private function getNamespace(string $filePath): ?string
    {
        $namespaceLine = preg_grep('/^namespace /', file($filePath));

        if (empty($namespaceLine)) {
            return null;
        }

        preg_match('/namespace (.*);$/', trim(reset($namespaceLine)), $match);

        return array_pop($match);
    }

    /**
     * Get the given file associated class name.
     *
     * @param string $fileName PHP class name.
     *
     * @return string
     */
    private function getClassName(string $fileName): string
    {
        return str_replace('.php', '', $fileName);
    }

    /**
     * Get default admin service id.
     *
     * @param SplFileInfo $file PHP class file.
     *
     * @return string
     */
    private function getServiceId(SplFileInfo $file): string
    {
        return self::DEFAULT_SERVICE_PREFIX . $this->getClassName(
                $file->getFilename()
            );
    }

}
