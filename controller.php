<?php defined('C5_EXECUTE') or die("Access Denied.");

class ApiBaseConfigPackage extends Package {

	protected $pkgHandle = 'api_base_config';
	protected $appVersionRequired = '5.6.0';
	protected $pkgVersion = '0.9';

	public function getPackageName() {
		return t("Api:Base:Config");
	}

	public function getPackageDescription() {
		return t("Provides API config management.");
	}

	public function install() {
		$installed = Package::getByHandle('api');
		if(!is_object($installed)) {
			throw new Exception(t('Please install the "API" package before installing %s', $this->getPackageName()));
		}

		parent::install();

		$pkg = Package::getByHandle($this->pkgHandle);
		ApiRoute::add('config', t('List, Add, and Edit Config Entries'), $pkg);

	}
	
	public function uninstall() {
		Loader::model('api_register', 'api');
		ApiRegister::removeByPackage($this->pkgHandle);//remove all the apis
		parent::uninstall();
	}

}