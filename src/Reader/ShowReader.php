<?php

declare(strict_types=1);

namespace KunicMarko\SonataAnnotationBundle\Reader;

use Doctrine\Common\Annotations\Reader;
use KunicMarko\SonataAnnotationBundle\Annotation\ShowField;

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

}
