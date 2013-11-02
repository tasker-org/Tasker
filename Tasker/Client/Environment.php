<?php
/**
 * Class Environment
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 02.11.13
 */
namespace Tasker\Client;

use Tasker\Object;

class Environment extends Object
{

	/**
	 * @return bool
	 */
	public static function isUnix()
	{
		return DIRECTORY_SEPARATOR === '/';
	}

	/**
	 * Checks if threading is supported by the current
	 * PHP configuration
	 *
	 * @return boolean
	 */
	public static function isMultiThreading() {
		$required_functions = array(
			'pcntl_fork',
		);

		foreach($required_functions as $function) {
			if (!function_exists($function)) {
				return false;
			}
		}

		return true;
	}
} 