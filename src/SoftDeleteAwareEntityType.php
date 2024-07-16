<?php

declare(strict_types=1);

namespace IDCT\SymfonyFormSoftDeleteAwareEntityType;

use Doctrine\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\ChoiceList\Factory\CachingFactoryDecorator;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Extends entity type to support the behavior of SoftDeleteable filter from
 * Doctrine extensions.
 */
class SoftDeleteAwareEntityType extends EntityType
{
    /**
     * @var SoftDeleteAwareIdReader[]
     */
    private array $idReaders = [];

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        // We need to inject our Id Reader.
        $idReaderNormalizer = fn (Options $options) => $this->getCachedIdReader($options['em'], $options['class']);
        $resolver->setNormalizer('id_reader', $idReaderNormalizer);
    }

    protected function getCachedIdReader(ObjectManager $manager, string $class): ?SoftDeleteAwareIdReader
    {
        /* Sadly injection of our id reader means duplication of code as there is
        no way to actually define the desired IdReader in the backbone of EntityType. */
        $hash = CachingFactoryDecorator::generateHash([$manager, $class]);

        if (isset($this->idReaders[$hash])) {
            return $this->idReaders[$hash];
        }

        $idReader = new SoftDeleteAwareIdReader($manager, $manager->getClassMetadata($class));

        // don't cache the instance for composite ids that cannot be optimized
        return $this->idReaders[$hash] = $idReader->isSingleId() ? $idReader : null;
    }
}
