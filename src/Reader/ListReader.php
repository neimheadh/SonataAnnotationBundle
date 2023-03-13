<?php

declare(strict_types=1);

namespace Neimheadh\SonataAnnotationBundle\Reader;

use Doctrine\Common\Annotations\Reader;
use Neimheadh\SonataAnnotationBundle\Annotation\ListAction;
use Neimheadh\SonataAnnotationBundle\Annotation\ListField;
use Neimheadh\SonataAnnotationBundle\Exception\MissingAnnotationArgumentException;
use ReflectionClass;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Mapper\MapperInterface;

/**
 * List configuration reader.
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
final class ListReader extends AbstractFieldConfigurationReader
{

    /**
     * {@inheritDoc}
     */
    public function __construct(Reader $annotationReader)
    {
        parent::__construct($annotationReader, ListField::class);
    }

    /**
     * {@inheritDoc}
     *
     * @param ListMapper $mapper Admin list mapper.
     */
    public function configureFields(
        ReflectionClass $class,
        MapperInterface $mapper
    ): void {
        parent::configureFields($class, $mapper);

        if ($mapper instanceof ListMapper) {
            if ($actions = $this->getListActions($class)) {
                $mapper->add(ListMapper::NAME_ACTIONS, null, [
                    'actions' => $actions,
                ]);
            }
        }
    }

    /**
     * Get list of actions.
     *
     * @param ReflectionClass $class Entity class.
     *
     * @return array
     */
    private function getListActions(ReflectionClass $class): array
    {
        $actions = [];
        /** @var array<object|ListAction> $annotations */
        $annotations = $this->annotationReader->getClassAnnotations($class);

        foreach ($annotations as $annotation) {
            if ($annotation instanceof ListAction) {
                if (!isset($annotation->name)) {
                    throw new MissingAnnotationArgumentException(
                        $annotation,
                        'name',
                        $class
                    );
                }

                $actions[$annotation->name] = $annotation->options;
            }
        }

        return $actions;
    }

}
