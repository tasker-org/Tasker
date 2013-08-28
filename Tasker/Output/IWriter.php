<?php
/**
 * Class IWriter
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 28.08.13
 */

namespace Tasker\Output;


interface IWriter
{

	const SUCCESS = 'success';

	const ERROR = 'error';

	const INFO = 'info';

	/**
	 * @param string $message
	 * @param string $type
	 * @return void
	 */
	public static function write($message, $type = self::SUCCESS);

	/**
	 * @param string $message
	 * @param string $type
	 * @return void
	 */
	public static function writeLn($message, $type = self::SUCCESS);

	/**
	 * @param \Exception $ex
	 * @return void
	 */
	public static function writeException(\Exception $ex);

	/**
	 * @param $vars
	 * @return void
	 */
	public static function dump();
}