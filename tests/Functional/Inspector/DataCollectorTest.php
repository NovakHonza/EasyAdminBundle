<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\Functional\Inspector;

use Doctrine\ORM\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use EasyCorp\Bundle\EasyAdminBundle\Inspector\DataCollector;
use EasyCorp\Bundle\EasyAdminBundle\Test\AbstractCrudTestCase;
use EasyCorp\Bundle\EasyAdminBundle\Tests\Functional\Apps\DefaultApp\Controller\CategoryCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Tests\Functional\Apps\DefaultApp\Controller\DashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Tests\Functional\Apps\DefaultApp\Entity\Category;
use Symfony\Component\VarDumper\Cloner\Data;

class DataCollectorTest extends AbstractCrudTestCase
{
    protected EntityRepository $categories;

    protected function setUp(): void
    {
        parent::setUp();

        $this->categories = $this->entityManager->getRepository(Category::class);

        $this->client->enableProfiler();
    }

    protected function getControllerFqcn(): string
    {
        return CategoryCrudController::class;
    }

    protected function getDashboardFqcn(): string
    {
        return DashboardController::class;
    }

    public function testIndex(): void
    {
        $sort = ['name' => 'DESC', 'slug' => 'ASC'];
        $this->client->request('GET', $this->generateIndexUrl().'?'.http_build_query([EA::SORT => $sort]));

        /** @var DataCollector $collector */
        $collector = $this->client->getProfile()->getCollector('easyadmin');

        $this->assertTrue($collector->isEasyAdminRequest());

        $this->assertSame(
            [
                'CRUD Controller FQCN' => CategoryCrudController::class,
                'CRUD Action' => Action::INDEX,
                'Entity ID' => null,
                'Sort' => $sort,
            ],
            array_map(static fn (Data $d) => $d->getValue(true), $collector->getData()),
        );
    }

    public function testEdit(): void
    {
        /** @var Category $category */
        $category = $this->categories->findOneBy([]);

        $this->client->request('GET', $this->generateEditFormUrl($category->getId()));

        /** @var DataCollector $collector */
        $collector = $this->client->getProfile()->getCollector('easyadmin');

        $this->assertTrue($collector->isEasyAdminRequest());

        $this->assertSame(
            [
                'CRUD Controller FQCN' => CategoryCrudController::class,
                'CRUD Action' => Action::EDIT,
                'Entity ID' => (string) $category->getId(),
                'Sort' => null,
            ],
            array_map(static fn (Data $d) => $d->getValue(true), $collector->getData()),
        );
    }

    public function testReset(): void
    {
        $this->client->request('GET', $this->generateIndexUrl());

        /** @var DataCollector $collector */
        $collector = $this->client->getProfile()->getCollector('easyadmin');

        $this->assertSame(
            [
                'CRUD Controller FQCN' => CategoryCrudController::class,
                'CRUD Action' => Action::INDEX,
                'Entity ID' => null,
                'Sort' => null,
            ],
            array_map(static fn (Data $d) => $d->getValue(true), $collector->getData()),
        );

        $collector->reset();

        $this->assertSame(
            [],
            array_map(static fn (Data $d) => $d->getValue(true), $collector->getData()),
        );
    }
}
