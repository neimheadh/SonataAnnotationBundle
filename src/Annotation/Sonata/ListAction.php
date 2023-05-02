<?php

declare(strict_types=1);

namespace Neimheadh\SonataAnnotationBundle\Annotation\Sonata;

use Attribute;
use Neimheadh\SonataAnnotationBundle\Annotation\AbstractAnnotation;
use ReflectionException;

/**
 * List action annotation.
 *
 * Allows you to configure your list page actions.
 *
 * @Annotation
 * @Target("CLASS")
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final class ListAction extends AbstractAnnotation
{

    /**
     * Action name.
     *
     * @var string|null
     */
    public ?string $name = null;

    /**
     * Action options.
     *
     * @var array
     */
    public array $options = [];

    /**
     * @param string|array|null $name    Action name or annotation parameters.
     * @param array             $options Action options.
     *
     * @throws ReflectionException
     */
    public function __construct(
        $name = null,
        array $options = []
    ) {
        $this->options = $options;

        if (is_array($name)) {
            $this->initAnnotation($name);
        } else {
            $this->name = $name;
        }
    }

}
