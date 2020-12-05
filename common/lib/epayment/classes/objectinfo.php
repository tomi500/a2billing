<?php


/*
  $Id: object_info.php,v 1.6 2003/06/20 16:23:08 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

class objectInfo {

	// class constructor
	public function __construct($object_array) {
		foreach($object_array as $key => $value) {
			$this-> $key = tep_db_prepare_input($value);
		}
	}
}
?>
