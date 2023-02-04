<?php

namespace KunicMarko\SonataAnnotationBundle\Tests\Resources\Extension;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\Tools\SchemaTool;
use Exception;
use KunicMarko\SonataAnnotationBundle\Tests\Resources\Model\Author;
use KunicMarko\SonataAnnotationBundle\Tests\Resources\Model\Book;
use Symfony\Bundle\FrameworkBundle\Test\TestContainer;


/**
 * Add database setUp() and tearDown()
 */
trait UseDatabaseTrait
{

    /**
     * {@inheritDoc}
     *
     * @throws ORMException
     * @throws Exception
     */
    protected function setUp(): void
    {
        /** @var TestContainer $container */
        $container = static::getContainer();
        /** @var EntityManager $em */
        $em = $container->get('doctrine.orm.default_entity_manager');

        $schema = new SchemaTool($em);
        $schema->createSchema(
          [
            $em->getClassMetadata(Author::class),
            $em->getClassMetadata(Book::class),
          ]
        );
    }

    /**
     * {@inheritDoc}
     *
     * @throws ORMException
     * @throws Exception
     */
    protected function tearDown(): void
    {
        /** @var TestContainer $container */
        $container = static::getContainer();
        /** @var EntityManager $em */
        $em = $container->get('doctrine.orm.default_entity_manager');

        $schema = new SchemaTool($em);
        $schema->dropSchema(
          [
            $em->getClassMetadata(Book::class),
            $em->getClassMetadata(Author::class),
          ]
        );
    }

}