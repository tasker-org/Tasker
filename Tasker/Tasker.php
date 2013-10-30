<?php
/**
 * Class Tasker
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 27.08.13
 */
namespace Tasker;

use Tasker\Config\ISettings;
use Tasker\Config\JsonConfig;
use Tasker\Config\ConfigContainer;
use Tasker\Config\Settings;
use Tasker\Setters\IRootPathSetter;
use Tasker\Tasks\CallableTask;
use Tasker\Tasks\ITask;
use Tasker\Tasks\ITaskService;
use Tasker\InvalidArgumentException;

class Tasker
{

	/** @var \Tasker\Config\ISettings  */
	private $settings;

	/** @var ConfigContainer  */
	private $configContainer;

	/** @var TasksContainer */
	private $taskContainer;

	/**
	 * @param ISettings $settings
	 */
	function __construct(ISettings $settings = null)
	{
		if($settings === null) {
			$settings = new Settings;
		}

		$this->settings = $settings;
		$this->taskContainer = new TasksContainer;
		$this->configContainer = new ConfigContainer;
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
				$this->configContainer->addConfig(new JsonConfig($path));
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
			$this->callSetters($task);
			$task = new CallableTask($name, array($task, 'run'), $configSection);
		}elseif(is_callable($task)){
			$task = new CallableTask($name, $task, $configSection);
		}

		if(!$task instanceof ITask) {
			throw new InvalidArgumentException('Invalid task format given');
		}

		$this->taskContainer->registerTask($task);
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
		return $results->setVerboseMode($this->settings->isVerboseMode());
	}

	/**
	 * @param $path
	 * @return mixed
	 */
	private function getFileExtension($path)
	{
		return pathinfo($path, PATHINFO_EXTENSION);
	}

	/**
	 * @param ITaskService $task
	 */
	protected function callSetters(ITaskService &$task)
	{
		if($task instanceof IRootPathSetter) {
			$task->setRootPath($this->settings->getRootPath());
		}
	}
}