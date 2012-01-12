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
		$api1['route'] = 'config';
		$api1['routeName'] = t('View Config Entrys');
		$api1['class'] = 'config';
		$api1['method'] = 'listConf';
		$api1['via'][] = 'get';
		
		$api2 = array();
		$api2['pkgHandle'] = $this->pkgHandle;
		$api2['route'] = 'config/:key';
		$api2['routeName'] = t('Get a Site Config Entry');
		$api2['class'] = 'config';
		$api2['method'] = 'getConf';
		$api2['via'][] = 'get';
		
		$api4 = array();
		$api4['pkgHandle'] = $this->pkgHandle;
		$api4['route'] = 'config/new';
		$api4['routeName'] = t('Add Config Entry');
		$api4['class'] = 'config';
		$api4['method'] = 'add';
		$api4['via'][] = 'post';

		$api5 = array();
		$api5['pkgHandle'] = $this->pkgHandle;
		$api5['route'] = 'config/destroy';
		$api5['routeName'] = t('Delete a Config Entry');
		$api5['class'] = 'config';
		$api5['method'] = 'destroy';
		$api5['via'][] = 'post';

		$api6 = array();
		$api6['pkgHandle'] = $this->pkgHandle;
		$api6['route'] = 'config/update';
		$api6['routeName'] = t('Update Config Entries');
		$api6['class'] = 'config';
		$api6['method'] = 'udpate';
		$api6['via'][] = 'post';

		Loader::model('api_register', 'api');
		ApiRegister::add($api1);
		ApiRegister::add($api4);
		ApiRegister::add($api5);
		ApiRegister::add($api6);
		ApiRegister::add($api2);

		parent::install(); //install the addon - meh
	}
	
	public function uninstall() {
		Loader::model('api_register', 'api');
		ApiRegister::removeByPackage($this->pkgHandle);//remove all the apis
		parent::uninstall();
	}

}