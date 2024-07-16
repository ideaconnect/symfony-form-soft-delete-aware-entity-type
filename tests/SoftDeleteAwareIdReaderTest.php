<?php

declare(strict_types=1);

namespace IDCT\Tests\SymfonyFormSoftDeleteAwareEntityType;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\FilterCollection;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\Persistence\ObjectManager;
use Gedmo\SoftDeleteable\SoftDeleteable;
use IDCT\SymfonyFormSoftDeleteAwareEntityType\SoftDeleteAwareIdReader;
use IDCT\Tests\SymfonyFormSoftDeleteAwareEntityType\Model\DummyEntity;
use PHPUnit\Framework\TestCase;

final class SoftDeleteAwareIdReaderTest extends TestCase
{
    public function testEmptyObjectPassed(): void
    {
        $idReader = $this->createPartialMock(SoftDeleteAwareIdReader::class, ['__construct']);
        $value = $idReader->getIdValue(null);
        $this->assertEmpty($value);
    }

    public function testSoftDeletedDisabled(): void
    {
        $om = $this->createMock(ObjectManager::class);
        $classMetadata = $this->createMock(ClassMetadata::class);
        $repository = $this->createMock(EntityRepository::class);
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $em = $this->createMock(EntityManagerInterface::class);

        $filters = $this->createMock(FilterCollection::class);
        $filters->method('isEnabled')
            ->willReturn(false);

        $em->method('getFilters')
            ->willReturn($filters);

        $queryBuilder->method('getEntityManager')
            ->willReturn($em);

        $repository->method('createQueryBuilder')
            ->willReturn($queryBuilder);

        $om->method('contains')
            ->willReturn(true);

        $om->method('getRepository')
            ->willReturn($repository);

        $classMetadata->method('getIdentifierFieldNames')
            ->willReturn(['id']);

        $classMetadata->method('getTypeOfField')
            ->willReturn('integer');

        $classMetadata->method('hasAssociation')
            ->willReturn(false);

        $classMetadata->method('getIdentifierValues')
            ->willReturn([123]);

        $idReader = new SoftDeleteAwareIdReader($om, $classMetadata);

        $mock = $this->createMock(SoftDeleteable::class);

        $id = $idReader->getIdValue($mock);
        $this->assertEquals(123, $id);
    }

    public function testSoftDeletedEnabledButNoAttribute(): void
    {
        $om = $this->createMock(ObjectManager::class);
        $classMetadata = $this->createMock(ClassMetadata::class);
        $repository = $this->createMock(EntityRepository::class);
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $em = $this->createMock(EntityManagerInterface::class);
        $filters = $this->createMock(FilterCollection::class);

        $filters->method('isEnabled')
            ->willReturn(true);

        $em->method('getFilters')
            ->willReturn($filters);

        $queryBuilder->method('getEntityManager')
            ->willReturn($em);

        $repository->method('createQueryBuilder')
            ->willReturn($queryBuilder);

        $objectGotInitialized = false;

        $om->method('initializeObject')
            ->willReturnCallback(function ($object) use (&$objectGotInitialized) {
                $objectGotInitialized = true;
            });

        $om->method('contains')
            ->willReturn(true);

        $om->method('getRepository')
            ->willReturn($repository);

        $classMetadata->method('getIdentifierFieldNames')
            ->willReturn(['id']);

        $classMetadata->method('getTypeOfField')
            ->willReturn('integer');

        $classMetadata->method('hasAssociation')
            ->willReturn(false);

        $classMetadata->method('getIdentifierValues')
            ->willReturn([123]);

        $idReader = new SoftDeleteAwareIdReader($om, $classMetadata);

        $mock = $this->createMock(SoftDeleteable::class);

        $id = $idReader->getIdValue($mock);
        $this->assertTrue($objectGotInitialized);
        $this->assertEquals(123, $id);
    }

    public function testSoftDeletedEnabledGotAttributeAndDeleted(): void
    {
        $om = $this->createMock(ObjectManager::class);
        $classMetadata = $this->createMock(ClassMetadata::class);
        $repository = $this->createMock(EntityRepository::class);
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $em = $this->createMock(EntityManagerInterface::class);
        $filters = $this->createMock(FilterCollection::class);

        $filters->method('isEnabled')
            ->willReturn(true);

        $em->method('getFilters')
            ->willReturn($filters);

        $queryBuilder->method('getEntityManager')
            ->willReturn($em);

        $repository->method('createQueryBuilder')
            ->willReturn($queryBuilder);

        $objectGotInitialized = false;

        $om->method('initializeObject')
            ->willReturnCallback(function ($object) use (&$objectGotInitialized) {
                $objectGotInitialized = true;
            });

        $om->method('contains')
            ->willReturn(true);

        $om->method('getRepository')
            ->willReturn($repository);

        $classMetadata->method('getIdentifierFieldNames')
            ->willReturn(['id']);

        $classMetadata->method('getTypeOfField')
            ->willReturn('integer');

        $classMetadata->method('hasAssociation')
            ->willReturn(false);

        $classMetadata->method('getIdentifierValues')
            ->willReturn([123]);

        $idReader = new SoftDeleteAwareIdReader($om, $classMetadata);

        $dummy = new DummyEntity();
        $dummy->setDeletedAtDate(new \DateTimeImmutable('-1 day')); // deleted
        $id = $idReader->getIdValue($dummy);
        $this->assertTrue($objectGotInitialized);
        $this->assertEmpty($id);
    }

    public function testSoftDeletedEnabledGotAttributeButNotDeleted(): void
    {
        $om = $this->createMock(ObjectManager::class);
        $classMetadata = $this->createMock(ClassMetadata::class);
        $repository = $this->createMock(EntityRepository::class);
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $em = $this->createMock(EntityManagerInterface::class);
        $filters = $this->createMock(FilterCollection::class);

        $filters->method('isEnabled')
            ->willReturn(true);

        $em->method('getFilters')
            ->willReturn($filters);

        $queryBuilder->method('getEntityManager')
            ->willReturn($em);

        $repository->method('createQueryBuilder')
            ->willReturn($queryBuilder);

        $objectGotInitialized = false;

        $om->method('initializeObject')
            ->willReturnCallback(function ($object) use (&$objectGotInitialized) {
                $objectGotInitialized = true;
            });

        $om->method('contains')
            ->willReturn(true);

        $om->method('getRepository')
            ->willReturn($repository);

        $classMetadata->method('getIdentifierFieldNames')
            ->willReturn(['id']);

        $classMetadata->method('getTypeOfField')
            ->willReturn('integer');

        $classMetadata->method('hasAssociation')
            ->willReturn(false);

        $classMetadata->method('getIdentifierValues')
            ->willReturn([123]);

        $idReader = new SoftDeleteAwareIdReader($om, $classMetadata);

        $mock = $this->createMock(DummyEntity::class);
        $mock->method('getDeletedAtDate')
            ->willReturn(null);

        $id = $idReader->getIdValue($mock);
        $this->assertTrue($objectGotInitialized);
        $this->assertEquals(123, $id);
    }

    public function testStandardPathNonSoftDeletableObject(): void
    {
        $om = $this->createMock(ObjectManager::class);
        $classMetadata = $this->createMock(ClassMetadata::class);

        $om->method('contains')
            ->willReturn(true);

        $classMetadata->method('getIdentifierFieldNames')
            ->willReturn(['id']);

        $classMetadata->method('getTypeOfField')
            ->willReturn('integer');

        $classMetadata->method('hasAssociation')
            ->willReturn(false);

        $classMetadata->method('getIdentifierValues')
            ->willReturn([123]);

        $idReader = new SoftDeleteAwareIdReader($om, $classMetadata);
        $mock = $this->getMockBuilder(\stdClass::class);

        $id = $idReader->getIdValue($mock);
        $this->assertEquals(123, $id);
    }
}
