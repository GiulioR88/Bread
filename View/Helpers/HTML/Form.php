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

		if ($method == 'checkbox' or $method == 'radio') {
			$controlGroup = $this->append('div');
			$controlGroup->addClass('control-group');
			$controls = $controlGroup->append('div');
			$controls->addClass('controls');
			$labe = $controls->append('label');

			if (array_key_exists('label', $option)) {
				$labe->text($option['label']);
			}
			$labe->addClass($method); // $method= radio or checkbox
			$input = $labe->append('input');
		} else {
			$controls = $this->preSet();
			$labe = $this('.control-label');		
			if (array_key_exists('label', $option)) {
				$ops = $option['label'];
				$labe->text($ops);
			}
			$input = $controls->append('input');

		}

		$input->attr(array('name' => $name, 'type' => $method));
		if (array_key_exists('value', $option)) {
			$input->attr(array('value' => $option['value']));
		}
		if (array_key_exists('id', $option)) {
			$input->attr(array('id' => $option['id']));
			//$temp = $input->id = $option['id'];
			//$input->id = $temp;
			//$label->attr(array('for' => $input->id));
			$pro=$input->attr('id');
			$labe->attr(array('for' => $pro));
			
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
	public function button($name, $type, $id = NULL) {
		$controlGroup = $this->append('div');
		$controlGroup->addClass('control-group');
		$controls = $controlGroup->append('div');
		$controls->addClass('controls');
		$btn = $controls->append('button')->text($name);
		$btn->addClass('btn');
		$btn->attr(array('name' => $name, 'type' => $type));
		if ($id)
			$btn->attr(array('id' => $id));
		//$btn->id = $id;
		return $btn;
	}

	/**
	 * Add a new <textarea rows=$rows> with the number of rows passed to it
	 */
	public function textArea($label, $name, $id = NULL, $rows = 4) {
		$controlGroup = $this->preSet();
		$labe = $this('.control-label');
		$labe->text($label);
		$textarea = $controlGroup->append('textarea')->attr(array('name' => $name, 'rows' => $rows));
		if ($id) {
			//$textarea->id = $id;
			$textarea->attr(array('id' => $id));
			$labe->attr(array('for' => $textarea->attr('id')));
			
		}
		return $textarea;
	}

	/**
	 * Add a new <select> with the options passed to it like a key=>value array
	 */
	public function select($label, $sel = Array()) {
		$controls = $this->preSet();
		$this('label')->text($label);
		$select = $controls->append('select');
		foreach ($sel as $key => $op) {
			$option = $select->append('option')->text($op);
			$option->attr(array('value' => $key));
		}

	}
	/**
	 * Returns the var $controls that contains <div class='control-group'>
	 * 																						<label class='control-label'>
	 * 																								<div class='controls'>
	 */

	public function preSet() {
		$controlGroup = $this->append('div');
		$controlGroup->addCLass('control-group');
		$controlLabel = $controlGroup->append('label');
		$controlLabel->addClass('control-label');
		$controls = $controlGroup->append('div');
		$controls->addClass('controls');
		return $controls;
	}
	/**TODO Sistemare l'inserimento degli ID tramite il __setID al posto di att(id)
	 * e la funzionalità di accesso ai tag tramite 'nometag' 
	 * 
	 */
	/**
	 * Search if an attribute $value exist in $arr 
	 * replaced by array_key_exist()
	 *
	public function search($arr = array(), $value) {
	
	  if ($arr) {
	    foreach ($arr as $key => $val) {
	      if ($key == $value)
	        return true;
	    }
	  }
	}*/

}

