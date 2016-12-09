<?php

/**
* https://blogs.kent.ac.uk/webdev/2011/07/14/phpunit-and-unserialized-pdo-instances/
* @backupGlobals disabled
*/

class bulkHandlerCSVTests extends PHPUnit_Framework_TestCase{

		protected static $f;
		protected static $o;
		protected static $module = 'Bulkhandler';

		//Change Moduleclass to your class name
		public static function setUpBeforeClass() {
				include 'setuptests.php';
				self::$f = FreePBX::create();
				self::$o = self::$f->Bulkhandler;
		}

		public function setup() {}

		public function testPHPUnit() {
				$this->assertEquals("test", "test", "PHPUnit is broken.");
				$this->assertNotEquals("test", "nottest", "PHPUnit is broken.");
		}

		public function testCreate() {
				$this->assertTrue(is_object(self::$o), sprintf("Did not get a %s object",self::$module));
		}

		public function testFiletoArray(){
			$expect = json_decode(file_get_contents(__DIR__.'/filetoarray.json'),true);
			$actual = self::$o->fileToArray(__DIR__.'/extensions.csv');
			$this->assertEquals($expect, $actual, "filetoarray did NOT return as expected");
		}
		/**
		 * @expectedException				Exception
		 * @expectedExceptionMessage Unable to parse file
		 */
		public function testFiletoArrayBadFormat(){
			$actual = self::$o->fileToArray(__DIR__.'/filetoarray.json');
		}
}
