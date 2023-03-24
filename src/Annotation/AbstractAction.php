<?php

declare(strict_types=1);

namespace Neimheadh\SonataAnnotationBundle\Annotation;

use ReflectionException;

/**
 * Action annotations main class.
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
abstract class AbstractAction extends AbstractAnnotation
{

    /**
     * Action template.
     *
     * @var string|null
     */
    public ?string $template = null;

    /**
     * @param string|array|null $template Action template or annotation
     *                                    parameters.
     *
     * @throws ReflectionException
     */
    public function __construct(
        $template = null
    ) {
        if (is_array($template)) {
            $this->initAnnotation($template);
        } else {
            $this->template = $template;
        }
    }

}
