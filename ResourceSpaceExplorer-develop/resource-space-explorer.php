<?php
ini_set('display_errors', 'on');
/*
Plugin Name: ResourceSpace Explorer
Description: Extends the Media Manager to add support for ResourceSpace Digital Assets Management system
Version:     1.0
Author:      Jonathan Pasquier
Text Domain: rsexplorer
Domain Path: /languages/
License:     GPL v2 or later

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

*/

defined( 'ABSPATH' ) or die();

require_once dirname( __FILE__ ) . '/class.rse.php';
require_once dirname( __FILE__ ) . '/class.template.php';
require_once dirname( __FILE__ ) . '/class.response.php';
require_once dirname( __FILE__ ) . '/class.settings.php';

ResourceSpaceExplorer::init( __FILE__ );
ResourceSpaceExplorer_Settings::init();
