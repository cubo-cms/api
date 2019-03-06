<?php
/**
 * @application    Cubo CMS API
 * @type           Controller
 * @class          CurrencyController
 * @version        2.0.4
 * @date           2019-03-05
 * @author         Dan Barto
 * @copyright      Copyright (c) 2019 Cubo CMS; see COPYRIGHT.md
 * @license        MIT License; see LICENSE.md
 */
namespace Cubo;

class CurrencyController extends Controller {
	protected $columns = ['name','accesslevel','alpha3','status','symbol','title'];
	protected $expandColumns = [];
}
?>