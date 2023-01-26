<?php

declare(strict_types=1);

namespace KunicMarko\SonataAnnotationBundle\Annotation;

/**
 * Action annotations main class.
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
abstract class AbstractAction implements AnnotationInterface
{
    /**
     * Action template.
     *
     * @var string
     */
    public string $template;
}
