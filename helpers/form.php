<?php

class FormHelper extends HelperBase {
	
	private $options = null;
	private $data = null;
		
	public function form_for($object, $name = null, $options = null) {
		
		// Special routes
		$special = array('add' => 'create', 'edit' => 'update');
							
		// Name of the form elements
		$name 	 = (empty($name)) ? get_class($object) : $name;
		$name 	 = strtolower($name);
		
		// Form and HTML options
		$options = (empty($options) || !is_array($options)) ? array() : $options;
		$options = (object) $options;
		
		// Save the DB object
		$this->data = $object;
				
		// Check for a controller
		if( !isset($options->controller) )
			$options->controller = $this->params['controller'];
		
		// Check for an action
		if( !isset($options->action) )
			$options->action = $this->params['action'];
			
		// Check if the action is special
		if( isset($special[$options->action]) )
			$options->action = $special[$options->action];
		
		// Use Post or Get
		if( !isset($options->method) )
			$options->method = 'post';
			
		// Set the form's name
		$options->name = $name;
			
		// Create the form tag
		echo '<form name="' . $options->name . '_form" method="' . $options->method . '" action="' . rtrim($this->config['ROOT'], '/') . '/' . $options->controller . '/' . $options->action . '">';
		
		// Store the options
		$this->options = $options;
		
		// Return the form
		return $this;
	}
	
	public function label($name, $text = null) {
		
		$text = (empty($text)) ? ucfirst($name) : $text;
		
		echo '<label for="' . $this->options->name . '[' . $name . ']">' . $text . '</label>';
				
	}
	
	public function textbox($name, $options = null) {
		$options = (empty($options) || !is_array($options)) ? array() : $options;
		
		if( isset($this->data->$name) )
			$options['value'] = $this->data->$name;
		
		$attributes = $this->attributes($name, $options);
					
		echo '<input type="text"' . $attributes . '/>';
	}
	
	public function password($name, $options = null) {
		$options = (empty($options) || !is_array($options)) ? array() : $options;
		
		if( isset($this->data->$name) )
			$options['value'] = $this->data->$name;
		
		$attributes = $this->attributes($name, $options);
				
		echo '<input type="password"' . $attributes . '/>';
	}

	public function textarea($name, $options = null) {
		$options = (empty($options) || !is_array($options)) ? array() : $options;
		
		if( isset($this->data->$name) )
			$options['value'] = $this->data->$name;
		
		$attributes = ' ';
				
		$options['name'] = $this->options->name . '[' . $name . ']';
		$options['id'] = $this->options->name . '[' . $name . ']';
		$options['value'] = (empty($options['value'])) ? null :$options['value'];
		
		// Create the html attributes
		foreach( $options as $prop => $value ) :
			if( $prop != 'value' )
				$attributes .= $prop . '="' . $value . '" ';
		endforeach;
				
		// echo '<input type="text"' . $attributes . '/>';
		echo '<textarea' . $attributes . '>' . $options['value'] . '</textarea>';
	}
	
	public function select($name, $data, $value, $text, $options = null) {
		$options = (empty($options) || !is_array($options)) ? array() : $options;
		
		if( isset($this->data->$name) )
			$options['value'] = $this->data->$name;
		
		$attributes = $this->attributes($name, $options);
		
		echo '<select' . $attributes . '>';
			foreach( $data as $row ) {
				if( $options['value'] == $row->$value )
					echo '<option selected="selected" value="' . $row->$value . '">' . $row->$text . '</option>';
				else
					echo '<option value="' . $row->$value . '">' . $row->$text . '</option>';
			}
		echo '</select>';
	}
	
	public function submit($text, $options = null) {
		$options = (empty($options) || !is_array($options)) ? array() : $options;
				
		$options['value'] = $text;
		
		$attributes = $this->attributes(null, $options);
		
		echo '<input type="submit"' . $attributes . ' />';
	}
	
	private function attributes($name, $options) {
		$attributes = ' ';
		$options = (empty($options) || !is_array($options)) ? array() : $options;
				
		if( !empty($name) ) {
			$options['name'] = $this->options->name . '[' . $name . ']';
			$options['id'] = $this->options->name . '[' . $name . ']';
		}
		
		// Create the html attributes
		foreach( $options as $prop => $value ) :
			$attributes .= $prop . '="' . $value . '" ';
		endforeach;
		
		return $attributes;
	}
	
	public function end_tag() {
		echo '</form>';
	}
	
	public function end_form_tag() {
		$this->end_tag();
	}
	
	
}
