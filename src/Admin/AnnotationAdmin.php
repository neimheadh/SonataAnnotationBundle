<?php

declare(strict_types=1);

namespace Neimheadh\SonataAnnotationBundle\Admin;

use Exception;
use Neimheadh\SonataAnnotationBundle\Annotation\Sonata\AddRoute;
use Neimheadh\SonataAnnotationBundle\Annotation\Sonata\Admin;
use Neimheadh\SonataAnnotationBundle\Annotation\Sonata\RemoveRoute;
use Neimheadh\SonataAnnotationBundle\Reader\ActionButtonReader;
use Neimheadh\SonataAnnotationBundle\Reader\DashboardActionReader;
use Neimheadh\SonataAnnotationBundle\Reader\DatagridReader;
use Neimheadh\SonataAnnotationBundle\Reader\DatagridValuesReader;
use Neimheadh\SonataAnnotationBundle\Reader\ExportReader;
use Neimheadh\SonataAnnotationBundle\Reader\FormReader;
use Neimheadh\SonataAnnotationBundle\Reader\ListReader;
use Neimheadh\SonataAnnotationBundle\Reader\RouteReader;
use Neimheadh\SonataAnnotationBundle\Reader\ShowReader;
use ReflectionClass;
use ReflectionException;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * Auto-created admin class by annotations.
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
class AnnotationAdmin extends AbstractAdmin
{

    /**
     * Action buttons annotation reader.
     *
     * @var ActionButtonReader
     */
    private ActionButtonReader $actionButtonReader;

    /**
     * Datagrid annotation reader.
     *
     * @var DatagridReader
     */
    private DatagridReader $datagridReader;

    /**
     * Datagrid values annotation reader.
     *
     * @var DatagridValuesReader
     * @todo Replace the previously buildDatagrid() to use datagrid values
     *       reader.
     */
    private DatagridValuesReader $datagridValuesReader;

    /**
     * Dashboard actions annotation reader.
     *
     * @var DashboardActionReader
     */
    private DashboardActionReader $dashboardActionReader;

    /**
     * Export annotation reader.
     *
     * @var ExportReader
     */
    private ExportReader $exportReader;

    /**
     * Form page annotation reader.
     *
     * @var FormReader
     */
    private FormReader $formReader;

    /**
     * List page annotation reader.
     *
     * @var ListReader
     */
    private ListReader $listReader;

    /**
     * Route configuration annotation reader.
     *
     * @var RouteReader
     */
    private RouteReader $routeReader;

    /**
     * Show page annotation reader.
     *
     * @var ShowReader
     */
    private ShowReader $showReader;

    /**
     * @param array                 $options               Admin options.
     * @param ActionButtonReader    $actionButtonReader    Action buttons
     *                                                     annotation reader.
     * @param DatagridReader        $datagridReader        Datagrid annotation
     *                                                     reader.
     * @param DatagridValuesReader  $datagridValuesReader  Datagrid values
     *                                                     annotation reader.
     * @param DashboardActionReader $dashboardActionReader Dashboard actions
     *                                                     annotation reader.
     * @param ExportReader          $exportReader          Export annotation
     *                                                     reader.
     * @param FormReader            $formReader            Form page annotation
     *                                                     reader.
     * @param ListReader            $listReader            List page annotation
     *                                                     reader.
     * @param RouteReader           $routeReader           Route configuration
     *                                                     annotation reader.
     * @param ShowReader            $showReader            Show page annotation
     *                                                     reader.
     */
    public function __construct(
        private array $options,
        ActionButtonReader $actionButtonReader,
        DatagridReader $datagridReader,
        DatagridValuesReader $datagridValuesReader,
        DashboardActionReader $dashboardActionReader,
        ExportReader $exportReader,
        FormReader $formReader,
        ListReader $listReader,
        RouteReader $routeReader,
        ShowReader $showReader
    ) {
        parent::__construct();

        $this->actionButtonReader = $actionButtonReader;
        $this->datagridReader = $datagridReader;
        $this->datagridValuesReader = $datagridValuesReader;
        $this->dashboardActionReader = $dashboardActionReader;
        $this->listReader = $listReader;
        $this->exportReader = $exportReader;
        $this->formReader = $formReader;
        $this->routeReader = $routeReader;
        $this->showReader = $showReader;
    }

    /**
     * Get the list of exported formats.
     *
     * @return array<string>
     * @throws ReflectionException
     */
    public function getExportFormats(): array
    {
        return $this->exportReader->getFormats(
            $this->getReflectionClass()
        ) ?: parent::getExportFormats();
    }

    /**
     * Configure action buttons.
     *
     * @param array       $buttonList Base button list.
     * @param string      $action     Current action.
     * @param object|null $object     Current object.
     *
     * @return array
     * @throws ReflectionException
     */
    protected function configureActionButtons(
        array $buttonList,
        string $action,
        ?object $object = null
    ): array {
        return $this->actionButtonReader
            ->getActions(
                $this->getReflectionClass(),
                parent::configureActionButtons(
                    $buttonList,
                    $action,
                    $object
                )
            );
    }

    /**
     * Configure dashboard actions.
     *
     * @param array<string, array<string, mixed>> $actions Base actions.
     *
     * @return array<string, array<string, mixed>>
     * @throws ReflectionException
     * @throws Exception
     */
    protected function configureDashboardActions(array $actions): array
    {
        return $this->dashboardActionReader
            ->getActions(
                $this->getReflectionClass(),
                parent::configureDashboardActions($actions)
            );
    }

    /**
     * Configure datagrid filters.
     *
     * @param DatagridMapper $filter Datagrid filter mapper.
     *
     * @return void
     * @throws ReflectionException
     */
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $this->datagridReader->configureFields(
            $this->getReflectionClass(),
            $filter
        );
    }

    /**
     * Configure default sorting values.
     *
     * @param array $sortValues Sorting values.
     *
     * @return void
     */
    protected function configureDefaultSortValues(array &$sortValues): void
    {
        $values = $this->options[Admin::OPTION_SORT_VALUES] ?? [];
        $fields = [
            DatagridInterface::PAGE,
            DatagridInterface::PER_PAGE,
            DatagridInterface::SORT_ORDER,
            DatagridInterface::SORT_BY,
        ];

        foreach ($fields as $field) {
            if (($values[$field] ?? null) !== null) {
                $sortValues[$field] = $values[$field];
            }
        }
    }

    /**
     * Get exported fields.
     *
     * @return array<string>
     * @throws ReflectionException
     */
    protected function configureExportFields(): array
    {
        return $this->exportReader->getFields(
            $this->getReflectionClass()
        ) ?: parent::getExportFields();
    }

    /**
     * Configure form page fields.
     *
     * @param FormMapper $form Form mapper.
     *
     * @return void
     * @throws ReflectionException
     */
    protected function configureFormFields(FormMapper $form): void
    {
        if ($this->getRequest()->get($this->getIdParameter())) {
            $this->formReader->configureEditFields(
                $this->getReflectionClass(),
                $form
            );
            return;
        }

        $this->formReader->configureCreateFields(
            $this->getReflectionClass(),
            $form
        );
    }

    /**
     * Configure list page fields.
     *
     * @param ListMapper $list List mapper.
     *
     * @return void
     * @throws ReflectionException
     */
    protected function configureListFields(ListMapper $list): void
    {
        $this->listReader
            ->configureFields(
                $this->getReflectionClass(),
                $list
            );
    }

    /**
     * Configure routes.
     *
     * @param RouteCollectionInterface $collection Route collection.
     *
     * @return void
     * @throws ReflectionException
     */
    protected function configureRoutes(
        RouteCollectionInterface $collection
    ): void {
        [$addRoutes, $removeRoutes] = $this->routeReader->getRoutes(
            $this->getReflectionClass()
        );

        /** @var AddRoute $route */
        foreach ($addRoutes as $route) {
            $collection->add(
                $route->name,
                $route->path ? $this->replaceIdParameterInRoutePath(
                    $route->path
                ) : $route->getName()
            );
        }

        /** @var RemoveRoute $route */
        foreach ($removeRoutes as $route) {
            $collection->remove($route->name);
        }
    }

    /**
     * Configure show page fields.
     *
     * @param ShowMapper $show Show mapper.
     *
     * @return void
     * @throws ReflectionException
     */
    protected function configureShowFields(ShowMapper $show): void
    {
        $this->showReader->configureFields(
            $this->getReflectionClass(),
            $show
        );
    }

    /**
     * Replace the '{id}' part in the given path with the currently managed
     * object id.
     *
     * @param string $path Path template.
     *
     * @return string
     */
    private function replaceIdParameterInRoutePath(string $path): string
    {
        return str_replace(
            AddRoute::ID_PARAMETER,
            $this->getRouterIdParameter(),
            $path
        );
    }

    /**
     * Get the reflection class of the current admin model.
     *
     * @return ReflectionClass
     * @throws ReflectionException
     */
    private function getReflectionClass(): ReflectionClass
    {
        return new ReflectionClass($this->getClass());
    }

}
