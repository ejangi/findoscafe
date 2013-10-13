<?php
class App {

	public $request = null;
	public $mimes = array(
			'html' => 'text/html',
			'json' => 'application/json',
			'js' => 'text/javascript'
		);

	public function __construct() {
		$this->request = new Request();
		Controller::set_app($this);
	}

	public function run() {
		$action = (string) $this->request->segments->at_position(0);
		if(method_exists('Controller', $action)) {
			Controller::$action();
		} elseif($action == '' && method_exists('Controller', 'index')) {
			Controller::index();
		} else {
			Controller::error();
		}
	}

	public function path() {
		static $path = null;
		if($path == null){
			$path = dirname(realpath(__FILE__));
		}
		return $path;
	}

	public function ext_to_mime_type($ext) {
		if(isset($this->mimes[$ext])) {
			return $this->mimes[$ext];
		} else {
			return 'text/plain';
		}
	}

}