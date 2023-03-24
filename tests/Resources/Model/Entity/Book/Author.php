<?php

namespace Neimheadh\SonataAnnotationBundle\Tests\Resources\Model\Entity\Book;

use Doctrine\ORM\Mapping as ORM;
use Neimheadh\SonataAnnotationBundle\Annotation\Sonata;
use Stringable;

/**
 * Author test model.
 *
 * @author Mathieu Wambre <contact@neimheadh.fr>
 *
 * @ORM\Entity()
 * @Sonata\Admin
 * @Sonata\AddChild(class=Book::class, field="author")
 */
class Author implements Stringable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public ?int $id = null;

    /**
     * Author name.
     *
     * @ORM\Column(type="string", length=125)
     *
     * @var string
     */
    public string $name = '';

    /**
     * Author main genre.
     *
     * @ORM\Column(type="string", length=125)
     *
     * @var string
     */
    public string $genre = '';

    /**
     * {@inheritDoc}
     */
    public function __toString(): string
    {
        return $this->name;
    }

}