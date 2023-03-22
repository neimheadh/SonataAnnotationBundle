<?php

declare(strict_types=1);

namespace Neimheadh\SonataAnnotationBundle\Annotation;

use Attribute;
use Neimheadh\SonataAnnotationBundle\Admin\AnnotationAdmin;

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
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class Admin implements AnnotationInterface
{

    /**
     * Admin label.
     *
     * @var string|null
     */
    public ?string $label = null;

    /**
     * Admin model manager type.
     *
     * @var string
     */
    public string $managerType = 'orm';

    /**
     * Admin group.
     *
     * @var string|null
     */
    public ?string $group = null;

    /**
     * Is admin shown in dashboard?
     *
     * @var bool
     */
    public bool $showInDashboard = true;

    /**
     * Is admin is kept open.
     *
     * @var bool
     */
    public bool $keepOpen = false;

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
     * Admin link icon.
     *
     * @var string|null
     */
    public ?string $icon = null;

    /**
     * Admin label translator strategy.
     *
     * @var string|null
     */
    public ?string $labelTranslatorStrategy = null;

    /**
     * Admin label translation catalogue.
     *
     * @var string|null
     */
    public ?string $labelCatalogue = null;

    /**
     * Admin pager type.
     *
     * @var string|null
     */
    public ?string $pagerType = null;

    /**
     * Admin controller.
     *
     * @var string|null
     */
    public ?string $controller = null;

    /**
     * Admin service id.
     *
     * @var string|null
     */
    public ?string $serviceId = null;

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
     * @param string|array|null $label                    Label or annotation
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
     */
    public function __construct(
        $label = null,
        string $managerType = 'orm',
        ?string $group = null,
        bool $showInDashboard = true,
        bool $keepOpen = true,
        bool $onTop = false,
        ?string $icon = null,
        ?string $labelTranslatorStrategy = null,
        ?string $labelCatalogue = null,
        ?string $pagerType = null,
        ?string $controller = null,
        ?string $serviceId = null,
        string $admin = AnnotationAdmin::class,
        ?string $code = null
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

        if (is_array($label)) {
            foreach ($label as $name => $value) {
                $this->$name = $value;
            }
        } else {
            $this->label = $label;
        }
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

}
