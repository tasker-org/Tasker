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
use Tasker\Tasks\ClosureTask;
use Tasker\Tasks\ITask;

class Tasker
{

	/** @var bool  */
	private $verboseMode;

	/** @var ConfigContainer  */
	private $configContainer;

	/** @var TasksContainer */
	private $taskContainer;

	/**
	 * @param bool $verbose
	 */
	function __construct($verbose = true)
	{
		$this->verboseMode = $verbose;
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
	 * @return ResultSet
	 */
	public function run()
	{
		$runner = $this->createRunner();
		return $runner->run($this->createResultSet());
	}

	/**
	 * @param $name
	 * @return \Exception|string
	 */
	public function runTask($name)
	{
		$runner = $this->createRunner();
		return $runner->runTask($name);
	}

	/**
	 * @return Runner
	 */
	protected function createRunner()
	{
		return new Runner($this->configContainer, $this->taskContainer);
	}

	/**
	 * @return ResultSet
	 */
	protected function createResultSet()
	{
		$results = new ResultSet;
		return $results->setVerboseMode($this->verboseMode);
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