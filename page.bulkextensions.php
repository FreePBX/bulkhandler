<?php
// This is a long running process, so extend time limit for execution.
// Typical PHP default is 30 seconds, but this only allows 100 to 200
// extensions to be processed. Setting time limit to 3000 seconds allows
// 10000 to 20000 extensions to be processed.
set_time_limit(3000);
// $change is used as a flag whether or not a reload is needed. If no changes
// are made, no reload will be prompted.
$change = false;
$output = "";

if ($_REQUEST["csv_type"] == "output") {
	exportextensions_allusers();
} elseif ($_REQUEST["csv_type"] == "input") {
    $aFields = array (
      "action" => array(false, -1),
      "extension" => array(false, -1),
      "name" => array(false, -1),
      "cid_masquerade" => array(false, -1),
      "sipname" => array(false, -1),
      "outboundcid" => array(false, -1),
      "ringtimer" => array(false, -1),
      "callwaiting" => array(false, -1),
      "call_screen" => array(false, -1),
      "password" => array(false, -1),
      "emergency_cid" => array(false, -1),
      "tech" => array(false, -1),
      "hardware" => array(false, -1),
      "devinfo_channel" => array(false, -1),		// for zap devices
      "devinfo_secret" => array(false, -1),
      "devinfo_notransfer" => array(false, -1),		// for iax2 devices
      "devinfo_dtmfmode" => array(false, -1), 		// used in core\core_devices_add<sip|zap|iax2>()
      "devinfo_canreinvite" => array(false, -1),	// used in core\core_devices_add<sip|zap|iax2>()
      "devinfo_context" => array(false, -1),
      "devinfo_immediate" => array(false, -1),		// for zap devices
      "devinfo_signalling" => array(false, -1),		// for zap devices
      "devinfo_echocancel" => array(false, -1),		// for zap devices
      "devinfo_echocancelwhenbridged" => array(false, -1),	// for zap devices
      "devinfo_echotraining" => array(false, -1),	// for zap devices
      "devinfo_busydetect" => array(false, -1),		// for zap devices
      "devinfo_busycount" => array(false, -1),		// for zap devices
      "devinfo_callprogress" => array(false, -1),
      "devinfo_host" => array(false, -1),
      "devinfo_type" => array(false, -1),
      "devinfo_nat" => array(false, -1),
      "devinfo_port" => array(false, -1),
      "devinfo_qualify" => array(false, -1),
      "devinfo_callgroup" => array(false, -1),
      "devinfo_pickupgroup" => array(false, -1),
      "devinfo_disallow" => array(false, -1),
      "devinfo_allow" => array(false, -1),
      "devinfo_dial" => array(false, -1),
      "devinfo_accountcode" => array(false, -1),
      "devinfo_mailbox" => array(false, -1),
      "devicetype" => array(false, -1),
      "deviceid" => array(false, -1),
      "deviceuser" => array(false, -1),
      "description" => array(false, -1),
      "dictenabled" => array(false, -1),
      "dictformat" => array(false, -1),
      "dictemail" => array(false, -1),
      "langcode" => array(false, -1),
      "record_in" => array(false, -1),
      "record_out" => array(false, -1),
      "vm" => array(false, -1),
      "vmpwd" => array(false, -1),
      "email" => array(false, -1),
      "pager" => array(false, -1),
      "attach" => array(false, -1),
      "saycid" => array(false, -1),
      "envelope" => array(false, -1),
      "delete" => array(false, -1),
      "options" => array(false, -1),
      "vmcontext" => array(false, -1),
      "vmx_state" => array(false, -1),
      "vmx_unavail_enabled" => array(false, -1),
      "vmx_busy_enabled" => array(false, -1),
      "vmx_play_instructions" => array(false, -1),
      "vmx_option_0_system_default" => array(false, -1),
      "vmx_option_0_number" => array(false, -1),
      "vmx_option_1_system_default" => array(false, -1),
      "vmx_option_1_number" => array(false, -1),
      "vmx_option_2_number" => array(false, -1),
      "account" => array(false, -1),
      "ddial" => array(false, -1),
      "pre_ring" => array(false, -1),
      "strategy" => array(false, -1),
      "grptime" => array(false, -1),
      "grplist" => array(false, -1),
      "annmsg_id" => array(false, -1),
      "ringing" => array(false, -1),
      "grppre" => array(false, -1),
      "dring" => array(false, -1),
      "needsconf" => array(false, -1),
      "remotealert_id" => array(false, -1),
      "toolate_id" => array(false, -1),
      "postdest" => array(false, -1)
      );

      $fh = fopen($_FILES["csvFile"]["tmp_name"], "r");
      if ($fh == NULL) {
	      $file_ok = FALSE;
      } else {
	      $file_ok = TRUE;
      }

      $k = 0;

      while ($file_ok && (($aInfo = fgetcsv($fh, 2000, ",", "\"")) !== FALSE)) {
              $k++;
	      if (empty($aInfo[0])) {
		      continue;
	      }

	      // If this is the first row then we need to check each field listed (these are the headings)
	      if ($i==0) {
		      for ($j=0; $j<count($aInfo); $j++) {
			      $aKeys = array_keys($aFields);
			      foreach ($aKeys as $sKey) {
				      if ($aInfo[$j] == $sKey) {
					      $aFields[$sKey][0] = true;
					      $aFields[$sKey][1] = $j;
				      }
			      }
		      }
		      $i++;
		      $output .= "<BR><BR>Row $k: Headers parsed. <BR>";
		      continue;
	      }

	      if ($aFields["action"][0]) {
		      $vars["action"] = trim($aInfo[$aFields["action"][1]]);
	      }

	      if ($aFields["extension"][0]) {
		      $vars["extension"]  = trim($aInfo[$aFields["extension"][1]]);
		      $vars["extdisplay"] = trim($aInfo[$aFields["extension"][1]]);
	      }

	      if ($aFields["name"][0]) {
		      $vars["name"] = trim($aInfo[$aFields["name"][1]]);
	      }

	      if ($aFields["cid_masquerade"][0]) {
		      $vars["cid_masquerade"] = trim($aInfo[$aFields["cid_masquerade"][1]]);
	      }

	      if ($aFields["sipname"][0]) {
		      $vars["sipname"] = trim($aInfo[$aFields["sipname"][1]]);
	      }

	      if ($aFields["outboundcid"][0]) {
		      $vars["outboundcid"] = trim($aInfo[$aFields["outboundcid"][1]]);
	      }

	      if ($aFields["ringtimer"][0]) {
		      $vars["ringtimer"] = trim($aInfo[$aFields["ringtimer"][1]]);
	      }

      	      if ($aFields["callwaiting"][0]) {
		      $vars["callwaiting"] = trim($aInfo[$aFields["callwaiting"][1]]);
	      }

	      if ($aFields["call_screen"][0]) {
		      $vars["call_screen"] = trim($aInfo[$aFields["call_screen"][1]]);
	      }

	      if ($aFields["password"][0]) {
		      $vars["password"] = trim($aInfo[$aFields["password"][1]]);
	      }

	      if ($aFields["emergency_cid"][0]) {
		      $vars["emergency_cid"] = trim($aInfo[$aFields["emergency_cid"][1]]);
	      }

	      if ($aFields["tech"][0]) {
		      $vars["tech"] = trim($aInfo[$aFields["tech"][1]]);
	      }

      	      if ($aFields["hardware"][0]) {
		      $vars["hardware"] = trim($aInfo[$aFields["hardware"][1]]);
	      }

	      if ($aFields["devinfo_channel"][0]) {
		      $vars["devinfo_channel"] = trim($aInfo[$aFields["devinfo_channel"][1]]);
	      }

	      if ($aFields["devinfo_secret"][0]) {
		      $vars["devinfo_secret"] = trim($aInfo[$aFields["devinfo_secret"][1]]);
	      }

	      if ($aFields["devinfo_notransfer"][0]) {
		      $vars["devinfo_notransfer"] = trim($aInfo[$aFields["devinfo_notransfer"][1]]);
	      }

	      if ($aFields["devinfo_dtmfmode"][0]) {
		      $vars["devinfo_dtmfmode"] = trim($aInfo[$aFields["devinfo_dtmfmode"][1]]);
	      }

	      if ($aFields["devinfo_canreinvite"][0]) {
		      $vars["devinfo_canreinvite"] = trim($aInfo[$aFields["devinfo_canreinvite"][1]]);
	      }

	      if ($aFields["devinfo_context"][0]) {
		      $vars["devinfo_context"] = trim($aInfo[$aFields["devinfo_context"][1]]);
	      }

	      if ($aFields["devinfo_immediate"][0]) {
		      $vars["devinfo_immediate"] = trim($aInfo[$aFields["devinfo_immediate"][1]]);
	      }

	      if ($aFields["devinfo_signalling"][0]) {
		      $vars["devinfo_signalling"] = trim($aInfo[$aFields["devinfo_signalling"][1]]);
	      }

	      if ($aFields["devinfo_echocancel"][0]) {
		      $vars["devinfo_echocancel"] = trim($aInfo[$aFields["devinfo_echocancel"][1]]);
	      }

	      if ($aFields["devinfo_echocancelwhenbridged"][0]) {
		      $vars["devinfo_echocancelwhenbridged"] = trim($aInfo[$aFields["devinfo_echocancelwhenbridged"][1]]);
	      }

	      if ($aFields["devinfo_echotraining"][0]) {
		      $vars["devinfo_echotraining"] = trim($aInfo[$aFields["devinfo_echotraining"][1]]);
	      }

	      if ($aFields["devinfo_busydetect"][0]) {
		      $vars["devinfo_busydetect"] = trim($aInfo[$aFields["devinfo_busydetect"][1]]);
	      }

	      if ($aFields["devinfo_busycount"][0]) {
		      $vars["devinfo_busycount"] = trim($aInfo[$aFields["devinfo_busycount"][1]]);
	      }

	      if ($aFields["devinfo_callprogress"][0]) {
		      $vars["devinfo_callprogress"] = trim($aInfo[$aFields["devinfo_callprogress"][1]]);
	      }

	      if ($aFields["devinfo_host"][0]) {
		      $vars["devinfo_host"] = trim($aInfo[$aFields["devinfo_host"][1]]);
	      }

	      if ($aFields["devinfo_type"][0]) {
		      $vars["devinfo_type"] = trim($aInfo[$aFields["devinfo_type"][1]]);
	      }

	      if ($aFields["devinfo_nat"][0]) {
		      $vars["devinfo_nat"] = trim($aInfo[$aFields["devinfo_nat"][1]]);
	      }

	      if ($aFields["devinfo_port"][0]) {
		      $vars["devinfo_port"] = trim($aInfo[$aFields["devinfo_port"][1]]);
	      }

	      if ($aFields["devinfo_qualify"][0]) {
		      $vars["devinfo_qualify"] = trim($aInfo[$aFields["devinfo_qualify"][1]]);
	      }

	      if ($aFields["devinfo_callgroup"][0]) {
		      $vars["devinfo_callgroup"] = trim($aInfo[$aFields["devinfo_callgroup"][1]]);
	      }

	      if ($aFields["devinfo_pickupgroup"][0]) {
		      $vars["devinfo_pickupgroup"] = trim($aInfo[$aFields["devinfo_pickupgroup"][1]]);
	      }

	      if ($aFields["devinfo_disallow"][0]) {
		      $vars["devinfo_disallow"] = trim($aInfo[$aFields["devinfo_disallow"][1]]);
	      }

	      if ($aFields["devinfo_allow"][0]) {
		      $vars["devinfo_allow"] = trim($aInfo[$aFields["devinfo_allow"][1]]);
	      }

	      if ($aFields["devinfo_dial"][0]) {
		      $vars["devinfo_dial"] = trim($aInfo[$aFields["devinfo_dial"][1]]);
	      }

	      if ($aFields["devinfo_accountcode"][0]) {
		      $vars["devinfo_accountcode"] = trim($aInfo[$aFields["devinfo_accountcode"][1]]);
	      }

	      if ($aFields["devinfo_mailbox"][0]) {
		      $vars["devinfo_mailbox"] = trim($aInfo[$aFields["devinfo_mailbox"][1]]);
	      }

	      if ($aFields["devicetype"][0]) {
		      $vars["devicetype"] = trim($aInfo[$aFields["devicetype"][1]]);
	      }

	      if ($aFields["deviceid"][0]) {
		      $vars["deviceid"] = trim($aInfo[$aFields["deviceid"][1]]);
	      }

      	      if ($aFields["deviceuser"][0]) {
		      $vars["deviceuser"] = trim($aInfo[$aFields["deviceuser"][1]]);
	      }

	      if ($aFields["description"][0]) {
		      $vars["description"] = trim($aInfo[$aFields["description"][1]]);
	      }

	      if ($aFields["dictenabled"][0]) {
		      $vars["dictenabled"] = trim($aInfo[$aFields["dictenabled"][1]]);
	      }

	      if ($aFields["dictformat"][0]) {
		      $vars["dictformat"] = trim($aInfo[$aFields["dictformat"][1]]);
	      }

	      if ($aFields["dictemail"][0]) {
		      $vars["dictemail"] = trim($aInfo[$aFields["dictemail"][1]]);
	      }

	      if ($aFields["langcode"][0]) {
		      $vars["langcode"] = trim($aInfo[$aFields["langcode"][1]]);
	      }

	      if ($aFields["record_in"][0]) {
		      $vars["record_in"] = trim($aInfo[$aFields["record_in"][1]]);
	      }

	      if ($aFields["record_out"][0]) {
		      $vars["record_out"] = trim($aInfo[$aFields["record_out"][1]]);
	      }

	      if ($aFields["vm"][0]) {
		      $vars["vm"] = trim($aInfo[$aFields["vm"][1]]);
	      }

	      if ($aFields["vmpwd"][0]) {
		      $vars["vmpwd"] = trim($aInfo[$aFields["vmpwd"][1]]);
	      }

	      if ($aFields["email"][0]) {
		      $vars["email"] = trim($aInfo[$aFields["email"][1]]);
	      }

	      if ($aFields["pager"][0]) {
		      $vars["pager"] = trim($aInfo[$aFields["pager"][1]]);
	      }

	      if ($aFields["attach"][0]) {
		      $vars["attach"] = trim($aInfo[$aFields["attach"][1]]);
	      }

	      if ($aFields["saycid"][0]) {
		      $vars["saycid"] = trim($aInfo[$aFields["saycid"][1]]);
	      }

	      if ($aFields["envelope"][0]) {
		      $vars["envelope"] = trim($aInfo[$aFields["envelope"][1]]);
	      }

	      if ($aFields["delete"][0]) {
		      $vars["delete"] = trim($aInfo[$aFields["delete"][1]]);
	      }

	      if ($aFields["options"][0]) {
		      $vars["options"] = trim($aInfo[$aFields["options"][1]]);
	      }

	      if ($aFields["vmcontext"][0]) {
		      $vars["vmcontext"] = trim($aInfo[$aFields["vmcontext"][1]]);
	      }

	      if ($aFields["vmx_state"][0]) {
		      $vars["vmx_state"] = trim($aInfo[$aFields["vmx_state"][1]]);
	      }

	      if ($aFields["vmx_unavail_enabled"][0]) {
		      $vars["vmx_unavail_enabled"] = trim($aInfo[$aFields["vmx_unavail_enabled"][1]]);
	      }

	      if ($aFields["vmx_busy_enabled"][0]) {
		      $vars["vmx_busy_enabled"] = trim($aInfo[$aFields["vmx_busy_enabled"][1]]);
	      }

	      if ($aFields["vmx_play_instructions"][0]) {
		      $vars["vmx_play_instructions"] = trim($aInfo[$aFields["vmx_play_instructions"][1]]);
	      }

	      if ($aFields["vmx_option_0_system_default"][0]) {
		      $vars["vmx_option_0_system_default"] = trim($aInfo[$aFields["vmx_option_0_system_default"][1]]);
	      }

	      if ($aFields["vmx_option_0_number"][0]) {
		      $vars["vmx_option_0_number"] = trim($aInfo[$aFields["vmx_option_0_number"][1]]);
	      }

	      if ($aFields["vmx_option_1_system_default"][0]) {
		      $vars["vmx_option_1_system_default"] = trim($aInfo[$aFields["vmx_option_1_system_default"][1]]);
	      }

	      if ($aFields["vmx_option_1_number"][0]) {
		      $vars["vmx_option_1_number"] = trim($aInfo[$aFields["vmx_option_1_number"][1]]);
	      }

	      if ($aFields["vmx_option_2_number"][0]) {
		      $vars["vmx_option_2_number"] = trim($aInfo[$aFields["vmx_option_2_number"][1]]);
	      }
	      
	      if ($aFields["account"][0]) {
		      $vars["account"] = trim($aInfo[$aFields["account"][1]]);
		      if ($vars["account"] == $vars["extension"]) {
			      $followme_set = TRUE;		/* indicate we have follow me settings to set */
		      } else {
			      $followme_set = FALSE;
		      }
	      }
	      
	      if ($aFields["ddial"][0]) {
		      $vars["ddial"] = trim($aInfo[$aFields["ddial"][1]]);
	      }

      	      if ($aFields["pre_ring"][0]) {
		      $vars["pre_ring"] = trim($aInfo[$aFields["pre_ring"][1]]);
	      }

      	      if ($aFields["strategy"][0]) {
		      $vars["strategy"] = trim($aInfo[$aFields["strategy"][1]]);
	      }

      	      if ($aFields["grptime"][0]) {
		      $vars["grptime"] = trim($aInfo[$aFields["grptime"][1]]);
	      }

      	      if ($aFields["grplist"][0]) {
		      $vars["grplist"] = trim($aInfo[$aFields["grplist"][1]]);
	      }

      	      if ($aFields["annmsg_id"][0]) {
		      $vars["annmsg_id"] = trim($aInfo[$aFields["annmsg_id"][1]]);
	      }

      	      if ($aFields["ringing"][0]) {
		      $vars["ringing"] = trim($aInfo[$aFields["ringing"][1]]);
	      }

      	      if ($aFields["grppre"][0]) {
		      $vars["grppre"] = trim($aInfo[$aFields["grppre"][1]]);
	      }

      	      if ($aFields["dring"][0]) {
		      $vars["dring"] = trim($aInfo[$aFields["dring"][1]]);
	      }

      	      if ($aFields["needsconf"][0]) {
		      $vars["needsconf"] = trim($aInfo[$aFields["needsconf"][1]]);
	      }

      	      if ($aFields["remotealert_id"][0]) {
		      $vars["remotealert_id"] = trim($aInfo[$aFields["remotealert_id"][1]]);
	      }

      	      if ($aFields["toolate_id"][0]) {
		      $vars["toolate_id"] = trim($aInfo[$aFields["toolate_id"][1]]);
	      }

      	      if ($aFields["postdest"][0]) {
		      $vars["postdest"] = trim($aInfo[$aFields["postdest"][1]]);
	      }

	      /* Needed fields for creating a Follow Me are account (aka grpnum), strategy, grptime, */
	      /* grplist and pre_ring.								     */
	      if ($followme_set) {
		      if (!isset($vars["strategy"]) || ($vars["strategy"] == "")) {
			      $vars["strategy"] = "ringallv2";		// default value
		      }
		      
		      if(!isset($vars["grptime"]) || ($vars["grptime"] == "")) {
			      $vars["grptime"] = "20";			// default value
		      }
		      
		      if(!isset($vars["grplist"]) || ($vars["grplist"] == "")) {
			      $vars["grplist"] = $vars["extension"];	// default value
		      }
		      
		      if(!isset($vars["pre_ring"]) || ($vars["pre_ring"] == "")) {
			      $vars["pre_ring"] = "0";			// default value
		      }
	      }

	      if (!(isset($amp_conf["AMPEXTENSIONS"]) && ($amp_conf["AMPEXTENSIONS"] == "deviceanduser"))) {
		      $vars["devicetype"] 	= "fixed";
		      $vars["deviceid"]	= $vars["deviceuser"] = $vars["extension"];
		      $vars["description"] 	= $vars["name"];
	      } else {
		      /* deviceid is required; if freepbx is in devicesandusers mode, deviceid cannot be left blank. */
		      if ($vars["deviceid"] == "") {
			      $vars["deviceid"] = $vars["extension"];
		      }
	      }

	      $vars["display"]	= "bulkextensions";
	      $vars["type"]	= "tool";

	      $_REQUEST = $vars;

	      switch ($vars["action"]) {
	      	case "add":
			// Only add if no voicemail, no user and no device entry already
			// exist for the extension we're trying to add.
			// Check the list of voicemail entries.
			// user_vmexists == false means add  new voicemail entry.
			$user_vmexists = FALSE;
			if ($vm_exists) {
				$uservm = voicemail_getVoicemail();
				$vmcontexts = array_keys($uservm);
				foreach ($vmcontexts as $vmcontext) {
					if (isset($uservm[$vmcontext][$vars["extension"]])) {
						$user_vmexists = TRUE;		// DO NOT add.
					}
				}
			}
			if ($user_vmexists || core_users_get($vars["extension"]) || core_devices_get($vars["extension"])) {
				$output .= "Row $k: Extension " . $vars["extension"] . " already exists" . "<BR>";
			} else {
				if ($vm_exists) {
					voicemail_mailbox_add($vars["extension"], $vars);
				}
				core_users_add($vars);
				core_devices_add($vars["deviceid"],$vars["tech"],$vars["devinfo_dial"],$vars["devicetype"],$vars["deviceuser"],$vars["description"],$vars["emergency_cid"]);
				
				if ($lang_exists) {
					languages_user_update($vars["extension"], $vars["langcode"]);
				}
				if ($dict_exists) {
					dictate_update($vars["extension"], $vars["dictenabled"], $vars["dictformat"], $vars["dictemail"]);
				}
				if ($findme_exists && $followme_set) {
					findmefollow_add($vars["account"], $vars["strategy"], $vars["grptime"], $vars["grplist"], $vars["postdest"], $vars["grppre"], $vars["annmsg_id"], $vars["dring"], $vars["needsconf"], $vars["remotealert_id"], $vars["toolate_id"], $vars["ringing"], $vars["pre_ring"], $vars["ddial"]);
				}
				$output .= "Row $k: Added: " . $vars["extension"] . "<BR>";
				$change = true;
			}
			break;
		case "edit":
			// Functions core_devices_del and core_users_del
			// do not check that the device or user actually
			// exists.
			// We check that the device or user exists before
                        // deleting by looking them up by the extension.
			// Only if the device or user exists do we call
			// core_devices_del or core_users_del.
			if (core_devices_get($vars["extension"])) {
				core_devices_del($vars["extension"]);
				$change = true;
			}
			if (core_users_get($vars["extension"])) {
				core_users_del($vars["extension"]);
				core_users_cleanastdb($vars["extension"]);
				if ($findme_exists) {
					findmefollow_del($vars["extension"]);
				}
				if ($dict_exists) {
					dictate_del($vars["extension"]);
				}
				if ($lang_exists) {
					languages_user_del($vars["extension"]);
				}
				$change = true;
			}
			// The voicemail functions have their own internal
			// checking.
			// If the voicemail box in question does not exist,
			// the functions simply return.  No harm done.
			//
			// When editting an existing extension do not call
			// voicemail_mailbox_remove, it will delete existing
			// voicemail messages, which is undesirable.
			if ($vm_exists) {
				voicemail_mailbox_del($vars["extension"]);
			}
			// Only add if no voicemail, no user and no device entry already
			// exist for the extension we're trying to add.
			// Check the list of voicemail entries.
			// user_vmexists == false means add new voicemail entry.
			$user_vmexists = FALSE;
			if ($vm_exists) {
				$uservm = voicemail_getVoicemail();
				$vmcontexts = array_keys($uservm);
				foreach ($vmcontexts as $vmcontext) {
					if (isset($uservm[$vmcontext][$vars["extension"]])) {
						$user_vmexists = TRUE;		// DO NOT add.
					}
				}
			}
			if ($user_vmexists || core_users_get($vars["extension"]) || core_devices_get($vars["extension"])) {
				$output .= "Row $k: Extension " . $vars["extension"] . " already exists" . "<BR>";
			} else {
				if ($vm_exists) {
					voicemail_mailbox_add($vars["extension"], $vars);
				}
				core_users_add($vars);
				core_devices_add($vars["deviceid"],$vars["tech"],$vars["devinfo_dial"],$vars["devicetype"],$vars["deviceuser"],$vars["description"],$vars["emergency_cid"]);
				if ($lang_exists) {
					languages_user_update($vars["extension"], $vars["langcode"]);
				}
				if ($dict_exists) {
					dictate_update($vars["extension"], $vars["dictenabled"], $vars["dictformat"], $vars["dictemail"]);
				}
				if ($findme_exists && $followme_set) {
					findmefollow_add($vars["account"], $vars["strategy"], $vars["grptime"], $vars["grplist"], $vars["postdest"], $vars["grppre"], $vars["annmsg_id"], $vars["dring"], $vars["needsconf"], $vars["remotealert_id"], $vars["toolate_id"], $vars["ringing"], $vars["pre_ring"], $vars["ddial"]);
				}
				$change = true;
			}
			$output .= "Row $k: Edited: " . $vars["extension"] . "<BR>";
			break;
		case "del":
			// Functions core_devices_del and core_users_del
			// do not check that the device or user actually
			// exists.
			// We check that the device or user exists before
                        // deleting by looking them up by the extension.
			// Only if the device or user exists do we call
			// core_devices_del or core_users_del.
			if (core_devices_get($vars["extension"])) {
				core_devices_del($vars["extension"]);
				$change = true;
			}
			if (core_users_get($vars["extension"])) {
				core_users_del($vars["extension"]);
				core_users_cleanastdb($vars["extension"]);
				if ($findme_exists) {
					findmefollow_del($vars["extension"]);
				}
				if ($dict_exists) {
					dictate_del($vars["extension"]);
				}
				if ($lang_exists) {
					languages_user_del($vars["extension"]);
				}
				$change = true;
			}
			// The voicemail functions have their own internal
			// checking.
			// If the voicemail box in question does not exist,
			// the functions simply return.  No harm done.
			//
			// call remove BEFORE del
			if ($vm_exists) {
				voicemail_mailbox_remove($vars["extension"]);
				voicemail_mailbox_del($vars["extension"]);
			}
			$output .= "Row $k: Deleted: " . $vars["extension"] . "<BR>";
			break;
		default:
			$output .= "Row $k: Unrecognized action: the only actions recognized are add, edit, del.\n";
			break;
	      }

	      if ($change) {
		  needreload();
	      }
      } // while loop

       print $output;

} else
{
	$table_output = "";
	$table_rows = generate_table_rows();
	if ($table_rows === NULL) {
		$table_output = "Table unavailable";
	} else {
		$table_output .=	"<table cellspacing='0' cellpadding='4' rules='rows'>";
		$table_output .=	"<tr valign='top'>
						<th align='left' valign='top'>#</th>
						<th align='left' valign='top'>Name</th>
						<th align='left' valign='top'>Default</th>
						<th align='left' valign='top'>Allowed</th>
						<th align='left' valign='top'>On Extensions page</th>
						<th align='left' valign='top'>Details</th>
					</tr>";
		$i = 1;
		foreach ($table_rows as $row) {
			$table_output .= "<tr>";
			$table_output .= "<td valign='top'>" . $i . "</td>";
			$i++;
			foreach ($row as $col) {
				$table_output .= "<td valign='top'>" . $col . "</td>";
			}
			$table_output .= "</tr>";
		}
		$table_output .= "</table>";
	}

?>
<h1>Bulk Extensions</h1>

<h2>Manage Extensions in bulk using CSV files.</h2>

<p>
Start by downloading the
<a href="modules/bulkextensions/template.csv">Template CSV file</a>
(right-click > save as) or clicking the Export Extensions button.
</p>
<p>
The table below explains each column in the CSV file. Modify the CSV file to add,
edit, or delete Extensions as desired. You can change the column order of the CSV
file as you like, however, the column names must be preserved. Then load the CSV
file. After the CSV file is processed, the action taken for each row will be
displayed.
</p>
<p>
<b>Bulk extension changes can take a long time to complete. It can take 30-60
seconds to add 100 extensions on a small system. However, on a system with 2000
extensions it can take about 5 minutes to add 100 new extensions.</b>
</p>

<form action="<?php $_SERVER["PHP_SELF"] ?>" name="uploadcsv" method="post" enctype="multipart/form-data">
<input id="csv_type" name="csv_type" type="hidden" value="none">
<input type="submit" onclick="document.getElementById('csv_type').value='output';" value="Export Extensions">
<br>
<br>
CSV File to Load: <input name="csvFile" type="file">
<input type="submit" onclick="document.getElementById('csv_type').value='input';"  value="Load File">
</form>
<hr>
<h3>Bulk Extensions CSV File Columns</h3>
<?php
	print $table_output;
}
?>
