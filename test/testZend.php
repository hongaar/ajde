<?php

class TestOfZend extends UnitTestCase {

	function testZendDate() {	
		$date = new Zend_Date();		
		$this->assertIsA($date, 'Zend_Date');
	}
		
}