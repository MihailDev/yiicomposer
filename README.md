Yii Composer Installer
=============================

Разработка для удобного использования Composer с фреймворком Yii

Настройка
------------

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