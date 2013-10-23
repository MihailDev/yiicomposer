<?php
namespace YiiComposer;

use Composer\Installer\LibraryInstaller;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;

class Installer extends LibraryInstaller
{
    /**
     * Package types to installer class map
     *
     * @var array
     */
    private $supportedTypes = array(
		'app'       => 'public_html/',
		'module'    => 'protected/modules/{name}/',
		'extension' => 'protected/extensions/{name}/',
		'widget'    => 'protected/widgets/{name}/'
    );

    /**
     * {@inheritDoc}
     */
    public function getInstallPath(PackageInterface $package)
    {
        $type = $package->getType();

        $type = strtolower($type);

        $subtype = 'none';

        if(preg_match('#yii-([^-]*)-(.*)#i', $type, $m)){
            $subtype = $m[1];
            $name = $m[2];
        }

        if(!isset($this->supportedTypes[$subtype])){
            throw new \InvalidArgumentException(
                'Sorry the package type of this package is not yet supported.'
            );
        }



        if ($this->composer->getPackage()) {
            $extra = $this->composer->getPackage()->getExtra();
            if (!empty($extra['yiicomposer'])){
                if(isset($extra['yiicomposer'][$subtype])){
					return str_replace('{name}', $name, $extra['yiicomposer'][$subtype]);
                }
            }
        }

        if(isset($this->supportedTypes[$subtype])){
        	return str_replace('{name}', $name, $this->supportedTypes[$subtype]);
        }else{
        	throw new \InvalidArgumentException(
                'Sorry the package type of this package is not yet supported.'
            );
        }
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

        if(preg_match('#yii-([^-]*)-(.*)#i', $packageType, $m)){
            $subtype = $m[1];
        }

		if(!isset($this->supportedTypes[$subtype])){
			return false;
		}

		return true;
	}
}
