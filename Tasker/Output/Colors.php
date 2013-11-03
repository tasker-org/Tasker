<?php
/**
 * Class Colors
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 28.08.13
 */
namespace Tasker\Output;

use Tasker\Client\Environment;
use Tasker\ErrorException;

class Colors
{

	/** @var array  */
	private static $bgColors = array(
		'red' => '41',
		'green' => '42',
		'blue' => '44'
	);

	/**
	 * @param $message
	 * @param $type
	 * @return string
	 */
	public static function getColored($message, $type = IWriter::NONE)
	{
		if($type != IWriter::NONE && Environment::isUnix()) {
			$pattern = "\033[__!COLOR__m__!MESSAGE__\033[0m";
			return str_replace('__!MESSAGE__', (string) $message, str_replace('__!COLOR__', static::getColor($type), $pattern));
		}

		return (string) $message;
	}

	/**
	 * @param $type
	 * @return mixed
	 * @throws \Tasker\ErrorException
	 */
	private static function getColor($type)
	{
		if(!isset(static::$bgColors[$type])) {
			throw new ErrorException('Unsupported color "' . $type . '"');
		}

		return static::$bgColors[$type];
	}
}