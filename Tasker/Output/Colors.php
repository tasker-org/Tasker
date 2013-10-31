<?php
/**
 * Class Colors
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 28.08.13
 */
namespace Tasker\Output;

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
	public static function getColored($message, $type)
	{
		if($type !== IWriter::NONE) {
			$pattern = "\033[__!COLOR__m__!MESSAGE__\033[0m";
			return str_replace('__!MESSAGE__', (string) $message, str_replace('__!COLOR__', static::getColor($type), $pattern));
		}

		return (string) $message;
	}

	/**
	 * @param $type
	 * @return string
	 */
	private static  function getColor($type)
	{
		$color = null;
		switch($type) {
			case IWriter::SUCCESS;
				$color = 'green';
				break;
			case IWriter::ERROR;
				$color = 'red';
				break;
			default;
				$color = 'blue';
				break;
		}

		return static::$bgColors[$color];
	}
}