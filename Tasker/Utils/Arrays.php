<?php
/**
 * Class Arrays
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 28.08.13
 */
namespace Tasker\Utils;

class Arrays
{

	/**
	 * @param object $obj
	 * @return array
	 */
	public static function objectToArray($obj)
	{
		if(is_object($obj)) {
			$obj = (array) $obj;
		}

		if(is_array($obj)) {
			$new = array();
			foreach($obj as $key => $val) {
				$new[$key] = static::objectToArray($val);
			}
		} else {
			$new = $obj;
		}

		return $new;
	}
}