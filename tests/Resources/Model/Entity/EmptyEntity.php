<?php

namespace Neimheadh\SonataAnnotationBundle\Tests\Resources\Model\Entity;

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