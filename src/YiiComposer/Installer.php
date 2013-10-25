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
    protected $yiiFrameworkName = 'yiisoft/yii';
    protected $yiiTypes = array();


    public function __construct(IOInterface $io, Composer $composer, $type = 'library', Filesystem $filesystem = null){
        $this->yiiTypes = array(
            'module' => '{vendor}'.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'{name}',
            'extension' => '{vendor}'.DIRECTORY_SEPARATOR.'extensions'.DIRECTORY_SEPARATOR.'{name}',
            'framework' => '{vendor}'.DIRECTORY_SEPARATOR.'framework'
        );

        if ($composer->getPackage()) {
            $extra = $composer->getPackage()->getExtra();


            if(!empty($extra['yiicomposer-framework'])){
                $this->yiiFrameworkName = strtolower(trim($extra['yiicomposer-framework']));
            }

            if(!empty($extra['yiicomposer-paths'])){
                foreach($extra['yiicomposer-paths'] as $type => $path){
                    $type = strtolower($type);
                    $this->yiiTypes[$type] = str_replace('/', DIRECTORY_SEPARATOR, $path);
                }
            }

        }

        parent::__construct($io, $composer, $type, $filesystem);
    }


    protected function isYii($type, $bool=true){
        $type = strtolower($type);

        if($type == 'yii-framework'){
            if($bool)
                return true;
            return array('type' => 'framework', 'name' => '');
        }

        if(preg_match('#yii-([^-]*)-(.*)#i', $type, $m)){
            if($bool)
                return true;
            return array('type' => $m[1], 'name' => $m[2]);
        }

        return false;
    }

    protected function yiiTypePath($type, $name=""){

        $this->initializeVendorDir();

        $path = "{vendor}".DIRECTORY_SEPARATOR."{type}".DIRECTORY_SEPARATOR."{name}";
        if(isset($this->yiiTypes[$type])){
            $path = $this->yiiTypes[$type];
        }
        $info = array("{vendor}" => $this->vendorDir, "{type}" => $type, "{name}" => $name);
        $path = strtr($path, $info);
        return rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
    }

    /**
     * {@inheritDoc}
     */
    public function getInstallPath(PackageInterface $package)
    {
        if($package->getName() == $this->yiiFrameworkName){
            return $this->yiiTypePath('framework');
        }

        $info = $this->isYii($package->getType(), false);
        if($info === false)
            return parent::getInstallPath($package);

        return $this->yiiTypePath($info['type'], $info['name']);
    }

    /**
     * {@inheritDoc}
     */
    public function uninstall(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        if($package->getName() == $this->yiiFrameworkName){
            $info['type'] = 'framework';
            $info['name'] = '';
        }else{
            $info = $this->isYii($package->getType(), false);
            if($info === false)
                return parent::uninstall($repo, $package);
        }


        if (!$repo->hasPackage($package)) {
            throw new \InvalidArgumentException('Package is not installed: '.$package);
        }

        $repo->removePackage($package);

        $installPath = $this->yiiTypePath($info['type'], $info['name']);
        $this->io->write(sprintf('Deleting %s - %s', $installPath, $this->filesystem->removeDirectory($installPath) ? '<comment>deleted</comment>' : '<error>not deleted</error>'));
    }

    /**
     * {@inheritDoc}
     */
    public function supports($packageType)
    {
		if($this->isYii($packageType))
            return true;

        return parent::supports($packageType);
	}
}
