<?php

namespace Neimheadh\SonataAnnotationBundle\Tests\Admin;

use Doctrine\ORM\EntityManager;
use DOMDocument;
use DOMElement;
use DOMNode;
use DOMNodeList;
use Exception;
use Neimheadh\SonataAnnotationBundle\Tests\Resources\Extension\SessionHelperTrait;
use Neimheadh\SonataAnnotationBundle\Tests\Resources\Extension\UseDatabaseTrait;
use Neimheadh\SonataAnnotationBundle\Tests\Resources\Model\Entity\Book\Author;
use Neimheadh\SonataAnnotationBundle\Tests\Resources\Model\Entity\Book\Book;
use Neimheadh\SonataAnnotationBundle\Tests\Resources\Model\Entity\Person;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Test\TestContainer;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * AnnotationAdmin test suite.
 */
class AnnotationAdminTest extends WebTestCase
{

    use SessionHelperTrait;
    use UseDatabaseTrait {
        setUp as _databaseSetup;
    }

    private string $logDir = '';

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $this->_databaseSetup();

        /** @var TestContainer $container */
        $container = static::getContainer();
        /** @var EntityManager $em */
        $em = $container->get('doctrine.orm.default_entity_manager');

        $projectDir = $container->getParameter('kernel.project_dir');
        $logDir = realpath("$projectDir/../var/log");

        if (!is_dir("$logDir/test")) {
            mkdir("$logDir/test", 0777, true);
        }

        $this->logDir = "$logDir/test";

        $em->getConnection()->prepare('DELETE FROM Book')->executeQuery();
        $em->getConnection()->prepare('DELETE FROM Author')->executeQuery();

        $author = new Author();
        $author->id = 1;
        $author->name = 'Stephen King';
        $author->genre = 'Horror';
        $em->persist($author);

        $book = new Book();
        $book->id = 1;
        $book->title = 'The Stand';
        $book->author = $author;
        $em->persist($book);

        $book = new Book();
        $book->id = 2;
        $book->title = 'Les furtifs';
        $em->persist($book);

        $em->flush();
    }

    /**
     * Test the application dashboard is correct.
     *
     * @test
     * @functional
     *
     * @return void
     * @throws Exception
     */
    public function shouldHaveValidDashboard(): void
    {
        /** @var TestContainer $container */
        $container = static::getContainer();

        /** @var KernelBrowser $client */
        $client = $container->get('test.client');

        $client->request(
            'GET',
            $container->get('router')->generate(
                'sonata_admin_dashboard'
            )
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        file_put_contents(
            "$this->logDir/dashboard.xml",
            $client->getResponse()->getContent()
        );

        $dom = new DOMDocument();
        $dom->loadXML($client->getResponse()->getContent());
        /** @var DOMElement $blocks */
        $blocks = $dom->getElementsByTagName('blocks')->item(0);
        /** @var DOMElement $left */
        $left = $blocks->getElementsByTagName('left')->item(0);
        $this->assertEquals(
            'sonata.admin.block.admin_list',
            $left->getAttribute('type')
        );
        /** @var DOMElement $groups */
        $groups = $left->getElementsByTagName('groups')->item(0);
        $this->assertEquals(2, $groups->getElementsByTagName('group')->length);
        /** @var DOMElement $group */
        $group = $groups->getElementsByTagName('group')->item(0);
        /** @var DOMNodeList|DOMElement[] $items */
        $items = $group->getElementsByTagName('item');
        $this->assertEquals('default', $group->getAttribute('label'));
        $this->assertEquals(1, $items->length);
        $this->assertEquals(Person::class, $items[0]->getAttribute('class'));

        /** @var DOMElement $group */
        $group = $groups->getElementsByTagName('group')->item(1);
        /** @var DOMNodeList|DOMElement[] $items */
        $items = $group->getElementsByTagName('item');
        $this->assertEquals('Book', $group->getAttribute('label'));
        $this->assertEquals(2, $items->length);

        $classes = [];
        foreach ($items as $item) {
            $classes[] = $item->getAttribute('class');
        }

        $this->assertContains(Book::class, $classes);
        $this->assertContains(Author::class, $classes);

        /** @var DOMNodeList|DOMElement[] $actions */
        $actions = $items[array_search(
            Book::class,
            $classes
        )]->getElementsByTagName('action');
        $this->assertEquals(3, $actions->length);
        $this->assertEquals(
            'export_book_list.html.twig',
            $actions[2]->textContent
        );
    }

    /**
     * Test resource models have their routes well-configured.
     *
     * @test
     * @functional
     *
     * @return void
     * @throws Exception
     */
    public function shouldHaveValidRoutes(): void
    {
        /** @var TestContainer $container */
        $container = static::getContainer();

        $this->assertAdminHasRoutes(
            $container->get('app.admin.Book'),
            [
                'list',
                'create',
                'edit',
                'delete',
                'show',
                'export',
                'custom',
            ]
        );

        $this->assertAdminHasRoutes(
            $container->get('app.admin.Author'),
            [
                'list',
                'create',
                'batch',
                'edit',
                'delete',
                'show',
                'export',
                'entity_book_book_list',
                'entity_book_book_create',
                'entity_book_book_edit',
                'entity_book_book_delete',
                'entity_book_book_show',
                'entity_book_book_export',
                'entity_book_book_custom',
            ]
        );

        $this->assertAdminHasRoutes(
            $container->get('admin.person'),
            [
                'list',
                'create',
                'batch',
                'edit',
                'delete',
                'show',
                'export',
            ]
        );
    }

    /**
     * Test Book admin form page.
     *
     * @test
     * @functional
     *
     * @return void
     * @throws Exception
     */
    public function shouldBookHaveValidCreatePage(): void
    {
        /** @var TestContainer $container */
        $container = static::getContainer();
        /** @var KernelBrowser $client */
        $client = $container->get('test.client');

        $client->request('GET', $this->generateRoute(
            'admin_tests_resources_entity_book_book_create'
        ));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        file_put_contents(
            "$this->logDir/create.book.xml",
            $client->getResponse()->getContent()
        );

        $dom = new DOMDocument();
        $dom->loadXML($client->getResponse()->getContent());

        /** @var DOMNodeList|DOMElement[] $fields */
        $fields = $dom->getElementsByTagName('field');
        $this->assertEquals(3, $fields->length);
        $this->assertEquals('title', $fields[0]->getAttribute('name'));
        $this->assertEquals('author', $fields[1]->getAttribute('name'));
        $this->assertEquals('_token', $fields[2]->getAttribute('name'));
        $this->assertEquals('', $fields[0]->textContent);
        $this->assertEquals('', $fields[1]->textContent);
    }

    /**
     * Test Book admin form page.
     *
     * @test
     * @functional
     *
     * @return void
     * @throws Exception
     */
    public function shouldBookHaveValidEditPage(): void
    {
        /** @var TestContainer $container */
        $container = static::getContainer();
        /** @var KernelBrowser $client */
        $client = $container->get('test.client');

        $client->request('GET', $this->generateRoute(
            'admin_tests_resources_entity_book_book_edit',
            ['id' => 1]
        ));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        file_put_contents(
            "$this->logDir/edit.book.xml",
            $client->getResponse()->getContent()
        );


        $dom = new DOMDocument();
        $dom->loadXML($client->getResponse()->getContent());

        /** @var DOMNodeList|DOMElement[] $fields */
        $fields = $dom->getElementsByTagName('field');
        $this->assertEquals(3, $fields->length);
        $this->assertEquals('title', $fields[0]->getAttribute('name'));
        $this->assertEquals('author', $fields[1]->getAttribute('name'));
        $this->assertEquals('_token', $fields[2]->getAttribute('name'));
        $this->assertEquals('The Stand', $fields[0]->textContent);
        $this->assertEquals('1', $fields[1]->textContent);
    }

    /**
     * Test book export is valid.
     *
     * @test
     * @function
     *
     * @return void
     * @throws Exception
     */
    public function shouldBookHaveValidExportJson(): void
    {
        /** @var TestContainer $container */
        $container = static::getContainer();
        /** @var KernelBrowser $client */
        $client = $container->get('test.client');

        $route = $container->get('router')
            ->generate(
                'admin_tests_resources_entity_book_book_export',
                ['format' => 'json']
            );

        ob_start();
        $client->request('GET', $route);
        $json = ob_get_contents();
        ob_end_clean();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        if (!$json) {
            $json = $client->getInternalResponse()->getContent();
        }
        $json = json_decode($json, true);

        $this->assertIsArray($json);
        $this->assertEquals(
            [
                ["Author" => "Stephen King", "title" => "The Stand"],
                ["Author" => null, "title" => "Les furtifs"],
            ],
            $json
        );
    }

    /**
     * Test Book admin list page.
     *
     * @test
     * @functional
     *
     * @return void
     * @throws Exception
     */
    public function shouldBookHaveValidListPage(): void
    {
        /** @var TestContainer $container */
        $container = static::getContainer();

        /** @var KernelBrowser $client */
        $client = $container->get('test.client');

        $client->request(
            'GET',
            $container->get('router')->generate(
                'admin_tests_resources_entity_book_book_list'
            )
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        file_put_contents(
            "$this->logDir/list.book.xml",
            $client->getResponse()->getContent()
        );

        $dom = new DOMDocument();
        $xml = $client->getResponse()->getContent();
        $dom->loadXML($xml);

        /** @var DOMElement $list */
        $list = $dom->getElementsByTagName('list')->item(0);
        /** @var DOMElement $items */
        $items = $list->getElementsByTagName('items')->item(0);
        /** @var DOMElement $filters */
        $filters = $list->getElementsByTagName('filters')->item(0);
        /** @var DOMElement $exports */
        $exports = $list->getElementsByTagName('exports')->item(0);
        /** @var DOMElement $actions */
        $actions = $list->getElementsByTagName('actions')->item(0);

        $this->assertEquals(
            2,
            $actions->getElementsByTagName('action')->length
        );
        /** @var DOMElement $action */
        $action = $actions->getElementsByTagName('action')->item(1);
        $this->assertEquals('export_book_list.html.twig', $action->textContent);

        $this->assertEquals(2, $items->getElementsByTagName('item')->length);
        $this->assertEquals(
            3,
            $filters->getElementsByTagName('filter')->length
        );
        $this->assertEquals(
            1,
            $exports->getElementsByTagName('format')->length
        );

        /** @var DOMElement $filter */
        $filter = $filters->getElementsByTagName('filter')->item(0);
        $this->assertEquals('title', $filter->textContent);

        /** @var DOMElement $filter */
        $filter = $filters->getElementsByTagName('filter')->item(1);
        $this->assertEquals('id', $filter->textContent);

        /** @var DOMElement $filter */
        $filter = $filters->getElementsByTagName('filter')->item(2);
        $this->assertEquals('author.name', $filter->textContent);

        /** @var DOMElement $item */
        $item = $items->getElementsByTagName('item')->item(0);
        /** @var DOMElement $actions */
        $actions = $item->getElementsByTagName('actions')->item(0);
        $this->assertEquals(
            1,
            $actions->getElementsByTagName('action')->length
        );
        /** @var DOMElement $action */
        $action = $actions->getElementsByTagName('action')->item(0);
        $this->assertEquals(
            'import_list_button.html.twig',
            $action->getAttribute('template')
        );
        $this->assertEquals('import', $action->textContent);
        /** @var DOMElement $fields */
        $fields = $item->getElementsByTagName('fields')->item(0);
        /** @var DOMNodeList|DOMElement[] $fieldList */
        $fieldList = $fields->getElementsByTagName('field');
        $this->assertEquals(4, $fieldList->length);
        $this->assertEquals('id', $fieldList[0]->getAttribute('name'));
        $this->assertEquals('author.name', $fieldList[1]->getAttribute('name'));
        $this->assertEquals('title', $fieldList[2]->getAttribute('name'));
        $this->assertEquals(
            'getCoverTitle',
            $fieldList[3]->getAttribute('name')
        );
        $this->assertEquals('1', $fieldList[0]->textContent);
        $this->assertEquals('Stephen King', $fieldList[1]->textContent);
        $this->assertEquals('The Stand', $fieldList[2]->textContent);
        $this->assertEquals(
            "The Stand\nStephen King",
            $fieldList[3]->textContent
        );

        /** @var DOMElement $item */
        $item = $items->getElementsByTagName('item')->item(1);
        /** @var DOMElement $actions */
        $actions = $item->getElementsByTagName('actions')->item(0);
        $this->assertEquals(
            1,
            $actions->getElementsByTagName('action')->length
        );
        /** @var DOMElement $action */
        $action = $actions->getElementsByTagName('action')->item(0);
        $this->assertEquals(
            'import_list_button.html.twig',
            $action->getAttribute('template')
        );
        $this->assertEquals('import', $action->textContent);
        /** @var DOMElement $fields */
        $fields = $item->getElementsByTagName('fields')->item(0);
        /** @var DOMNodeList|DOMElement[] $fieldList */
        $fieldList = $fields->getElementsByTagName('field');
        $this->assertEquals(4, $fieldList->length);
        $this->assertEquals('id', $fieldList[0]->getAttribute('name'));
        $this->assertEquals('author.name', $fieldList[1]->getAttribute('name'));
        $this->assertEquals('title', $fieldList[2]->getAttribute('name'));
        $this->assertEquals(
            'getCoverTitle',
            $fieldList[3]->getAttribute('name')
        );
        $this->assertEquals('2', $fieldList[0]->textContent);
        $this->assertEquals('', $fieldList[1]->textContent);
        $this->assertEquals('Les furtifs', $fieldList[2]->textContent);
        $this->assertEquals("Les furtifs\n", $fieldList[3]->textContent);

        /** @var DOMElement $format */
        $format = $exports->getElementsByTagName('format')->item(0);
        $this->assertEquals('json', $format->textContent);
    }

    /**
     * Test Book admin show page.
     *
     * @test
     * @functional
     *
     * @return void
     * @throws Exception
     */
    public function shouldBookHaveValidShowPage(): void
    {
        /** @var TestContainer $container */
        $container = static::getContainer();
        /** @var KernelBrowser $client */
        $client = $container->get('test.client');

        $client->request('GET', $this->generateRoute(
            'admin_tests_resources_entity_book_book_show',
            ['id' => 1]
        ));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        file_put_contents(
            "$this->logDir/show.book.xml",
            $client->getResponse()->getContent()
        );

        $dom = new DOMDocument();
        $dom->loadXML($client->getResponse()->getContent());

        $fields = $dom->getElementsByTagName('field');
        $this->assertEquals(3, $fields->length);
        $this->assertShowField($fields->item(0), 'id', '1');
        $this->assertShowField($fields->item(1), 'author.name', 'Stephen King');
        $this->assertShowField($fields->item(2), 'title', 'The Stand');
    }

    /**
     * Test the given admin has given actions routes.
     *
     * @param AbstractAdmin $admin   Admin class.
     * @param array         $actions Action names.
     *
     * @return void
     * @throws Exception
     */
    private function assertAdminHasRoutes(
        AbstractAdmin $admin,
        array $actions
    ): void {
        /** @var TestContainer $container */
        $container = static::getContainer();
        /** @var Router $router */
        $router = $container->get('router');
        $prefix = $admin->getBaseRouteName();

        $routes = $router->getRouteCollection()->all();
        $routes = array_values(
            array_filter(
                array_keys($routes),
                fn($name) => preg_match(
                    "/^{$prefix}_/",
                    $name
                )
            )
        );
        $names = array_map(
            fn($suffix) => "{$prefix}_$suffix",
            $actions,
        );

        $this->assertEquals($names, $routes);
    }

    /**
     * Assert show page field.
     *
     * @param DOMElement $item  DOM item.
     * @param string     $label Field label.
     * @param string     $value Field value.
     *
     * @return void
     */
    private function assertShowField(
        DOMNode $item,
        string $label,
        string $value
    ): void {
        $this->assertEquals(
            $label,
            $item->getElementsByTagName('label')->item(0)->textContent,
        );
        $this->assertEquals(
            $value,
            $item->getElementsByTagName('value')->item(0)->textContent,
        );
    }

    /**
     * Test all actions are available in list by default.
     *
     * @test
     * @functional
     *
     * @return void
     * @throws Exception
     */
    public function shouldHaveAllListActionByDefault(): void
    {
        /** @var TestContainer $container */
        $container = static::getContainer();

        /** @var KernelBrowser $client */
        $client = $container->get('test.client');

        $client->request(
            'GET',
            $container->get('router')->generate(
                'admin_tests_resources_entity_book_author_list'
            )
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        file_put_contents(
            "$this->logDir/list.author.xml",
            $client->getResponse()->getContent()
        );

        $dom = new DOMDocument();
        $xml = $client->getResponse()->getContent();
        $dom->loadXML($xml);

        /** @var DOMElement $list */
        $list = $dom->getElementsByTagName('list')->item(0);
        /** @var DOMElement $items */
        $items = $list->getElementsByTagName('items')->item(0);

        /** @var DOMElement $item */
        $item = $items->getElementsByTagName('item')->item(0);
        /** @var DOMElement $fields */
        $actions = $item->getElementsByTagName('actions')->item(0);
        /** @var DOMNodeList|DOMElement[] $fieldList */
        $actionList = $actions->getElementsByTagName('action');

        $this->assertEquals(3, $actionList->length);
        $this->assertEquals('show', $actionList->item(0)->nodeValue);
        $this->assertEquals('edit', $actionList->item(1)->nodeValue);
        $this->assertEquals('delete', $actionList->item(2)->nodeValue);
    }

    /**
     * Test all fields are available by default.
     *
     * @test
     * @functional
     *
     * @return void
     * @throws Exception
     */
    public function shouldHaveAllFieldActivateByDefault(): void
    {
        /** @var TestContainer $container */
        $container = static::getContainer();

        /** @var KernelBrowser $client */
        $client = $container->get('test.client');

        $client->request(
            'GET',
            $container->get('router')->generate(
                'admin_tests_resources_entity_book_author_list'
            )
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        file_put_contents(
            "$this->logDir/list.author.xml",
            $client->getResponse()->getContent()
        );

        $dom = new DOMDocument();
        $xml = $client->getResponse()->getContent();
        $dom->loadXML($xml);

        /** @var DOMElement $root */
        $root = $dom->getElementsByTagName('list')->item(0);
        /** @var DOMElement $items */
        $items = $root->getElementsByTagName('items')->item(0);
        /** @var DOMElement $filters */
        $filters = $root->getElementsByTagName('filters')->item(0);

        /** @var DOMNodeList|DOMElement[] $filterList */
        $filterList = $filters->getElementsByTagName('filter');

        $this->assertEquals(3, $filterList->length);
        $this->assertEquals('id', $filterList[0]->nodeValue);
        $this->assertEquals('name', $filterList[1]->nodeValue);
        $this->assertEquals('genre', $filterList[2]->nodeValue);

        /** @var DOMElement $item */
        $item = $items->getElementsByTagName('item')->item(0);
        /** @var DOMElement $fields */
        $fields = $item->getElementsByTagName('fields')->item(0);
        /** @var DOMNodeList|DOMElement[] $fieldList */
        $fieldList = $fields->getElementsByTagName('field');

        $this->assertEquals(3, $fieldList->length);
        $this->assertEquals('id', $fieldList->item(0)->getAttribute('name'));
        $this->assertEquals('name', $fieldList->item(1)->getAttribute('name'));
        $this->assertEquals('genre', $fieldList->item(2)->getAttribute('name'));

        $client->request(
            'GET',
            $container->get('router')->generate(
                'admin_tests_resources_entity_book_author_show',
                ['id' => 1]
            )
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        file_put_contents(
            "$this->logDir/show.author.xml",
            $client->getResponse()->getContent()
        );

        $dom = new DOMDocument();
        $xml = $client->getResponse()->getContent();
        $dom->loadXML($xml);

        /** @var DOMElement $root */
        $root = $dom->getElementsByTagName('show')->item(0);
        /** @var DOMNodeList|DOMElement[] $fieldList */
        $fieldList = $root->getElementsByTagName('field');

        $this->assertEquals(3, $fieldList->length);
        $this->assertEquals(
            'id',
            $fieldList[0]->getElementsByTagName('label')[0]->nodeValue
        );
        $this->assertEquals(
            'name',
            $fieldList[1]->getElementsByTagName('label')[0]->nodeValue
        );
        $this->assertEquals(
            'genre',
            $fieldList[2]->getElementsByTagName('label')[0]->nodeValue
        );

        $client->request(
            'GET',
            $container->get('router')->generate(
                'admin_tests_resources_entity_book_author_create'
            )
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        file_put_contents(
            "$this->logDir/create.author.xml",
            $client->getResponse()->getContent()
        );

        $dom = new DOMDocument();
        $xml = $client->getResponse()->getContent();
        $dom->loadXML($xml);

        /** @var DOMElement $root */
        $root = $dom->getElementsByTagName('edit')->item(0);
        /** @var DOMNodeList|DOMElement[] $fieldList */
        $fieldList = $root->getElementsByTagName('field');

        $this->assertEquals(3, $fieldList->length);
        $this->assertEquals('name', $fieldList->item(0)->getAttribute('name'));
        $this->assertEquals('genre', $fieldList->item(1)->getAttribute('name'));
        $this->assertEquals(
            '_token',
            $fieldList->item(2)->getAttribute('name')
        );
    }

    /**
     * Generate route.
     *
     * @param string $name   Route name.
     * @param array  $params Route params.
     *
     * @return string
     * @throws Exception
     */
    private function generateRoute(string $name, array $params = []): string
    {
        return static::getContainer()->get('router')->generate(
            $name,
            $params
        );
    }
}