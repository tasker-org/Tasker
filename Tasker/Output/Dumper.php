<?php
/**
 * Class Dumper
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 28.08.13
 */
namespace Tasker\Output;

/**
 * Dumps PHP variables.
 *
 * @author     David Grudl
 */
class Dumper
{
	const MAX_LENGTH = 70;
	const MAX_DEPTH = 50;


	/**
	 * Dumps information about a variable in readable format.
	 * @param  mixed  variable to dump
	 * @return string
	 */
	public static function toLine($var)
	{
		if (is_bool($var)) {
			return $var ? 'TRUE' : 'FALSE';

		} elseif ($var === NULL) {
			return 'NULL';

		} elseif (is_int($var)) {
			return "$var";

		} elseif (is_float($var)) {
			$var = var_export($var, TRUE);
			return strpos($var, '.') === FALSE ? $var . '.0' : $var;

		} elseif (is_string($var)) {
			if ($cut = @iconv_strlen($var, 'UTF-8') > self::MAX_LENGTH) {
				$var = iconv_substr($var, 0, self::MAX_LENGTH, 'UTF-8') . '...';
			} elseif ($cut = strlen($var) > self::MAX_LENGTH) {
				$var = substr($var, 0, self::MAX_LENGTH) . '...';
			}
			return (preg_match('#[^\x09\x0A\x0D\x20-\x7E\xA0-\x{10FFFF}]#u', $var) || preg_last_error() ? '"' . strtr($var, $table) . '"' : "'$var'");

		} elseif (is_array($var)) {
			$out = '';
			$counter = 0;
			foreach ($var as $k => & $v) {
				$out .= ($out === '' ? '' : ', ');
				if (strlen($out) > self::MAX_LENGTH) {
					$out .= '...';
					break;
				}
				$out .= ($k === $counter ? '' : self::toLine($k) . ' => ')
					. (is_array($v) ? 'array(...)' : self::toLine($v));
				$counter = is_int($k) ? max($k + 1, $counter) : $counter;
			}
			return "array($out)";

		} elseif ($var instanceof \Exception) {
			return 'Exception ' . get_class($var) . ': ' . ($var->getCode() ? '#' . $var->getCode() . ' ' : '') . $var->getMessage();

		} elseif (is_object($var)) {
			return get_class($var) . '(#' . substr(md5(spl_object_hash($var)), 0, 4) . ')';

		} elseif (is_resource($var)) {
			return 'resource(' . get_resource_type($var) . ')';

		} else {
			return 'unknown type';
		}
	}
}