<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * This file is part of A2Billing (http://www.a2billing.net/)
 *
 * A2Billing, Commercial Open Source Telecom Billing platform,   
 * powered by Star2billing S.L. <http://www.star2billing.com/>
 * 
 * @copyright   Copyright (C) 2004-2009 - Star2billing S.L. 
 * @author      Belaid Arezqui <areski@gmail.com>
 * @license     http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @package     A2Billing
 *
 * Software License Agreement (GNU Affero General Public License)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 * 
**/


$disable_load_conf = true;

include ("lib/customer.defines.php");

getpost_ifset(array ('error', 'password', 'username', 'pr_email', 'action'));

function sendForgot($error,$forgotString) {
	header("Content-type: text/xml");
	echo "<response><error>$error</error><forgotString><![CDATA[$forgotString]]></forgotString></response>";
	die();
}

if (isset ($pr_email) && isset ($action)) {
	if ($action == "email") {
		if (!isset ($_SESSION["date_forgot"]) || (time() - $_SESSION["date_forgot"]) > 10) {
			$_SESSION["date_forgot"] = time();
		} else {
			sendForgot(9,gettext("Please wait 1 minute before making any other request for the forgot password!"));
		}
		$pr_email = filter_var($pr_email, FILTER_VALIDATE_EMAIL);
		if ($pr_email===false) {
			sendForgot(7,gettext("Please provide your valid email address<br>to get your login information"));
		}
		$DBHandle = DbConnect();
		$QUERY = "SELECT id,username, lastname, firstname, email, uipass, useralias FROM cc_card WHERE email='" . $pr_email . "' ";
		$res = $DBHandle->Execute($QUERY);
		$num = 0;
		if ($res)
			$num = $res->RecordCount();
		if (!$num) {
			sendForgot(6,gettext("No such login exists"));
		}
		for ($i = 0; $i < $num; $i++) {
			$list[] = $res->fetchRow();
		}
		foreach ($list as $recordset) {
			list ($id_card, $username, $lastname, $firstname, $email, $uipass, $cardalias) = $recordset;
			try {
				$mail = new Mail(Mail :: $TYPE_FORGETPASSWORD, $id_card);
				$mail -> send();
			} catch (A2bMailException $e) {
				sendForgot(7,gettext("Mail sender error.<br>Try again please."));
			}
		}
		sendForgot(5,gettext("Your login information email<br>has been sent to you."));
	} else {
		sendForgot(7,gettext("Invalid Action"));
	}
}

include ("lib/customer.module.access.php");
include ("lib/customer.smarty.php");

if (has_rights(ACX_ACCESS)) {
    Header("Location: userinfo");
    exit();
}

$zippostcode = '';
//      -= Need to install GeoIP http://ua2.php.net/manual/en/geoip.setup.php =-
if (function_exists('geoip_db_avail') && (geoip_db_avail(GEOIP_REGION_EDITION_REV0) || geoip_db_avail(GEOIP_REGION_EDITION_REV1))) {
        $countryregion = geoip_record_by_name($_SERVER['REMOTE_ADDR']);
        $zippostcode = "value='".$countryregion['postal_code']."'"; // ZIP/POSTAL CODE
} else {
        $countrycode = $region = "";
	$countryregion = array();
}

$country_city_list = array (array('Jeru'  ,'Israel' ),
                            array('Berlin','Germany')
                            );
$town = "";
foreach ($country_city_list as $cur_value) {
        if ($cur_value[1]==$countryregion['country_name'])
            $town = $cur_value[0];
}

$countrycode = $countryregion['country_code3'];
if ($countrycode=="") {
    $countrycode = 'USA';
}

$curzonename = "";
$timezone_list = get_timezones();
                $one_select = false;
                if (function_exists('geoip_time_zone_by_country_and_region')) {
                        if ($countryregion===false) {
                                $country = $region = "";
                        } else {
                                $country = $countryregion['country_code'];
                                $region = $countryregion['region'];
                        }
                        if ($region == "") {
                                if ($country == "") $country = geoip_country_code_by_name($_SERVER['REMOTE_ADDR']);
                                $region = '01';
                        }
                        if ($country == "") {
                                $country = 'US';
                                $region = 'CA';
                        }
                        try {
                                $UserDateTimeZone       = new \DateTimeZone(geoip_time_zone_by_country_and_region($country,$region));
                        } catch (\Exception $e) {
                                $UserDateTimeZone       = new \DateTimeZone('UTC');
                        }
                        $zonename               = $UserDateTimeZone->getName();
//                      $UserDateTime           = new DateTime(null, $UserDateTimeZone);
//                      $servergmt              = $UserDateTimeZone->getOffset($UserDateTime);
                        $UserDateTime           = new DateTime('2017-12-14', $UserDateTimeZone);
                        $servergmt              = $UserDateTime->getOffset();
                } else $servergmt = SERVER_GMT;

                foreach ($timezone_list as $key => $cur_value) {
                        $timezone_list[$key] = array (
                                $cur_value[2],
                                $key
                        );
                                if (in_array($servergmt, $cur_value) && !$one_select) {
                                        $cur_id_timezone = $key.";".$zonename;
                                        if ($town=="" || strpos($cur_value[2], $town) !== false) {
                                                $timezone_list[$key][1] = $cur_id_timezone;
                                                if ($zonename != "") $curzonename = $timezone_list[$key][0] = substr_replace($cur_value[2],") ".$zonename,strpos($cur_value[2],')'));
                                                if (!isset($id_timezone) || $key == $id_timezone)
                                                        $id_timezone = $cur_id_timezone;
                                                $one_select = true;
                                        }
                                }
                }

$smarty -> assign("curzonename", $curzonename);
$smarty -> assign("curzonecode", $cur_id_timezone);
$smarty -> assign("error", $error);

$smarty -> assign("username", $username);
$smarty -> assign("password", $password);

$smarty -> display('index.tpl');
