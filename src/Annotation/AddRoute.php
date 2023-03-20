<?php

declare(strict_types=1);

namespace Neimheadh\SonataAnnotationBundle\Annotation;

use Attribute;

/**
 * Add route annotation.
 *
 * Add custom route to your admin class.
 *
 * @Annotation
 * @Target("CLASS")
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class AddRoute implements RouteAnnotationInterface
{
    public const ID_PARAMETER = '{id}';

    /**
     * Route name.
     *
     * @var string
     */
    public string $name;

    /**
     * Route path.
     *
     * @var string
     */
    public string $path;
}
