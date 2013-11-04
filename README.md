Yii Composer Installer
=============================

Разработка для удобного использования Composer с фреймворком Yii

        {
            "config":{
                "vendor-dir": "www/protected/vendor"
            },
            "extra": {
                "yiicomposer-paths":{
                    "module": "{vendor}/modules/{name}",
                    "extension":"{vendor}/extensions/{name}",
                    "framework": "{vendor}/framework",
                    "yiisoft/yii": "$framework$"
                },
                "yiicomposer-console-config":"www/protected/config/console.php",
                "yiicomposer-console-commands":[
                    {
                        "controller":"migrate"
                    },
                    {
                        "controller":"test",
                        "action": "test",
                        "params": {
                            "param1": "value1",
                            "param2": "value2",
                            "param3": "value3"
                        }
                    }
                ]
            },
            "require": {
                "mihaildev/yiicomposer": "dev-master",
                "yiisoft/yii": "1.1.14"
            },
            "scripts":{
                "post-update-cmd": "YiiComposer\\Console::update"
            }
        }

Настройка путей
------------

За настройку путей отвечает переменная "yiicomposer-paths".
Формирование пути, основываясь на следующих переменных:
{vendor} – путь к папке для хранения всех пакетов берётся из основных настроек "vendor-dir"
{type} – тип пакета, если указан (пример yii-extension-test - {type} будет равен extension)
 Тип пакета можно указать как в настройках пакета (в файле composer.json параметр "type" пакета) так и в настройках проектного файла в разделе "yiicomposer-paths" как для пакета yiisoft/yii установлен тип framework. Тип задаётся в нескольких форматах для параметра type в пакете yii-{type} или yii-{type}-{name} или в проектном файле в разделе "yiicomposer-paths" в формате ${type}$ или ${type}-{name}$

{package} – название пакета оригинальное (пример mihaildev/testextension будет равен mihaildev/testextension)
{name} – название по умолчанию берётся из названия пакета вторая часть (пример "name": "mihaildev/testmodule" {name} равен testmodule), если название установлено в типе пакета, то yiicomposer возьмёт его (пример yii-extension-test - {name} будет равен test) также можно переназначить и в настройках (пример "clevertech/yii-booster": "$extension-yii-booster$" {name} будет равен yii-booster)


В ней можно преназначить основные потдерживаемые типы (module, extension, framework) а также добавить свои типы.

        "extra":{
            "yiicomposer-paths":{
                "sometype": "{vendor}/some/{name}"
                "yiiext/migrate-command": "$sometype-hochutut$"
                "mihaildev/testextension": "$sometype-hochutut2$"
            }
        },
        "require": {
            "mihaildev/yiicomposer": "dev-master",
            "mihaildev/testextension": "dev-master",
            "yiisoft/yii": "dev-master",
            "yiiext/migrate-command": "dev-master"
        }


Настройка пакета
------------

Вариант 1
```json
{
    "name": "mihaildev/testextension",
    "type": "yii-extension-test"
}
```

в данном случае {type} равен extension а {name} равен test

Вариант 2
```json
{
    "name": "mihaildev/testextension",
    "type": "yii-extension"
}
```

в данном случае {type} равен extension а {name} равен testextension


