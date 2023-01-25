<?php

namespace KunicMarko\SonataAnnotationBundle\Tests\Resources\Model;

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

    /**
     * Book author.
     *
     * @var Author|null
     */
    public ?Author $author = null;

    /**
     * Library containing book.
     *
     * @var Library|null
     */
    public ?Library $library = null;
}