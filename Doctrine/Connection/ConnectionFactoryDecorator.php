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

namespace Wakeapp\Bundle\DbalEnumTypeBundle\Doctrine\Connection;

use Doctrine\Bundle\DoctrineBundle\ConnectionFactory;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Types\Type;
use Wakeapp\Bundle\EnumerBundle\Registry\EnumRegistryService;
use Wakeapp\Component\DbalEnumType\Type\AbstractEnumType;

class ConnectionFactoryDecorator
{
    /**
     * @var EnumRegistryService|null
     */
    private $enumRegistry;

    /**
     * @var ConnectionFactory
     */
    private $original;

    /**
     * @var string[]
     */
    private $typeRegistry;

    /**
     * @param ConnectionFactory $original
     * @param array $typeRegistry
     * @param EnumRegistryService|null $enumRegistry
     */
    public function __construct(ConnectionFactory $original, array $typeRegistry, ?EnumRegistryService $enumRegistry)
    {
        $this->enumRegistry = $enumRegistry;
        $this->original = $original;
        $this->typeRegistry = $typeRegistry;
    }

    /**
     * @param array $params
     * @param Configuration|null $config
     * @param EventManager|null $eventManager
     * @param array $mappingTypes
     *
     * @return Connection
     *
     * @throws DBALException
     */
    public function createConnection(
        array $params,
        ?Configuration $config = null,
        ?EventManager $eventManager = null,
        array $mappingTypes = []
    ): Connection {
        $connection = $this->original->createConnection($params, $config, $eventManager, $mappingTypes);

        foreach ($this->typeRegistry as $typeClass => $typeName) {
            if (Type::hasType($typeName) === false) {
                Type::addType($typeName, $typeClass);
            }
        }

        if (!$this->enumRegistry) {
            return $connection;
        }

        foreach (Type::getTypesMap() as $typeName => $className) {
            $type = Type::getType($typeName);

            if (!$type instanceof AbstractEnumType) {
                continue;
            }

            $enumClass = $type::getEnumClass();

            if ($this->enumRegistry->hasEnum($enumClass)) {
                $type::setValues($this->enumRegistry->getOriginalList($enumClass));
            }
        }

        return $connection;
    }
}
