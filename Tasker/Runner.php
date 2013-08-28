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

	/**
	 * @param ConfigContainer $config
	 * @param TasksContainer $tasks
	 */
	function __construct(ConfigContainer $config, TasksContainer $tasks)
	{
		$this->config = $config;
		$this->tasks = $tasks;
	}

	/**
	 * @param ResultSet $set
	 * @return ResultSet
	 */
	public function run(ResultSet $set)
	{
		if(count($tasks = $this->tasks->getTasksName())) {
			foreach ($tasks as $taskName) {
				if($set->isVerboseMode()) {
					$set->addResult('Running task "' . $taskName . '"', Writer::INFO);
				}

				$result = $this->runTask($taskName);
				if($result !== null) {
					$set->addResult($result);
				}

				$set->addResult('Task '. $taskName . ' completed!', Writer::INFO);
			}
		}

		return $set;
	}

	/**
	 * @param $name
	 * @return \Exception|string
	 */
	public function runTask($name)
	{
		try {
			$result = $this->tasks->getTask($name)->run($this->config->getSection($name));
		}catch (\Exception $ex) {
			$result = $ex;
		}

		return $result;
	}
}