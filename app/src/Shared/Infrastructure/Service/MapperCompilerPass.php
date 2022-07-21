<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Service;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class MapperCompilerPass implements CompilerPassInterface
{
    private const TARGET_TAG = 'projects.mapper';

    public function process(ContainerBuilder $container)
    {
        $parentServices = $container->findTaggedServiceIds(self::TARGET_TAG);
        foreach ($parentServices as $id => $tags) {
            $definition = $container->getDefinition($id);
            foreach ($tags as $tag) {
                if (!isset($tag['target_tag'])) {
                    throw new InvalidConfigurationException(
                        sprintf('The tag "%s" must have target_tag field.', self::TARGET_TAG)
                    );
                }
                $target = $tag['target_tag'];
                $childServices = $container->findTaggedServiceIds($target);
                $definition->addArgument(array_keys($childServices));
            }
        }
    }
}
