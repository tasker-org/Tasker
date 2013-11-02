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
} 