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
  public function __construct(Page $page, $action = null, $method = 'post') {
    parent::__construct($page, 'form');
    $this->addClass('form-horizontal');
    $this->attr(array(
      'action' => $action, 'method' => $method
    ));
  }

  public function __call($type, $arguments) {
    $name = array_shift($arguments);
    $options = array_merge(array(
      'label' => null,
      'required' => true,
      'multiple' => false
    ), array_shift($arguments));
    extract($options);
    $controlGroup = $this->append('div.control-group');
    $controls = $controlGroup->append('div.controls');
    switch ($type) {
    case 'select':
      $input = $controls->append('select')->attr(array(
        'name' => $name,
        'multiple' => $multiple
      ));
      break;
    case 'textarea':
      $input = $controls->append('textarea')->attr(array(
        'name' => $name
      ));
      break;
    default:
      $input = $controls->append('input')->attr(array(
        'name' => $name, 'type' => $type
      ));
    }
    if ($label) {
      $label = $this->document->create('label', $label);
      switch ($type) {
      case 'radio':
      case 'checkbox':
        $label->addClass($type)->prepend($input);
        $controls->append($label);
        break;
      default:
        $label->addClass('control-label');
        $controlGroup->prepend($label);
      }
    }
    $input->attr('required', $required);
    return $input;
  }

  public function actions($actions = array()) {
    $formActions = $this->append('div.form-actions');
    foreach ($actions as $type => $action) {
      list($type, $class) = explode('.', $type)
        + array(
          null, null
        );
      $formActions->append('button', $action)->attr(array(
        'type' => $type, 'class' => $class ? "btn btn-$class" : "btn"
      ));
    }
  }
  public function text($text) {
    return $this->__call(__FUNCTION__, func_get_args());
  }
}
