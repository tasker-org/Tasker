<?php
/**
 * Class Writter
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 28.08.13
 */
namespace Tasker\Output;

class Writer implements IWriter
{

	/**
	 * @param $message
	 * @param string $type
	 */
	public static function write($message, $type = self::SUCCESS)
	{
		echo Colors::getColored($message, $type);
	}

	/**
	 * @param $message
	 * @param string $type
	 */
	public static function writeLn($message, $type = self::SUCCESS)
	{
		static::write($message, $type);
		echo PHP_EOL;
	}

	/**
	 * @param \Exception $ex
	 */
	public static function writeException(\Exception $ex)
	{
		static::writeLn($ex->getMessage(), self::ERROR);
	}

	/**
	 * @return void
	 */
	public static function dump()
	{
		foreach(func_get_args() as $var) {
			var_export($var);
		}

		exit;
	}
}