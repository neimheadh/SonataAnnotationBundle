<?php

declare(strict_types=1);

namespace KunicMarko\SonataAnnotationBundle\Reader;

use KunicMarko\SonataAnnotationBundle\Annotation\DashboardAction;

/**
 * DashboardAction reader.
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
final class DashboardActionReader extends AbstractActionReader
{

    /**
     * {@inheritDoc}
     */
    protected function isSupported(object $annotation): bool
    {
        return $annotation instanceof DashboardAction;
    }
}
