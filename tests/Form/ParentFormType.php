<?php

declare(strict_types=1);

namespace IDCT\Tests\SymfonyFormSoftDeleteAwareEntityType\Form;

use IDCT\SymfonyFormSoftDeleteAwareEntityType\SoftDeleteAwareEntityType;
use IDCT\Tests\SymfonyFormSoftDeleteAwareEntityType\Model\DummyEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ParentFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('test', SoftDeleteAwareEntityType::class, [
                'class' => DummyEntity::class,
                'choice_label' => 'name',
            ])
        ;
    }
}
