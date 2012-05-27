<?php

class TestOfPhtmlParser extends UnitTestCase {

	function setUp() {
		$this->empty = new Ajde_Template('test/phtml/', 'empty'); 	
		$this->include = new Ajde_Template('test/phtml/', 'include');
	}
	
	function testCreateParser() {	
		$this->assertIsA($this->empty->getParser(), 'Ajde_Template_Parser_Phtml');
	}
	
	function testParseEmptyTemplate() {
		$result = trim($this->empty->getParser()->parse());
		$this->assertTrue(empty($result));
	}
	
	function testParseIncludeTemplate() {
		$result = trim($this->include->getParser()->parse());
		$this->assertTrue($result === "<p>You're viewing the default homepage.</p>");
	}
	
}