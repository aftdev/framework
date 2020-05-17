<?php

namespace AftDev\DbEloquent\Serializer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ModelNormalizer implements NormalizerInterface, DenormalizerInterface, CacheableSupportsMethodInterface
{
    /**
     * {@inheritdoc}
     */
    public function hasCacheableSupportsMethod(): bool
    {
        return __CLASS__ === static::class;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, string $format = null)
    {
        return $data instanceof Model;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, string $type, string $format = null)
    {
        return is_a($type, Model::class, true);
    }

    /**
     * {@inheritdoc}
     *
     * Will try to fetch the row from the database.
     * If not found we will return an empty object.
     *
     * @throws NotNormalizableValueException
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        /** @var Model $modelType */
        $modelType = $data['model'] ?? null;
        $modelId = $data['id'] ?? null;

        if (null === $modelType || null === $modelId) {
            throw new NotNormalizableValueException('The data needs to be an array containing a model and id key.');
        }

        try {
            return $modelType::findOrFail($modelId);
        } catch (ModelNotFoundException $e) {
            return new $modelType();
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidArgumentException
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        if (!$object instanceof Model) {
            throw new InvalidArgumentException('The object must implement the "'.Model::class.'".');
        }

        return [
            'model' => get_class($object),
            'id' => $object->getKey(),
        ];
    }
}
