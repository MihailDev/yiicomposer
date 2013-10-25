<?php
namespace YiiComposer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Repository\InstalledRepositoryInterface;
use Composer\Package\PackageInterface;
use Composer\Util\Filesystem;
use Composer\Installer\LibraryInstaller;


/*
 * задачи:
 * 2 все плюшки вынести в venodor и добавить алиасы
 * 3 запустить миграции
 * */

class Installer extends LibraryInstaller
{
    protected $yiiPaths = array();


    public function __construct(IOInterface $io, Composer $composer, $type = 'library', Filesystem $filesystem = null){
        $this->yiiPaths = array(
            'module' => '{vendor}'.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'{name}',
            'extension' => '{vendor}'.DIRECTORY_SEPARATOR.'extensions'.DIRECTORY_SEPARATOR.'{name}',
            'yiisoft/yii' => '{vendor}'.DIRECTORY_SEPARATOR.'framework'
        );

        if ($composer->getPackage()) {
            $extra = $composer->getPackage()->getExtra();

            if(!empty($extra['yiicomposer-paths'])){
                $this->yiiPaths = array_merge($this->yiiPaths, $extra['yiicomposer-paths']);
            }
        }

        parent::__construct($io, $composer, $type, $filesystem);
    }


    protected function yiiPackageInfo($type){
        $type = strtolower($type);

        if($type == 'yii-framework'){
            return array('type' => 'framework', 'name' => '');
        }

        if(preg_match('#yii-([^-]*)-(.*)#i', $type, $m)){
            return array('type' => $m[1], 'name' => $m[2]);
        }

        return false;
    }

    protected function getYiiPackageBasePath(PackageInterface $package){

        $packageName = $package->getName();
        $type = 'empty';
        $name = '';
        $path = false;

        $info = $this->yiiPackageInfo($package->getType());
        if(!empty($info)){
            $type = $info['type'];
            $name = $info['name'];
            $path = "{vendor}".DIRECTORY_SEPARATOR."{type}".DIRECTORY_SEPARATOR."{name}";
        }

        if(isset($this->yiiPaths[$packageName])){
            $path = $this->yiiPaths[$packageName];
        }elseif($type !== false && isset($this->yiiPaths[$type])){
            $path = $this->yiiPaths[$type];
        }

        if($path === false)
            return false;

        $path = str_replace('/', DIRECTORY_SEPARATOR, $path);
        $path = str_replace('\\', DIRECTORY_SEPARATOR, $path);
        $packageName = str_replace('/', DIRECTORY_SEPARATOR, $packageName);

        $this->initializeVendorDir();
        $info = array("{vendor}" => $this->vendorDir, "{type}" => $type, "{package}" => $packageName, "{name}" => $name);
        $path = strtr($path, $info);

        return rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
    }



    /**
     * {@inheritDoc}
     */
    protected function getPackageBasePath(PackageInterface $package){
        $path = $this->getYiiPackageBasePath($package);
        if($path === false)
            return parent::getPackageBasePath($package);

        return $path;
    }

    /**
     * {@inheritDoc}
     */
    public function supports($packageType)
    {
        if(preg_match('#yii-[^-]*-.*#i', $type)){
            return true;
        }

        return parent::supports($packageType);
    }
}
