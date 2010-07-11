<?php


class WelcomeController extends ApplicationController {
	
	function index() {
		
		$this->title = 'Hello World';
		$this->categories = Category::find(1);
		
	}
	
}

