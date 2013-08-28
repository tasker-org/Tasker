<?php
/**
 * Class CallableTask
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 28.08.13
 */
namespace Tasker\Tasks;

use Tasker\InvalidArgumentException;

class CallableTask implements ITask
{

	/** @var  string */
	private $name;

	/** @var callable  */
	private $callable;

	/**
	 * @param $name
	 * @param $callable
	 * @throws InvalidArgumentException
	 */
	function __construct($name, $callable)
	{
		if(!is_callable($callable)) {
			throw new InvalidArgumentException('Function must be callable');
		}

		$this->callable = $callable;
		$this->name = $name;
	}


	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param array $config
	 * @return mixed
	 */
	public function run(array $config)
	{
		return call_user_func($this->callable, $config);
	}
}