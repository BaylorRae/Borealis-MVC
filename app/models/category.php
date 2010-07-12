<?php

class Category extends ActiveRecord\Model {
	
	static $has_many = array(
			array('articles')
		);
}

