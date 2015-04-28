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
		if($_REQUEST['quietmode']) {
			$this->export($_REQUEST['export']);
		} else {
			$type = (!empty($_REQUEST['type']) && $_REQUEST['type'] == 'export') ? 'export' : 'import';
			switch($type) {
				case "export":
					return load_view(__DIR__."/views/export.php",array("typed" => $type, "types" => $this->getTypes($type)));
				break;
				case "import":
				default:
					return load_view(__DIR__."/views/import.php",array("typed" => $type, "types" => $this->getTypes($type)));
				break;
			}
		}

	}

	private function fileToArray($file) {
		$rawData = array();

		return $rawData;
	}

	private function arrayToFile($rawData, $type) {
		return $file;
	}

	public function getActionBar($request) {
		$buttons = array();
		switch($request['display']) {
			case 'bulkhandler':
				$buttons = array(
					'reset' => array(
						'name' => 'reset',
						'id' => 'reset',
						'value' => _('Reset')
					),
					'submit' => array(
						'name' => 'submit',
						'id' => 'submit',
						'value' => _('Submit')
					)
				);
			break;
		}
		return $buttons;
	}

	public function getTypes($type='import') {
		$modules = $this->freepbx->Hooks->processHooks();
		$types = array();
		foreach($modules as $k => $module) {
			switch($type) {
				case "import":
					foreach($module as $el) {
						foreach($el as $type => $name) {
							dbug($name);
							$types[$k."-".$type] = array(
								"name" => $name,
								"mod" => $k,
								"type" => $type
							);
						}
					}
				break;
				case "export":
					foreach($module as $el) {
						foreach($el as $type => $name) {
							$types[$k."-".$type] = array(
								"name" => $name,
								"mod" => $k,
								"type" => $type
							);
						}
					}
				break;
			}
		}
		return $types;
	}

	public function import($type, $rawData) {
		$modules = $this->freepbx->Hooks->processHooks($type, $rawData);
		foreach($modules as $module) {

		}
	}

	public function export($type) {
		$time_start = microtime(true);
		$modules = $this->freepbx->Hooks->processHooks($type);
		$rows = array();
		$headers = array();
		$row = 0;
		foreach($modules as $module) {
			if(!empty($module)) {}
			foreach($module as $items) {
				$currentheaders = array_keys($items);
				$headers = array_merge($headers,array_combine($currentheaders, $currentheaders));
			}
		}
		$headers = array_keys($headers);
		foreach($modules as $module) {
			if(!empty($module)) {}
			foreach($module as $items) {
				$rows[$row] = array_fill(0, count($headers), "");
				foreach($items as $key => $value) {
					$d = array_search($key,$headers);
					$rows[$row][$d] = $value;
				}
				$row++;
			}
		}

		$out = fopen('php://output', 'w');
		header('Content-type: application/octet-stream');
		header('Content-Disposition: attachment; filename="export.csv"');
		fputcsv($out,$headers);
		$headersc = count($headers);
		foreach($rows as $row) {
			fputcsv($out,  $row);
		}
		fclose($out);
		dbug('Total execution time in seconds: ' . (microtime(true) - $time_start));
	}

	public function validate($type, $rawData) {
		$modules = $this->freepbx->Hooks->processHooks($type, $rawData);
		foreach($modules as $module) {

		}
	}
}
