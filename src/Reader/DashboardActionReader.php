<?php

declare(strict_types=1);

namespace KunicMarko\SonataAnnotationBundle\Reader;

use Doctrine\Common\Annotations\Reader;
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
    public function __construct(
        Reader $annotationReader
    ) {
        parent::__construct($annotationReader, DashboardAction::class);
    }

}
