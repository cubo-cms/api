<?php
namespace Cubo;

defined('__CUBO__') || new \Exception("No use starting a class without an include");

class Controller {
	protected $_Model;
	protected $_Router;
	protected $_View;
	protected $columns = "*";
	
	// Constructor saves router
	public function __construct($_Router = null) {
		$this->_Router = $_Router ?? Application::getRouter();
	}
	
	// Default access levels
	protected $_Authors = [ROLE_AUTHOR,ROLE_EDITOR,ROLE_PUBLISHER,ROLE_MANAGER,ROLE_ADMINISTRATOR];
	protected $_Editors = [ROLE_EDITOR,ROLE_PUBLISHER,ROLE_MANAGER,ROLE_ADMINISTRATOR];
	protected $_Publishers = [ROLE_PUBLISHER,ROLE_MANAGER,ROLE_ADMINISTRATOR];
	protected $_Managers = [ROLE_MANAGER,ROLE_ADMINISTRATOR];
	protected $_Administrators = [ROLE_ADMINISTRATOR];
	
	// Returns true if the model includes an access property
	private function containsAccessProperty() {
		if(is_array($this->columns))
			return in_array('accesslevel',$this->columns);
		else
			return $this->columns == "*" || !(strpos($this->columns,'accesslevel') === false);
	}
	
	// Returns true if the model includes a status property
	private function containsStatusProperty() {
		if(is_array($this->columns))
			return in_array('status',$this->columns);
		else
			return $this->columns == "*" || !(strpos($this->columns,'status') === false);
	}
	
	// Returns router
	public function getRouter() {
		return $this->_Router;
	}
	
	// Returns filter for list permission
	public function requireListPermission() {
		$filter = [];
		if($this->containsAccessProperty())
			if(Session::isAuthor())
				$filter[] = '`accesslevel` IN ('.ACCESS_PUBLIC.','.ACCESS_REGISTERED.','.ACCESS_ADMIN.')';
			elseif(Session::isRegistered())
				$filter[] = '`accesslevel` IN ('.ACCESS_PUBLIC.','.ACCESS_REGISTERED.')';
			else
				$filter[] = '`accesslevel` IN ('.ACCESS_PUBLIC.','.ACCESS_GUEST.')';
		if($this->containsStatusProperty())
			$filter[] = "`status`=".STATUS_PUBLISHED;
		return implode(' AND ',$filter) ?? '1';
	}
	
	// Returns filter for view permission
	private function requireViewPermission() {
		$filter = [];
		if($this->containsAccessProperty())
			if(Session::isRegistered())
				$filter[] = '`accesslevel` IN ('.ACCESS_PUBLIC.','.ACCESS_REGISTERED.','.ACCESS_PRIVATE.')';
			else
				$filter[] = '`accesslevel` IN ('.ACCESS_PUBLIC.','.ACCESS_GUEST.','.ACCESS_PRIVATE.')';
		if($this->containsStatusProperty())
			$filter[] = "`status`=".STATUS_PUBLISHED;
		return implode(' AND ',$filter) ?? '1';
	}
	
	public function all() {
		$model = __CUBO__.'\\'.$this->getRouter()->getController();
		try {
			if(class_exists($model)) {
				$this->_Model = new $model;
				$_Data = $this->_Model::getAll($this->columns,$this->requireListPermission(),'name');
				if($_Data) {
					return $this->render($_Data);
				} else {
					// No items returned, return nothing
					return $this->render([]);
				}
			} else {
				$model = $this->getRouter()->getController();
				throw new Error(['class'=>__CLASS__,'method'=>__METHOD__,'severity'=>1,'response'=>405,'message'=>"Model '{$model}' does not exist"]);
			}
		} catch(Error $_Error) {
			$_Error->showMessage();
		}
		return false;
	}
	
	// Default method redirects to view
	public function default() {
		return $this->get();
	}
	
	// Call view with requested method
	protected function render($_Data) {
		$view = __CUBO__.'\\'.$this->getRouter()->getController().'view';
		$method = $this->getRouter()->getMethod();
		if(class_exists($view)) {
			if(method_exists($view,$method)) {
				// Send retrieved data to view and return output
				$this->_View = new $view;
				return $this->_View->$method($_Data);
			} else {
				// Method does not exist for this view
				$view = $this->getRouter()->getController();
				throw new Error(['class'=>__CLASS__,'method'=>__METHOD__,'line'=>__LINE__,'file'=>__FILE__,'severity'=>1,'response'=>405,'message'=>"View '{$view}' does not have the method '{$method}' defined"]);
			}
		} else {
			// View not found
			$view = $this->getRouter()->getController();
			throw new Error(['class'=>__CLASS__,'method'=>__METHOD__,'line'=>__LINE__,'file'=>__FILE__,'severity'=>1,'response'=>405,'message'=>"View '{$view}' does not exist"]);
		}
		return false;
	}
	
	public function get() {
		$model = __CUBO__.'\\'.$this->getRouter()->getController();
		try {
			if(class_exists($model)) {
				$this->_Model = new $model;
				$_Data = $this->_Model::get($this->getRouter()->getName(),$this->columns,$this->requireViewPermission());
				if($_Data) {
					// Pass data to view
					return $this->render($_Data);
				} else {
					// Could not retrieve item; pass empty model to view
					return $this->render($this->_Model);
				}
			} else {
				$model = $this->getRouter()->getController();
				throw new Error(['class'=>__CLASS__,'method'=>__METHOD__,'line'=>__LINE__,'file'=>__FILE__,'severity'=>1,'response'=>405,'message'=>"Model '{$model}' does not exist"]);
			}
		} catch(Error $_Error) {
			$_Error->showMessage();
		}
		return false;
	}
}
?>