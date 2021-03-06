<?php
/**
 * @application    Cubo CMS API
 * @type           View
 * @class          CountryView
 * @version        2.0.4
 * @date           2019-03-05
 * @author         Dan Barto
 * @copyright      Copyright (c) 2019 Cubo CMS; see COPYRIGHT.md
 * @license        MIT License; see LICENSE.md
 */
namespace Cubo;

class CountryView extends View {
	protected static $hideColumns = ['accesslevel','status'];
}
?>