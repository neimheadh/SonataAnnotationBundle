<?php

namespace Neimheadh\SonataAnnotationBundle\Tests\Resources\Model\Entity\Book;

use Doctrine\ORM\Mapping as ORM;
use Neimheadh\SonataAnnotationBundle\Annotation as Sonata;

/**
 * Book test model.
 *
 * @author Mathieu Wambre <contact@neimheadh.fr>
 *
 * @ORM\Entity
 * @Sonata\Admin(label="Book admin")
 * @Sonata\Access(role="ROLE_USER", permissions={"READ"})
 * @Sonata\ListAction(
 *     name="import",
 *     options={"template"="import_list_button.html.twig"}
 * )
 * @Sonata\AddRoute(name="custom", path="/book/custom")
 * @Sonata\RemoveRoute(name="batch")
 * @Sonata\ExportFormats({"json"})
 * @Sonata\DashboardAction("export_book_list.html.twig")
 * @Sonata\ActionButton("export_book_list.html.twig")
 */
class Book
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @Sonata\ListField()
     * @Sonata\DatagridField()
     * @Sonata\ShowField(position=1)
     */
    public int $id;

    /**
     * Book author.
     *
     * @ORM\ManyToOne(targetEntity=Author::class)
     * @Sonata\ListAssociationField(field="name")
     * @Sonata\DatagridAssociationField(field="name")
     * @Sonata\ShowAssociationField(position=2, field="name")
     * @Sonata\ExportAssociationField(field="name", label="Author")
     * @Sonata\FormField(position=2)
     *
     * @var Author|null
     */
    public ?Author $author = null;

    /**
     * Book title.
     *
     * @ORM\Column(type="string", length=125)
     * @Sonata\ListField()
     * @Sonata\DatagridField(position=1)
     * @Sonata\ShowField()
     * @Sonata\ExportField()
     * @Sonata\FormField(position=1)
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