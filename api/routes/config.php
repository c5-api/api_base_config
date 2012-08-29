<?php defined('C5_EXECUTE') or die('Access Denied');

class ConfigApiRouteController extends ApiRouteController {

	public function run($key = false) {
		switch (API_REQUEST_METHOD) {
			case 'DELETE':
				break;
			case 'PATCH':
				break;
			case 'POST':
				if($key) {
					$this->setCode(400);
					$this->respond();
				}
				return $this->add();
				
			case 'GET':
				if(!$key) {
					return $this->listConf();
				}
				return $this->getConf($key);
			
			default: //BAD REQUEST
				$this->setCode(400);
				$this->respond();
		}
	}

	private function listConf() {
		$db = Loader::db();
		$r = $db->Execute('SELECT cfKey FROM Config WHERE pkgID = 0 and uID = 0');
		$conf = array();
		while($row = $r->FetchRow()) {
			$conf[] = $row['cfKey'];
		}
		return $conf;
	}

	private function getConf($id) {
		$cfg = new Config();
		$conf = $cfg->get($id, true);
		if(is_object($conf)) {
			unset($conf->error);
			return $conf;
		}
		$this->setCode(404);
		$this->respond();
	}

	private function add() {
		$vars = $_POST;
		//print_r($vars);
		$key = $vars['key'];
		$value = $vars['value'];
		if(!$key || !$value) { //bad request
			$this->setCode(400);
			$this->respond();
		}
		
		$conf = new Config();
		$obj = $conf->get($key, true);

		if(is_object($obj)) { //it already exists, error time!
			$this->setCode(409);
			$this->respond();
		}
		$conf->save($key, $value);
		$this->setCode(201);
		return $this->getConf($key);
		
	}
}