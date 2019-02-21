<?php
namespace Cubo;

defined('__CUBO__') || new \Exception("No use starting a class without an include");

// Custom xml_encode function
if(!function_exists('xml_encode')) {
	function toXml($object,$xml = null,$class = null) {
		$returnType = $xml;
		if(!is_object($xml)) {
			$type = (is_object($object) ? basename(str_replace('\\','/',get_class($object))) : $xml.'-list');
			$xml = new \SimpleXMLElement("<{$type}/>");
		}
		foreach((array)$object as $key=>$value) {
			if(is_array($value) || is_object($value)) {
				$type = (is_object($value) ? basename(str_replace('\\','/',get_class($value))) : $key);
				toXml($value,$xml->addChild($type),$key);
			} else {
				$xml->addChild($key,$value);
			}
		}
		return $xml;
	}
	function xml_encode($object,$class) {
		$simpleXml = toXml($object,$class);
		$dom = new \DOMDocument('1.0','utf-8');
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		$dom->loadXML($simpleXml->asXML());
		return $dom->saveXML();
	}
}

class View {
	protected static $hideColumns = [];
	protected static $class;
	
	// Constructor saves router and class
	public function __construct() {
		self::$class = ucfirst(Application::getRouter()->getController());
	}
	
	// Method all
	public function all(&$_Data) {
		// Send to formatter
		$format = strtolower(Application::getRouter()->getFormat());
		return self::$format($_Data);
	}
	
	// Default method: all
	public function default(&$_Data) {
		return $this->all($_Data);
	}
	
	// Method get
	public function get(&$_Data) {
		// Send to formatter
		$format = strtolower(Application::getRouter()->getFormat());
		return self::$format($_Data);
	}
	
	// Redirect format html to json
	protected static function html(&$data) {
		return self::json($data);
	}
	
	// Format json
	protected static function json(&$data) {
		self::parseData($data);
		header("Content-Type: application/json");
		return json_encode($data,(isset($_GET['pretty']) ? JSON_PRETTY_PRINT : null));
	}
	
	// Convert json strings to object or array
	protected static function parseData(&$data) {
		if(is_array($data)) {
			foreach($data as &$item)
				self::parseData($item);
		} elseif(is_object($data)) {
			$view = __CUBO__.'\\'.self::$class.'view';
			foreach($data as $property=>$value) {
				if(in_array($property,$view::$hideColumns)) {
					unset($data->$property);
				} elseif(substr($property,0,1) == '@')
					$data->$property = json_decode($data->$property);
			}
		}
	}
	
	// Format xml
	protected static function xml(&$data) {
		self::parseData($data);
		header("Content-Type: application/xml");
		return xml_encode($data,self::$class);
	}
}
?>