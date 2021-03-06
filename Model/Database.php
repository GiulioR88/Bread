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

namespace Bread\Model;

use Bread\Configuration\Manager as CM;
use Bread\Model\Database\Exceptions;
use Exception;

class Database {
  protected static $drivers = array();

  public static function register($driver, $class) {
    if (is_string($driver)) {
      $driver = static::factory($driver);
    }
    return static::$drivers[$class] = $driver;
  }

  public static function driver($class) {
    $classes = class_parents($class);
    array_unshift($classes, $class);
    foreach ($classes as $c) {
      if (isset(static::$drivers[$c])) {
        return static::$drivers[$c];
      }
      elseif ($url = CM::get($c, 'database.url')) {
        return static::register($url, $c);
      }
    }
    throw new Exceptions\DriverNotRegistered($class);
  }

  public static function factory($url) {
    $scheme = parse_url($url, PHP_URL_SCHEME);
    if (!$Driver = CM::get(__CLASS__, "drivers.$scheme")) {
      throw new Exception("Driver for {$scheme} not found.");
    }
    if (!is_subclass_of($Driver, 'Bread\Model\Database\Interfaces\Driver')) {
      throw new Exception("{$Driver} isn't a valid driver.");
    }
    return new $Driver($url);
  }
}

CM::defaults('Bread\Model\Database', array(
  'drivers' => array(
    'mongodb' => 'Bread\Model\Database\Drivers\MongoDB',
    'mysql' => 'Bread\Model\Database\Drivers\MySQL',
    'db2' => 'Bread\Model\Database\Drivers\ODBC',
    'ldap' => 'Bread\Model\Database\Drivers\LDAP'
  )
));
