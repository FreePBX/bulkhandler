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

	public function getTypes() {
		$modules = $this->freepbx->Hooks->processHooks();
		foreach($modules as $module) {
		}
	}

	public function import($type) {
		$modules = $this->freepbx->Hooks->processHooks($type, $rawData);
		foreach($modules as $module) {

		}
	}

	public function export($type) {
		$modules = $this->freepbx->Hooks->processHooks($type);
		foreach($modules as $module) {

		}
	}

	public function validate($type) {
		$modules = $this->freepbx->Hooks->processHooks($type, $rawData);
		foreach($modules as $module) {

		}
	}
}
