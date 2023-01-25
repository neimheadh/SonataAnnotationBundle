<?php

namespace KunicMarko\SonataAnnotationBundle\Tests\Resources\Model;

use KunicMarko\SonataAnnotationBundle\Annotation as Sonata;

/**
 * Author test model.
 *
 * @author Mathieu Wambre <contact@neimheadh.fr>
 *
 * @Sonata\Admin
 * @Sonata\AddChild(class=Book::class, field="author")
 */
class Author
{

}