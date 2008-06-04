<?php
// This is a long running process, so extend time limit for execution.
// Typical PHP default is 30 seconds, but this only allows 100 to 200
// extensions to be processed. Setting time limit to 3000 seconds allows
// 10000 to 20000 extensions to be processed.
set_time_limit(3000);

// $change is used as a flag whether or not a reload is needed. If no changes
// are made, no reload will be prompted.
$change = false;

if ( $_REQUEST["csv_uploaded"] )
{

    $aFields = array 
    (
      "action" => array(false, -1),
      "extension" => array(false, -1),
      "name" => array(false, -1),
      "cid_masquerade" => array(false, -1),		// used in core\core_users_add(), see line 2441
      "sipname" => array(false, -1),  			// used in core\core_users_add(), see line 2354 (has''default)
      "directdid" => array(false, -1),
      "didalert" => array(false, -1),
      "mohclass" => array(false, -1),  			// used in core\core_users_add(), see line 2402
      "outboundcid" => array(false, -1),
      "ringtimer" => array(false, -1),  		// used in core\core_users_add(), see line 2410
      "callwaiting" => array(false, -1), 		// used in core\core_users_add()
      "emergency_cid" => array(false, -1),
      "tech" => array(false, -1),
      "hardware" => array(false, -1),
      "devinfo_secret" => array(false, -1),
      "devinfo_channel" => array(false, -1), 		// used for ZAP devices ( see core\core_devices_addzap() )
      "devinfo_dtmfmode" => array(false, -1), 		// used in core\core_devices_add<sip|zap|iax2>()
      "devinfo_canreinvite" => array(false, -1),	// used in core\core_devices_add<sip|zap|iax2>() 
      "devinfo_context" => array(false, -1),
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
      "faxexten" => array(false, -1), 			// used in core\core_users_add()
      "faxemail" => array(false, -1), 			// used in core\core_users_add()
      "answer" => array(false, -1), 			// used in core\core_users_add()
      "wait" => array(false, -1), 			// used in core\core_users_add()
      "privacyman" => array(false, -1), 		// used in core\core_users_add()
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
      "vmx_state" => array(false, -1), 			// used in core\core_users_add()
      "devicetype" => array(false, -1),			// fixed or Adhoc (used in core\core_devices_add())
      "password" => array(false, -1),			// defaults to '' in core_users_add() (line 2404)
      "noanswer" => array(false, -1), 			// defaults to '' in core_users_add() (line 2412)
      "devinfo_immediate" => array(false, -1),		// for zap
      "devinfo_signalling" => array(false, -1),
      "devinfo_echocancel" => array(false, -1),
      "devinfo_echocancelwhenbridged" => array(false, -1),
      "devinfo_echotraining" => array(false, -1),
      "devinfo_busydetect" => array(false, -1),
      "devinfo_busycount" => array(false, -1),
      "devinfo_callprogress" => array(false, -1),	
      "devinfo_notransfer" => array(false, -1),		// for iax2
      "display" => array(false, -1)			// not needed
      // dictation services variables omitted ($dictenabled, $dictformat, $dictemail)
      );
    
      $fh = fopen($_FILES['csvFile']['tmp_name'], "r");

      $k = 0;
      
      while (($aInfo = fgetcsv($fh, 1000, ",")) !== FALSE) 
      {
              $k++;
	      if ( empty($aInfo[0]) )
	      {
		      continue;
	      }
	      
	      // If this is the first row then we need to check each fields listed (these are the headings)
	      if ($i==0)
	      {
		      for ($j=0; $j<count($aInfo); $j++)
		      {
			      $aKeys = array_keys($aFields);
                
			      foreach ($aKeys as $sKey)
			      {
				      if ($aInfo[$j] == $sKey)
				      {
					      $aFields[$sKey][0] = true;
					      $aFields[$sKey][1] = $j;
				      }
			      }
		      }
            
		      $i++;
		      print "<BR><BR>Row $k: Headers parsed. <BR>";
		      continue;
	      }

	      if ($aFields["action"][0])
	      {
		      $vars["action"] = trim($aInfo[$aFields["action"][1]]);
	      }
	      
	      if ($aFields["extension"][0])
	      {
		      $vars["extension"] = trim($aInfo[$aFields["extension"][1]]);
		      $vars["deviceuser"] = trim($aInfo[$aFields["extension"][1]]);
		      $vars["user"] = trim($aInfo[$aFields["extension"][1]]);
		      $vars["extdisplay"] = trim($aInfo[$aFields["extension"][1]]);
	      }
	      
	      if ($aFields["name"][0])
	      {
		      $vars["name"] = trim($aInfo[$aFields["name"][1]]);
		      $vars["description"] = trim($aInfo[$aFields["description"][1]]);
	      }
	      
	      if ($aFields["cid_masquerade"][0])
	      {
		      $vars["cid_masquerade"] = trim($aInfo[$aFields["cid_masquerade"][1]]);
	      }
	      
	      if ($aFields["sipname"][0])
	      {
		      $vars["sipname"] = trim($aInfo[$aFields["sipname"][1]]);
	      }
	      
	      if ($aFields["directdid"][0])
	      {
		      $vars["directdid"] = trim($aInfo[$aFields["directdid"][1]]);
	      }
	      
	      if ($aFields["didalert"][0])
	      {
		      $vars["didalert"] = trim($aInfo[$aFields["didalert"][1]]);
	      }
	      
	      if ($aFields["mohclass"][0])
	      {
		      $vars["mohclass"] = trim($aInfo[$aFields["mohclass"][1]]);
	      }
	      
	      if ($aFields["outboundcid"][0])
	      {
		      $vars["outboundcid"] = trim($aInfo[$aFields["outboundcid"][1]]); 
	      }
	      
	      if ($aFields["ringtimer"][0])
	      {
		      $vars["ringtimer"] = trim($aInfo[$aFields["ringtimer"][1]]);
	      }	      
	      
      	      if ($aFields["callwaiting"][0])
	      {
		      $vars["callwaiting"] = trim($aInfo[$aFields["callwaiting"][1]]);
	      }
	      
	      if ($aFields["emergency_cid"][0])
	      {
		      $vars["emergency_cid"] = trim($aInfo[$aFields["emergency_cid"][1]]);
	      }

	      if ($aFields["tech"][0])
	      {
		      $vars["tech"] = trim($aInfo[$aFields["tech"][1]]);
	      }

      	      if ($aFields["hardware"][0])
	      {
		      $vars["hardware"] = trim($aInfo[$aFields["hardware"][1]]);
	      }

	      if ($aFields["devinfo_secret"][0])
	      {
		      $vars["devinfo_secret"] = trim($aInfo[$aFields["devinfo_secret"][1]]);
	      }
	      
	      if ($aFields["devinfo_channel"][0])
	      {
		      $vars["devinfo_channel"] = trim($aInfo[$aFields["devinfo_channel"][1]]);
	      }	      
	      
	      if ($aFields["devinfo_dtmfmode"][0])
	      {
		      $vars["devinfo_dtmfmode"] = trim($aInfo[$aFields["devinfo_dtmfmode"][1]]);
	      }
	      
	      if ($aFields["devinfo_canreinvite"][0])
	      {
		      $vars["devinfo_canreinvite"] = trim($aInfo[$aFields["devinfo_canreinvite"][1]]);
	      }

	      if ($aFields["devinfo_context"][0])
	      {
		      $vars["devinfo_context"] = trim($aInfo[$aFields["devinfo_context"][1]]);
	      }

	      if ($aFields["devinfo_host"][0])
	      {
		      $vars["devinfo_host"] = trim($aInfo[$aFields["devinfo_host"][1]]);
	      }
	      
	      if ($aFields["devinfo_type"][0])
	      {
		      $vars["devinfo_type"] = trim($aInfo[$aFields["devinfo_type"][1]]);
	      }
	
	      if ($aFields["devinfo_nat"][0])
	      {
		      $vars["devinfo_nat"] = trim($aInfo[$aFields["devinfo_nat"][1]]);
	      }

	      if ($aFields["devinfo_port"][0])
	      {
		      $vars["devinfo_port"] = trim($aInfo[$aFields["devinfo_port"][1]]);
	      }
	      
	      if ($aFields["devinfo_qualify"][0])
	      {
		      $vars["devinfo_qualify"] = trim($aInfo[$aFields["devinfo_qualify"][1]]);
	      }
	      
	      if ($aFields["devinfo_callgroup"][0])
	      {
		      $vars["devinfo_callgroup"] = trim($aInfo[$aFields["devinfo_callgroup"][1]]);
	      }
	      
	      if ($aFields["devinfo_pickupgroup"][0])
	      {
		      $vars["devinfo_pickupgroup"] = trim($aInfo[$aFields["devinfo_pickupgroup"][1]]);
	      }
	      
	      if ($aFields["devinfo_disallow"][0])
	      {
		      $vars["devinfo_disallow"] = trim($aInfo[$aFields["devinfo_disallow"][1]]);
	      }
	      
	      if ($aFields["devinfo_allow"][0])
	      {
		      $vars["devinfo_allow"] = trim($aInfo[$aFields["devinfo_allow"][1]]);
	      }
	      
	      if ($aFields["devinfo_dial"][0])
	      {
		      $vars["devinfo_dial"] = trim($aInfo[$aFields["devinfo_dial"][1]]);
	      }
	      
	      if ($aFields["devinfo_accountcode"][0])
	      {
		      $vars["devinfo_accountcode"] = trim($aInfo[$aFields["devinfo_accountcode"][1]]);
	      }
	      	      
	      if ($aFields["devinfo_mailbox"][0])
	      {
		      $vars["devinfo_mailbox"] = trim($aInfo[$aFields["devinfo_mailbox"][1]]);
	      }
	      
	      if ($aFields["faxexten"][0])
	      {
		      $vars["faxexten"] = trim($aInfo[$aFields["faxexten"][1]]);
	      }

	      if ($aFields["faxemail"][0])
	      {
		      $vars["faxemail"] = trim($aInfo[$aFields["faxemail"][1]]);
	      }
	      
	      if ($aFields["answer"][0])
	      {
		      $vars["answer"] = trim($aInfo[$aFields["answer"][1]]);
	      }
	      	      
	      if ($aFields["wait"][0])
	      {
		      $vars["wait"] = trim($aInfo[$aFields["wait"][1]]);
	      }
	      	      
	      if ($aFields["privacyman"][0])
	      {
		      $vars["privacyman"] = trim($aInfo[$aFields["privacyman"][1]]);
	      }
	      	      
      	      if ($aFields["record_in"][0])
	      {
		      $vars["record_in"] = trim($aInfo[$aFields["record_in"][1]]);
	      }
	      
	      if ($aFields["record_out"][0])
	      {
		      $vars["record_out"] = trim($aInfo[$aFields["record_out"][1]]);
	      }
	      
	      if ($aFields["vm"][0])
	      {
		      $vars["vm"] = trim($aInfo[$aFields["vm"][1]]);
	      }
	      
	      if ($aFields["vmpwd"][0])
	      {
		      $vars["vmpwd"] = trim($aInfo[$aFields["vmpwd"][1]]);
	      }
	      
	      if ($aFields["email"][0])
	      {
		      $vars["email"] = trim($aInfo[$aFields["email"][1]]);
	      }
	      
	      if ($aFields["pager"][0])
	      {
		      $vars["pager"] = trim($aInfo[$aFields["pager"][1]]);
	      }
	      
	      if ($aFields["attach"][0])
	      {
		      $vars["attach"] = trim($aInfo[$aFields["attach"][1]]);
	      }
	      
	      if ($aFields["saycid"][0])
	      {
		      $vars["saycid"] = trim($aInfo[$aFields["saycid"][1]]);
	      }
	      
	      if ($aFields["envelope"][0])
	      {
		      $vars["envelope"] = trim($aInfo[$aFields["envelope"][1]]);
	      }
	      
	      if ($aFields["delete"][0])
	      {
		      $vars["delete"] = trim($aInfo[$aFields["delete"][1]]);
	      }
	      
	      if ($aFields["options"][0])
	      {
		      $vars["options"] = trim($aInfo[$aFields["options"][1]]);
	      }
	      
	      if ($aFields["vmcontext"][0])
	      {
		      $vars["vmcontext"] = trim($aInfo[$aFields["vmcontext"][1]]);
	      }

	      if ($aFields["vmx_state"][0])
	      {
		      $vars["vmx_state"] = trim($aInfo[$aFields["vmx_state"][1]]);
	      }	      
	      
	      if ($aFields["devicetype"][0])
	      {
		      $vars["devicetype"] = trim($aInfo[$aFields["devicetype"][1]]);
	      }	    

	      if ($aFields["password"][0])
	      {
		      $vars["password"] = trim($aInfo[$aFields["password"][1]]);
	      }	    

	      if ($aFields["noanswer"][0])
	      {
		      $vars["noanswer"] = trim($aInfo[$aFields["noanswer"][1]]);
	      }

	      if ($aFields["devinfo_immediate"][0])
	      {
		      $vars["devinfo_immediate"] = trim($aInfo[$aFields["devinfo_immediate"][1]]);
	      }

	      if ($aFields["devinfo_signalling"][0])
	      {
		      $vars["devinfo_signalling"] = trim($aInfo[$aFields["devinfo_signalling"][1]]);
	      }

	      if ($aFields["devinfo_echocancel"][0])
	      {
		      $vars["devinfo_echocancel"] = trim($aInfo[$aFields["devinfo_echocancel"][1]]);
	      }

	      if ($aFields["devinfo_echocancelwhenbridged"][0])
	      {
		      $vars["devinfo_echocancelwhenbridged"] = trim($aInfo[$aFields["devinfo_echocancelwhenbridged"][1]]);
	      }

	      if ($aFields["devinfo_echotraining"][0])
	      {
		      $vars["devinfo_echotraining"] = trim($aInfo[$aFields["devinfo_echotraining"][1]]);
	      }

	      if ($aFields["devinfo_busydetect"][0])
	      {
		      $vars["devinfo_busydetect"] = trim($aInfo[$aFields["devinfo_busydetect"][1]]);
	      }

	      if ($aFields["devinfo_busycount"][0])
	      {
		      $vars["devinfo_busycount"] = trim($aInfo[$aFields["devinfo_busycount"][1]]);
	      }

	      if ($aFields["devinfo_callprogress"][0])
	      {
		      $vars["devinfo_callprogress"] = trim($aInfo[$aFields["devinfo_callprogress"][1]]);
	      }

	      if ($aFields["devinfo_notransfer"][0])
	      {
		      $vars["devinfo_notransfer"] = trim($aInfo[$aFields["devinfo_notransfer"][1]]);
	      }
	
	      
	      $_REQUEST = $vars;
	      
	      switch ($vars["action"])
	      {
	      	case "add":
			// Only add if no voicemail, no user and no device entry already
			// exist for the extension we're trying to add.

			// Check the list of voicemail entries.
			$uservm = voicemail_getVoicemail();
			$vmcontexts = array_keys($uservm);
			
			// vmexists == false means add  new voicemail entry.
			$vmexists = false;
			foreach ($vmcontexts as $vmcontext)
			{
				if (isset($uservm[$vmcontext][$vars["extension"]]))
				{
					$vmexists = true;		// DO NOT add.
				}
			}
			if ($vmexists || core_users_get($vars["extension"]) || core_devices_get($vars["extension"]))
			{
				print "Row $k: Extension " . $vars["extension"] . " already exists" . "<BR>";
			} else
                        {
				voicemail_mailbox_add($mbox, $vars);
				core_users_add($vars,$vars["vmcontext"]);	      
				core_devices_add($vars["extension"],$vars["tech"],$vars["devinfo_dial"],$vars["devicetype"],$vars["deviceuser"],$vars["name"],$vars["emergency_cid"]);
				print "Row $k: Added: " . $vars["extension"] . "<BR>";
				$change = true;
			}

			break;
		case "edit":
                        // Edit is just a delete and an add. Voicemail
                        // messages are not deleted.  
			// First delete:
			// Functions core_devices_del and core_users_get
			// do not check that the device or user actually
			// do exist.
			// We check that the device or user exists before deleting
			// by looking them up by the extension.
			// Only if the device or user exists do we call
			// core_devices_del or core_users_del.
			if (core_devices_get($vars["extension"]))
			{
				core_devices_del($vars["extension"]);
			}

			if (core_users_get($vars["extension"]))
			{
				core_users_del($vars["extension"]);
				core_users_cleanastdb($vars["extension"]);
			}

			// The voicemail functions have their own internal
			// checking.
			// If the voicemail box in question does not exist,
			// the functions simply return.  No harm done.
			voicemail_mailbox_del($vars["extension"]);


			// Then add:
			// No checks necessary; all deleted.

			voicemail_mailbox_add($mbox, $vars);
			core_users_add($vars,$vars["vmcontext"]);	      
			core_devices_add($vars["extension"],$vars["tech"],$vars["devinfo_dial"],$vars["devicetype"],$vars["deviceuser"],$vars["name"],$vars["emergency_cid"]);
			$change = true;

			print "Row $k: Edited: " . $vars["extension"] . "<BR>";			
			break;
		case "del":
			// Functions core_devices_del and core_users_del
			// do not check that the device or user actually
			// exists.
			// We check that the device or user exists before
                        // deleting by looking them up by the extension.
			// Only if the device or user exists do we call
			// core_devices_del or core_users_del.
			if (core_devices_get($vars["extension"]))
			{
				core_devices_del($vars["extension"]);
				$change = true;
			}

			if (core_users_get($vars["extension"]))
			{
				core_users_del($vars["extension"]);
				core_users_cleanastdb($vars["extension"]);
				$change = true;
			}

			// The voicemail functions have their own internal
			// checking.
			// If the voicemail box in question does not exist,
			// the functions simply return.  No harm done.
			voicemail_mailbox_remove($vars["extension"]);
			if(voicemail_mailbox_del($vars["extension"]))
			{
				$change = true;
			}
			if (function_exists('findmefollow_del'))
			{
			    findmefollow_del($vars["extension"]);
			}

			print "Row $k: Deleted: " . $vars["extension"] . "<BR>";
			break;
		default:
			print "Row $k: Unrecognized action: the only actions recognized are add, edit, del.\n";
			break;
	      }

              // 
	      if ($change)
	      {
		  needreload();
	      }
      	   
      } // while loop
      
} else 
{
	/****************************************************************
	*	$description is the same as $name			*
	*	$extension, $deviceuser and $user are all the same 	*
	*	can leave devinfo_mailbox blank, since  		*
	*	voicemail_mailboxadd will fill it in			*
	****************************************************************/
?>
<br><br>
<p>Right click -> Save as : <a href="modules/bulkextensions/template.csv">Template CSV</a> (file includes examples of adding, editing and deleting extensions)<br><br>
<form action="<?php $_SERVER['PHP_SELF'] ?>" name="uploadcsv" method="post" enctype="multipart/form-data">
Choose Account CSV File: <input name="csvFile" type="file">
<input name="csv_uploaded" type="hidden" value="1">
<input type="submit" value="Import!">
</form>
<h3>Please be patient.  Bulk extension changes can take a long time to complete.  Please allow up to 30 seconds per 100 extensions.</h3> 
<?php
}
?>
