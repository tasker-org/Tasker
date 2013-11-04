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
	private $bgColors = array(
		'red' => '41',
		'green' => '42',
		'blue' => '44'
	);

	/**
	 * @param $message
	 * @param $type
	 * @return string
	 */
	public function getColored($message, $type = IWriter::NONE)
	{
		if($type != IWriter::NONE && Environment::isUnix()) {
			return chr(27) . "[" . static::getColor($type) . "m" .(string) $message . chr(27) . "[0m";
		}

		return (string) $message;
	}

	/**
	 * @param $type
	 * @return mixed
	 * @throws \Tasker\ErrorException
	 */
	private function getColor($type)
	{
		if(!isset($this->bgColors[$type])) {
			throw new ErrorException('Unsupported color "' . $type . '"');
		}

		return $this->bgColors[$type];
	}
}