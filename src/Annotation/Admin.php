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
     * @var string
     */
    public string $label;

    /**
     * Admin model manager type.
     *
     * @var string
     */
    public string $managerType = 'orm';

    /**
     * Admin group.
     *
     * @var string
     */
    public string $group;

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
     * @var string
     */
    public string $icon;

    /**
     * Admin label translator strategy.
     *
     * @var string
     */
    public string $labelTranslatorStrategy;

    /**
     * Admin label translation catalogue.
     *
     * @var string
     */
    public string $labelCatalogue;

    /**
     * Admin pager type.
     *
     * @var string
     */
    public string $pagerType;

    /**
     * Admin controller.
     *
     * @var string
     */
    public string $controller;

    /**
     * Admin service id.
     *
     * @var string
     */
    public string $serviceId;

    /**
     * Admin service class.
     *
     * @var string
     */
    public string $admin = AnnotationAdmin::class;

    /**
     * Admin code.
     *
     * @var string
     */
    public string $code;

    /**
     * Get service "sonata.admin" tag options.
     *
     * @return array<string|bool>
     */
    public function getTagOptions(): array
    {
        return [
            'code' => $this->code ?? null,
            'controller' => $this->controller ?? null,
            'manager_type' => $this->managerType,
            'group' => $this->group ?? null,
            'label' => $this->label ?? null,
            'show_in_dashboard' => $this->showInDashboard,
            'keep_open' => $this->keepOpen,
            'on_top' => $this->onTop,
            'icon' => $this->icon ?? null,
            'label_translator_strategy' => $this->labelTranslatorStrategy ?? null,
            'label_catalogue' => $this->labelCatalogue ?? null,
            'pager_type' => $this->pagerType ?? null,
        ];
    }

}
