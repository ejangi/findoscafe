<?php
/**
 * A request segment
 *
 * @package BlockHead
 **/
class Segment
{
	
	
	
	/**
	 * Storage for all segments in use
	 *
	 * @var array $_segments
	 */
	protected static $_segments = array();
	public $position = 0;
	public $value = '';
	
	
	
	/**
	 * Construct
	 *
	 * @return void
	 **/
	public function __construct($position = null, $value = null)
	{
		$this->position = $position;
		$this->value = $value;

		if($value != null)
		{
			self::$_segments[$position] = $this;
		}
	}
	
	
	
	/**
	 * Get the position of segment
	 *
	 * @return int
	 **/
	public function position()
	{
		return (int) $this->position;
	}
	
	
	
	/**
	 * Get the VALUE of this segment
	 *
	 * @return string
	 */
	public function __toString()
	{
		return (string) $this->value;
	}
	
	
	
	/**
	 * Get the value of this segment
	 *
	 * @return string
	 **/
	public function value()
	{
		return $this->__toString();
	}
	
	
	
	/**
	 * Return the segment stored at position $position
	 *
	 * @param int $position The index of the segment to return
	 * @return object
	 **/
	public function at_position($position)
	{
		if(array_key_exists($position, self::$_segments))
		{
			return self::$_segments[$position];
		}
		
		foreach(self::$_segments as $segment)
		{
			if($segment->position() == $position)
			{
				return $segment;
			}
		}

		// throw new SegmentDoesNotExist;
		return new Segment;
	}
	
	
	
} // END class Segment






/**
 * A class to deal with HTTP Requests
 *
 * @package Request
 * @subpackage BlockHead
 * @author James Angus - ejangi.com
 **/
class Request
{
	
	
	
	/**
	 * The permalink being requested
	 *
	 * @var string
	 **/
	protected $_uri_string = '';
	
	/**
	 * The uri string being requested
	 *
	 * @var string
	 **/
	protected $_uri_ext = '';
	
	
	
	/**
	 * Segments storage property
	 *
	 * @var object $segments - Contains instance of Segment class
	 **/
	public $segments = NULL;
	
	
	
	/**
	 * Constructor
	 *
	 * @return void
	 **/
	public function __construct()
	{
		$uri = str_replace('/'.basename($_SERVER['SCRIPT_FILENAME']), '', $_SERVER['REQUEST_URI']);
		$uri = preg_replace('/\?.*/i', '', $uri);
		$this->_uri_string = preg_replace('|\.[a-z0-9]*$|i', '', $uri);
		preg_match('|\.[a-z0-9]*$|i', $uri, $ext_matches);
		$this->_uri_ext = ltrim(end($ext_matches), '.');
		
		$segmentation = trim($this->_uri_string, '/');
		$segments = explode('/', $segmentation);

		foreach($segments as $position => $value)
		{
			new Segment((int)$position, $value);
		}
	
		$this->segments = Segment::at_position(0);
	} // __construct()
	
	
	
	/**
	 * Return the value of _uri_string
	 *
	 * @return string
	 **/
	public function uri_string()
	{
		return $this->_uri_string;
	} // uri_string()
	
	
	
	/**
	 * Return the value of _uri_string
	 *
	 * @return string
	 **/
	public function uri_ext()
	{
		return $this->_uri_ext;
	} // uri_ext()
	
	
	
	/**
	 * The type of request: POST, GET, PUT, etc...
	 *
	 * @return string
	 **/
	public function type()
	{
		return strtolower($_SERVER['REQUEST_METHOD']);
	} // type()
	
	
	
	/**
	 * Is the current request a POST request?
	 *
	 * @return bool
	 **/
	public function is_post()
	{
		if($this->type() == 'post')
		{
			return TRUE;
		}
		
		return FALSE;
	} // is_post()
	
	
	
	/**
	 * Is this site using SSL?
	 *
	 * @return bool
	 **/
	public function is_secure()
	{
		$ssl = FALSE;
		
		if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] && strtolower($_SERVER['HTTPS']) != 'off') {
			$ssl = TRUE;
		}
		
		return $ssl;
	} // is_secure()
	
	
	
	/**
	 * Return the BASE url of the website
	 *
	 * @return string
	 **/
	public function base($force_secure = false)
	{
		$protocol = (self::is_secure() || $force_secure) ? 'https:' : 'http:';
		$return = $protocol.'//'.self::host().'/';
		return $return;
	} // base()
	
	
	
	/**
	 * Return the complete URL for the current page
	 *
	 * @return string
	 **/
	public function url_string()
	{
		return self::base().ltrim(self::uri_string(), '/');
	}
	
	
	
	/**
	 * Return the HTTP HOST value
	 *
	 * @return string
	 **/
	public function host()
	{
		return @$_SERVER['HTTP_HOST'];
	}
	
	
	
	/**
	 * Return an associative array of HTTP vars sent via POST
	 *
	 * @return array
	 **/
	public function get_post_vars()
	{
		if(!isset($_POST))
		{
			return array();
		}
		
		return $_POST;
	}
	
	
	
	/**
	 * Return an associative array of HTTP vars sent via POST that include the given prefix name
	 *
	 * @return array
	 **/
	public function get_post_vars_with_prefix($prefix)
	{
		$return = array();
		$vars = $this->get_post_vars();
		
		foreach($vars as $name => $value)
		{
			if(preg_match('/^'.preg_quote($prefix, '/').'.*/', $name))
			{
				$name = str_replace($prefix, '', $name);
				$return[$name] = $value;
			}
		}
		
		return $return;
	}
	
	
	
	/**
	 * Generate a secure post string
	 *
	 * @return string
	 **/
	public function secure_post_string()
	{
		$salt = 'a MAgicAL S4l7 fR0m A magical lAND';
		$ip = @$_SERVER['REMOTE_ADDR'];
		$user_agent = @$_SERVER['HTTP_HOST'];
		$base = $this->base();
		
		return sha1($salt.$base.$ip.$user_agent);
	}
	
	
	
	/**
	 * Redirect the user to the given URL
	 *
	 * @return void
	 **/
	public function redirect($url)
	{
		// Make sure the session is saved before we move on:
		if(isset($GLOBALS['session']))
		{
			unset($GLOBALS['session']);
		}
		
		if(substr($url, 0, 1) == '/')
		{
			$url = self::base().ltrim($url, '/');
		}
		
		header('location: '.$url);
		exit;
	}
	
	
	
	/**
	 * Return the real IP address for the user
	 *
	 * @return string
	 **/
	public function user_ip_address()
	{
		$ip = NULL;
		
		if(!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
		{
		  $ip = $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
		{
		  $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else
		{
		  $ip = $_SERVER['REMOTE_ADDR'];
		}
		
		return $ip;
	}
	
	
	
} // END class Request