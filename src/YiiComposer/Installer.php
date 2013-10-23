<?php
namespace YiiComposer;

use Composer\Installer\LibraryInstaller;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;

class Installer extends LibraryInstaller
{
    /**
     * Package types
     * @var array
     */
    private $supportedTypes = array('app', 'module', 'extension', 'framework');

    protected function yiiProtectedPath(){
        if ($this->composer->getPackage()) {
            $extra = $this->composer->getPackage()->getExtra();
            if (!empty($extra['yiicomposer']['protected'])){
                return $extra['yiicomposer']['protected'];
            }
        }

        return 'protected/';
    }

    protected function yiiFrameworkPath(){
        if ($this->composer->getPackage()) {
            $extra = $this->composer->getPackage()->getExtra();
            if (!empty($extra['yiicomposer']['framework'])){
                return $extra['yiicomposer']['framework'];
            }
        }

        return 'framework/';
    }

    protected function yiiTypePath($type, $name=""){
        switch($type){
            case 'framework': return $this->yiiFrameworkPath(); break;
            case 'app': return $this->yiiProtectedPath().str_replace('-','/',$name)."/"; break;
            case 'module': return $this->yiiProtectedPath().'modules/'.$name."/"; break;
            case 'extension': return $this->yiiProtectedPath().'extensions/'.$name."/"; break;
        }

        return $this->yiiProtectedPath().'vendor/'.$name."/";
    }

    /**
     * {@inheritDoc}
     */
    public function getInstallPath(PackageInterface $package)
    {
        $type = $package->getType();

        $type = strtolower($type);

        if($type == 'yii-framework'){
            return $this->yiiTypePath('framework');
        }

        $subtype = 'none';

        if(preg_match('#yii-([^-]*)-(.*)#i', $type, $m)){
            $subtype = $m[1];
            $name = $m[2];
        }

        if(!in_array($subtype, $this->supportedTypes)){
            throw new \InvalidArgumentException(
                'Sorry the package type of this package is not yet supported.'
            );
        }

        return $this->yiiTypePath($subtype, $name);
    }

    public function uninstall(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        if (!$repo->hasPackage($package)) {
            throw new \InvalidArgumentException('Package is not installed: '.$package);
        }

        $repo->removePackage($package);

        $installPath = $this->getInstallPath($package);
        $this->io->write(sprintf('Deleting %s - %s', $installPath, $this->filesystem->removeDirectory($installPath) ? '<comment>deleted</comment>' : '<error>not deleted</error>'));
    }

    /**
     * {@inheritDoc}
     */
    public function supports($packageType)
    {
		$packageType = strtolower($packageType);

        $subtype = 'none';

        if($packageType == 'yii-framework'){
            return true;
        }

        if(preg_match('#yii-([^-]*)-(.*)#i', $packageType, $m)){
            $subtype = $m[1];
        }

        if(!in_array($subtype, $this->supportedTypes)){
			return false;
		}

		return true;
	}
}
