<?php

namespace Neimheadh\SonataAnnotationBundle\Tests\Resources\Model;

use Neimheadh\SonataAnnotationBundle\Annotation as Sonata;

if (!class_exists('Neimheadh\\SonataAnnotationBundle\\Tests\\Resources\\Model\\IHaveABadClassName')) {
    /**
     * Test model with bad class name.
     *
     * @author Mathieu Wambre <contact@neimheadh.fr>
     *
     * @Sonata\Admin
     */
    class IHaveABadClassName
    {

    }
}