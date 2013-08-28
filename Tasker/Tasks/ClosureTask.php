<?php
/**
 * Class ClosureTask
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 27.08.13
 */
namespace Tasker\Tasks;

class ClosureTask implements ITask
{

	/** @var  string */
	private $name;

	/** @var callable  */
	private $function;

	/**
	 * @param $name
	 * @param callable $function
	 */
	function __construct($name, \Closure $function)
	{
		$this->function = $function;
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
	public function run($config)
	{
		return call_user_func($this->function, $config);
	}
}