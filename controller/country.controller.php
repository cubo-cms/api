<?php
namespace Cubo;

defined('__CUBO__') || new \Exception("No use starting a class without an include");

class CountryController extends Controller {
	protected $columns = ['name','title','nativename','alpha2','alpha3','accesslevel','status'];
}
?>