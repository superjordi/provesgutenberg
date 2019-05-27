<?php
/*
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

class MEXP_RP_Service {

	// TODO read from settings
	private static $PJ_RESOURCE_SPACE_KEY = "092cfff9711867de10ea0041114f6e89fb44e1b0d3692dc1b097ba1694b938f7";
	private static $PJ_RESOURCE_SPACE_DOMAIN = "http://mediateca.fundesplai.org";

	private static $TEST_JSON = "[{\"score\":\"0\",\"ref\":\"2\",\"resource_type\":\"1\",\"has_image\":\"0\",\"is_transcoding\":\"0\",\"creation_date\":\"2017-12-19 11:27:39\",\"rating\":\"\",\"user_rating\":\"\",\"user_rating_count\":\"\",\"user_rating_total\":\"\",\"file_extension\":\"jpg\",\"preview_extension\":\"jpg\",\"image_red\":\"\",\"image_green\":\"\",\"image_blue\":\"\",\"thumb_width\":\"\",\"thumb_height\":\"\",\"archive\":\"0\",\"access\":\"0\",\"colour_key\":\"\",\"created_by\":\"1\",\"file_modified\":\"2017-12-19 11:27:39\",\"file_checksum\":\"\",\"request_count\":\"0\",\"new_hit_count\":\"1\",\"expiry_notification_sent\":\"0\",\"preview_tweaks\":\"0|1\",\"file_path\":\"\",\"group_access\":\"\",\"user_access\":\"\",\"field12\":\"2016-02-28 11:55:56\",\"field8\":\"Test!tot\",\"field3\":\"\",\"total_hit_count\":\"1\"},{\"score\":\"0\",\"ref\":\"1\",\"resource_type\":\"1\",\"has_image\":\"0\",\"is_transcoding\":\"0\",\"creation_date\":\"2017-12-19 11:21:55\",\"rating\":\"\",\"user_rating\":\"\",\"user_rating_count\":\"\",\"user_rating_total\":\"\",\"file_extension\":\"jpg\",\"preview_extension\":\"jpg\",\"image_red\":\"\",\"image_green\":\"\",\"image_blue\":\"\",\"thumb_width\":\"\",\"thumb_height\":\"\",\"archive\":\"0\",\"access\":\"0\",\"colour_key\":\"\",\"created_by\":\"1\",\"file_modified\":\"2017-12-19 11:21:55\",\"file_checksum\":\"\",\"request_count\":\"0\",\"new_hit_count\":\"1\",\"expiry_notification_sent\":\"0\",\"preview_tweaks\":\"0|1\",\"file_path\":\"\",\"group_access\":\"\",\"user_access\":\"\",\"field12\":\"2017-12-19 10:21\",\"field8\":\"Test!\",\"field3\":\"\",\"total_hit_count\":\"1\"},{\"score\":\"0\",\"ref\":\"3\",\"resource_type\":\"1\",\"has_image\":\"1\",\"is_transcoding\":\"0\",\"creation_date\":\"2017-12-19 11:36:27\",\"rating\":\"\",\"user_rating\":\"\",\"user_rating_count\":\"\",\"user_rating_total\":\"\",\"file_extension\":\"jpg\",\"preview_extension\":\"jpg\",\"image_red\":\"636\",\"image_green\":\"737\",\"image_blue\":\"187\",\"thumb_width\":\"150\",\"thumb_height\":\"66\",\"archive\":\"0\",\"access\":\"0\",\"colour_key\":\"EKWNG\",\"created_by\":\"1\",\"file_modified\":\"2017-12-19 12:53:56\",\"file_checksum\":\"\",\"request_count\":\"0\",\"new_hit_count\":\"2\",\"expiry_notification_sent\":\"0\",\"preview_tweaks\":\"0|1\",\"file_path\":\"\",\"group_access\":\"\",\"user_access\":\"\",\"field12\":\"2002-06-13 12:17:18\",\"field8\":\"Test!tot tyu\",\"field3\":\"\",\"total_hit_count\":\"2\",\"url_pre\":\"http:\/\/mediateca.fundesplai.org\/filestore\/3_d1debe2892e0c22\/3pre_69674cd7eac8311.jpg?v=2017-12-19+12%3A53%3A56\"}]";
	private static $TEST_JSON2 = "[{\"score\":\"2\",\"ref\":\"3\",\"resource_type\":\"1\",\"has_image\":\"1\",\"is_transcoding\":\"0\",\"creation_date\":\"2017-12-19 11:36:27\",\"rating\":\"\",\"user_rating\":\"\",\"user_rating_count\":\"\",\"user_rating_total\":\"\",\"file_extension\":\"jpg\",\"preview_extension\":\"jpg\",\"image_red\":\"636\",\"image_green\":\"737\",\"image_blue\":\"187\",\"thumb_width\":\"150\",\"thumb_height\":\"66\",\"archive\":\"0\",\"access\":\"0\",\"colour_key\":\"EKWNG\",\"created_by\":\"1\",\"file_modified\":\"2017-12-19 12:53:56\",\"file_checksum\":\"\",\"request_count\":\"0\",\"new_hit_count\":\"2\",\"expiry_notification_sent\":\"0\",\"preview_tweaks\":\"0|1\",\"file_path\":\"\",\"group_access\":\"\",\"user_access\":\"\",\"field12\":\"2002-06-13 12:17:18\",\"field8\":\"Test!tot tyu\",\"field3\":\"\",\"total_hit_count\":\"2\",\"url_pre\":\"http:\/\/www.escolesproves.fundesplai.org\/wp-content\/uploads\/2018\/01\/3pre_69674cd7eac8311.jpg?v=2017-12-19+12%3A53%3A56\"}]";

	public function __construct() {

	}

	


}

