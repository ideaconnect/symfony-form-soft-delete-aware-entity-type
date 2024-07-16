<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\DeletableEntity;
use App\Entity\EntityWithDeletableRelation;
use IDCT\SymfonyFormSoftDeleteAwareEntityType\SoftDeleteAwareEntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntityWithDeletableRelationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('relatedDeletableEntity', SoftDeleteAwareEntityType::class, [
                'class' => DeletableEntity::class,
                'placeholder' => 'placeholder',
                'choice_label' => 'name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EntityWithDeletableRelation::class,
        ]);
    }
}
