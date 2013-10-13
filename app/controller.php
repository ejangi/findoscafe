<?php
class Controller {

	public static function index() {
		$base = rtrim(self::$app->request->base(), '/');
		$addresses = Config::$addresses;
		$logo_url = Config::$logo_url;
		$title = Config::$title;
		$social = Config::$social;
		$yield = self::partial('splash', get_defined_vars(), 'html');
		self::view('index', get_defined_vars(), 'html');
	}

	public static function error() {
		self::view('error', get_defined_vars());
	}




	protected static $app = null;
	protected static $ext = 'html';
	private static $utils = false;

	public function set_app($app) {
		self::$app =& $app;
		self::$ext = (string) self::$app->request->uri_ext();
		if(self::$ext == '') self::$ext = 'html';
	}

	protected function partial($view, $variables = null, $force_type = null) {
		ob_start();
		self::view($view, $variables, $force_type, true);
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}

	protected function view($view, $variables = null, $force_type = null, $suppress_header = false) {
		$ext = self::$ext;
		if($force_type != null) {
			$ext = $force_type;
		}
		$view_path = self::$app->path().'/../views/'.$view.'.'.$ext.'.php';

		if(file_exists($view_path)) {
			if(!self::$utils) {
				self::$utils = true;
				include self::$app->path().'/utilities.php';
			}
			if($variables != null && count($variables) > 0) {
				foreach($variables as $key => $value) {
					$$key = $value;
				}
			}
			if(!$suppress_header) {
				$mime = self::$app->ext_to_mime_type($ext);
				header('Content-Type: '.$mime);
			}
			include $view_path;
		} else {
			header("HTTP/1.0 404 Not Found");
			echo "Template '{$view}' does not exist";
		}
	}

}