<?php

namespace Neimheadh\SonataAnnotationBundle\Tests\Resources\Model\Test\ArgumentAnnotation;

use Neimheadh\SonataAnnotationBundle\Annotation as Sonata;

#[Sonata\Admin(label: 'Test')]
#[Sonata\Access(role: 'ROLE_ADMIN', permissions: ['LIST', 'VIEW', 'EXPORT'])]
class ArgumentAnnotation
{

}