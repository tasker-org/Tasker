<?php
/**
 * Class Tasker
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 27.08.13
 */
namespace Tasker;

use Tasker\Configs\ArrayConfig;
use Tasker\Configs\JsonConfig;
use Tasker\Configuration\Container;
use Tasker\Configuration\IConfig;
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
	 * @param string|array|\Tasker\Configuration\IConfig $config
	 * @return $this
	 * @throws InvalidArgumentException
	 */
	public function addConfig($config)
	{
		if(is_array($config)) {
			$config = new ArrayConfig($config);
		}elseif(is_string($config)) {
			if(!file_exists($config)) {
				throw new InvalidArgumentException('Given path "' . $config . '" does not exist.');
			}

			switch ($this->getFileExtension($config)) {
				case JsonConfig::EXTENSION:
					$config = new JsonConfig($config);
					break;
			}
		}

		if(!$config instanceof IConfig) {
			throw new InvalidArgumentException('Invalid type "' . gettype($config) . '" given.');
		}

		$this->container->addConfig($config);
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
			$task = new CallableTask(array($task, 'run'));
		}elseif(is_callable($task)){
			$task = new CallableTask($task);
		}

		if(!$task instanceof ITask) {
			throw new InvalidArgumentException('Invalid task type given.');
		}

		if($name !== null) {
			$task->setName($name);
		}

		if($configSection !== null) {
			$task->setSectionName($name);
		}

		$this->tasksContainer->registerTask($task);
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
		return pathinfo((string) $path, PATHINFO_EXTENSION);
	}
}