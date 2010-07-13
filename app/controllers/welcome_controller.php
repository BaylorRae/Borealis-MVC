<?php


class WelcomeController extends ApplicationController {
	
	
	function index() {
		
		$this->categories = Category::find('all');
		
	}
	
	function show() {
		if( $this->params('id') ) {
			$this->category = Category::find($this->params('id'));
			$this->renderView($this, 'show', 'json');
		}else
			$this->renderAction($this, 'index');
	}
	
}

