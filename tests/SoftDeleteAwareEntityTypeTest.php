<?php

declare(strict_types=1);

namespace IDCT\Tests\SymfonyFormSoftDeleteAwareEntityType;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\Persistence\ObjectManager;
use IDCT\SymfonyFormSoftDeleteAwareEntityType\SoftDeleteAwareEntityType;
use IDCT\SymfonyFormSoftDeleteAwareEntityType\SoftDeleteAwareIdReader;
use IDCT\Tests\SymfonyFormSoftDeleteAwareEntityType\Model\DummyEntity;
use IDCT\Tests\SymfonyFormSoftDeleteAwareEntityType\Model\DummyOptions;
use IDCT\Tests\SymfonyFormSoftDeleteAwareEntityType\Service\DummyOptionsResolver;
use PHPUnit\Framework\TestCase;

final class SoftDeleteAwareEntityTypeTest extends TestCase
{
    public function testEmptyObjectPassed(): void
    {
        $idReader = $this->createPartialMock(SoftDeleteAwareIdReader::class, ['__construct']);
        $value = $idReader->getIdValue(null);
        $this->assertEmpty($value);
    }

    public function testIfOurIdReaderIsSet(): void
    {
        $classMetadata = $this->createMock(ClassMetadata::class);
        $classMetadata->method('getTypeOfField')
            ->willReturn('integer');

        $classMetadata->method('getIdentifierFieldNames')
            ->willReturn(['id']);

        $classMetadata->method('getIdentifier')
            ->willReturn('id');

        $dummyEm = $this->createMock(ObjectManager::class);
        $dummyEm->method('getClassMetadata')
            ->willReturn($classMetadata);

        $dummyRm = $this->createMock(ManagerRegistry::class);
        $class = DummyEntity::class;

        $optionsResolver = new DummyOptionsResolver();
        $managerRegistry = $this->createMock(ManagerRegistry::class);

        /** @var SoftDeleteAwareEntityType */
        $entityType = $this->getMockBuilder(SoftDeleteAwareEntityType::class)
            ->setConstructorArgs([$dummyRm])
            ->onlyMethods([])
            ->getMock();

        $entityType->configureOptions($optionsResolver);
        $testNormalizers = $optionsResolver->getTestNormalizers();

        $options = new DummyOptions();
        $options['em'] = $dummyEm;
        $options['class'] = $class;
        $this->assertArrayHasKey('id_reader', $testNormalizers);
        $reader = $testNormalizers['id_reader'][0]($options);

        $this->assertEquals(SoftDeleteAwareIdReader::class, \get_class($reader));
        $reader2 = $testNormalizers['id_reader'][0]($options); // check cache
        $this->assertTrue($reader === $reader2);
    }
}
