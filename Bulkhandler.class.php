<?php
// vim: set ai ts=4 sw=4 ft=php:
namespace FreePBX\modules;
class Bulkhandler implements \BMO {
	public function __construct($freepbx = null) {
		if ($freepbx == null) {
			throw new \Exception("Not given a FreePBX Object");
		}
		$this->freepbx = $freepbx;
		$this->db = $freepbx->Database;
	}

	public function doConfigPageInit($page) {
	}

	public function install() {

	}
	public function uninstall() {

	}
	public function backup(){

	}
	public function restore($backup){
	}

	public function showPage() {
		if(!empty($_REQUEST['quietmode']) && $_REQUEST['activity'] == 'export') {
			$this->export($_REQUEST['export']);
		} else {
			$activity = (!empty($_REQUEST['activity']) && $_REQUEST['activity'] == 'import') ? 'import' : 'export';
			$message = '';
			switch($activity) {
				case "import":
					if(!empty($_FILES)) {
						$ret = $this->uploadFile();
						if(!$ret['status']) {
							$message = $ret['message'];
						} else {
							try {
								$array = $this->fileToArray($ret['localfilename'],$ret['extension']);
								return load_view(__DIR__."/views/validate.php",array("type" => $_POST['type'], "activity" => $activity, "imports" => $array, "headers" => $this->getHeaders($_REQUEST['type'])));
							} catch(\Exception $e) {
								$message = $e->getMessage();
							}
						}

					}
					return load_view(__DIR__."/views/import.php",array("message" => $message, "activity" => $activity, "types" => $this->getTypes($activity)));
				break;
				case "export":
				default:
					return load_view(__DIR__."/views/export.php",array("message" => $message, "activity" => $activity, "types" => $this->getTypes($activity)));
				break;
			}
		}

	}

	private function uploadFile() {
		$temp = sys_get_temp_dir() . "/bhimports";
		if(!file_exists($temp)) {
			if(!mkdir($temp)) {
				return array("status" => false, "message" => sprintf(_("Cant Create Temp Directory: %s"),$temp));
			}
		}
		$error = $_FILES["import"]["error"];
		switch($error) {
			case UPLOAD_ERR_OK:
				$extension = pathinfo($_FILES["import"]["name"], PATHINFO_EXTENSION);
				$extension = strtolower($extension);
				if($extension == 'csv') {
					$tmp_name = $_FILES["import"]["tmp_name"];
					$dname = $_FILES["import"]["name"];
					$id = time();
					$name = pathinfo($_FILES["import"]["name"],PATHINFO_FILENAME) . '-' . $id . '.' . $extension;
					move_uploaded_file($tmp_name, $temp."/".$name);
					if(!file_exists($temp."/".$name)) {
						return array("status" => false, "message" => _("Cant find uploaded file"), "localfilename" => $temp."/".$name);
					}
					return array("status" => true, "filename" => $dname, "localfilename" => $temp."/".$name, "id" => $id, "extension" => $extension);
				} else {
					return array("status" => false, "message" => _("Unsupported file format"));
					break;
				}
			break;
			case UPLOAD_ERR_INI_SIZE:
				return array("status" => false, "message" => _("The uploaded file exceeds the upload_max_filesize directive in php.ini"));
			break;
			case UPLOAD_ERR_FORM_SIZE:
				return array("status" => false, "message" => _("The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form"));
			break;
			case UPLOAD_ERR_PARTIAL:
				return array("status" => false, "message" => _("The uploaded file was only partially uploaded"));
			break;
			case UPLOAD_ERR_NO_FILE:
				return array("status" => false, "message" => _("No file was uploaded"));
			break;
			case UPLOAD_ERR_NO_TMP_DIR:
				return array("status" => false, "message" => _("Missing a temporary folder"));
			break;
			case UPLOAD_ERR_CANT_WRITE:
				return array("status" => false, "message" => _("Failed to write file to disk"));
			break;
			case UPLOAD_ERR_EXTENSION:
				return array("status" => false, "message" => _("A PHP extension stopped the file upload"));
			break;
		}
		return array("status" => false, "message" => _("Can Not Find Uploaded Files"));
	}

	private function fileToArray($file, $format='csv') {
		$rawData = array();
		switch($format) {
			case 'csv':
				$header = null;
				ini_set("auto_detect_line_endings", true);
				$handle = fopen($file, "r");
				//http://php.net/manual/en/filesystem.configuration.php#ini.auto-detect-line-endings

				while ($row = fgetcsv($handle)) {
					if ($header === null) {
						$header = $row;
						continue;
					}
					$rawData[] = array_combine($header, $row);
				}
			break;
			default:
				throw new \Exception(_("Unsupported file format"));
			break;
		}
		return $rawData;
	}

	private function arrayToFile($rawData, $type, $format='csv') {
		switch($format) {
			case 'csv':
			default:
				$filename = ($type ? $type : 'export') . '.csv';
				$out = fopen('php://output', 'w');
				header('Content-type: application/octet-stream');
				header('Content-Disposition: attachment; filename="' . $filename . '"');
				foreach($rawData as $row) {
					fputcsv($out,  $row);
				}
				fclose($out);
			break;
		}
	}

	public function getHeaders($type) {
		$headers = array();

		$modules = $this->freepbx->Hooks->processHooks($type);
		foreach ($modules as $key => $module) {
			if ($module) {
				$headers = array_merge($headers, $module);
			}
		}

		return $headers;
	}

	public function getTypes($activity='import') {
		$modules = $this->freepbx->Hooks->processHooks();
		$types = array();
		foreach($modules as $key => $module) {
			switch($activity) {
				case "import":
					foreach($module as $type => $name) {
						if(!isset($types[$key."-".$type])) {
							$types[$key."-".$type] = array(
								"name" => $name['name'],
								"description" => $name['description'],
								"mod" => $key,
								"type" => $type,
								"active" => (count($types) == 0),
								"headers" => $this->getHeaders($type)
							);
						}
					}
				break;
				case "export":
					foreach($module as $type => $name) {
						if(!isset($types[$key."-".$type])) {
							$types[$key."-".$type] = array(
								"name" => $name['name'],
								"description" => $name['description'],
								"mod" => $key,
								"type" => $type,
								"active" => (count($types) == 0)
							);
						}
					}
				break;
			}
		}
		return $types;
	}

	public function ajaxRequest($req, &$setting) {
		return true;
	}

	public function ajaxHandler() {
		$ret = array("status" => true);
		switch ($_REQUEST['command']) {
			case "import":
				$ret = $this->import($_POST['type'], array($_POST['imports']));
			break;
		}
		return $ret;
	}

	public function import($type, $rawData) {
		try {
			$methods = $this->freepbx->Hooks->returnHooks();
		} catch(\Exception $e) {
			return array("status" => false, "message" => $e->getMessage());
		}
		foreach($methods as $method) {
			$mod = $method['module'];
			$meth = $method['method'];
			$ret = \FreePBX::$mod()->$meth($type, $rawData);
			if($ret['status'] === false) {
				return array("status" => false, "message" => "There was an error in ".$mod.", message:".$ret['message']);
			}
		}
		return array("status" => true);
	}

	public function export($type) {
		$time_start = microtime(true);
		$modules = $this->freepbx->Hooks->processHooks($type);
		$rows = array();
		$headers = array();
		foreach($modules as $key => $module) {
			if(empty($module)) {
				continue;
			}
			foreach($module as $items) {
				foreach(array_keys($items) as $h) {
					if(!in_array($h,$headers)) {
						$headers[] = $h;
					}
				}
			}
		}

		foreach($modules as $module) {
			if(empty($module)) {
				continue;
			}
			foreach($module as $id => $items) {
				if(empty($rows[$id])) {
					$rows[$id] = array_fill(0, count($headers), "");
				}
				foreach($items as $key => $value) {
					$d = array_search($key,$headers);
					$rows[$id][$d] = $value;
				}
			}
		}
		array_unshift($rows,$headers);
		//dbug('Total execution time in seconds: ' . (microtime(true) - $time_start));
		$this->arrayToFile($rows, $type, 'csv');
	}

	public function validate($type, $rawData) {
		$modules = $this->freepbx->Hooks->processHooks($type, $rawData);
		foreach($modules as $module) {

		}
	}
}
