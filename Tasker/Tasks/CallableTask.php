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

	/** @var  string */
	private $name;

	/** @var callable  */
	private $callable;

	/** @var  string */
	private $section;

	/**
	 * @param $name
	 * @param $callable
	 * @param null $section
	 * @throws InvalidArgumentException
	 */
	function __construct($name, $callable, $section = null)
	{
		if(!is_callable($callable)) {
			throw new InvalidArgumentException('Function must be callable');
		}

		$this->callable = $callable;
		$this->name = (string) $name;

		if($section === null) {
			$section = $this->name;
		}
		$this->section = $section;
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

	/**
	 * @return string
	 */
	public function getSectionName()
	{
		return $this->section;
	}
}