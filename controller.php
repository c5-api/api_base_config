<?php defined('C5_EXECUTE') or die("Access Denied.");

class ApiBaseConfigPackage extends Package {

	protected $pkgHandle = 'api_base_config';
	protected $appVersionRequired = '5.5.0';
	protected $pkgVersion = '1.0';

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
		
		$api1 = array();
		$api1['pkgHandle'] = $this->pkgHandle;
		$api1['route'] = 'config/:key';
		$api1['routeName'] = t('Get or Delete a Site Config Entry');
		$api1['class'] = 'config';
		$api1['method'] = 'getConf';
		$api1['via'][] = 'get';
		$api1['via'][] = 'delete';
		
		$api2 = array();
		$api2['pkgHandle'] = $this->pkgHandle;
		$api2['route'] = 'config/:pkg/:key';
		$api2['routeName'] = t('Get or Delete a Package Config Entry');
		$api2['class'] = 'config';
		$api2['method'] = 'getConf';
		$api2['via'][] = 'get';
		$api2['via'][] = 'delete';
		
		$api3 = array();
		$api3['pkgHandle'] = $this->pkgHandle;
		$api3['route'] = 'config/new';
		$api3['routeName'] = t('Add Config Entry');
		$api3['class'] = 'config';
		$api3['method'] = 'add';
		$api3['via'][] = 'post';

		Loader::model('api_register', 'api');
		ApiRegister::add($api);
		ApiRegister::add($api2);
		ApiRegister::add($api3);

		parent::install(); //install the addon - meh
	}
	
	public function uninstall() {
		Loader::model('api_register', 'api');
		ApiRegister::removeByPackage($this->pkgHandle);//remove all the apis
		parent::uninstall();
	}

}