<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\MonorepoBuilder\Release\ReleaseWorker;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    // release workers - in order to execute
    $services->set(ReleaseWorker\UpdateReplaceReleaseWorker::class);
    $services->set(ReleaseWorker\SetCurrentMutualDependenciesReleaseWorker::class);
    $services->set(ReleaseWorker\AddTagToChangelogReleaseWorker::class);
    $services->set(ReleaseWorker\TagVersionReleaseWorker::class);
    $services->set(ReleaseWorker\PushTagReleaseWorker::class);
    $services->set(ReleaseWorker\SetNextMutualDependenciesReleaseWorker::class);
    $services->set(ReleaseWorker\UpdateBranchAliasReleaseWorker::class);
    $services->set(ReleaseWorker\PushNextDevReleaseWorker::class);
};
