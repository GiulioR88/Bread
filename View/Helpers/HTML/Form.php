<?php
/**
 * Bread PHP Framework (http://github.com/saiv/Bread)
 * Copyright 2010-2012, SAIV Development Team <development@saiv.it>
 *
 * Licensed under a Creative Commons Attribution 3.0 Unported License.
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright  Copyright 2010-2012, SAIV Development Team <development@saiv.it>
 * @link       http://github.com/saiv/Bread Bread PHP Framework
 * @package    Bread
 * @since      Bread PHP Framework
 * @license    http://creativecommons.org/licenses/by/3.0/
 */

namespace Bread\View\Helpers\HTML;

class Form extends Node {
	public function __construct(Page $page) {
		parent::__construct($page, 'form');
		$this->addClass('form-horizontal');
	}
	/**
	 * Add a new <input name=$args[0]['name'] type=$method  value=$args[0]['value'] id=$args[0]['id'] 
	 * element into the form with the passed name,label,id,value and type.
	 * Magic method : $method is the name of the method called and $args is an array with the param.
	 * passed to the called method.
	 */
	/*
	public function __call($method, $args = Array()) {
	  $controlGroup = $this->append('<div></div>');
	  $controlGroup->addClass('control-group');
	  if ($method == 'checkbox' or $method == 'radio') {
	    $controls = $controlGroup->append('<div></div>');
	    $controls->addClass('controls');
	    $label = $controls->append('<label></label>')->text($args[1]);
	    $label->addClass($method); // $method= radio or checkbox
	    $input = $label->append('<input/>');
	  } else {
	    $label = $controlGroup->append('<label></label>')->text($args[1]);
	    $label->addClass('control-label');
	    $controls = $controlGroup->append('<div></div>');
	    $controls->addClass('controls');
	    $input = $controls->append('<input/>');
	
	  }
	  $input->attr(array('name' => $args[0], 'type' => $method, 'value' => $args[2]));
	  if ($args[3]!=null)
	    $input->id = $args[3];
	  $label->attr(array('for' => $input->id))
	  return $input;
	}*/
	public function __call($method, $args = array()) {

		$controlGroup = $this->append('<div></div>');
		$controlGroup->addClass('control-group');
		if ($method == 'checkbox' or $method == 'radio') {
			$controls = $controlGroup->append('<div></div>');
			$controls->addClass('controls');
			$label = $controls->append('<label></label>')->text($args[0]['label']);
			$label->addClass($method); // $method= radio or checkbox
			$input = $label->append('<input/>');
		} else {
			$label = $controlGroup->append('<label></label>')->text($args[0]['label']);
			$label->addClass('control-label');
			$controls = $controlGroup->append('<div></div>');
			$controls->addClass('controls');
			$input = $controls->append('<input/>');

		}

		$input->attr(array('name' => $args[0]['name'], 'type' => $method, 'value' => $args[0]['value']));
		$input->id = $args[0]['id'];
		$label->attr(array('for' => $input->id));

		return $input;

	}

	/**
	 * Call the function __call() for input type=text
	 */ 
	public function text($text) {
		return $this->__call(__FUNCTION__, func_get_args());
	}

	/**
	 * Add a new <button type=$type name=$button> with the given name
	 */
	public function button($name, $type) {
		$controlGroup = $this->append('<div></div>');
		$controlGroup->addClass('control-group');
		$controls = $controlGroup->append('<div></div>');
		$controls->addClass('controls');
		$btn = $controls->append('<button></button>')->text($name);
		$btn->addClass('btn');
		$btn->attr(array('name' => $name, 'type' => $type));

		return $btn;
	}

	/**
	 * Add a new <textarea rows=$rows> with the number of rows passed to it
	 */
	public function textArea($name, $text, $rows = null) {
		$controlGroup = $this->append('<div></div>');
		$controlGroup->addCLass('control-group');
		$controlLabel = $controlGroup->append('<label></label>')->text($text);
		$controlLabel->addClass('control-label');
		$controls = $controlGroup->append('<div></div>');
		$controls->addClass('controls');
		$textarea = $controls->append('<textarea></textarea>')->attr(array('name' => $name, 'rows' => $rows));
		$textarea->id = $text;
		$controlLabel->attr(array('for' => $textarea->id));
		return $textarea;
	}

	/**
	 * Add a new <select> with the options passed to it like a key=>value array
	 */
	public function select($label, $sel = Array()) {
		$controlGroup = $this->append('<div></div>');
		$controlGroup->addClass('control-group');
		$label = $controlGroup->append('<label></label>')->text($label);
		$label->addClass('control-label');
		$controls = $controlGroup->append('<div></div>');
		$controls->addClass('controls');
		$select = $controls->append('<select></select>');
		foreach ($sel as $key => $op) {
			$option = $select->append('<option></option>')->text($op);
			$option->attr(array('value' => $key));
		}

	}

}

