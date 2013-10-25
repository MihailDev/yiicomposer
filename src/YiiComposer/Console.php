<?php
/**
 * Date: 25.10.13
 * Time: 16:13
 */

namespace YiiComposer;
use Composer\Composer;
use Composer\Script\Event;
use YiiComposer\Installer;

class Console{
    public static function getYiiPath(Event $event){
        $paths = Installer::getYiiPaths($event->getComposer());
        return Installer::getYiiPackageBasePath('framework', self::getVendorDir($event->getComposer()), $paths).DIRECTORY_SEPARATOR."framework";
    }

    public static function getVendorDir(Composer $composer){
        return rtrim($composer->getConfig()->get('vendor-dir'), '/');
    }

    public static function getConfigFile(Composer $composer){
        if ($composer->getPackage()) {
            $extra = $composer->getPackage()->getExtra();

            if(!empty($extra['yiicomposer-console-config'])){
                return $extra['yiicomposer-console-config'];
            }
        }

        return self::getVendorDir($composer).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR."console.php";
    }

    public static function update(Event $event)
    {
        defined('YII_PATH') or self::defineYiiPath($event);
        defined('CONSOLE_CONFIG') or self::defineConfigFile($event);

        $app = self::yii();
        if($app !== null){
            $app->commandRunner->addCommands(\Yii::getPathOfAlias('system.cli.commands'));
            $app->commandRunner->run(array('yiic', 'migrate'));
        }

        echo "\n";
    }

    public static function defineYiiPath(Event $event){
        define('YII_PATH', self::getYiiPath($event));
    }

    public static function defineConfigFile(Event $event){
        define('CONSOLE_CONFIG', self::getConfigFile($event->getComposer()));
    }

    public static function yii()
    {
        if (!is_file(YII_PATH.'/yii.php'))
        {
            return null;
        }

        require_once(YII_PATH . '/yii.php');
        spl_autoload_register(array('YiiBase', 'autoload'));

        if (\Yii::app() === null) {
            if (is_file(CONSOLE_CONFIG)) {
                $app = \Yii::createConsoleApplication(CONSOLE_CONFIG);
            } else {
                echo "File from CONSOLE CONFIG not found\n";
                echo "please set rigth 'yiicomposer-console-config'\n";
                throw new \Exception("File from CONSOLE_CONFIG not found");
            }
        } else {
            $app = \Yii::app();
        }
        return $app;
    }

} 