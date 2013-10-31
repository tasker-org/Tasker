<?php
/**
 * Class Tasker
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 27.08.13
 */
namespace Tasker;

use Tasker\Configs\JsonConfig;
use Tasker\Configuration\Container;
use Tasker\Tasks\CallableTask;
use Tasker\Tasks\ITask;
use Tasker\Tasks\ITaskService;

class Tasker
{

	/** @var Container  */
	private $container;

	/** @var TasksContainer */
	private $tasksContainer;

	/** @var Runner  */
	private $runner;

	function __construct()
	{
		$this->tasksContainer = new TasksContainer;
		$this->container = new Container;
		$this->runner = new Runner($this->container);
	}

	/**
	 * @param $path
	 * @return $this
	 * @throws InvalidArgumentException
	 */
	public function addConfig($path)
	{
		if(!file_exists($path)) {
			throw new InvalidArgumentException('Given path "' . $path . '" does not exist.');
		}

		switch ($this->getFileExtension($path)) {
			case JsonConfig::EXTENSION:
				$this->container->addConfig(new JsonConfig($path));
			break;
		}

		return $this;
	}

	/**
	 * @param $task
	 * @param null $name
	 * @param null $configSection
	 * @return $this
	 * @throws InvalidArgumentException
	 */
	public function registerTask($task, $name = null, $configSection = null)
	{
		if(!$task instanceof ITask && $name === null) {
			throw new InvalidArgumentException('Please set task name');
		}

		if(!$task instanceof ITask && $task instanceof ITaskService) {
			$task = new CallableTask($name, array($task, 'run'), $configSection);
		}elseif(is_callable($task)){
			$task = new CallableTask($name, $task, $configSection);
		}

		if(!$task instanceof ITask) {
			throw new InvalidArgumentException('Invalid task format given');
		}

		$this->tasksContainer->registerTask($task, $name);
		return $this;
	}

	/**
	 * @return ResultSet
	 */
	public function run()
	{
		return $this->runner->run($this->tasksContainer);
	}

	/**
	 * @param string $name
	 * @return mixed
	 */
	public function runTask($name)
	{
		$task = $this->tasksContainer->getTask($name);
		return $task->run($this->container->getSection($task->getSectionName()));
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