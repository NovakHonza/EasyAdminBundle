<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\Unit\Form;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\FieldMapping;
use EasyCorp\Bundle\EasyAdminBundle\Form\EventListener\CrudAutocompleteSubscriber;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Doctrine\Form\ChoiceList\IdReader;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Uid\Uuid;
use Twig\Environment;

class CrudAutocompleteSubscriberUuidTest extends TestCase
{
    private const UUID_STRING = '1ecc57ff-5604-6800-83f2-5765340cd236';

    protected function setUp(): void
    {
        if (!class_exists(PostgreSQLPlatform::class)) {
            $this->markTestSkipped('Doctrine DBAL 3.x+ is required (PostgreSQLPlatform/MySQLPlatform classes).');
        }
    }

    public function testUuidConvertedToRfc4122OnNativeGuidPlatform(): void
    {
        $expectedValue = Uuid::fromString(self::UUID_STRING)->toRfc4122();

        $repository = $this->createMock(EntityRepository::class);
        $repository->expects($this->once())
            ->method('findBy')
            ->with(['id' => [$expectedValue]])
            ->willReturn([]);

        $event = $this->createFormEvent(new PostgreSQLPlatform(), $repository);

        $subscriber = new CrudAutocompleteSubscriber($this->createStub(Environment::class));
        $subscriber->preSubmit($event);
    }

    public function testUuidConvertedToBinaryOnNonNativeGuidPlatform(): void
    {
        $expectedValue = Uuid::fromString(self::UUID_STRING)->toBinary();

        $repository = $this->createMock(EntityRepository::class);
        $repository->expects($this->once())
            ->method('findBy')
            ->with(['id' => [$expectedValue]])
            ->willReturn([]);

        $event = $this->createFormEvent(new MySQLPlatform(), $repository);

        $subscriber = new CrudAutocompleteSubscriber($this->createStub(Environment::class));
        $subscriber->preSubmit($event);
    }

    public function testUuidFormatDiffersPerPlatform(): void
    {
        $capturedPostgres = null;
        $capturedMysql = null;

        $postgresRepo = $this->createStub(EntityRepository::class);
        $postgresRepo->method('findBy')
            ->willReturnCallback(static function (array $criteria) use (&$capturedPostgres) {
                $capturedPostgres = $criteria['id'][0];

                return [];
            });

        $mysqlRepo = $this->createStub(EntityRepository::class);
        $mysqlRepo->method('findBy')
            ->willReturnCallback(static function (array $criteria) use (&$capturedMysql) {
                $capturedMysql = $criteria['id'][0];

                return [];
            });

        $subscriber = new CrudAutocompleteSubscriber($this->createStub(Environment::class));
        $subscriber->preSubmit($this->createFormEvent(new PostgreSQLPlatform(), $postgresRepo));
        $subscriber->preSubmit($this->createFormEvent(new MySQLPlatform(), $mysqlRepo));

        $this->assertSame(self::UUID_STRING, $capturedPostgres, 'PostgreSQL (native GUID) should receive RFC4122 string');
        $this->assertSame(16, \strlen($capturedMysql), 'MySQL (no native GUID) should receive 16-byte binary');
        $this->assertNotEquals($capturedPostgres, $capturedMysql, 'The two formats must differ');
    }

    private function createFormEvent(object $platform, EntityRepository $repository): FormEvent
    {
        $className = 'App\Entity\Dummy';

        $idReader = $this->createStub(IdReader::class);
        $idReader->method('isIntId')->willReturn(false);
        $idReader->method('getIdField')->willReturn('id');

        $classMetadata = $this->createStub(ClassMetadata::class);
        $classMetadata->method('getFieldMapping')
            ->with('id')
            ->willReturn(new FieldMapping('uuid', 'id', 'id'));

        $connection = $this->createStub(Connection::class);
        $connection->method('getDatabasePlatform')->willReturn($platform);

        $em = $this->createStub(EntityManagerInterface::class);
        $em->method('getClassMetadata')->with($className)->willReturn($classMetadata);
        $em->method('getConnection')->willReturn($connection);
        $em->method('getRepository')->with($className)->willReturn($repository);

        $options = [
            'id_reader' => $idReader,
            'em' => $em,
            'class' => $className,
            'compound' => false,
            'choices' => [],
        ];

        $autocompleteConfig = $this->createStub(FormConfigInterface::class);
        $autocompleteConfig->method('getOptions')->willReturn($options);

        $autocompleteForm = $this->createStub(FormInterface::class);
        $autocompleteForm->method('getConfig')->willReturn($autocompleteConfig);

        $form = $this->createStub(FormInterface::class);
        $form->method('get')->with('autocomplete')->willReturn($autocompleteForm);
        $form->method('add')->willReturnSelf();

        $event = $this->createStub(FormEvent::class);
        $event->method('getData')->willReturn(['autocomplete' => self::UUID_STRING]);
        $event->method('getForm')->willReturn($form);

        return $event;
    }
}
