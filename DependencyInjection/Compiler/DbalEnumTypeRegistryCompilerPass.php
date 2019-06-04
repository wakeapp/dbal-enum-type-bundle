<?php

declare(strict_types=1);

/*
 * This file is part of the DbalEnumTypeBundle package.
 *
 * (c) Wakeapp <https://wakeapp.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Wakeapp\Bundle\DbalEnumTypeBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\Finder;
use Wakeapp\Bundle\DbalEnumTypeBundle\DependencyInjection\WakeappDbalEnumTypeExtension;
use Wakeapp\Bundle\DbalEnumTypeBundle\Doctrine\Connection\ConnectionFactoryDecorator;
use Wakeapp\Component\DbalEnumType\Type\AbstractEnumType;
use function array_unique;
use function get_declared_classes;
use function is_subclass_of;

class DbalEnumTypeRegistryCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(ConnectionFactoryDecorator::class)) {
            return;
        }

        $dbalEnumTypes = $this->getDbalEnumTypeClasses($container);

        $container
            ->getDefinition(ConnectionFactoryDecorator::class)
            ->replaceArgument(1, $dbalEnumTypes)
        ;
    }

    /**
     * @param ContainerBuilder $container
     *
     * @return array
     */
    private function getDbalEnumTypeClasses(ContainerBuilder $container): array
    {
        $finder = $this->getFinder($container);

        foreach ($finder as $splFileInfo) {
            include_once($splFileInfo->getPathname());
        }

        $declaredClassList = get_declared_classes();
        $sourceClasses = [];

        foreach ($declaredClassList as $className) {
            if (!is_subclass_of($className, AbstractEnumType::class)) {
                continue;
            }

            $sourceClasses[$className] = $className::getTypeName();
        }

        return array_unique($sourceClasses);
    }

    /**
     * @param ContainerBuilder $container
     *
     * @return Finder
     */
    private function getFinder(ContainerBuilder $container): Finder
    {
        $sourceList = $container->getParameter(WakeappDbalEnumTypeExtension::PARAMETER_SOURCES);
        $container->getParameterBag()->remove(WakeappDbalEnumTypeExtension::PARAMETER_SOURCES);

        $projectDir = $container->getParameter('kernel.project_dir');

        $finder = new Finder();
        $finder->files()->name('*.php');

        foreach ($sourceList as $directoryOrFile) {
            $finder->in($projectDir . DIRECTORY_SEPARATOR . $directoryOrFile);
        }

        return $finder;
    }
}
