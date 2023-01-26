<?php

declare(strict_types=1);

namespace KunicMarko\SonataAnnotationBundle\Reader;

use KunicMarko\SonataAnnotationBundle\Annotation\ActionButton;

/**
 * ActionButton annotation reader.
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
final class ActionButtonReader extends AbstractActionReader
{

    /**
     * {@inheritDoc}
     */
    protected function isSupported(object $annotation): bool
    {
        return $annotation instanceof ActionButton;
    }
}
