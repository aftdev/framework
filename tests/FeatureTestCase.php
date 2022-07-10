<?php

namespace AftDev\Test;

use AftDev\Db\Migration\PhinxApplication;
use AftDev\DbEloquent\Capsule\CapsuleManager;
use Illuminate\Support\Arr;
use Laminas\ServiceManager\ServiceManager;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @internal
 * @coversNothing
 */
class FeatureTestCase extends TestCase
{
    /**
     * Whether or not the test database was created/migrated.
     *
     * @var bool
     */
    protected static $dbMigrated = false;

    /**
     * @var ServiceManager
     */
    protected $container;

    /**
     * @var CapsuleManager
     */
    protected $connectionManager;

    /**
     * Whether or not you want to use database.
     *
     * @var bool
     */
    protected $useDb = false;

    protected function setUp(): void
    {
        $this->container = $this->getFreshContainer();

        if ($this->useDb) {
            $this->connectionManager = $this->container->get(CapsuleManager::class);
            $this->startDb();
        }

        parent::setUp();
    }

    protected function tearDown(): void
    {
        // rollback transaction.
        if ($this->useDb) {
            $this->connectionManager->getConnection()->rollBack();
        }

        parent::tearDown();
    }

    /**
     * Fetch a new container.
     *
     * @return mixed
     */
    protected function getFreshContainer()
    {
        return require __DIR__.'/../config/container.php';
    }

    protected function startDb()
    {
        if (!self::$dbMigrated) {
            $phinx = $this->container->get(PhinxApplication::class);
            $phinx->call('migrate', []);

            self::$dbMigrated = true;
        }

        $this->connectionManager->getConnection()->beginTransaction();
    }

    /**
     * Mock a service in the container and return its prophecy.
     */
    protected function mockService(string $name, object $mock)
    {
        $service = $mock;
        if ($mock instanceof ObjectProphecy) {
            $service = $mock->reveal();
        }

        $this->container->setAllowOverride(true);
        $this->container->setService($name, $service);
        $this->container->setAllowOverride(false);

        return $mock;
    }

    protected function overrideConfig($configName, $configValue)
    {
        $config = $this->container->get('config');

        Arr::set($config, $configName, $configValue);
    }
}
