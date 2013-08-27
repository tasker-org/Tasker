<?php
/**
 * Class Task
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 27.08.13
 */
namespace Tasker;

class Task implements ITask
{

	private $name;

	private $job;

	function __construct($name, $job)
	{
		$this->job = $job;
		$this->name = $name;
	}


	public function getName()
	{
		return $this->name;
	}

	public function getJob()
	{
		return $this->job;
	}

	public function run()
	{
		// TODO: Implement runJob() method.
	}

	protected function setJob($job)
	{

	}
}