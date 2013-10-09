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


class ProcessHandler{

function isActive() {
        $pid = ProcessHandler::getPID();

        if ($pid == null) {
            $ret = false;
        } else {
            $ret = posix_kill ( $pid, 0 );
        }

        if ($ret == false) {
            ProcessHandler::activate();
        }

        return $ret;
}

function activate() {
        $pid = ProcessHandler::getPID();

        if ($pid != null && $pid == getmypid()) {
            return "Already running!\n";
        } else {
            $fp = fopen(PID,"w+");

            if ($fp) {
//                if (!fwrite($fp,"<"."?php\n\$pid = ".getmypid().";\n?".">"))
                if (!fwrite($fp,getmypid()."\n")) {
                    die("Can not create pid file!\n");
                }

                fclose($fp);
            } else {
                die("Can not create pid file!\n");
            }
        }
}

function getPID() {
        if (file_exists(PID)) {
	    $fp = fopen(PID, "r");
	    $pid = fgets($fp, 4096);
	    fclose($fp);
            return $pid;
        } else {
            return null;
        }
}

}
?>
