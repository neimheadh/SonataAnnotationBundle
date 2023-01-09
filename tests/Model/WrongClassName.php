<?php

namespace KunicMarko\SonataAnnotationBundle\Tests\Model;

use KunicMarko\SonataAnnotationBundle\Annotation as Sonata;

if (!class_exists('KunicMarko\\SonataAnnotationBundle\\Tests\\Model\\IHaveABadClassName')) {
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