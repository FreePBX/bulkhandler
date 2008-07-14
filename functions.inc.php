<?php
/* $Id:$ */

function exportextensions_allusers()
{

	global $db;
	$action = "edit";
	$fname = "bulkext__" .  (string) time() . ".csv";
	$csv_header = "action,extension,name,cid_masquerade,sipname,directdid,didalert,mohclass,outboundcid,ringtimer,callwaiting,emergency_cid,tech,hardware,devinfo_secret,devinfo_channel,devinfo_dtmfmode,devinfo_canreinvite,devinfo_context,devinfo_host,devinfo_type,devinfo_nat,devinfo_port,devinfo_qualify,devinfo_callgroup,devinfo_pickupgroup,devinfo_disallow,devinfo_allow,devinfo_dial,devinfo_accountcode,devinfo_mailbox,faxexten,faxemail,answer,wait,privacyman,record_in,record_out,vm,vmpwd,email,pager,attach,saycid,envelope,delete,options,vmcontext,vmx_state,devicetype,password,noanswer,devinfo_immediate,devinfo_signalling,devinfo_echocancel,devinfo_echocancelwhenbridged,devinfo_echotraining,devinfo_busydetect,devinfo_busycount,devinfo_callprogress,devinfo_notransfer\n";
	$data = $csv_header;
	$exts = get_all_exts();

	foreach($exts as $ext)
	{
		$e = $ext[0];
		$u_info = core_users_get($e);
		$d_info = core_devices_get($e);
		$voicemail = get_voicemail_info($e); 
		if($voicemail == null)
		{
			$v_enabled = "disabled";
			$v_context = '';
			$v_pwd = '';
			$v_email = '';
			$v_pager = '';
			$v_options = '';
			$v_attach = '';
			$v_saycid = '';
			$v_envelope = '';
			$v_delete = '';
		}
		else
		{
			$v_enabled = "enabled";
			$v_context = isset($voicemail['vmcontext'])?$voicemail['vmcontext']:'';
			$v_pwd = isset($voicemail['pwd'])?$voicemail['pwd']:'';
			$v_email = isset($voicemail['email'])?$voicemail['email']:'';
			$v_pager = isset($voicemail['pager'])?$voicemail['pager']:'';

			$x = isset($voicemail['options'])?$voicemail['options']:'';
			$p = 0;
			$first = true;
			$c = count($x);
			reset($x);

			while($p < $c)
			{
				if((key($x) != 'attach') && (key($x) != 'saycid') && (key($x) != 'envelope') && (key($x) != 'delete'))
				{
					if($first)
					{
						$vmopts_output = key($x) . "=" . $x[key($x)];
						$first = false;
					} else
					{
						$vmopts_output = $vmopts_output . "|" . key($x) . "=" . $x[key($x)];
					}
				}
				$p++;
				next($x);
			}
			$v_options = isset($vmopts_output)?$vmopts_output:'';
			$vmopts_output = "";			
			$v_attach = "attach=" . isset($x['attach'])?$x['attach']:'no'; 
			$v_saycid = "saycid=" . isset($x['saycid'])?$x['saycid']:'no';
			$v_envelope = "envelope=" . isset($x['envelope'])?$x['envelope']:'no';
			$v_delete = "delete=" . isset($x['delete'])?$x['delete']:'no';

		}

		$csv_line[0] 	= $action;
		$csv_line[1] 	= isset($u_info['extension'])?$u_info['extension']:'';
		$csv_line[2] 	= isset($u_info['name'])?$u_info['name']:'';
		$csv_line[3] 	= isset($u_info['cidnum'])?$u_info['cidnum']:'';
		$csv_line[4] 	= isset($u_info['sipname'])?$u_info['sipname']:'';
		$csv_line[5] 	= isset($u_info['directdid'])?$u_info['directdid']:'';
		$csv_line[6] 	= isset($u_info['didalert'])?$u_info['didalert']:'';
		$csv_line[7] 	= isset($u_info['mohclass'])?$u_info['mohclass']:'';
		$csv_line[8] 	= isset($u_info['outboundcid'])?$u_info['outboundcid']:'';
		$csv_line[9] 	= isset($u_info['ringtimer'])?$u_info['ringtimer']:'';
		$csv_line[10]	= isset($u_info['callwaiting'])?$u_info['callwaiting']:'';
		$csv_line[11]	= isset($d_info['emergency_cid'])?$d_info['emergency_cid']:'';
		$csv_line[12]	= isset($d_info['tech'])?$d_info['tech']:'';
		$csv_line[13]	= ''; //hardware
		$csv_line[14]	= isset($d_info['secret'])?$d_info['secret']:'';
		$csv_line[15]	= isset($d_info['channel'])?$d_info['channel']:'';   
		$csv_line[16]	= isset($d_info['dtmfmode'])?$d_info['dtmfmode']:'';
		$csv_line[17]	= isset($d_info['canreinvite'])?$d_info['canreinvite']:'';
		$csv_line[18]	= isset($d_info['context'])?$d_info['context']:'';
		$csv_line[19]	= isset($d_info['host'])?$d_info['host']:'';
		$csv_line[20]	= isset($d_info['type'])?$d_info['type']:'';
		$csv_line[21]	= isset($d_info['nat'])?$d_info['nat']:'';
		$csv_line[22]	= isset($d_info['port'])?$d_info['port']:'';
		$csv_line[23]	= isset($d_info['qualify'])?$d_info['qualify']:'';
		$csv_line[24]	= isset($d_info['callgroup'])?$d_info['callgroup']:'';
		$csv_line[25]	= isset($d_info['pickupgroup'])?$d_info['pickupgroup']:'';
		$csv_line[26]	= isset($d_info['disallow'])?$d_info['disallow']:'';
		$csv_line[27]	= isset($d_info['allow'])?$d_info['allow']:'';
		$csv_line[28]	= isset($d_info['accountcode'])?$d_info['accountcode']:'';
		$csv_line[28]	= isset($d_info['dial'])?$d_info['dial']:'';
		$csv_line[29] 	= isset($d_info['accountcode'])?$d_info['accountcode']:'';
		$csv_line[30]	= isset($d_info['mailbox'])?$d_info['mailbox']:'';
		$csv_line[31]	= isset($u_info['faxexten'])?$u_info['faxexten']:'';
		$csv_line[32]	= isset($u_info['faxemail'])?$u_info['faxemail']:'';
		$csv_line[33]	= isset($u_info['answer'])?$u_info['answer']:'';
		$csv_line[34]	= isset($u_info['wait'])?$u_info['wait']:'';
		$csv_line[35]	= isset($u_info['privacyman'])?$u_info['privacyman']:'';
		$csv_line[36]	= isset($d_info['record_in'])?$d_info['record_in']:'';
		$csv_line[37]	= isset($d_info['record_out'])?$d_info['record_out']:'';
		$csv_line[38]	= $v_enabled;
		$csv_line[39]	= $v_pwd;
		$csv_line[40]	= $v_email;
		$csv_line[41]	= $v_pager;
		$csv_line[42]	= $v_attach;
		$csv_line[43]	= $v_saycid;
		$csv_line[44]	= $v_envelope;
		$csv_line[45]	= $v_delete;
		$csv_line[46]	= $v_options;
		$csv_line[47]	= $v_context;
		$csv_line[48]	= isset($u_info['vmx_state'])?$u_info['vmx_state']:'';
		$csv_line[49]	= isset($d_info['devicetype'])?$d_info['devicetype']:'';
		$csv_line[50]	= isset($u_info['password'])?$u_info['password']:'';
		$csv_line[51]	= isset($u_info['noanswer'])?$u_info['noanswer']:'';
		$csv_line[52]	= isset($d_info['immediate'])?$d_info['immediate']:'';
		$csv_line[53]	= isset($d_info['signalling'])?$d_info['signalling']:'';
		$csv_line[54]	= isset($d_info['echocancel'])?$d_info['echocancel']:'';
		$csv_line[55]	= isset($d_info['echocancelwhenbridged'])?$d_info['echocancelwhenbridged']:'';
		$csv_line[56]	= isset($d_info['echotraining'])?$d_info['echotraining']:'';
		$csv_line[57]	= isset($d_info['busydetect'])?$d_info['busydetect']:'';
		$csv_line[58]	= isset($d_info['busycount'])?$d_info['busycount']:'';
		$csv_line[59]	= isset($d_info['callprogress'])?$d_info['callprogress']:'';
		$csv_line[60]	= isset($d_info['notransfer'])?$d_info['notransfer']:'';

		for($i = 0; $i < count($csv_line); $i++)
		{
			if($i != count($csv_line) - 1)
			{
				$data = $data . $csv_line[$i] . ",";
			} else
			{
				$data = $data . $csv_line[$i];
			}
				
		}
		$data = $data . "\n";

		unset($csv_line);

	}

	force_download($data, $fname);
	return;
}

function get_all_exts()
{
	$sql = "SELECT extension FROM users ORDER BY extension";
	
	$extens = sql($sql,"getAll");

	if (isset($extens))
	{
		return $extens;
	} else
	{
		return null;
	}
}

function get_voicemail_info($mbox)
{
	global $amp_conf;
	
	$vmconf = null;
	$section = null;
	my_parse_voicemailconf(rtrim($amp_conf["ASTETCDIR"],"/")."/voicemail.conf", $vmconf, $section);
	if($vmconf == null) echo("Uh-oh");
	//my_parse_voicemailconf("/etc/asterisk/voicemail.conf", $vmconf, $section);
	//$uservm = voicemail_getVoicemail();
        $vmcontexts = array_keys($vmconf);
        
        foreach ($vmcontexts as $vmcontext) {
		//echo("$vmcontext<br/>");
		//echo("$mbox<br/>");
                if(isset($vmconf[$vmcontext][$mbox])){
                        $vmbox['vmcontext'] = $vmcontext;                                                                                
                        $vmbox['pwd'] =		$vmconf[$vmcontext][$mbox]['pwd'];
                        $vmbox['name'] =	$vmconf[$vmcontext][$mbox]['name'];
                        $vmbox['email'] =	$vmconf[$vmcontext][$mbox]['email'];
                        $vmbox['pager'] =	$vmconf[$vmcontext][$mbox]['pager'];
                        $vmbox['options'] =	$vmconf[$vmcontext][$mbox]['options'];
                        return $vmbox;
                }
        }
                                                                            
        return null;


}


/** Recursively read voicemail.conf (and any included files)                    
 * This function is called by get_voicemail_info()
 */
function my_parse_voicemailconf($filename, &$vmconf, &$section) {
        if (is_null($vmconf)) {
                $vmconf = array();
        }
        if (is_null($section)) {
                $section = "general";
        }

        if (file_exists($filename)) {                                      
                $fd = fopen($filename, "r");                       
                while ($line = fgets($fd, 1024)) {
                        if (preg_match("/^\s*(\d+)\s*=>\s*(\d*),(.*),(.*),(.*),(.*)\s*([;#].*)?/",$line,$matches)) {
                                // "mailbox=>password,name,email,pager,options"
                                // this is a voicemail line
                                $vmconf[$section][ $matches[1] ] = array("mailbox"=>$matches[1],
                                                                        "pwd"=>$matches[2],
                                                                        "name"=>$matches[3],
                                                                        "email"=>$matches[4],
                                                                        "pager"=>$matches[5],
                                                                        "options"=>array(),
                                                                        );

                                // parse options
                                                                           
                                foreach (explode("|",$matches[6]) as $opt) {
                                        $temp = explode("=",$opt);
                                        //output($temp);
                                        if (isset($temp[1])) {
                                                list($key,$value) = $temp;
                                                $vmconf[$section][ $matches[1] ]["options"][$key] = $value;
                                        }
                                }
                   } else if (preg_match("/^\s*(\d+)\s*=>\s*dup,(.*)\s*([;#].*)?/",$line,$matches)) {
                                // "mailbox=>dup,name"
                                // duplace name line
                                $vmconf[$section][ $matches[1] ]["dups"][] = $matches[2];
                        } else if (preg_match("/^\s*#include\s+(.*)\s*([;#].*)?/",$line,$matches)) {
                                // include another file

                                if ($matches[1][0] == "/") {
                                        // absolute path
                                        $filename = $matches[1];
                                } else {
                                        // relative path
                                        $filename =  dirname($filename)."/".$matches[1];
                                }

                                my_parse_voicemailconf($filename, $vmconf, $section);

                        } else if (preg_match("/^\s*\[(.+)\]/",$line,$matches)) {
                                // section name
                                $section = strtolower($matches[1]);
                        } else if (preg_match("/^\s*([a-zA-Z0-9-_]+)\s*=\s*(.*?)\s*([;#].*)?$/",$line,$matches)) {
                                // name = value
                                // option line
                                $vmconf[$section][ $matches[1] ] = $matches[2];
                        }
                }
                fclose($fd);                              
        }
}

function force_download ($data, $name, $mimetype='', $filesize=false) { 
    // File size not set? 
    if ($filesize == false OR !is_numeric($filesize)) { 
        $filesize = strlen($data); 
    } 

    // Mimetype not set? 
    if (empty($mimetype)) { 
        $mimetype = 'application/octet-stream'; 
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

?>

