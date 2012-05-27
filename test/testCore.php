<?php

class TestOfAjdeCore extends UnitTestCase {

	function testCreateApp(){
		$app = Ajde_Application::create();
		$this->assertIsA($app, 'Ajde_Application');
	}
	
}