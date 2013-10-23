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
    protected $yiiProtected = 'protected';
    protected $yiiFramework = 'framework';
    protected $yiiFrameworkName = 'yiisoft/yii';


    public function __construct(IOInterface $io, Composer $composer, $type = 'library', Filesystem $filesystem = null)
    {
        if ($composer->getPackage()) {
            $extra = $composer->getPackage()->getExtra();
            if(isset($extra['yii-protected'])){
                $this->yiiProtected = rtrim($extra['yii-protected'], '/\\');
            }

            if(isset($extra['yii-framework'])){
                $this->yiiFramework = rtrim($extra['yii-framework'], '/\\');
            }

            if(isset($extra['yii-framework-name'])){
                $this->yiiFrameworkName = $extra['yii-framework-name'];
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
        switch($type){
            case 'framework': return  $this->yiiFramework.DIRECTORY_SEPARATOR; break;
            case 'module': return $this->yiiProtected.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR; break;
            case 'extension': return $this->yiiProtected.DIRECTORY_SEPARATOR.'extensions'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR; break;
            case 'other': return $this->yiiProtected.DIRECTORY_SEPARATOR.str_replace('-',DIRECTORY_SEPARATOR,$name).DIRECTORY_SEPARATOR; break;
        }

        return $this->yiiProtected.DIRECTORY_SEPARATOR.$type.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR;
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
