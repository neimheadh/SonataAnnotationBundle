<?php

namespace KunicMarko\SonataAnnotationBundle\Tests\Model;

use KunicMarko\SonataAnnotationBundle\Annotation as Sonata;

/**
 * Book test model.
 *
 * @author Mathieu Wambre <contact@neimheadh.fr>
 *
 * @Sonata\Admin()
 * @Sonata\Access(role="ROLE_USER", permissions={"READ"})
 */
class Book
{

}