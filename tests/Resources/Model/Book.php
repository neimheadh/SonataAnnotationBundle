<?php

namespace KunicMarko\SonataAnnotationBundle\Tests\Resources\Model;

use Doctrine\ORM\Mapping as ORM;
use KunicMarko\SonataAnnotationBundle\Annotation as Sonata;

/**
 * Book test model.
 *
 * @author Mathieu Wambre <contact@neimheadh.fr>
 *
 * @ORM\Entity
 * @Sonata\Admin()
 * @Sonata\Access(role="ROLE_USER", permissions={"READ"})
 * @Sonata\ListAction(
 *     name="import",
 *     options={"template"="import_list_button.html.twig"}
 * )
 */
class Book
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="int")
     * @Sonata\ListField()
     * @Sonata\DatagridField()
     */
    public int $id;

    /**
     * Book author.
     *
     * @Sonata\ListAssociationField(field="name")
     * @Sonata\DatagridAssociationField(field="name")
     *
     * @var Author|null
     */
    public ?Author $author = null;

    /**
     * Book title.
     *
     * @Sonata\ListField()
     * @Sonata\DatagridField(position=1)
     *
     * @var string|null
     */
    public string $title = '';

    /**
     * Get cover title.
     *
     * @Sonata\ListField()
     *
     * @return string
     */
    public function getCoverTitle(): string
    {
        return "$this->title\n$this->author";
    }
}