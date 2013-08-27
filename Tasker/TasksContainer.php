<?php
/**
 * Class Tasks
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 27.08.13
 */
namespace Tasker;

class TasksContainer
{

	/** @var array  */
	private $tasks = array();

	/**
	 * @param ITask $task
	 * @throws \InvalidArgumentException
	 */
	public function registerTask(ITask $task)
	{
		if(isset($this->tasks[$task->getName()])) {
			throw new \InvalidArgumentException;
		}

		$this->tasks[$task->getName()] = $task;
	}

	/**
	 * @param $name
	 * @param bool $need
	 * @return ITask|null
	 * @throws \InvalidArgumentException
	 */
	public function getTask($name, $need = true)
	{
		if(isset($this->tasks[$name])) {
			return $this->tasks[$name];
		}

		if($need === true) {
			throw new \InvalidArgumentException;
		}
	}

	/**
	 * @return array
	 */
	public function getTasksName()
	{
		return array_keys($this->getTasks());
	}

	/**
	 * @return array
	 */
	public function getTasks()
	{
		return $this->tasks;
	}
}