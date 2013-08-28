<?php
/**
 * Class Tasker
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 27.08.13
 */
namespace Tasker;

use Tasker\Config\JsonConfig;
use Tasker\Config\ConfigContainer;

class Tasker
{

	/** @var ConfigContainer  */
	private $configContainer;

	/** @var TasksContainer */
	private $taskContainer;

	function __construct()
	{
		$this->taskContainer = new TasksContainer;
		$this->configContainer = new ConfigContainer;
	}

	/**
	 * @param $path
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	public function addConfig($path)
	{
		if(!file_exists($path)) {
			throw new \InvalidArgumentException;
		}

		switch ($this->getFileExtension($path)) {
			case 'json':
				$this->configContainer->addConfig(new JsonConfig($path));
			break;
		}

		return $this;
	}

	/**
	 * @param $task
	 * @param null $name
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	public function registerTask($task, $name = null)
	{
		if($task instanceof ITask){
			$this->taskContainer->registerTask($task);
		}elseif($task instanceof \Closure){
			$this->taskContainer->registerTask(new ClosureTask($name, $task));
		}else{
			throw new \InvalidArgumentException;
		}

		return $this;
	}

	/**
	 * @param null $task
	 * @return Results
	 */
	public function run($task = null)
	{
		$runner = new Runner($this->configContainer, $this->taskContainer);
		if($task !== null) {
			$result = new Results(array($runner->runTask($task)));
		}else{
			$result = $runner->run();
		}

		return $result;
	}

	/**
	 * @param $path
	 * @return mixed
	 */
	private function getFileExtension($path)
	{
		return pathinfo($path, PATHINFO_EXTENSION);
	}

}