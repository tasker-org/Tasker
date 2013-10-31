<?php
/**
 * Class CallableTask
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 28.08.13
 */
namespace Tasker\Tasks;

use Tasker\InvalidArgumentException;

class CallableTask extends Task
{

	/** @var callable  */
	private $callable;

	/**
	 * @param $callable
	 * @throws InvalidArgumentException
	 */
	function __construct($callable)
	{
		if(!is_callable($callable)) {
			throw new InvalidArgumentException('Function must be callable');
		}

		$this->callable = $callable;
	}

	/**
	 * @param array $config
	 * @return mixed
	 */
	public function run($config)
	{
		return call_user_func($this->callable, $config);
	}
}