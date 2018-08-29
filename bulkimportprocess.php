<?php
if (function_exists('proc_nice')) {
	@proc_nice(10);
}
$bootstrap_settings['include_compress'] = false;
$restrict_mods = array('bulkhandler' => true);
if (!@include_once(getenv('FREEPBX_CONF') ? getenv('FREEPBX_CONF') : '/etc/freepbx.conf')) {
	include_once('/etc/asterisk/freepbx.conf');
}
$freepbx = FreePBX::Create();

$json = json_decode(base64_decode($argv[1]),true);
extract($json);
$bh = $freepbx->Bulkhandler();
foreach ($json['header'] as $key => $header) { 
		if (isset($header['identifier']) && $header['identifier']) { 
			$identifiers[] = $key;		
		} 
	} 
	$eachrow = array();
	$dataarray = array();
	$array = $bh->fileToArray($json['localfilename'],$json['extension']);
	$arraynew = array();
	if(!is_array($json['customfields'])){
		$customf = array();
	}else {
		$customf = $json['customfields'];
	}
	foreach ($array as $key => $value) {
		$row = array();
		foreach($value as $fkey => $val){
			 $fkey = $bh->removeBomUtf8($fkey);
			 if (array_key_exists($fkey,$customf)){
			 //if any value is there in csv we dont want to override
				$row[$fkey] = $val?$val:$customf[$fkey];
			}else {
				$row[$fkey] = $val;
			}
		}
		$arraynew[$key] = $row;
	}
	//adding uploaded file name to the request
	$request['dbfilename'] = $filename;
	//lets create a temp file to read the status from module 
	$bh->direct_import($request['type'], $arraynew, $request,$tempfile);
exit();
