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

namespace Bread\Networking\HTTP;

use Bread\Networking;
use Bread\Stream;
use DateTime;

class Response extends Message implements Stream\Interfaces\Writable {
  public $statusLine;
  public $status;
  public $reason;
  private $chunkedEncoding = false;
  private $writable = true;
  private $closed = false;

  protected static $statusCodes = array(
    100 => "Continue",
    101 => "Switching Protocols",
    102 => "Processing",
    122 => "Request-URI too long",
    200 => "OK",
    201 => "Created",
    202 => "Accepted",
    203 => "Non-Authoritative Information",
    204 => "No Content",
    205 => "Reset Content",
    206 => "Partial Content",
    207 => "Multi-Status",
    226 => "IM Used",
    300 => "Multiple Choices",
    301 => "Moved Permanently",
    302 => "Found",
    303 => "See Other",
    304 => "Not Modified",
    305 => "Use Proxy",
    306 => "Switch Proxy",
    307 => "Temporary Redirect",
    400 => "Bad Request",
    401 => "Unauthorized",
    402 => "Payment Required",
    403 => "Forbidden",
    404 => "Not Found",
    405 => "Method Not Allowed",
    406 => "Not Acceptable",
    407 => "Proxy Authentication Required",
    408 => "Request Timeout",
    409 => "Conflict",
    410 => "Gone",
    411 => "Length Required",
    412 => "Precondition Failed",
    413 => "Request Controller Too Large",
    414 => "Request-URI Too Long",
    415 => "Unsupported Media Type",
    416 => "Requested Range Not Satisfiable",
    417 => "Expectation Failed",
    418 => "I'm a teapot",
    422 => "Unprocessable Controller",
    423 => "Locked",
    424 => "Failed Dependency",
    425 => "Unordered Collection",
    426 => "Upgrade Required",
    500 => "Internal Server Error",
    501 => "Not Implemented",
    502 => "Bad Gateway",
    503 => "Service Unavailable",
    504 => "Gateway Timeout",
    505 => "HTTP Version Not Supported",
    506 => "Variant Also Negotiates",
    507 => "Insufficient Storage",
    509 => "Bandwidth Limit Exceeded",
    510 => "Not Extended"
  );

  public function __construct(Request $request, $status = 200, $body = null,
    $headers = array(), $protocol = 'HTTP/1.1') {
    $this->request = $request;
    $this->status($status, $protocol);
    parent::__construct($request->connection, $protocol, $this->statusLine, $headers, $body);
  }

  public function isWritable() {
    return $this->writable;
  }

  public function status($status, $protocol = 'HTTP/1.1') {
    $this->status = $status;
    $this->reason = self::$statusCodes[$this->status];
    $this->statusLine = implode(' ', array(
      $protocol, $status, $this->reason
    ));
  }

  public function write($data) {
    if ($this->chunkedEncoding) {
      $len = strlen($data);
      $chunk = dechex($len) . "\r\n" . $data . "\r\n";
      $flushed = $this->connection->write($chunk);
    }
    else {
      $flushed = $this->connection->write($data);
    }
    return $flushed;
  }

  public function end($data = null) {
    if (null !== $data) {
      $this->write($data);
    }
    if ($this->chunkedEncoding) {
      $this->connection->write("0\r\n\r\n");
    }
    $this->emit('close');
    $this->removeAllListeners();
    if ('close' === $this->request->headers['Connection']) {
      $this->connection->end();
    }
  }

  public function close() {
    if ($this->closed) {
      return;
    }
    $this->closed = true;
    $this->writable = false;
    $this->emit('close');
    $this->removeAllListeners();
    $this->connection->close();
  }
}
