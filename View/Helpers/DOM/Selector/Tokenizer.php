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

namespace Bread\View\Helpers\DOM\Selector;

use Exception as ParseException;

/**
 * Tokenizer lexes a CSS Selector to tokens.
 *
 * This component is a port of the Python lxml library,
 * which is copyright Infrae and distributed under the BSD license.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Tokenizer {
  /**
   * Takes a CSS selector and returns an array holding the Tokens
   * it contains.
   *
   * @param string $s The selector to lex.
   *
   * @return array Token[]
   */
  public function tokenize($s) {
    if (function_exists('mb_internal_encoding')
      && ((int) ini_get('mbstring.func_overload')) & 2) {
      $mbEncoding = mb_internal_encoding();
      mb_internal_encoding('ASCII');
    }

    $tokens = array();
    $pos = 0;
    $s = preg_replace('#/\*.*?\*/#s', '', $s);

    while (true) {
      if (preg_match('#\s+#A', $s, $match, 0, $pos)) {
        $precedingWhitespacePos = $pos;
        $pos += strlen($match[0]);
      }
      else {
        $precedingWhitespacePos = 0;
      }

      if ($pos >= strlen($s)) {
        if (isset($mbEncoding)) {
          mb_internal_encoding($mbEncoding);
        }

        return $tokens;
      }

      if (preg_match('#[+-]?\d*n(?:[+-]\d+)?#A', $s, $match, 0, $pos)
        && 'n' !== $match[0]) {
        $sym = substr($s, $pos, strlen($match[0]));
        $tokens[] = new Token('Symbol', $sym, $pos);
        $pos += strlen($match[0]);

        continue;
      }

      $c = $s[$pos];
      $c2 = substr($s, $pos, 2);
      if (in_array($c2, array(
        '~=', '|=', '^=', '$=', '*=', '::', '!='
      ))) {
        $tokens[] = new Token('Token', $c2, $pos);
        $pos += 2;

        continue;
      }

      if (in_array($c, array(
        '>', '+', '~', ',', '.', '*', '=', '[', ']', '(', ')', '|', ':', '#'
      ))) {
        if (in_array($c, array(
          '.', '#', '['
        )) && $precedingWhitespacePos > 0) {
          $tokens[] = new Token('Token', ' ', $precedingWhitespacePos);
        }
        $tokens[] = new Token('Token', $c, $pos);
        ++$pos;

        continue;
      }

      if ('"' === $c || "'" === $c) {
        // Quoted string
        $oldPos = $pos;
        list($sym, $pos) = $this->tokenizeEscapedString($s, $pos);

        $tokens[] = new Token('String', $sym, $oldPos);

        continue;
      }

      $oldPos = $pos;
      list($sym, $pos) = $this->tokenizeSymbol($s, $pos);

      $tokens[] = new Token('Symbol', $sym, $oldPos);

      continue;
    }
  }

  /**
   * Tokenizes a quoted string (i.e. 'A string quoted with \' characters'),
   * and returns an array holding the unquoted string contained by $s and
   * the new position from which tokenizing should take over.
   *
   * @param string  $s   The selector string containing the quoted string.
   * @param integer $pos The starting position for the quoted string.
   *
   * @return array
   *
   * @throws ParseException When expected closing is not found
   */
  private function tokenizeEscapedString($s, $pos) {
    $quote = $s[$pos];

    $pos = $pos + 1;
    $start = $pos;
    while (true) {
      $next = strpos($s, $quote, $pos);
      if (false === $next) {
        throw new ParseException(
          sprintf('Expected closing %s for string in: %s', $quote, substr($s, $start)));
      }

      $result = substr($s, $start, $next - $start);
      if (strlen($result) > 0 && '\\' === $result[strlen($result) - 1]) {
        // next quote character is escaped
        $pos = $next + 1;
        continue;
      }

      if (false !== strpos($result, '\\')) {
        $result = $this->unescapeStringLiteral($result);
      }

      return array(
        $result, $next + 1
      );
    }
  }

  /**
   * Unescapes a string literal and returns the unescaped string.
   *
   * @param string $literal The string literal to unescape.
   *
   * @return string
   *
   * @throws ParseException When invalid escape sequence is found
   */
  private function unescapeStringLiteral($literal) {
    return preg_replace_callback('#(\\\\(?:[A-Fa-f0-9]{1,6}(?:\r\n|\s)?|[^A-Fa-f0-9]))#', function (
      $matches) use ($literal) {
      if ($matches[0][0] == '\\' && strlen($matches[0]) > 1) {
        $matches[0] = substr($matches[0], 1);
        if (in_array($matches[0][0], array(
          '0',
          '1',
          '2',
          '3',
          '4',
          '5',
          '6',
          '7',
          '8',
          '9',
          'A',
          'B',
          'C',
          'D',
          'E',
          'F',
          'a',
          'b',
          'c',
          'd',
          'e',
          'f'
        ))) {
          return chr(trim($matches[0]));
        }
      }
      else {
        throw new ParseException(
          sprintf('Invalid escape sequence %s in string %s', $matches[0], $literal));
      }
    }, $literal);
  }

  /**
   * Lexes selector $s and returns an array holding the name of the symbol
   * contained in it and the new position from which tokenizing should take
   * over.
   *
   * @param string  $s   The selector string.
   * @param integer $pos The position in $s at which the symbol starts.
   *
   * @return array
   *
   * @throws ParseException When Unexpected symbol is found
   */
  private function tokenizeSymbol($s, $pos) {
    $start = $pos;

    if (!preg_match('#[^\w\-]#', $s, $match, PREG_OFFSET_CAPTURE, $pos)) {
      // Goes to end of s
      return array(
        substr($s, $start), strlen($s)
      );
    }

    $matchStart = $match[0][1];

    if ($matchStart == $pos) {
      throw new ParseException(
        sprintf('Unexpected symbol: %s at %s', $s[$pos], $pos));
    }

    $result = substr($s, $start, $matchStart - $start);
    $pos = $matchStart;

    return array(
      $result, $pos
    );
  }
}
