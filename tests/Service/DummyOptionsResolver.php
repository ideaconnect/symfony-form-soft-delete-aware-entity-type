<?php

declare(strict_types=1);

namespace IDCT\Tests\SymfonyFormSoftDeleteAwareEntityType\Service;

use Symfony\Component\OptionsResolver\OptionsResolver;

class DummyOptionsResolver extends OptionsResolver
{
    protected array $testNormalizers;

    public function __construct()
    {
        $this->testNormalizers = [];
    }

    public function getTestNormalizers()
    {
        return $this->testNormalizers;
    }

    public function setNormalizer(string $option, \Closure $normalizer)
    {
        $this->testNormalizers[$option] = [$normalizer];
    }
}
