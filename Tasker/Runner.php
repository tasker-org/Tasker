<?php
/**
 * Class Runner
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 27.08.13
 */
namespace Tasker;

use Tasker\Config\ConfigContainer;
use Tasker\Config\ISettings;
use Tasker\Output\IWriter;
use Tasker\Output\Writer;
use Tasker\Utils\Timer;
use Tasker\Threading\Memory;
use Tasker\Threading\Thread;

class Runner
{

	/** @var \Tasker\Config\ConfigContainer  */
	private $config;

	/** @var \Tasker\TasksContainer  */
	private $tasks;

	/** @var \Tasker\Config\ISettings  */
	private $settings;

	/** @var array|Thread[] */
	private $threads = array();

	/**
	 * @param ConfigContainer $config
	 * @param TasksContainer $tasks
	 * @param ISettings $settings
	 */
	function __construct(ConfigContainer $config, TasksContainer $tasks, ISettings $settings)
	{
		$this->config = $config;
		$this->tasks = $tasks;
		$this->settings = $settings;
	}

	/**
	 * @param IResultSet $set
	 * @return IResultSet
	 */
	public function run(IResultSet $set)
	{
		$tasks = $this->tasks->getTasksName();
		if(count($tasks)) {
			Timer::d('process');
			$set->addResult('Running tasks...', IWriter::NONE);
			Memory::init();

			$this->processTasks($tasks, $set);

			while(!empty($this->threads)) {
				foreach($this->threads as $name => $thread) {
					if(!$thread->isAlive()) {
						unset($this->threads[$name]);
						$this->processTaskResult($name, $set);
						$this->processTasks($tasks, $set);
					}
				}

				// let the CPU do its work
				usleep(50000);
			}

			Memory::clear();
			$set->addResult('Tasks completed in ' . Timer::convert(Timer::d('process'), Timer::SECONDS) . ' s', IWriter::NONE);
		}else{
			$set->addResult('No tasks for process.', IWriter::NONE);
		}

		return $set;
	}

	/**
	 * @param $name
	 */
	public function runTask($name)
	{
		try {
			$task = $this->tasks->getTask($name);
			$result = array(IWriter::SUCCESS, $task->run($this->config->getSection($task->getSectionName())));
		}catch (\Exception $ex) {
			$result = array(IWriter::ERROR, $ex->getMessage());
		}

		Memory::set($name, $result);
	}

	/**
	 * @param IResultSet $set
	 * @param $taskName
	 */
	private function processTaskResult($taskName, IResultSet $set)
	{
		list($type, $result) = (array) Memory::get($taskName);
		if($result !== null) {
			$set->addResult('Task "'. $taskName . '" completed with result:', Writer::INFO);
			if(is_array($result)) {
				$set->mergeResults($result);
			}else{
				$set->addResult($result, $type);
			}
		}else{
			$set->addResult('Task "'. $taskName . '" completed!', Writer::SUCCESS);
		}
	}

	/**
	 * @param array $tasks
	 * @param IResultSet $set
	 * @return $this
	 */
	protected function processTasks(array &$tasks, IResultSet $set)
	{
		foreach ($tasks as $i => $taskName) {
			if(count($this->threads) < $this->settings->getThreadsLimit()) {
				$this->createThread($taskName, $set);
				unset($tasks[$i]);
			}
		}

		return $this;
	}

	/**
	 * @param $taskName
	 * @param IResultSet $set
	 * @return $this
	 */
	protected function createThread($taskName, IResultSet $set)
	{
		if($set->isVerboseMode()) {
			$set->addResult('Running task "' . $taskName . '"', Writer::INFO);
		}

		$thread = new Thread(array($this, 'runTask'));
		$thread->start($taskName);
		$this->threads[$taskName] = $thread;
		return $thread;
	}
}