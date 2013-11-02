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
use Tasker\Configuration\Setting;
use Tasker\Tasks\CallableTask;
use Tasker\Tasks\ITask;
use Tasker\Tasks\ITaskService;
use Tasker\Threading\ThreadsRunner;

class Tasker
{

	/** @var Container  */
	private $container;

	/** @var TasksContainer */
	private $tasksContainer;

	function __construct()
	{
		$this->tasksContainer = new TasksContainer;
		$this->container = new Container;
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
		return $this->createRunner()->run($this->tasksContainer);
	}

	/**
	 * @param string $name
	 * @return mixed
	 */
	public function runTask($name)
	{
		$task = $this->tasksContainer->getTask($name);
		return $task->run($this->container->getConfig($task->getSectionName()));
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
	 * @return Runner|ThreadsRunner
	 * @throws InvalidStateException
	 */
	protected function createRunner()
	{
		$setting = $this->createSetting();
		if($setting->isMultiThreading()) {
			$runner = new ThreadsRunner($setting);
		}else{
			$runner = new Runner($setting);
		}

		if(!$runner instanceof IRunner) {
			throw new InvalidStateException('Tasks runner must be instance of Tasker\IRunner.');
		}

		return $runner;
	}

	/**
	 * @return Setting
	 */
	protected function createSetting()
	{
		return new Setting($this->container->buildContainer());
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