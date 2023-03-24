<?php

namespace Neimheadh\SonataAnnotationBundle\Tests\Resources\Model\Entity;

use Neimheadh\SonataAnnotationBundle\Annotation\Sonata;
use Doctrine\ORM\Mapping as ORM;

/**
 * Empty entity test class.
 *
 * @ORM\Entity
 */
class EmptyEntity
{

    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private int $_id;
}