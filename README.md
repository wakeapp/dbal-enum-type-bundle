Dbal Enum Type Bundle
=====================

[![Latest Stable Version](https://poser.pugx.org/wakeapp/dbal-enum-type-bundle/v/stable)](https://packagist.org/packages/wakeapp/dbal-enum-type-bundle)
[![Total Downloads](https://poser.pugx.org/wakeapp/dbal-enum-type-bundle/downloads)](https://packagist.org/packages/wakeapp/dbal-enum-type-bundle)

Введение
--------

Бандл предоставляет интеграцию с компонентом [DbalEnumType](https://github.com/wakeapp/dbal-enum-type).

Установка
---------

### Шаг 1: Загрузка бандла

Откройте консоль и, перейдя в директорию проекта, выполните следующую команду для загрузки наиболее подходящей
стабильной версии этого бандла:

```bash
    composer require wakeapp/dbal-enum-type-bundle
```
*Эта команда подразумевает что [Composer](https://getcomposer.org) установлен и доступен глобально.*

### Шаг 2: Подключение бандла

После включите бандл добавив его в список зарегистрированных бандлов в `app/AppKernel.php` файл вашего проекта:

```php
<?php declare(strict_types=1);
// app/AppKernel.php

class AppKernel extends Kernel
{
    // ...

    public function registerBundles()
    {
        $bundles = [
            // ...

            new Wakeapp\Bundle\DbalEnumTypeBundle\WakeappDbalEnumTypeBundle(),
        ];

        return $bundles;
    }

    // ...
}
```

Конфигурация
------------

Чтобы начать использовать бандл предварительная конфигурация **не** требуется.

Использование
-------------

С примерами использования можно ознакомиться в документации [DbalEnumType](https://github.com/wakeapp/dbal-enum-type).

Лицензия
--------

[![license](https://img.shields.io/badge/License-MIT-green.svg?style=flat-square)](./LICENSE)
