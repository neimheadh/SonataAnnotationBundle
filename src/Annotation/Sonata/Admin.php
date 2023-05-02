<?php

declare(strict_types=1);

namespace Neimheadh\SonataAnnotationBundle\Annotation\Sonata;

use Attribute;
use InvalidArgumentException;
use Neimheadh\SonataAnnotationBundle\Admin\AnnotationAdmin;
use Neimheadh\SonataAnnotationBundle\Annotation\AbstractAnnotation;
use ReflectionException;
use Sonata\AdminBundle\Datagrid\DatagridInterface;

/**
 * Admin annotation.
 *
 * Auto-build the admin service of your model class.
 *
 * @Annotation
 * @Target("CLASS")
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 *
 * @property FormField[] $formFields
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class Admin extends AbstractAnnotation
{

    /**
     * Sort values option name.
     */
    public const OPTION_SORT_VALUES = 'sort_values';

    /**
     * Admin service class.
     *
     * @var string
     */
    public string $admin = AnnotationAdmin::class;

    /**
     * Admin code.
     *
     * @var string|null
     */
    public ?string $code = null;

    /**
     * Admin controller.
     *
     * @var string|null
     */
    public ?string $controller = null;

    /**
     * Default sort order.
     *
     * @var string
     */
    public string $defaultOrder = 'ASC';

    /**
     * Default list first page.
     *
     * @var int
     */
    public int $defaultPage = 1;

    /**
     * Default page size.
     *
     * @var int|null
     */
    public ?int $defaultPageSize = null;

    /**
     * Default sort field.
     *
     * @var string|null
     */
    public ?string $defaultSort = null;

    /**
     * Admin group.
     *
     * @var string|null
     */
    public ?string $group = null;

    /**
     * Admin link icon.
     *
     * @var string|null
     */
    public ?string $icon = null;

    /**
     * Is admin is kept open.
     *
     * @var bool
     */
    public bool $keepOpen = false;

    /**
     * Admin label.
     *
     * @var string|null
     */
    public ?string $label = null;

    /**
     * Admin label translation catalogue.
     *
     * @var string|null
     */
    public ?string $labelCatalogue = null;

    /**
     * Admin label translator strategy.
     *
     * @var string|null
     */
    public ?string $labelTranslatorStrategy = null;

    /**
     * Admin model manager type.
     *
     * @var string
     */
    public string $managerType = 'orm';

    /**
     * Is admin a top menu?
     *
     * This option put your admin link directly in the menu and not as a
     * sub-menu.
     *
     * @var bool
     */
    public bool $onTop = false;

    /**
     * Admin pager type.
     *
     * @var string|null
     */
    public ?string $pagerType = null;

    /**
     * Admin service id.
     *
     * @var string|null
     */
    public ?string $serviceId = null;

    /**
     * Is admin shown in dashboard?
     *
     * @var bool
     */
    public bool $showInDashboard = true;

    /**
     * Datagrid fields.
     *
     * @var array<DatagridField>
     */
    private array $datagridFields = [];

    /**
     * Export fields.
     *
     * @var array<ExportField>
     */
    private array $exportFields = [];

    /**
     * Form fields.
     *
     * @var array<FormField>
     */
    private array $formFields = [];

    /**
     * List fields.
     *
     * @var array<ListField>
     */
    private array $listFields = [];

    /**
     * Show fields.
     *
     * @var array<ShowField>
     */
    private array $showFields = [];

    /**
     * @param array|string|null $label                    Label or annotation
     *                                                    parameters.
     * @param string            $managerType              Model manager type.
     * @param string|null       $group                    Admin group.
     * @param bool              $showInDashboard          Show in dashboard?
     * @param bool              $keepOpen                 Keep open.
     * @param bool              $onTop                    Is admin a top menu?
     * @param string|null       $icon                     Admin link icon.
     * @param string|null       $labelTranslatorStrategy  Label translator
     *                                                    strategy.
     * @param string|null       $labelCatalogue           Admin label
     *                                                    translation
     *                                                    catalogue.
     * @param string|null       $pagerType                Pager type.
     * @param string|null       $controller               Controller.
     * @param string|null       $serviceId                Service id.
     * @param string            $admin                    Service class.
     * @param string|null       $code                     Code.
     * @param DatagridField[]   $datagridFields           Datagrid fields.
     * @param FormField[]       $formFields               Form fields.
     * @param ListField[]       $listFields               List fields.
     * @param ShowField[]       $showFields               Show fields.
     *
     * @throws ReflectionException
     */
    public function __construct(
        array|string $label = null,
        string $managerType = 'orm',
        ?string $group = null,
        bool $showInDashboard = true,
        bool $keepOpen = false,
        bool $onTop = false,
        ?string $icon = null,
        ?string $labelTranslatorStrategy = null,
        ?string $labelCatalogue = null,
        ?string $pagerType = null,
        ?string $controller = null,
        ?string $serviceId = null,
        string $admin = AnnotationAdmin::class,
        ?string $code = null,
        array $datagridFields = [],
        array $formFields = [],
        array $listFields = [],
        array $showFields = [],
        array $exportFields = [],
        int $defaultPage = 1,
        string $defaultOrder = 'ASC',
        ?string $defaultSort = null,
        ?int $defaultPageSize = null,
    ) {
        $this->managerType = $managerType;
        $this->group = $group;
        $this->showInDashboard = $showInDashboard;
        $this->keepOpen = $keepOpen;
        $this->onTop = $onTop;
        $this->icon = $icon;
        $this->labelTranslatorStrategy = $labelTranslatorStrategy;
        $this->labelCatalogue = $labelCatalogue;
        $this->pagerType = $pagerType;
        $this->controller = $controller;
        $this->serviceId = $serviceId;
        $this->admin = $admin;
        $this->code = $code;
        $this->defaultPage = $defaultPage;
        $this->defaultOrder = $defaultOrder;
        $this->defaultSort = $defaultSort;
        $this->defaultPageSize = $defaultPageSize;

        $this->setDatagridFields($datagridFields)
            ->setFormFields($formFields)
            ->setListFields($listFields)
            ->setShowFields($showFields)
            ->setExportFields($exportFields);

        if (is_array($label)) {
            $this->initAnnotation($label);
        } else {
            $this->label = $label;
        }
    }

    /**
     * Get admin options.
     *
     * @return array
     */
    public function getOptions(): array
    {
        return [
            self::OPTION_SORT_VALUES => [
                DatagridInterface::PAGE => $this->defaultPage,
                DatagridInterface::PER_PAGE => $this->defaultPageSize,
                DatagridInterface::SORT_BY => $this->defaultSort,
                DatagridInterface::SORT_ORDER => $this->defaultOrder,
            ]
        ];
    }

    /**
     * Get service "sonata.admin" tag options.
     *
     * @return array<string|bool>
     */
    public function getTagOptions(): array
    {
        return [
            'code' => $this->code,
            'controller' => $this->controller,
            'manager_type' => $this->managerType,
            'group' => $this->group,
            'label' => $this->label,
            'show_in_dashboard' => $this->showInDashboard,
            'keep_open' => $this->keepOpen,
            'on_top' => $this->onTop,
            'icon' => $this->icon,
            'label_translator_strategy' => $this->labelTranslatorStrategy,
            'label_catalogue' => $this->labelCatalogue,
            'pager_type' => $this->pagerType,
        ];
    }

    /**
     * Get datagrid fields.
     *
     * @return array<DatagridField>
     */
    public function getDatagridFields(): array
    {
        return $this->datagridFields;
    }

    /**
     * Get export fields.
     *
     * @return array<ExportField>
     */
    public function getExportFields(): array
    {
        return $this->exportFields;
    }

    /**
     * Get form fields.
     *
     * @return array<FormField>
     */
    public function getFormFields(): array
    {
        return $this->formFields;
    }

    /**
     * Get list fields.
     *
     * @return array<ListField>
     */
    public function getListFields(): array
    {
        return $this->listFields;
    }

    /**
     * Get show fields.
     *
     * @return array<ShowField>
     */
    public function getShowFields(): array
    {
        return $this->showFields;
    }

    /**
     * Set datagrid fields.
     *
     * @param array<DatagridField> $datagridFields Datagrid fields.
     *
     * @return $this
     */
    public function setDatagridFields(array $datagridFields): self
    {
        ($e = $this->getInvalidArrayException(
            DatagridField::class,
            $datagridFields
        )) && throw $e;

        $this->datagridFields = $datagridFields;

        return $this;
    }

    /**
     * Set export fields.
     *
     * @param array<ExportField> $exportFields Export fields.
     *
     * @return $this
     */
    public function setExportFields(array $exportFields): self
    {
        ($e = $this->getInvalidArrayException(
            ExportField::class,
            $exportFields
        )) && throw $e;

        $this->exportFields = $exportFields;

        return $this;
    }

    /**
     * Set form fields.
     *
     * @param array<FormField> $formFields Form fields.
     *
     * @return $this
     */
    public function setFormFields(array $formFields): self
    {
        ($e = $this->getInvalidArrayException(
            FormField::class,
            $formFields
        )) && throw $e;

        $this->formFields = $formFields;

        return $this;
    }

    /**
     * Set list fields.
     *
     * @param array<ListField> $listFields List fields.
     *
     * @return $this
     */
    public function setListFields(array $listFields): self
    {
        ($e = $this->getInvalidArrayException(
            ListField::class,
            $listFields
        )) && throw $e;

        $this->listFields = $listFields;

        return $this;
    }

    /**
     * Set show fields.
     *
     * @param array<ShowField> $showFields Show fields.
     *
     * @return $this
     */
    public function setShowFields(array $showFields): self
    {
        ($e = $this->getInvalidArrayException(
            ShowField::class,
            $showFields
        )) && throw $e;

        $this->showFields = $showFields;

        return $this;
    }

    /**
     * Get invalid argument exception for typed array.
     *
     * @param string $class Excepted class.
     * @param array  $array Given array.
     *
     * @return InvalidArgumentException|null
     */
    private function getInvalidArrayException(
        string $class,
        array $array
    ): ?InvalidArgumentException {
        foreach ($array as $entry) {
            if (!is_object($entry) || !$entry instanceof $class) {
                return new InvalidArgumentException(
                    sprintf(
                        'Array of %s expected, array contains an %s element.',
                        $class,
                        is_object($entry) ? $entry::class : gettype($entry)
                    )
                );
            }
        }

        return null;
    }

}
