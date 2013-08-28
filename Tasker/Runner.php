<?php
/**
 * Class Runner
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 27.08.13
 */
namespace Tasker;

use Tasker\Config\ConfigContainer;
use Tasker\Output\Writer;

class Runner
{

	/** @var \Tasker\Config\ConfigContainer  */
	private $config;

	/** @var \Tasker\TasksContainer  */
	private $tasks;

	/** @var \Tasker\Results  */
	private $results;

	/**
	 * @param ConfigContainer $config
	 * @param TasksContainer $tasks
	 */
	function __construct(ConfigContainer $config, TasksContainer $tasks)
	{
		$this->config = $config;
		$this->tasks = $tasks;
		$this->results = new Results();
	}

	/**
	 * @return Results
	 */
	public function run()
	{
		if(count($tasks = $this->tasks->getTasksName())) {
			foreach ($tasks as $taskName) {
				$this->results->addResult($this->runTask($taskName));
			}
		}

		return $this->results;
	}

	/**
	 * @param $name
	 * @return $this
	 */
	public function runTask($name)
	{
		try {
			$result = $this->tasks->getTask($name)->run($this->config->getSection($name));
			if($result === null) {
				$result = 'Task "' . $name . '" completed';
			}
		}catch (\Exception $ex) {
			$result = $ex;
		}

		return $result;
	}
}