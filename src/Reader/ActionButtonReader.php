<?php

declare(strict_types=1);

namespace KunicMarko\SonataAnnotationBundle\Reader;

use Doctrine\Common\Annotations\Reader;
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
    public function __construct(Reader $annotationReader)
    {
        parent::__construct($annotationReader, ActionButton::class);
    }

}
