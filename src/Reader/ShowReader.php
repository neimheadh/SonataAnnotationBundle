<?php

declare(strict_types=1);

namespace Neimheadh\SonataAnnotationBundle\Reader;

use Doctrine\Common\Annotations\Reader;
use Neimheadh\SonataAnnotationBundle\Annotation\Sonata\Admin;
use Neimheadh\SonataAnnotationBundle\Annotation\Sonata\ShowField;

/**
 * Show configuration reader.
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
final class ShowReader extends AbstractFieldConfigurationReader
{

    /**
     * {@inheritDoc}
     */
    public function __construct(Reader $annotationReader)
    {
        parent::__construct($annotationReader, ShowField::class);
    }

    /**
     * {@inheritDoc}
     */
    protected function getAdminAnnotationFields(
        Admin $annotation,
        ?string $action
    ): array {
        return $annotation->getShowFields();
    }

}
