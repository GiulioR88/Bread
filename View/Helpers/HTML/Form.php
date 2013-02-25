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
	 * Add a new <input name type value id > element into the form with the passed 
	 * name and the optional array $option=[labe,id,value].
	 */
	public function __call($method, $args = array()) {
		$name = array_shift($args);
		$option = array_shift($args);
		$controlGroup = $this->append('<div></div>');
		$controlGroup->addClass('control-group');
		if ($method == 'checkbox' or $method == 'radio') {
			$controls = $controlGroup->append('<div></div>');
			$controls->addClass('controls');
			$label = $controls->append('<label></label>');
			if (array_key_exists('label', $option)){
				$label->text($option['label']);
			}
			$label->addClass($method); // $method= radio or checkbox
			$input = $label->append('<input/>');
		} else {
			$label = $controlGroup->append('<label></label>');
			if (array_key_exists('label', $option))
				$label->text($option['label']);
			$label->addClass('control-label');
			$controls = $controlGroup->append('<div></div>');
			$controls->addClass('controls');
			$input = $controls->append('<input/>');

		}

		$input->attr(array('name' => $name, 'type' => $method));
		if (array_key_exists('value', $option))
			$input->attr(array('value' => $option['value']));
		if (array_key_exists('id',$option)) {
			$input->id = $option['id'];
			$label->attr(array('for' => $input->id));
		}
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

	/**
	 * Search if an attribute $value exist in $arr 
	 * replaced by array_key_exist()
	 */
	public function search($arr = array(), $value) {

		if ($arr) {
			foreach ($arr as $key => $val) {
				if ($key == $value)
					return true;
			}
		}
	}

}

