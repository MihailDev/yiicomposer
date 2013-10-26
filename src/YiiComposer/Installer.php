<?php
namespace YiiComposer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Repository\InstalledRepositoryInterface;
use Composer\Package\PackageInterface;
use Composer\Util\Filesystem;
use Composer\Installer\LibraryInstaller;


class Installer extends LibraryInstaller
{
    protected $yiiPaths = array();


    public function __construct(IOInterface $io, Composer $composer, $type = 'library', Filesystem $filesystem = null){
        $this->yiiPaths = self::getYiiPaths($composer);

        parent::__construct($io, $composer, $type, $filesystem);
    }

    public static function getYiiPaths(Composer $composer){
        $yiiPaths = array(
            'module' => '{vendor}'.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'{name}',
            'extension' => '{vendor}'.DIRECTORY_SEPARATOR.'extensions'.DIRECTORY_SEPARATOR.'{name}',
            'framework' => '{vendor}'.DIRECTORY_SEPARATOR.'framework',
            'yiisoft/yii' => '$framework$'
        );

        if ($composer->getPackage()) {
            $extra = $composer->getPackage()->getExtra();

            if(!empty($extra['yiicomposer-paths'])){
                $yiiPaths = array_merge($yiiPaths, $extra['yiicomposer-paths']);
            }
        }

        return $yiiPaths;
    }


    public static function yiiPackageInfo($type){
        $type = strtolower($type);

        if(preg_match('#yii-([^-]+)-(.+)#i', $type, $m)){
            return array('type' => $m[1], 'name' => $m[2]);
        }

        return false;
    }

    public static function getYiiPackageBasePath($packageType, $paths, $vendorDir, $packageName=""){

        $type = 'empty';
        $name = '';
        $path = false;

        $info = self::yiiPackageInfo($packageType);
        if(!empty($info)){
            $type = $info['type'];
            $name = $info['name'];
            $path = "{vendor}".DIRECTORY_SEPARATOR."{type}".DIRECTORY_SEPARATOR."{name}";
        }

        if(isset($paths[$packageName])){
            $path = $paths[$packageName];
        }elseif($type !== false && isset($paths[$type])){
            $path = $paths[$type];
        }

        if($path === false)
            return false;

        if(preg_match('#(?<=\$).*(?=\$)#i', $path, $m)){
            if(isset($paths[$m[0]])){
                $path = $paths[$m[0]];
            }else{
                throw new \Exception("Unknown to identify directory! ".$path);
            }
        }

        $path = str_replace('/', DIRECTORY_SEPARATOR, $path);
        $path = str_replace('\\', DIRECTORY_SEPARATOR, $path);
        $packageName = str_replace('/', DIRECTORY_SEPARATOR, $packageName);

        $info = array("{vendor}" => $vendorDir, "{type}" => $type, "{package}" => $packageName, "{name}" => $name);
        $path = strtr($path, $info);

        return rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
    }



    /**
     * {@inheritDoc}
     */
    protected function getPackageBasePath(PackageInterface $package){
        $this->initializeVendorDir();
        $path = $this->getYiiPackageBasePath($package->getType(), $this->yiiPaths, $this->vendorDir, $package->getName());
        if($path === false)
            return parent::getPackageBasePath($package);

        return $path;
    }

    /**
     * {@inheritDoc}
     */
    public function supports($packageType)
    {
        if($this->yiiPackageInfo($packageType) !== false){
            return true;
        }

        return parent::supports($packageType);
    }
}
