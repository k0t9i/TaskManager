<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure;

use App\Shared\Infrastructure\Service\MapperCompilerPass;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

final class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new MapperCompilerPass());
    }

    public function getProjectDir(): string
    {
        return dirname(__DIR__, 3).'/symfony';
    }

    public function getCacheDir(): string
    {
        $dir = $_SERVER['APP_CACHE_DIR'];

        return $this->getProjectDir().'/var/cache/'.$dir.'/'.$this->environment;
    }

    public function getLogDir(): string
    {
        $dir = $_SERVER['APP_LOG_DIR'];

        return $this->getProjectDir().'/var/log/'.$dir;
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $this->configureContainerInDir($container, 'shared');
        $this->configureContainerInDir($container, 'app');
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $this->configureRoutesInDir($routes, 'shared');
        $this->configureRoutesInDir($routes, 'app');
    }

    /**
     * Gets the path to the bundles configuration file.
     */
    private function getBundlesPath(): string
    {
        return $this->getConfigDir().'/shared/bundles.php';
    }

    /**
     * Gets the path to the configuration directory.
     */
    private function getConfigDir(): string
    {
        return dirname($this->getProjectDir()).'/config';
    }

    private function configureContainerInDir(ContainerConfigurator $container, string $dir): void
    {
        $configDir = $this->getConfigDir().'/'.$dir;

        $container->import($configDir.'/{packages}/*.yaml');
        $container->import($configDir.'/{packages}/'.$this->environment.'/*.yaml');

        if (is_file($configDir.'/services.yaml')) {
            $container->import($configDir.'/services.yaml');
            $container->import($configDir.'/{services}_'.$this->environment.'.yaml');
        }
    }

    private function configureRoutesInDir(RoutingConfigurator $routes, string $dir): void
    {
        $configDir = $this->getConfigDir().'/'.$dir;

        $routes->import($configDir.'/{routes}/'.$this->environment.'/*.yaml');
        $routes->import($configDir.'/{routes}/*.yaml');

        if (is_file($configDir.'/routes.yaml')) {
            $routes->import($configDir.'/routes.yaml');
        }
    }
}
