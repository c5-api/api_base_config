<?php defined('C5_EXECUTE') or die("Access Denied.");

class ApiConfig extends ApiController {
	
	public function getConf($pkg, $id = null) {
		if(API_REQUEST_METHOD == 'GET') {
			$self = new self;
			return $self->get($pkg, $id);
		} else if(API_REQUEST_METHOD == 'DELETE') {
			$self = new self;
			return $self->delete($pkg, $id);
		}
		throw new Exception('ERROR_INVALID_ROUTE', 501);
	}
	
	private function get($pkg, $id = null) {
		if(!$id) {
			$id = $pkg;
			unset($pkg);
		}
		//echo $id;
		//$id = $_POST['key'];
		$cfg = new Config();
		if($pkg) {
			$cfg->setPackageObject(Package::getByHandle($pkg));
		}
		$conf = $cfg->get($id, true);
		$resp = ApiResponse::getInstance();
		if(is_object($conf)) {
			unset($conf->error);
			$resp->setData($conf);
			$resp->send();
		} else {
			$resp->setError(true);
			$resp->setCode(404);
			$resp->setMessage('ERROR_NOT_FOUND');
			$resp->send();
		}
	}
	
	public function add() {
		$vars = $_POST;
		$resp = ApiResponse::getInstance();
		//print_r($vars);
		$uID = $vars['uID'];
		$pkg = $vars['pkg'];//ID or handle
		if(is_numeric($pkg)) {
			$pkg = Package::getByID($pkg);
		} else if(is_string($pkg)) {
			$pkg = Package::getByHandle($pkg);
		} else {
			$pkg = null;
		}

		$key = $vars['key'];
		$value = $vars['value'];
		if(!$key || !$value) { //bad request
			$resp->setError(true);
			$resp->setMessage('ERROR_INVALID_REQUEST');
			$resp->setCode(400);
			$resp->send();
		}
		
		$conf = new Config();
		if(is_object($pkg)) {
			$conf->setPackageObject($pkg);
		}
		$get = $conf;//do I need to do this?
		$obj = $get->get($key, true);

		if(is_object($obj)) { //it already exists, error time!
			$resp->setError(true);
			$resp->setMessage('ERROR_ALREADY_EXISTS');
			$resp->setCode(409);
			$resp->send();
		}
		$conf->save($key, $value);
		
		$data = $conf->get($key, true);
		unset($data->error);
		$resp->setData($data);
		$resp->send();
		
	}
	
	private function delete($pkg, $id = null) {
		if(!$id) {
			$id = $pkg;
			unset($pkg);
		}
		//echo $id;
		//$id = $_POST['key'];
		$cfg = new Config();
		if($pkg) {
			$cfg->setPackageObject(Package::getByHandle($pkg));
		}
		$conf = $cfg->get($id, true);
		$resp = ApiResponse::getInstance();
		if(is_object($conf)) {
			$cfg->clear($key);
			$resp->send();
		} else {
			$resp->setError(true);
			$resp->setCode(404);
			$resp->setMessage('ERROR_NOT_FOUND');
			$resp->send();
		}
	}
}