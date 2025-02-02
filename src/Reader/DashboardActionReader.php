<?php

declare(strict_types=1);

namespace Neimheadh\SonataAnnotationBundle\Reader;

use Doctrine\Common\Annotations\Reader;
use Neimheadh\SonataAnnotationBundle\Annotation\Sonata\DashboardAction;

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
