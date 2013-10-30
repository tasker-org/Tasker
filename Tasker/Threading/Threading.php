<?php
/**
 * Class Threading
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 30.10.13
 */
namespace Tasker\Threading;

class Threading
{

	/**
	 * checks if threading is supported by the current
	 * PHP configuration
	 *
	 * @return boolean
	 */
	public static function available() {
		$required_functions = array(
			'pcntl_fork',
		);

		foreach( $required_functions as $function ) {
			if ( !function_exists( $function ) ) {
				return false;
			}
		}

		return true;
	}
} 