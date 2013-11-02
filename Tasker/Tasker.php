<?php
/**
 * Class Tasker
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 27.08.13
 */
namespace Tasker;

use Tasker\Configs\ArrayConfig;
use Tasker\Utils\Memory;
use Tasker\Utils\Timer;
use Tasker\Configs\JsonConfig;
use Tasker\Configuration\Container;
use Tasker\Configuration\IConfig;
use Tasker\Configuration\Setting;
use Tasker\Tasks\CallableTask;
use Tasker\Tasks\ITask;
use Tasker\Tasks\ITaskService;
use Tasker\Threading\ThreadsRunner;
use Tasker\Utils\Metrics;

class Tasker
{

	/** @var \Tasker\Configuration\Container  */
	private $container;

	/** @var \Tasker\TasksContainer  */
	private $tasksContainer;

	/** @var \Tasker\Configuration\Setting  */
	private $setting;

	function __construct()
	{
		$this->tasksContainer = new TasksContainer;
		$this->container = new Container;
		$this->setting = new Setting($this->container);
	}

	/**
	 * @param string|array|\Tasker\Configuration\IConfig $config
	 * @return $this
	 * @throws InvalidArgumentException
	 */
	public function addConfig($config)
	{
		if(is_array($config)) {
			$this->addArrayConfig($config);
		}elseif(is_string($config)) {
			$this->addFileConfig($config);
		}elseif($config instanceof IConfig) {
			$this->container->addConfiguration($config);
		}else{
			throw new InvalidArgumentException('Invalid type "' . gettype($config) . '" given.');
		}

		return $this;
	}

	/**
	 * @param ITask|callable|ITaskService $task
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

		$task->setSetting($this->setting);
		$this->tasksContainer->addTask($task);
		return $this;
	}

	/**
	 * @return ResultSet
	 */
	public function run()
	{
		$this->container->buildContainer()->lock();
		$resultSet = new ResultSet($this->setting->isVerbose());

		$tasks = $this->tasksContainer->getTasks();
		Timer::d(__METHOD__);
		if(count($tasks)) {
			$resultSet->printResult('Running tasks...');
			$this->createRunner($resultSet)->run($tasks);
		}else{
			$resultSet->printResult('No tasks for process.');
		}

		$duration = Timer::convert(Timer::d(__METHOD__), Timer::SECONDS);
		$resultSet->printResult('Tasks completed in ' . $duration . ' s. Memory usage ' . Metrics::formatBytes(Memory::getUsage()));
		return $resultSet;
	}

	/**
	 * @param array $config
	 * @return $this
	 */
	protected function addArrayConfig(array $config)
	{
		$this->container->addConfiguration(new ArrayConfig($config));
		return $this;
	}

	/**
	 * @param string $path
	 * @return $this
	 * @throws InvalidArgumentException
	 */
	protected function addFileConfig($path)
	{
		if(!file_exists($path)) {
			throw new InvalidArgumentException('Given path "' . $path . '" does not exist.');
		}

		switch ($this->getFileExtension($path)) {
			case JsonConfig::EXTENSION:
				$config = new JsonConfig($path);
				break;
			default:
				$config = null;
				break;
		}

		if($config !== null) {
			$this->container->addConfiguration($config);
		}

		return $this;
	}

	/**
	 * @param IResultSet $resultSet
	 * @return Runner|ThreadsRunner
	 */
	protected function createRunner(IResultSet $resultSet)
	{
		if(count($this->tasksContainer->getTasks()) > 1 && $this->setting->isMultiThreading()) {
			return new ThreadsRunner($this->setting, $resultSet);
		}

		return new Runner($this->setting, $resultSet);
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