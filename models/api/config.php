<?php defined('C5_EXECUTE') or die("Access Denied.");

class ApiConfig extends ApiController {

	/**
	 * Get a config entry
	 * @route /config/:key
	 * @method GET
	 * @errors ERROR_NOT_FOUND
	 */		
	public function getConf($id) {
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

	/**
	 * List Config Entires
	 * @route /config
	 * @method GET
	 */		
	public function listConf() {
		$resp = ApiResponse::getInstance();
		$db = Loader::db();
		$r = $db->Execute('SELECT cfKey FROM Config WHERE pkgID = 0 and uID = 0');
		$conf = array();
		while($row = $r->FetchRow()) {
			$conf[] = $row['cfKey'];
		}
		$resp->setData($conf);
		$resp->send();
	}

	/**
	 * Add Config Entries
	 * @route /config/add
	 * @method POST
	 * @errors ERROR_BAD_REQUEST | ERROR_ALREADY_EXISTS
	 */		
	public function add() {
		$vars = $_POST;
		$resp = ApiResponse::getInstance();
		//print_r($vars);
		$key = $vars['key'];
		$value = $vars['value'];
		if(!$key || !$value) { //bad request
			$resp->setError(true);
			$resp->setMessage('ERROR_BAD_REQUEST');
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
	
	/**
	 * Delete Config Entries
	 * @route /config/destroy
	 * @method POST
	 * @errors ERROR_NOT_FOUND
	 */	
	public function destroy() {
		$vars = $_POST;
		$id = $vars['key'];
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

	/**
	 * Update Config Entries
	 * @route /config/update
	 * @method POST
	 * @errors ERROR_NOT_FOUND
	 */		
	public function update() {
		$vars = $_POST;
		$id = $vars['key'];
		$value = $vars['value'];
		$cfg = new Config();
		$conf = $cfg->get($id, true);
		$resp = ApiResponse::getInstance();
		if(is_object($conf)) {
			$cfg->save($key, $value);
			$resp->send();
		} else {
			$resp->setError(true);
			$resp->setCode(404);
			$resp->setMessage('ERROR_NOT_FOUND');
			$resp->send();
		}
	}
}