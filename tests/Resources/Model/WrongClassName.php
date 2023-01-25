<?php

namespace KunicMarko\SonataAnnotationBundle\Tests\Resources\Model;

use KunicMarko\SonataAnnotationBundle\Annotation as Sonata;

if (!class_exists('KunicMarko\\SonataAnnotationBundle\\Tests\\Resources\\Model\\IHaveABadClassName')) {
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