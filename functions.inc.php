<?php
//This file is part of FreePBX.
//
//    FreePBX is free software: you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation, either version 2 of the License, or
//    (at your option) any later version.
//
//    FreePBX is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with FreePBX.  If not, see <http://www.gnu.org/licenses/>.
//
//    Copyright 2008 sasargen
//    Portions Copyright 2009, 2010, 2011 Mikael Carlsson, mickecamino@gmail.com
//

/* functions.inc.php - functions for BulkExtensions module. */
if (file_exists("modules/voicemail/functions.inc.php")) {
    include_once("modules/voicemail/functions.inc.php");	// for using Voicemail module functions to retrieve Voicemail settings
    };
if (file_exists("modules/dictate/functions.inc.php")) {
    include_once("modules/dictate/functions.inc.php");		// for using dictation services functions to retrieve dictation settings
    };
if (file_exists("modules/languages/functions.inc.php")) {
    include_once("modules/languages/functions.inc.php");	// for using languages functions to retrieve language setting
    };
if (file_exists("modules/findmefollow/functions.inc.php")) {
    include_once("modules/findmefollow/functions.inc.php");	// for using findmefollow functions to retreive follow me settings
    };
if (file_exists("modules/fax/functions.inc.php")) {
    include_once("modules/fax/functions.inc.php");             // for using fax functions to retreive fax settings
    };

/* Verify existence of Voicemail, dictate, languages and findmefollow functions. */
if (function_exists("voicemail_mailbox_get") && function_exists("voicemail_mailbox_add") && function_exists("voicemail_mailbox_del") && function_exists("voicemail_mailbox_remove") && class_exists("vmxObject")) {
	$vm_exists	= TRUE;
} else {
	$vm_exists	= FALSE;
}
if (function_exists("dictate_get") && function_exists("dictate_update") && function_exists("dictate_del")) {
	$dict_exists	= TRUE;
} else {
	$dict_exists	= FALSE;
}
if (function_exists("languages_user_get") && function_exists("languages_user_update") && function_exists("languages_user_del")) {
	$lang_exists	= TRUE;
} else {
	$lang_exists	= FALSE;
}
if (function_exists("findmefollow_get") && function_exists("findmefollow_add") && function_exists("findmefollow_del")) {
	$findme_exists	= TRUE;
} else {
	$findme_exists	= FALSE;
}
if (function_exists("fax_get_user") && function_exists("fax_save_user") && function_exists("fax_delete_user")) {
       $fax_exists     = TRUE;
} else {
       $fax_exists     = FALSE;
}

function exportextensions_allusers() {
	global $db;
	global $vm_exists;
	global $dict_exists;
	global $lang_exists;
	global $findme_exists;
	global $fax_exists;
	$action		= "edit";
	$fname		= "bulkext__" .  (string) time() . $_SERVER["SERVER_NAME"] . ".csv";
	$csv_header 	= "action,extension,name,cid_masquerade,sipname,outboundcid,ringtimer,callwaiting,call_screen,pinless,password,noanswer_dest,noanswer_cid,busy_dest,busy_cid,chanunavail_dest,chanunavail_cid,emergency_cid,tech,hardware,devinfo_channel,devinfo_secret,devinfo_notransfer,devinfo_dtmfmode,devinfo_canreinvite,devinfo_context,devinfo_immediate,devinfo_signalling,devinfo_echocancel,devinfo_echocancelwhenbrdiged,devinfo_echotraining,devinfo_busydetect,devinfo_busycount,devinfo_callprogress,devinfo_host,devinfo_type,devinfo_nat,devinfo_port,devinfo_qualify,devinfo_callgroup,devinfo_pickupgroup,devinfo_disallow,devinfo_allow,devinfo_dial,devinfo_accountcode,devinfo_mailbox,devinfo_deny,devinfo_permit,devicetype,deviceid,deviceuser,description,dictenabled,dictformat,dictemail,langcode,record_in,record_out,vm,vmpwd,email,pager,attach,saycid,envelope,delete,options,vmcontext,vmx_state,vmx_unavail_enabled,vmx_busy_enabled,vmx_play_instructions,vmx_option_0_sytem_default,vmx_option_0_number,vmx_option_1_system_default,vmx_option_1_number,vmx_option_2_number,account,ddial,pre_ring,strategy,grptime,grplist,annmsg_id,ringing,grppre,dring,needsconf,remotealert_id,toolate_id,postdest,faxenabled,faxemail\n";
	$data 		= $csv_header;
	$exts 		= get_all_exts();

	foreach ($exts as $ext) {
		$e 	= $ext[0];
		$u_info = core_users_get($e);
		$d_info = core_devices_get($e);
		if ($vm_exists) {
			$v_info	= voicemail_mailbox_get($e);
		} else {
			$v_info = NULL;
		}
		/* To properly obtain Voicemail information, detect enabled/disabled vm value.   */
		/* Parse extra Voicemail options.						 */
		if ($v_info == NULL) {
			$v_enabled	= "disabled";
		} else {
			$v_enabled 	= "enabled";
			$v_options 	= isset($v_info["options"])?$v_info["options"]:"";
			$vm_other_opts 	= "";
			$i 		= 0;
			$first 		= TRUE;
			$c 		= count($v_options);
			reset($v_options);
			while ($i < $c) {
				if ((key($v_options) != "attach") && (key($v_options) != "saycid") && (key($v_options) != "envelope") && (key($v_options) != "delete")) {
					if ($first) {
						$vm_other_opts	= key($v_options) . "=" . $v_options[key($v_options)];
						$first 		= false;
					} else {
						$vm_other_opts .=  "|" . key($v_options) . "=" . $v_options[key($v_options)];
					}
				}
				$i++;
				next($v_options);
			}
		}
		/* Obtain vmx settings. */
		if ($vm_exists) {
			$vmxobj		= new vmxObject($e);
		} else {
			$vmxobj		= NULL;
		}
		
		if (is_object($vmxobj)) {
			$vmx_state 		= ($vmxobj->isEnabled())?"checked":"";
			$vmx_unavail_enabled 	= ($vmxobj->getState("unavail")=="enabled")?"checked":"";
			$vmx_busy_enabled 	= ($vmxobj->getState("busy")=="enabled")?"checked":"";
			$vmx_play_instructions 	= ($vmxobj->getVmPlay())?"checked":"";
			$vmx_option_0_number 	= $vmxobj->getMenuOpt(0);
			if ($vmx_option_0_number == "") {
				$vmx_option_0_system_default = "checked";
			} else {
				$vmx_option_0_system_default = "";
			}
			if (is_object($vmxobj)) {
				if ($vmxobj->hasFollowMe() && $vmxobj->isFollowMe()) {
					$vmx_option_1_system_default 	= "checked";
					$vmx_option_1_number 		= "";
				} else {
					$vmx_option_1_system_default 	= "";
					$vmx_option_1_number 		= $vmxobj->getMenuOpt(1);
				}
				$vmx_option_2_number 			= $vmxobj->getMenuOpt(2);
			}
		}
			
		/* Obtain dictation services settings. */
		if ($dict_exists) {
			$dictate_settings = dictate_get($e);
		}

		/* Obtain language code. */
		if ($lang_exists) {
			$langcode = languages_user_get($e);
		}

		/* Obtain follow me settings. */
		if ($findme_exists) {
			$followme_settings = findmefollow_get($u_info["extension"], TRUE);
		}
		if (isset($followme_settings)) {
			$account	= isset($followme_settings["grpnum"])?$followme_settings["grpnum"]:"";
			$strategy	= isset($followme_settings["strategy"])?$followme_settings["strategy"]:"";
			$grptime	= isset($followme_settings["grptime"])?$followme_settings["grptime"]:"";
			$grppre		= isset($followme_settings["grppre"])?$followme_settings["grppre"]:"";
			$grplist	= isset($followme_settings["grplist"])?$followme_settings["grplist"]:"";
			$annmsg_id	= isset($followme_settings["annmsg_id"])?$followme_settings["annmsg_id"]:"";
			$postdest	= isset($followme_settings["postdest"])?$followme_settings["postdest"]:"";
			$dring 		= isset($followme_settings["dring"])?$followme_settings["dring"]:"";
			$needsconf 	= isset($followme_settings["needsconf"])?$followme_settings["needsconf"]:"";
			$remotealert_id = isset($followme_settings["remotealert_id"])?$followme_settings["remotealert_id"]:"";
			$toolate_id 	= isset($followme_settings["toolate_id"])?$followme_settings["toolate_id"]:"";
			$ringing 	= isset($followme_settings["ringing"])?$followme_settings["ringing"]:"";
			$pre_ring 	= isset($followme_settings["pre_ring"])?$followme_settings["pre_ring"]:"";
			$ddial 		= isset($followme_settings["ddial"])?$followme_settings["ddial"]:"";
		}

		/* Obtain fax settings */
		if ($fax_exists) {
		    $fax_settings = fax_get_user($e);
		    }
		if (isset($fax_settings)) {
		$faxenabled     = isset($fax_settings["faxenabled"])?$fax_settings["faxenabled"]:"";
		$faxemail       = isset($fax_settings["faxemail"])?$fax_settings["faxemail"]:"";
		}
		$csv_line[0] 	= $action;
		$csv_line[1] 	= isset($u_info["extension"])?$u_info["extension"]:"";
		$csv_line[2] 	= isset($u_info["name"])?$u_info["name"]:"";
		$csv_line[3] 	= isset($u_info["cid_masquerade"])?$u_info["cid_masquerade"]:"";
		$csv_line[4] 	= isset($u_info["sipname"])?$u_info["sipname"]:"";
		$csv_line[5] 	= isset($u_info["outboundcid"])?$u_info["outboundcid"]:"";
		$csv_line[6] 	= isset($u_info["ringtimer"])?$u_info["ringtimer"]:"";
		$csv_line[7]	= isset($u_info["callwaiting"])?$u_info["callwaiting"]:"";
		$csv_line[8]	= isset($u_info["call_screen"])?$u_info["call_screen"]:"0";
		$csv_line[9]	= isset($u_info["pinless"])?$u_info["pinless"]:"";
		$csv_line[10]	= isset($u_info["password"])?$u_info["password"]:"";
		$csv_line[11]   = isset($u_info["noanswer_dest"])?$u_info["noanswer_dest"]:"";
		$csv_line[12]   = isset($u_info["noanswer_cid"])?$u_info["noanswer_cid"]:"";
		$csv_line[13]   = isset($u_info["busy_dest"])?$u_info["busy_dest"]:"";
		$csv_line[14]   = isset($u_info["busy_cid"])?$u_info["busy_cid"]:"";
		$csv_line[15] = isset($u_info["chanunavail_dest"])?$u_info["chanunavail_dest"]:"";		
		$csv_line[16]  = isset($u_info["chanunavail_cid"])?$u_info["chanunavail_cid"]:"";		
		$csv_line[17]	= isset($d_info["emergency_cid"])?$d_info["emergency_cid"]:"";
		$csv_line[18]	= isset($d_info["tech"])?$d_info["tech"]:"";
		$csv_line[19]	= ""; 	// hardware
		$csv_line[20]	= isset($d_info["channel"])?$d_info["channel"]:"";
		$csv_line[21]	= isset($d_info["secret"])?$d_info["secret"]:"";
		$csv_line[22]	= isset($d_info["notransfer"])?$d_info["notransfer"]:"";
		$csv_line[23]	= isset($d_info["dtmfmode"])?$d_info["dtmfmode"]:"";
		$csv_line[24]	= isset($d_info["canreinvite"])?$d_info["canreinvite"]:"";
		$csv_line[25]	= isset($d_info["context"])?$d_info["context"]:"";
		$csv_line[26]	= isset($d_info["immediate"])?$d_info["immediate"]:"";
		$csv_line[27]	= isset($d_info["signalling"])?$d_info["signalling"]:"";
		$csv_line[28]	= isset($d_info["echocancel"])?$d_info["echocancel"]:"";
		$csv_line[29]	= isset($d_info["echocancelwhenbridged"])?$d_info["echocancelwhenbridged"]:"";
		$csv_line[30]	= isset($d_info["echotraining"])?$d_info["echotraining"]:"";
		$csv_line[31]	= isset($d_info["busydetect"])?$d_info["busydetect"]:"";
		$csv_line[32]	= isset($d_info["busycount"])?$d_info["busycount"]:"";
		$csv_line[33]	= isset($d_info["callprogress"])?$d_info["callprogress"]:"";
		$csv_line[34]	= isset($d_info["host"])?$d_info["host"]:"";
		$csv_line[35]	= isset($d_info["type"])?$d_info["type"]:"";
		$csv_line[36]	= isset($d_info["nat"])?$d_info["nat"]:"";
		$csv_line[37]	= isset($d_info["port"])?$d_info["port"]:"";
		$csv_line[38]	= isset($d_info["qualify"])?$d_info["qualify"]:"";
		$csv_line[39]	= isset($d_info["callgroup"])?$d_info["callgroup"]:"";
		$csv_line[40]	= isset($d_info["pickupgroup"])?$d_info["pickupgroup"]:"";
		$csv_line[41]	= isset($d_info["disallow"])?$d_info["disallow"]:"";
		$csv_line[42]	= isset($d_info["allow"])?$d_info["allow"]:"";
		$csv_line[43]	= isset($d_info["dial"])?$d_info["dial"]:"";
		$csv_line[44]	= isset($d_info["accountcode"])?$d_info["accountcode"]:"";
		$csv_line[45]	= isset($d_info["mailbox"])?$d_info["mailbox"]:"";
		$csv_line[46]	= isset($d_info["deny"])?$d_info["deny"]:"";
		$csv_line[47]	= isset($d_info["permit"])?$d_info["permit"]:"";
		$csv_line[48]	= isset($d_info["devicetype"])?$d_info["devicetype"]:"fixed";
		$csv_line[49]	= (isset($d_info["deviceid"]) || ($d_info["deviceid"]==""))?$d_info["deviceid"]:(isset($u_info["extension"])?$u_info["extension"]:"");
		$csv_line[50]	= (isset($d_info["deviceuser"]) && ($d_info["deviceuser"] != ""))?$d_info["deviceuser"]:(isset($u_info["extension"])?$u_info["extension"]:"none");
		$csv_line[51]	= isset($d_info["description"])?$d_info["description"]:(isset($u_info["name"])?$u_info["name"]:"");

		$csv_line[52]	= isset($dictate_settings["enabled"])?$dictate_settings["enabled"]:"disabled";	// dictenabled
		$csv_line[53]	= isset($dictate_settings["format"])?$dictate_settings["format"]:"ogg";		// dictformat (ogg is default)
		$csv_line[54]	= isset($dictate_settings["email"])?$dictate_settings["email"]:""; 		// dictemail
		$csv_line[55]	= isset($langcode)?$langcode:"";
		$csv_line[56]	= isset($d_info["record_in"])?$d_info["record_in"]:"";
		$csv_line[57]	= isset($d_info["record_out"])?$d_info["record_out"]:"";
		$csv_line[58]	= $v_enabled; // vm
		$csv_line[59]	= isset($v_info["pwd"])?$v_info["pwd"]:"";
		$csv_line[60]	= isset($v_info["email"])?$v_info["email"]:"";
		$csv_line[61]	= isset($v_info["pager"])?$v_info["pager"]:"";
		$csv_line[62]	= isset($v_info["options"]["attach"])?("attach=" . $v_info["options"]["attach"]):"attach=no";
		$csv_line[63]	= isset($v_info["options"]["saycid"])?("saycid=" . $v_info["options"]["saycid"]):"saycid=no";
		$csv_line[64]	= isset($v_info["options"]["envelope"])?("envelope=" . $v_info["options"]["envelope"]):"envelope=no";
		$csv_line[65]	= isset($v_info["options"]["delete"])?("delete=". $v_info["options"]["delete"]):"delete=no";
		$csv_line[66]	= isset($vm_other_opts)?$vm_other_opts:""; // additional options
		$csv_line[67]	= isset($v_info["vmcontext"])?$v_info["vmcontext"]:"";
		$csv_line[68]	= isset($vmx_state)?$vmx_state:"";
		$csv_line[69]	= isset($vmx_unavail_enabled)?$vmx_unavail_enabled:"";
		$csv_line[70]	= isset($vmx_busy_enabled)?$vmx_busy_enabled:"";
		$csv_line[71]	= isset($vmx_play_instructions)?$vmx_play_instructions:"";
		$csv_line[72]	= isset($vmx_option_0_system_default)?$vmx_option_0_system_default:"";
		$csv_line[73]	= isset($vmx_option_0_number)?$vmx_option_0_number:"";
		$csv_line[74]	= isset($vmx_option_1_system_default)?$vmx_option_1_system_default:"";
		$csv_line[75]	= isset($vmx_option_1_number)?$vmx_option_1_number:"";
		$csv_line[76]	= isset($vmx_option_2_number)?$vmx_option_2_number:"";
		$csv_line[77]	= isset($account)?$account:"";
		$csv_line[78]	= isset($ddial)?$ddial:"";
		$csv_line[79]	= isset($pre_ring)?$pre_ring:"";
		$csv_line[80]	= isset($strategy)?$strategy:"";
		$csv_line[81]	= isset($grptime)?$grptime:"";
		$csv_line[82]	= isset($grplist)?$grplist:"";
		$csv_line[83]	= isset($annmsg_id)?$annmsg_id:"";
		$csv_line[84]	= isset($ringing)?$ringing:"";
		$csv_line[85]	= isset($grppre)?$grppre:"";
		$csv_line[86]	= isset($dring)?$dring:"";
		$csv_line[87]	= isset($needsconf)?$needsconf:"";
		$csv_line[88]	= isset($remotealert_id)?$remotealert_id:"";
		$csv_line[89]	= isset($toolate_id)?$toolate_id:"";
		$csv_line[90]	= isset($postdest)?$postdest:"";
		$csv_line[91]   = isset($faxenabled)?$faxenabled:"";
		$csv_line[92]   = isset($faxemail)?$faxemail:"";

		for ($i = 0; $i < count($csv_line); $i++) {
			/* If the string contains a comma, enclose it in double-quotes. */
			if (strpos($csv_line[$i], ",") !== FALSE) {
				$csv_line[$i] = "\"" . $csv_line[$i] . "\"";
			}
			if ($i != count($csv_line) - 1) {
				$data = $data . $csv_line[$i] . ",";
			} else {
				$data = $data . $csv_line[$i];
			}
		}
		$data = $data . "\n";
		unset($csv_line);
	}
	force_download($data, $fname);
	return;
}

function get_all_exts() {
	$sql 	= "SELECT extension FROM users ORDER BY extension";
	$extens = sql($sql,"getAll");
	if (isset($extens)) {
		return $extens;
	} else {
		return null;
	}
}

function force_download ($data, $name, $mimetype="", $filesize=false) {
    // File size not set?
    if ($filesize == false OR !is_numeric($filesize)) {
        $filesize = strlen($data);
    }
    // Mimetype not set?
    if (empty($mimetype)) {
        $mimetype = "application/octet-stream";
    }
    // Make sure there's not anything else left
    ob_clean_all();
    // Start sending headers
    header("Pragma: public"); // required
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: private",false); // required for certain browsers
    header("Content-Transfer-Encoding: binary");
    header("Content-Type: " . $mimetype);
    header("Content-Length: " . $filesize);
    header("Content-Disposition: attachment; filename=\"" . $name . "\";" );
    // Send data
    echo $data;
    die();
}

function ob_clean_all () {
    $ob_active = ob_get_length () !== false;
    while($ob_active) {
        ob_end_clean();
        $ob_active = ob_get_length () !== false;
    }
    return true;
}

function generate_table_rows() {
	$langcookie =  $_COOKIE['lang'];
	if (file_exists("modules/bulkextensions/i18n/$langcookie/LC_MESSAGES/table.csv")) {		// check if translated file exists
		$fh = fopen("modules/bulkextensions/i18n/$langcookie/LC_MESSAGES/table.csv", "r");	// open it
    		} else { 										// nope, no translated file was found, open the default one
	        $fh = fopen("modules/bulkextensions/table.csv", "r");
    		}
        if ($fh == NULL) {
                return NULL;
	}
	$k = 0;
	while (($csv_data = fgetcsv($fh, 1000, ",", "\"")) !== FALSE) {
		$k++;
		/* Name,Default,Allowed,On Extensions page,Details */
		for ($i = 0; $i < 5; $i++) {
			if (isset($csv_data[$i])) {
    				$table[$k][$i] = $csv_data[$i];
			} else {
				$table[$k][$i] = "";
			}
		}
	}
	fclose($fh);
	return $table;
}

// Function to add extensions destination.
// Takes two parameters:
// $destvars = array of the three destinations
// $extension = the extension to add the destination
function bulk_extensions_dest_add($destvars, $extension)
{
extract ($destvars);
$sql="UPDATE `users` set `noanswer_dest`='$noanswer_dest', `busy_dest`='$busy_dest', `chanunavail_dest`='$chanunavail_dest' WHERE `extension`='$extension'";
sql($sql);
}
?>
