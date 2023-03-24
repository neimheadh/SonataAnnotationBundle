<?php

namespace Neimheadh\SonataAnnotationBundle\Tests\Resources\Model\Test\ArgumentAnnotation;

use Doctrine\ORM\Mapping as ORM;
use Neimheadh\SonataAnnotationBundle\Annotation\Sonata;
use Neimheadh\SonataAnnotationBundle\Tests\Resources\Model\Entity\Book\Book;

/**
 * @ORM\Entity()
 */
#[Sonata\Access(role: 'ROLE_ADMIN', permissions: ['LIST', 'VIEW', 'EXPORT'])]
#[Sonata\ActionButton(template: 'test.html.twig')]
#[Sonata\AddChild(class: Book::class, field: 'book')]
#[Sonata\AddRoute(name: 'test', path: '/test')]
#[Sonata\Admin('Test')]
#[Sonata\DashboardAction(template: 'test.html.twig')]
#[Sonata\DatagridValues(values: ['_sort_by' => 'p.name'])]
#[Sonata\ExportFormats(['json'])]
#[Sonata\ListAction(name: 'test', options: ['template' => 'test.action.html.twig'])]
#[Sonata\RemoveRoute(name: 'list')]
class ArgumentAnnotation
{

    /**
     * @ORM\Id
     * @ORM\Column(type="int")
     */
    #[Sonata\DatagridField(position: 2)]
    #[Sonata\ExportField(label: 'Id')]
    #[Sonata\FormField(position: 2)]
    #[Sonata\ListField(position: 2)]
    #[Sonata\ShowField(position: 2)]
    public int $id;

    #[Sonata\DatagridAssociationField(position: 1, field: 'id')]
    #[Sonata\ExportAssociationField(label: 'Book id', field: 'id')]
    #[Sonata\FormField(position: 1)]
    #[Sonata\ListAssociationField(position: 1, field: 'id')]
    #[Sonata\ShowAssociationField(position: 1, field: 'id')]
    public Book $book;
}