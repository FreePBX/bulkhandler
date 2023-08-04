<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<div class="header-section">
	<h1 class="header"><?php echo _("Data Importing")?></h1>
	<div class="progress hidden">
		<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
			<span  id="myspan" class="is_scalar">0% <?php echo _("Complete")?></span>
		</div>
	</div>
</div>
<div class="alert alert-success hidden">
    <a href="#" class="close" data-dismiss="alert">&times;</a>
    <?php echo sprintf(_("<strong>Success! </strong>%s Data Imported successfully."), $totalnows)?>
</div>

<div class="alert alert-info hidden">
    <a href="#" class="close" data-dismiss="alert">&times;</a>
    <span  id="insertspan" class="is_scalar"></span><br>
    <span  id="updatespan" class="is_scalar"></span><br>
    <span  id="baddata" class="is_scalar"></span>
</div>

<input type="hidden" name="direct_import" id="direct_import" value="yes">
<input type="hidden" name="num_rows" id="num_rows" value="<?php echo $totalnows;?>">

<a href="?display=bulkhandler" class="btn btn-primary" align="right"><?php echo _("Go Back")?></a>
<?php
//to get the progress we need file with details 
//This file will be handled by th module 
// js will request and get the status  of the progress
$time = time();
$temp = sys_get_temp_dir() . "/bhimports";
$tempfile = $temp."/Bulkhandler_".$time;
$handle = fopen($tempfile, "w");
fwrite($handle, "COUNT=0");
fclose($handle);
echo "<input type='hidden' name='temp_file' id='temp_file' value='$tempfile'>";

$freepbx = FreePBX::Create();
$root = $freepbx->Config->get("AMPWEBROOT");
$array['tempfile'] = $tempfile;
$array['localfilename'] = $localfilename;
$array['extension'] = $extension;
$array['filename'] = $filename;
$array['customfields'] = $customfields;
$array['header'] = $headers;
$array['request'] = $request;

$arg = escapeshellarg(base64_encode(json_encode($array, JSON_THROW_ON_ERROR)));
 
dbug("SCRIPT: php ".$root."/admin/modules/bulkhandler/bulkimportprocess.php ".$arg." > /dev/null 2>&1 &", 0);
exec("php ".$root."/admin/modules/bulkhandler/bulkimportprocess.php ".$arg." > /dev/null 2>&1 &");
