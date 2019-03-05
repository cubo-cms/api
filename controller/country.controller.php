<?php
/**
 * @application    Cubo CMS API
 * @type           Controller
 * @class          CountryController
 * @version        2.0.4
 * @date           2019-03-05
 * @author         Dan Barto
 * @copyright      Copyright (c) 2019 Cubo CMS; see COPYRIGHT.md
 * @license        MIT License; see LICENSE.md
 */
namespace Cubo;

class CountryController extends Controller {
	protected $columns = ['name','title','nativename','alpha2','alpha3','accesslevel','status'];
	protected $expandColumns = [];
}
?>