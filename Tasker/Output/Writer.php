<?php
/**
 * Class Writter
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 28.08.13
 */
namespace Tasker\Output;

use Tasker\Exception;
use Tasker\InfoException;

class Writer implements IWriter
{

	/** @var  Colors */
	private $colors;

	function __construct()
	{
		$this->colors = new Colors();
	}

	/**
	 * @param $message
	 * @param string $type
	 */
	public function write($message, $type = self::SUCCESS)
	{
		echo $this->colors->getColored(trim(var_export($message, true), "'"), $type);
	}

	/**
	 * @param $message
	 * @param string $type
	 */
	public function writeLn($message, $type = self::SUCCESS)
	{
		static::write($message, $type);
		echo PHP_EOL;
	}

	/**
	 * @param \Exception $ex
	 */
	public function writeException(\Exception $ex)
	{
		if($ex instanceof InfoException) {
			static::writeLn($ex->getMessage(), self::INFO);
		}else{
			$message = get_class($ex) . ' in ' . $ex->getFile() . ' on line ' . $ex->getLine() . ' with message: ' . $ex->getMessage();
			static::writeLn($message, self::ERROR);

			if(!$ex instanceof Exception) {
				static::writeLn($ex->getTraceAsString(), self::ERROR);
			}
		}
	}

	/**
	 * @return void
	 */
	public function dump()
	{
		foreach(func_get_args() as $var) {
			Dumper::toLine($var);
		}

		exit;
	}
}