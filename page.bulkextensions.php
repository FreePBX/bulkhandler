<?php
// This is a long running process, so extend time limit for execution.
// Typical PHP default is 30 seconds, but this only allows 100 to 200
// extensions to be processed. Setting time limit to 3000 seconds allows
// 10000 to 20000 extensions to be processed.
set_time_limit(3000);

// $change is used as a flag whether or not a reload is needed. If no changes
// are made, no reload will be prompted.
$change = false;
if ( $_REQUEST["csv_type"] == 'output')
{
	exportextensions_allusers();
} elseif ( $_REQUEST["csv_type"] == 'input')
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
      if($fh == NULL)
      {
	      $file_ok = false;
      } else
      {
	      $file_ok = true;
      }
      
      $k = 0;
      
      while ($file_ok && (($aInfo = fgetcsv($fh, 1000, ",")) !== FALSE)) 
      {
              $k++;
	      if ( empty($aInfo[0]) )
	      {
		      continue;
	      }
	      
	      // If this is the first row then we need to check each field listed (these are the headings)
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
			// Functions core_devices_del and core_users_del
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
<h1>Bulk Extensions</h1>

<h2>Manage Extensions in bulk using CSV files.</h2>

<p>Start by downloading the
<a href="modules/bulkextensions/template.csv">Template CSV file</a>
(right-click > save as) or clicking the Export Extensions button. The table
below explains each column in the CSV file. Modify the CSV file to add, edit,
or delete Extensions as desired. Then load the CSV file. As the CSV file is
processed, the action taken for each row will be displayed. <b>Bulk extension
changes can take a long time to complete. Please allow up to 30 seconds per
100 extensions.</b></p>

<form action="<?php $_SERVER['PHP_SELF'] ?>" name="uploadcsv" method="post" enctype="multipart/form-data">
<input id="csv_type" name="csv_type" type="hidden" value="none">
<input type="submit" onclick="document.getElementById('csv_type').value='output';" value="Export Extensions">
<br>
<br>
CSV File to Load: <input name="csvFile" type="file">
<input type="submit" onclick="document.getElementById('csv_type').value='input';"  value="Load File">
</form>
<hr>
<h3>Bulk Extensions CSV File Columns</h3>

<table cellspacing="0" cellpadding="4" rules="rows">
 <tr valign="top">
  <th align="left" valign="top">#</th>
  <th align="left" valign="top">Name</th>
  <th align="left" valign="top">Default</th>
  <th align="left" valign="top">Allowed</th>
  <th align="left" valign="top">On Extensions page</th>
  <th align="left" valign="top">Details</th>
 </tr>
 <tr valign="top">
  <td valign="top">1</td>
  <td valign="top">action</td>
  <td valign="top"></td>
  <td valign="top">add, del, edit</td>
  <td valign="top"></td>
  <td valign="top">Add, Delete, or Edit an Extension.</td>
 </tr>
 <tr valign="top">
  <td valign="top">2</td>
  <td valign="top">extension</td>
  <td valign="top"></td>
  <td valign="top"></td>
  <td valign="top">User Extension</td>
  <td valign="top">The extension number to dial to reach this user.</td>
 </tr>
 <tr valign="top">
  <td valign="top">3</td>
  <td valign="top">name</td>
  <td valign="top"></td>
  <td valign="top"></td>
  <td valign="top">Display Name</td>
  <td valign="top">The caller id name for calls from this user will be set to this name. Only
  enter the name, NOT the number.</td>
 </tr>
 <tr valign="top">
  <td valign="top">4</td>
  <td valign="top">cid_masquerade</td>
  <td valign="top"></td>
  <td valign="top"></td>
  <td valign="top">CID Num Alias</td>
  <td valign="top">The CID Number to use for internal calls, if different from the extension
  number. This is used to masquerade as a different user. A common example is a
  team of support people who would like their internal callerid to display the
  general support number (a ringgroup or queue). There will be no effect on
  external calls.</td>
 </tr>
 <tr valign="top">
  <td valign="top">5</td>
  <td valign="top">sipname</td>
  <td valign="top"></td>
  <td valign="top"></td>
  <td valign="top">SIP Alias</td>
  <td valign="top">If you want to support direct sip dialing of users internally or through
  anonymous sip calls, you can supply a friendly name that can be used in
  addition to the users extension to call them.</td>
 </tr>
 <tr valign="top">
  <td valign="top">6</td>
  <td valign="top">directdid</td>
  <td valign="top"></td>
  <td valign="top"></td>
  <td valign="top">Direct DID</td>
  <td valign="top">The direct DID that is associated with this extension. The DID should be
  in the same format as provided by the provider (e.g. full number, 4 digits for
  10x4, etc).
  <br><br>
  Format should be: <b>XXXXXXXXXX</b>
  <br><br>
  Leave this field blank to disable the direct DID feature for this extension.
  All non-numeric characters will be stripped.</td>
 </tr>
 <tr valign="top">
  <td valign="top">7</td>
  <td valign="top">didalert</td>
  <td valign="top"></td>
  <td valign="top"></td>
  <td valign="top">DID Alert Info</td>
  <td valign="top">Alert Info can be used for distinctive ring on SIP phones. Set this value
  to the desired Alert Info to be sent to the phone when this DID is called.
  Leave blank to use default values. Will have no effect if no Direct DID is
  set.</td>
 </tr>
 <tr valign="top">
  <td valign="top">8</td>
  <td valign="top">mohclass</td>
  <td valign="top">default</td>
  <td valign="top">default, none, any valid MoH class</td>
  <td valign="top">Music on Hold</td>
  <td valign="top">Set the MoH class that will be used for calls that come in on this Direct
  DID. For example, choose a type appropriate for a originating country which
  may have announcements in their language. Only effects MoH class when the call
  came in from the Direct DID.</td>
 </tr>
 <tr valign="top">
  <td valign="top">9</td>
  <td valign="top">outboundcid</td>
  <td valign="top"></td>
  <td valign="top"></td>
  <td valign="top">Outbound CID</td>
  <td valign="top">Overrides the caller id when dialing out a trunk. Any setting here will
  override the common outbound caller id set in the Trunks admin.
  <br><br>
  Format: <b>&quot;caller name&quot; &lt;#######&gt;</b>
  <br><br>
  Leave this field blank to disable the outbound callerid feature for this
  user.</td>
 </tr>
 <tr valign="top">
  <td valign="top">10</td>
  <td valign="top">ringtimer</td>
  <td valign="top">0</td>
  <td valign="top">0-120</td>
  <td valign="top">Ring Time</td>
  <td valign="top">Number of seconds to ring prior to going to voicemail. 0 (Default) will
  use the value set in the General Tab. If no voicemail is configured this will
  be ignored.</td>
 </tr>
 <tr valign="top">
  <td valign="top">11</td>
  <td valign="top">callwaiting</td>
  <td valign="top">enabled</td>
  <td valign="top">enabled, disabled</td>
  <td valign="top">Call Waiting</td>
  <td valign="top">Set the initial/current Call Waiting state for this user's extension.</td>
 </tr>
 <tr valign="top">
  <td valign="top">12</td>
  <td valign="top">emergency_cid</td>
  <td valign="top"></td>
  <td valign="top"></td>
  <td valign="top">Emergency CID</td>
  <td valign="top">This caller id will always be set when dialing out an Outbound Route
  flagged as Emergency. The Emergency CID overrides all other caller id
  settings.</td>
 </tr>
 <tr valign="top">
  <td valign="top">13</td>
  <td valign="top">tech</td>
  <td valign="top"></td>
  <td valign="top">sip, iax2, zap, custom</td>
  <td valign="top"></td>
  <td valign="top">Device channel type.</td>
 </tr>
 <tr valign="top">
  <td valign="top">14</td>
  <td valign="top">hardware</td>
  <td valign="top"></td>
  <td valign="top">generic, custom</td>
  <td valign="top"></td>
  <td valign="top"></td>
 </tr>
 <tr valign="top">
  <td valign="top">15</td>
  <td valign="top">devinfo_secret</td>
  <td valign="top"></td>
  <td valign="top"></td>
  <td valign="top">secret</td>
  <td valign="top">See sip.conf file or iax.conf file.</td>
 </tr>
 <tr valign="top">
  <td valign="top">16</td>
  <td valign="top">devinfo_channel</td>
  <td valign="top"></td>
  <td valign="top"></td>
  <td valign="top">channel</td>
  <td valign="top">See zapata.conf.</td>
 </tr>
 <tr valign="top">
  <td valign="top">17</td>
  <td valign="top">devinfo_dtmfmode</td>
  <td valign="top">rfc2833</td>
  <td valign="top"></td>
  <td valign="top">dtmfmode</td>
  <td valign="top">See sip.conf file.</td>
 </tr>
 <tr valign="top">
  <td valign="top">18</td>
  <td valign="top">devinfo_canreinvite</td>
  <td valign="top">no</td>
  <td valign="top"></td>
  <td valign="top">canreinvite</td>
  <td valign="top">See sip.conf file.</td>
 </tr>
 <tr valign="top">
  <td valign="top">19</td>
  <td valign="top">devinfo_context</td>
  <td valign="top">from-internal</td>
  <td valign="top"></td>
  <td valign="top">context</td>
  <td valign="top">Device context.</td>
 </tr>
 <tr valign="top">
  <td valign="top">20</td>
  <td valign="top">devinfo_host</td>
  <td valign="top">dynamic</td>
  <td valign="top"></td>
  <td valign="top">host</td>
  <td valign="top">See sip.conf file or iax.conf file.</td>
 </tr>
 <tr valign="top">
  <td valign="top">21</td>
  <td valign="top">devinfo_type</td>
  <td valign="top">friend</td>
  <td valign="top"></td>
  <td valign="top">type</td>
  <td valign="top">See sip.conf file or iax.conf file.</td>
 </tr>
 <tr valign="top">
  <td valign="top">22</td>
  <td valign="top">devinfo_nat</td>
  <td valign="top">yes</td>
  <td valign="top"></td>
  <td valign="top">nat</td>
  <td valign="top">See sip.conf file.</td>
 </tr>
 <tr valign="top">
  <td valign="top">23</td>
  <td valign="top">devinfo_port</td>
  <td valign="top"></td>
  <td valign="top"></td>
  <td valign="top">port</td>
  <td valign="top">See sip.conf file or iax.conf file. Default for sip is 5060, iax2 is
  4569.</td>
 </tr>
 <tr valign="top">
  <td valign="top">24</td>
  <td valign="top">devinfo_qualify</td>
  <td valign="top">yes</td>
  <td valign="top"></td>
  <td valign="top">qualify</td>
  <td valign="top">See sip.conf file or iax.conf file.</td>
 </tr>
 <tr valign="top">
  <td valign="top">25</td>
  <td valign="top">devinfo_callgroup</td>
  <td valign="top"></td>
  <td valign="top"></td>
  <td valign="top">callgroup</td>
  <td valign="top">See sip.conf file.</td>
 </tr>
 <tr valign="top">
  <td valign="top">26</td>
  <td valign="top">devinfo_pickupgroup</td>
  <td valign="top"></td>
  <td valign="top"></td>
  <td valign="top">pickupgroup</td>
  <td valign="top">See sip.conf file.</td>
 </tr>
 <tr valign="top">
  <td valign="top">27</td>
  <td valign="top">devinfo_disallow</td>
  <td valign="top"></td>
  <td valign="top"></td>
  <td valign="top">disallow</td>
  <td valign="top">See conf file for device tech. Codec(s) to disallow.</td>
 </tr>
 <tr valign="top">
  <td valign="top">28</td>
  <td valign="top">devinfo_allow</td>
  <td valign="top"></td>
  <td valign="top"></td>
  <td valign="top">allow</td>
  <td valign="top">See conf file for device tech. Codec(s) to allow.</td>
 </tr>
 <tr valign="top">
  <td valign="top">29</td>
  <td valign="top">devinfo_dial</td>
  <td valign="top"></td>
  <td valign="top"></td>
  <td valign="top">dial</td>
  <td valign="top">See conf file for device tech. Default is TECH/exten, i.e SIP/101. For zap
  it is TECH/channel, i.e. ZAP/1.</td>
 </tr>
 <tr valign="top">
  <td valign="top">30</td>
  <td valign="top">devinfo_accountcode</td>
  <td valign="top"></td>
  <td valign="top"></td>
  <td valign="top">accountcode</td>
  <td valign="top">See conf file for device tech.</td>
 </tr>
 <tr valign="top">
  <td valign="top">31</td>
  <td valign="top">devinfo_mailbox</td>
  <td valign="top"></td>
  <td valign="top"></td>
  <td valign="top">mailbox</td>
  <td valign="top">See conf file for device tech. Default is exten@device, i.e 101@device.</td>
 </tr>
 <tr valign="top">
  <td valign="top">32</td>
  <td valign="top">faxexten</td>
  <td valign="top">default</td>
  <td valign="top">default, disabled, system, any valid extension</td>
  <td valign="top">Fax Extension</td>
  <td valign="top">Select 'system' to have the system receive and email faxes.
  <br><br>
  The FreePBX default is defined in General Settings.</td>
 </tr>
 <tr valign="top">
  <td valign="top">33</td>
  <td valign="top">faxemail</td>
  <td valign="top"></td>
  <td valign="top"></td>
  <td valign="top">Fax Email</td>
  <td valign="top">Email address is used if 'system' has been chosen for the fax extension
  above.
  <br><br>
  Leave this blank to use the FreePBX default in General Settings.</td>
 </tr>
 <tr valign="top">
  <td valign="top">34</td>
  <td valign="top">answer</td>
  <td valign="top">0</td>
  <td valign="top">0-2</td>
  <td valign="top">Fax Detection Type</td>
  <td valign="top">Selecting Zaptel or NVFax will immediately answer the call and play
  ringing tones to the caller for the number of seconds in Pause below. Use
  NVFax on SIP or IAX trunks. 0 = None, 1 = Zaptel, 2 = NVFax.</td>
 </tr>
 <tr valign="top">
  <td valign="top">35</td>
  <td valign="top">wait</td>
  <td valign="top">0</td>
  <td valign="top"></td>
  <td valign="top">Pause after answer</td>
  <td valign="top">The number of seconds we should wait after performing an Immediate Answer.
  The primary purpose of this is to pause and listen for a fax tone before
  allowing the call to proceed. Default is 0.</td>
 </tr>
 <tr valign="top">
  <td valign="top">36</td>
  <td valign="top">privacyman</td>
  <td valign="top">0</td>
  <td valign="top">0, 1</td>
  <td valign="top">Privacy Manager</td>
  <td valign="top">If no Caller ID is sent, Privacy Manager asks the caller to enter their 10
  digit phone number. The caller is given 3 attempts. 0 = No, 1 = Yes.</td>
 </tr>
 <tr valign="top">
  <td valign="top">37</td>
  <td valign="top">record_in</td>
  <td valign="top">Adhoc</td>
  <td valign="top">Adhoc, Always, Never</td>
  <td valign="top">Record Incoming</td>
  <td valign="top">Record all inbound calls received at this extension.</td>
 </tr>
 <tr valign="top">
  <td valign="top">38</td>
  <td valign="top">record_out</td>
  <td valign="top">Adhoc</td>
  <td valign="top">Adhoc, Always, Never</td>
  <td valign="top">Record Outgoing</td>
  <td valign="top">Record all outbound calls received at this extension.</td>
 </tr>
 <tr valign="top">
  <td valign="top">39</td>
  <td valign="top">vm</td>
  <td valign="top">disabled</td>
  <td valign="top">enabled, disabled</td>
  <td valign="top">Status</td>
  <td valign="top">Set voicemail status for this user.</td>
 </tr>
 <tr valign="top">
  <td valign="top">40</td>
  <td valign="top">vmpwd</td>
  <td valign="top"></td>
  <td valign="top"></td>
  <td valign="top">Voicemail Password</td>
  <td valign="top">This is the password used to access the voicemail system.
  <br><br>
  This password can only contain numbers.
  <br><br>
  A user can change the password you enter here after logging into the voicemail
  system (*98) with a phone.</td>
 </tr>
 <tr valign="top">
  <td valign="top">41</td>
  <td valign="top">email</td>
  <td valign="top"></td>
  <td valign="top"></td>
  <td valign="top">Email Address</td>
  <td valign="top">The email address that voicemails are sent to.</td>
 </tr>
 <tr valign="top">
  <td valign="top">42</td>
  <td valign="top">pager</td>
  <td valign="top"></td>
  <td valign="top"></td>
  <td valign="top">Pager Email Address</td>
  <td valign="top">Pager/mobile email address to which short voicemail notifcations are
  sent.</td>
 </tr>
 <tr valign="top">
  <td valign="top">43</td>
  <td valign="top">attach</td>
  <td valign="top">attach=no</td>
  <td valign="top">attach=yes, attach=no</td>
  <td valign="top">Email Attachment</td>
  <td valign="top">Option to attach voicemails to email.</td>
 </tr>
 <tr valign="top">
  <td valign="top">44</td>
  <td valign="top">saycid</td>
  <td valign="top">saycid=no</td>
  <td valign="top">saycid=yes, saycid=no</td>
  <td valign="top">Play CID</td>
  <td valign="top">Read back caller's telephone number prior to playing the incoming message,
  and just after announcing the date and time the message was left.</td>
 </tr>
 <tr valign="top">
  <td valign="top">45</td>
  <td valign="top">envelope</td>
  <td valign="top">envelope=no</td>
  <td valign="top">envelope=yes, envelope=no</td>
  <td valign="top">Play Envelope</td>
  <td valign="top">Envelope controls whether or not the voicemail system will play the
  message envelope (date/time) before playing the voicemail message. This settng
  does not affect the operation of the envelope option in the advanced voicemail
  menu.</td>
 </tr>
 <tr valign="top">
  <td valign="top">46</td>
  <td valign="top">delete</td>
  <td valign="top">delete=no</td>
  <td valign="top">delete=yes, delete=no</td>
  <td valign="top">Delete Vmail</td>
  <td valign="top">If set to &quot;yes&quot; the message will be deleted from the
  voicemailbox (after having been emailed).  Provides functionality that allows
  a user to receive their voicemail via email alone, rather than having the
  voicemail able to be retrieved from the Web interface or the Extension
  handset. CAUTION: MUST HAVE attach voicemail to email SET TO YES OTHERWISE
  YOUR MESSAGES WILL BE LOST FOREVER.</td>
 </tr>
 <tr valign="top">
  <td valign="top">47</td>
  <td valign="top">options</td>
  <td valign="top"></td>
  <td valign="top"></td>
  <td valign="top">VM Options</td>
  <td valign="top">Separate options with pipe ( | )
  <br><br>ie: review=yes|maxmessage=60</td>
 </tr>
 <tr valign="top">
  <td valign="top">48</td>
  <td valign="top">vmcontext</td>
  <td valign="top">default</td>
  <td valign="top"></td>
  <td valign="top">VM Context</td>
  <td valign="top">This is the Voicemail Context which is normally set to default. Do not
  change unless you understand the implications.</td>
 </tr>
 <tr valign="top">
  <td valign="top">49</td>
  <td valign="top">vmx_state</td>
  <td valign="top"></td>
  <td valign="top">checked, (leave blank to disable)</td>
  <td valign="top">VmX Locater&trade;</td>
  <td valign="top">Enable/Disable the VmX Locater feature for this user. When enabled all
  settings are controlled by the user in the User Portal (ARI). Disabling will
  not delete any existing user settings but will disable access to the
  feature</td>
 </tr>
 <tr valign="top">
  <td valign="top">50</td>
  <td valign="top">devicetype</td>
  <td valign="top">fixed</td>
  <td valign="top">fixed, adhoc</td>
  <td valign="top"></td>
  <td valign="top">Extensions require that devicetype is fixed. If devicetype is adhoc,
  FreePBX must manage it in Device and Users mode, not Extensions mode.</td>
 </tr>
 <tr valign="top">
  <td valign="top">51</td>
  <td valign="top">password</td>
  <td valign="top"></td>
  <td valign="top"></td>
  <td valign="top">User Password</td>
  <td valign="top">A user will enter this password when logging onto a device.</td>
 </tr>
 <tr valign="top">
  <td valign="top">52</td>
  <td valign="top">noanswer</td>
  <td valign="top"></td>
  <td valign="top"></td>
  <td valign="top">Defaults to blank.</td>
 </tr>
 <tr valign="top">
  <td valign="top">53</td>
  <td valign="top">devinfo_immediate</td>
  <td valign="top">no</td>
  <td valign="top"></td>
  <td valign="top">immediate</td>
  <td valign="top">See zapata.conf file.</td>
 </tr>
 <tr valign="top">
  <td valign="top">54</td>
  <td valign="top">devinfo_signalling</td>
  <td valign="top">fxo_ks</td>
  <td valign="top"></td>
  <td valign="top">signalling</td>
  <td valign="top">See zapata.conf file.</td>
 </tr>
 <tr valign="top">
  <td valign="top">55</td>
  <td valign="top">devinfo_echocancel</td>
  <td valign="top">yes</td>
  <td valign="top"></td>
  <td valign="top">echocancel</td>
  <td valign="top">See zapata.conf file.</td>
 </tr>
 <tr valign="top">
  <td valign="top">56</td>
  <td valign="top">devinfo_echocancelwhenbridged</td>
  <td valign="top">no</td>
  <td valign="top"></td>
  <td valign="top">echocancelwhenbridged</td>
  <td valign="top">See zapata.conf file.</td>
 </tr>
 <tr valign="top">
  <td valign="top">57</td>
  <td valign="top">devinfo_echotraining</td>
  <td valign="top">800</td>
  <td valign="top"></td>
  <td valign="top">echotraining</td>
  <td valign="top">See zapata.conf file.</td>
 </tr>
 <tr valign="top">
  <td valign="top">58</td>
  <td valign="top">devinfo_busydetect</td>
  <td valign="top">no</td>
  <td valign="top"></td>
  <td valign="top">busydetect</td>
  <td valign="top">See zapata.conf file.</td>
 </tr>
 <tr valign="top">
  <td valign="top">59</td>
  <td valign="top">devinfo_busycount</td>
  <td valign="top">7</td>
  <td valign="top"></td>
  <td valign="top">busycount</td>
  <td valign="top">See zapata.conf file.</td>
 </tr>
 <tr valign="top">
  <td valign="top">60</td>
  <td valign="top">devinfo_callprogress</td>
  <td valign="top">no</td>
  <td valign="top"></td>
  <td valign="top">callprogress</td>
  <td valign="top">See zapata.conf file.</td>
 </tr>
 <tr valign="top">
  <td valign="top">61</td>
  <td valign="top">devinfo_notransfer</td>
  <td valign="top">yes</td>
  <td valign="top"></td>
  <td valign="top">notransfer</td>
  <td valign="top">See iax.conf file.</td>
 </tr>
</table>

<?php
}
?>

