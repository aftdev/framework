<?php

declare(strict_types=1);

namespace AftDev\Api;

use AftDev\Api\Exception\UnknownVersionException;
use AftDev\ServiceManager\Resolver;
use cebe\openapi\Reader;
use cebe\openapi\spec\OpenApi;
use Psr\Cache\CacheItemPoolInterface;
use SplFileInfo;

class OpenApiManager
{
    public const VERSION_HEADER_NAME = 'X-Api-Version';

    private const BASE_VERSION = '_base';

    private array $cachedVersions = [];
    private array $versions = [];
    private ?string $currentVersion = self::BASE_VERSION;

    public function __construct(
        private string $specFile,
        array $versions = [],
        ?string $currentVersion = null,
        private ?Resolver $resolver = null,
        private ?CacheItemPoolInterface $cache = null,
    ) {
        $this->setVersions($versions);
        $this->setCurrentVersion($currentVersion ?? self::BASE_VERSION);
    }

    /**
     * Set the default version.
     */
    public function setCurrentVersion(string $version): void
    {
        $this->currentVersion = $version;
    }

    public function hasVersion(string $version): bool
    {
        return isset($this->versions[$version]);
    }

    public function getVersions(): array
    {
        return array_keys($this->versions);
    }

    /**
     * @throws UnknownVersionException
     */
    public function getCurrentVersion(): OpenApi
    {
        return $this->getVersion($this->currentVersion);
    }

    /**
     * @throws UnknownVersionException
     */
    public function getVersion(string $version): OpenApi
    {
        if (isset($this->cachedVersions[$version])) {
            return $this->cachedVersions[$version];
        }

        $versionMutations = $this->getVersionMutations($version);

        $versionOpenApi = $this->getBase();

        // Override Version
        if (self::BASE_VERSION != $version) {
            $versionOpenApi->info->version = $version;
        }

        $this->applyMutations($versionOpenApi, $versionMutations);

        return $this->cachedVersions[$version] = $versionOpenApi;
    }

    /**
     * Set the openapi versions.
     *
     * The most recent version will be set as the current version.
     */
    private function setVersions(array $versions): void
    {
        $this->versions = $versions;
        krsort($this->versions);

        $lastVersion = key($this->versions);
        if ($lastVersion) {
            $this->setCurrentVersion($lastVersion);
        }
    }

    /**
     * Get the base openapi.
     */
    private function getBase(): OpenApi
    {
        $info = new SplFileInfo($this->specFile);
        if (!$info->isFile()) {
            throw new \ValueError('Could not find the openapi spec file');
        }

        switch ($info->getExtension()) {
            case 'yaml':
            case 'yml':
                $helper = 'readFromYamlFile';

                break;

            case 'json':
                $helper = 'readFromJsonFile';

                break;

            default:
                throw new \ValueError('Invalid file extension');
        }

        return $this->base = Reader::$helper($this->specFile);
    }

    /**
     * @throws UnknownVersionException
     */
    private function getVersionMutations(string $version): array
    {
        if (self::BASE_VERSION != $version && !$this->hasVersion($version)) {
            throw new UnknownVersionException(sprintf('Unknown version %s', $version));
        }

        $mutations = [];
        foreach ($this->versions as $loopVersion => $versionMutations) {
            $mutations = array_merge($mutations, $versionMutations);

            if ($loopVersion === $version) {
                break;
            }
        }

        return $mutations;
    }

    private function applyMutations(OpenApi $openApi, array $mutations = [])
    {
        foreach ($mutations as $mutation) {
            if ($this->resolver) {
                $this->resolver->call(
                    $mutation,
                    [
                        OpenApi::class => $openApi,
                        '$openApi' => $openApi,
                    ]
                );
            } elseif (is_callable($mutation)) {
                $mutation($openApi);
            }
        }
    }
}
