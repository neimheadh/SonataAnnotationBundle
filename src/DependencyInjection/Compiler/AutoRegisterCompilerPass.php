<?php

declare(strict_types=1);

namespace Neimheadh\SonataAnnotationBundle\DependencyInjection\Compiler;

use Doctrine\Common\Annotations\Reader;
use Exception;
use Neimheadh\SonataAnnotationBundle\Annotation\Admin;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
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
        /** @var Reader $annotationReader */
        $annotationReader = $container->get('annotation_reader');

        $files = $this->findFiles(
            $container->getParameter('sonata_annotation.directory')
        );

        foreach ($files as $file) {
            if (!($className = $this->getFullyQualifiedClassName($file))) {
                continue;
            }

            if (!class_exists($className)) {
                continue;
            }

            if (!($annotation = $annotationReader->getClassAnnotation(
                new ReflectionClass($className),
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
                    $annotation->getTagOptions(),
                    ['model_class' => $className],
                )
            );

            $container->setDefinition(
                $annotation->serviceId ?? $this->getServiceId($file),
                $definition
            );
        }
    }

    /**
     * List PHP files in given directory.
     *
     * @param string $directory Directory path.
     *
     * @return iterable
     */
    private function findFiles(string $directory): iterable
    {
        try {
            return Finder::create()
                ->in($directory)
                ->files()
                ->name('*.php');
        } catch (DirectoryNotFoundException $e) {
        }

        return [];
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
