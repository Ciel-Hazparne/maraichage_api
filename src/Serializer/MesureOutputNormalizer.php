<?php

namespace App\Serializer;

use App\Entity\Mesure;
use App\Dto\MesureOutput;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;

class MesureOutputNormalizer implements ContextAwareNormalizerInterface, CacheableSupportsMethodInterface
{
    public function __construct(
        private readonly NormalizerInterface $normalizer
    ) {}

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof Mesure && ($context['output']['class'] ?? null) === MesureOutput::class;
    }

    public function normalize($object, string $format = null, array $context = []): array
    {
        if (!$object instanceof Mesure) {
            return [];
        }

        $output = new MesureOutput(
            $object->getId(),
            $object->getValeur(),
            $object->getUnite(),
            $object->getLibelle(),
            $object->getCreatedAt()
        );

        return $this->normalizer->normalize($output, $format, $context);
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
