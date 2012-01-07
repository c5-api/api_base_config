<?php defined('C5_EXECUTE') or die("Access Denied.");

class ApiConfig extends ApiController {
	
	public function getConf($id) {
		if(API_REQUEST_METHOD == 'GET') {
			$self = new self;
			return $self->get($id);
		} else if(API_REQUEST_METHOD == 'DELETE') {
			$self = new self;
			return $self->delete($id);
		}
		throw new Exception('ERROR_INVALID_ROUTE', 501);
	}
	
	public function listConf() {
		$resp = ApiResponse::getInstance();
		$self = new self;
		$conf = $self->getEntries();
		$resp->setData($conf);
		$resp->send();
	}

	private function getEntries() {
		$db = Loader::db();
		$r = $db->Execute('SELECT * FROM Config');
		$objs = array();
		while($row = $r->FetchRow()) {
			$objs[] = $row;
		}
		return $objs;
	}

	private function get($id) {
		$cfg = new Config();
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
		$key = $vars['key'];
		$value = $vars['value'];
		if(!$key || !$value) { //bad request
			$resp->setError(true);
			$resp->setMessage('ERROR_INVALID_REQUEST');
			$resp->setCode(400);
			$resp->send();
		}
		
		$conf = new Config();
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
	
	private function delete($id) {
		$cfg = new Config();
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