<?php
/**
 * Class Tasks
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 27.08.13
 */
namespace Tasker;

use Tasker\Tasks\ITask;

class TasksContainer
{

	/** @var array|ITask[]  */
	private $tasks = array();

	/**
	 * @param ITask $task
	 * @param null $name
	 * @throws InvalidStateException
	 */
	public function registerTask(ITask $task, $name = null)
	{
		if($name === null) {
			$name = $task->getName();
		}

		if(isset($this->tasks[$name])) {
			throw new InvalidStateException('Task with same name "' . $name . ' exist yet.');
		}

		$this->tasks[$name] = $task;
	}

	/**
	 * @param $name
	 * @param bool $need
	 * @return ITask
	 * @throws InvalidArgumentException
	 */
	public function getTask($name, $need = true)
	{
		if(isset($this->tasks[$name])) {
			return $this->tasks[$name];
		}

		if($need === true) {
			throw new InvalidArgumentException('Task with name "' . $name . '" does not exist.');
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
	 * @return array|Tasks\ITask[]
	 */
	public function getTasks()
	{
		return $this->tasks;
	}
}