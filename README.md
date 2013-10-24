Yii Composer Installer
=============================

Разработка для удобного использования Composer с фреймворком Yii

Тут представлен пример развёртывания проекта для тестирования

Фаил проекта composer.json

    {
      "require": {
          "mihaildev/yiicomposer": "2.0.0",
          "mihaildev/testextension": "1.0.1",
          "mihaildev/testmodule": "1.0.1",
          "yiisoft/yii": "1.1.14"
      },
      "config":{
          "vendor-dir": "protected/vendor"
      },
      "extra": {
          "yii-protected": "protected",
          "yii-framework": "framework",
          "yii-framework-name": "yiisoft/yii"
      }
    }

Фаил composer.json модуля (специально сделал тестовый модуль https://github.com/MihailDev/testmodule)

    {
        "name": "mihaildev/testmodule",
        "type": "yii-module-test",
        "version": "1.0.1",
        "require": {
            "mihaildev/yiicomposer": "2.0.0"
        }
    }

Фаил composer.json приложения (специально сделал тестовое приложение https://github.com/MihailDev/testextension)

    {
        "name": "mihaildev/testextension",
        "type": "yii-extension-test",
        "version": "1.0.1",
        "require": {
            "mihaildev/yiicomposer": "2.0.0"
        }
    }

YiiComposer реагирует на поле "type" он распознаёт 3 типа:

1) "type": "yii-extension-{НазваниеПриложения}" (пример "type": "yii-extension-test")
в данном случае пакет будет установлен в папку protected/extensions/{НазваниеПриложения} (из примера protected/extensions/test/)

2) "type": "yii-module-{НазваниеМодуля}" (пример "type": "yii-module-test")
в данном случае пакет будет установлен в папку protected/modules/{НазваниеМодуля} (из примера protected/modules/test/)

3) "type": "yii-other-{НазваниеПапки1-НазваниеПапки2 ...}" (пример "type": "yii-other-test1-test2")
в данном случае пакет будет установлен в папку protected/{НазваниеПапки1}/{НазваниеПапки2} (из примера protected/test1/test2/)

Также если YiiComposer не знает типа но он написан в виде yii-{Тип}-{Имя} то он установит пакет в папку protected/{Тип}/{Имя}/

Для того чтоб настройит путь к папке protected в раздел "extra" добавьте параметр "yii-protected" по умолчанию "protected"

так же YiiComposer умеет устанавливать сам фреймворк Yii

Для того чтоб задайть путь к папке с фреймворком  в раздел "extra" добавьте параметр "yii-framework" по умолчанию "framework"

Для того чтоб изменит распознавание название фреймворка в раздел "extra" добавьте параметр "yii-framework-name" по умолчанию "yiisoft/yii"