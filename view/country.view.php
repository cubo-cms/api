<?php
namespace Cubo;

defined('__CUBO__') || new \Exception("No use starting a class without an include");

class CountryView extends View {
	protected static $hideColumns = ['accesslevel','status'];
}
?>