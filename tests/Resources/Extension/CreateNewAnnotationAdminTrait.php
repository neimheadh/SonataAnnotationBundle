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
use LogicException;
use Sonata\AdminBundle\Builder\ListBuilderInterface;
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
     * @return AnnotationAdmin
     */
    private function createNewAnnotationAdmin(): AnnotationAdmin
    {
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
        /** @var ListBuilderInterface $listBuilder */
        $listBuilder = $container->get('sonata.admin.builder.orm_list');

        return new AnnotationAdmin(
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
    }
}