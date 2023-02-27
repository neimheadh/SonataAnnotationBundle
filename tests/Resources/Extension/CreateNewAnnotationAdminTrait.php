<?php

namespace KunicMarko\SonataAnnotationBundle\Tests\Resources\Extension;

use KunicMarko\SonataAnnotationBundle\Admin\AnnotationAdmin;
use KunicMarko\SonataAnnotationBundle\Reader\ActionButtonReader;
use KunicMarko\SonataAnnotationBundle\Reader\DashboardActionReader;
use KunicMarko\SonataAnnotationBundle\Reader\DatagridReader;
use KunicMarko\SonataAnnotationBundle\Reader\DatagridValuesReader;
use KunicMarko\SonataAnnotationBundle\Reader\ExportReader;
use KunicMarko\SonataAnnotationBundle\Reader\FormReader;
use KunicMarko\SonataAnnotationBundle\Reader\ListReader;
use KunicMarko\SonataAnnotationBundle\Reader\RouteReader;
use KunicMarko\SonataAnnotationBundle\Reader\ShowReader;
use KunicMarko\SonataAnnotationBundle\Tests\Resources\Model\Book;
use LogicException;
use Sonata\AdminBundle\Builder\DatagridBuilderInterface;
use Sonata\AdminBundle\Builder\ListBuilderInterface;
use Sonata\AdminBundle\Builder\RouteBuilderInterface;
use Sonata\AdminBundle\Builder\ShowBuilderInterface;
use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionFactoryInterface;
use Sonata\AdminBundle\Model\ModelManagerInterface;
use Sonata\AdminBundle\Route\RouteGeneratorInterface;
use Sonata\AdminBundle\Security\Handler\SecurityHandlerInterface;
use Sonata\AdminBundle\Translator\LabelTranslatorStrategyInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\TestContainer;

/**
 * Add createNewAnnotationAdmin function to KernelBrowser tests.
 */
trait CreateNewAnnotationAdminTrait
{

    /**
     * Create a new annotation admin.
     *
     * @param string $class Model class.
     *
     * @return AnnotationAdmin
     */
    private function createNewAnnotationAdmin(
      string $class = Book::class
    ): AnnotationAdmin {
        if (!$this instanceof KernelTestCase) {
            throw new LogicException(
              sprintf(
                '%s trait can only be used by %s classes.',
                CreateNewAnnotationAdminTrait::class,
                KernelTestCase::class,
              )
            );
        }

        /** @var TestContainer $container */
        $container = static::getContainer();

        /** @var ActionButtonReader $actionButtonReader */
        $actionButtonReader = $container->get(
          'sonata.annotation.reader.action_button'
        );
        /** @var DatagridReader $datagridReader */
        $datagridReader = $container->get('sonata.annotation.reader.datagrid');
        /** @var DatagridValuesReader $datagridValuesReader */
        $datagridValuesReader = $container->get(
          'sonata.annotation.reader.datagrid_values'
        );
        /** @var DashboardActionReader $dashboardActionReader */
        $dashboardActionReader = $container->get(
          'sonata.annotation.reader.dashboard_action'
        );
        /** @var ExportReader $exportReader */
        $exportReader = $container->get('sonata.annotation.reader.export');
        /** @var FormReader $formReader */
        $formReader = $container->get('sonata.annotation.reader.form');
        /** @var ListReader $listReader */
        $listReader = $container->get('sonata.annotation.reader.list');
        /** @var RouteReader $routeReader */
        $routeReader = $container->get('sonata.annotation.reader.route');
        /** @var ShowReader $showReader */
        $showReader = $container->get('sonata.annotation.reader.show');
        /** @var ModelManagerInterface $modelManager */
        $modelManager = $container->get('sonata.admin.manager.orm');
        /** @var ListBuilderInterface $listBuilder */
        $listBuilder = $container->get('sonata.admin.builder.orm_list');
        /** @var RouteGeneratorInterface $routeGenerator */
        $routeGenerator = $container->get(
          'sonata.admin.route.default_generator'
        );
        /** @var RouteBuilderInterface $routeBuilder */
        $routeBuilder = $container->get('sonata.admin.route.path_info');
        /** @var SecurityHandlerInterface $security */
        $security = $container->get('sonata.admin.security.handler.noop');
        /** @var FieldDescriptionFactoryInterface $fieldDescriptionFactory */
        $fieldDescriptionFactory = $container->get(
          'sonata.admin.field_description_factory.orm'
        );
        /** @var LabelTranslatorStrategyInterface $labelTranslationStrategy */
        $labelTranslationStrategy = $container->get(
          'sonata.admin.label.strategy.native'
        );
        /** @var DatagridBuilderInterface $datagridBuilder */
        $datagridBuilder = $container->get('sonata.admin.builder.orm_datagrid');
        /** @var ShowBuilderInterface $showBuilder */
        $showBuilder = $container->get('sonata.admin.builder.orm_show');

        $admin = new AnnotationAdmin(
          $actionButtonReader,
          $datagridReader,
          $datagridValuesReader,
          $dashboardActionReader,
          $exportReader,
          $formReader,
          $listReader,
          $routeReader,
          $showReader,
        );

        $admin->setModelManager($modelManager);
        $admin->setModelClass($class);
        $admin->setListBuilder($listBuilder);
        $admin->setRouteGenerator($routeGenerator);
        $admin->setBaseControllerName(CRUDController::class);
        $admin->setRouteBuilder($routeBuilder);
        $admin->setSecurityHandler($security);
        $admin->setFieldDescriptionFactory($fieldDescriptionFactory);
        $admin->setLabelTranslatorStrategy($labelTranslationStrategy);
        $admin->setDatagridBuilder($datagridBuilder);
        $admin->setShowBuilder($showBuilder);

        return $admin;
    }

}