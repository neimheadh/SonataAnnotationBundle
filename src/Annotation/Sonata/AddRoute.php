<?php

declare(strict_types=1);

namespace Neimheadh\SonataAnnotationBundle\Annotation\Sonata;

use Attribute;
use Neimheadh\SonataAnnotationBundle\Annotation\AbstractAnnotation;
use Neimheadh\SonataAnnotationBundle\Annotation\RouteAnnotationInterface;
use ReflectionException;

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
#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final class AddRoute extends AbstractAnnotation implements
    RouteAnnotationInterface
{

    public const ID_PARAMETER = '{id}';

    /**
     * Route name.
     *
     * @var string|null
     */
    public ?string $name = null;

    /**
     * Route path.
     *
     * @var string|null
     */
    public ?string $path = null;

    /**
     * @param string|array|null $name Route name or annotation parameters.
     * @param string|null       $path Route path.
     *
     * @throws ReflectionException
     */
    public function __construct(
        $name = null,
        ?string $path = null
    ) {
        $this->path = $path;

        if (is_array($name)) {
            $this->initAnnotation($name);
        } else {
            $this->name = $name;
        }
    }

}
