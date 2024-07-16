<?php

declare(strict_types=1);

namespace IDCT\Tests\SymfonyFormSoftDeleteAwareEntityType\Model;

use Gedmo\Mapping\Annotation\SoftDeleteable;
use Gedmo\SoftDeleteable\SoftDeleteable as SoftDeleteableInterface;

#[SoftDeleteable(fieldName: 'deletedAtDate')]
class DummyEntity implements SoftDeleteableInterface
{
    protected ?\DateTimeInterface $deletedAtDate = null;

    public function getDeletedAtDate(): ?\DateTimeInterface
    {
        return $this->deletedAtDate;
    }

    public function getId()
    {
        return 0;
    }

    public function setDeletedAtDate(\DateTimeInterface $dateTime): self
    {
        $this->deletedAtDate = $dateTime;

        return $this;
    }
}
