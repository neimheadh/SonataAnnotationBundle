<?php

namespace Neimheadh\SonataAnnotationBundle\Tests\Resources\Model\Test\BadChildAdmin;

use Neimheadh\SonataAnnotationBundle\Annotation as Sonata;

/**
 * @Sonata\Admin
 * @Sonata\AddChild(class="WrongOne", field="none")
 */
class BadChildAdmin
{

}