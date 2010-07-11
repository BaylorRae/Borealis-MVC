<?php


class WelcomeController extends ApplicationController {
	
	function index() {
		
		$this->categories = Category::find(1);
		
		$this->renderText('hello');
	}
	
}

