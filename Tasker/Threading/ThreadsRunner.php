<?php
/**
 * Class ThreadsRunner
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 27.08.13
 */
namespace Tasker\Threading;

use Tasker\Configuration\ISetting;
use Tasker\Output\Dumper;
use Tasker\Output\IWriter;
use Tasker\Output\Writer;
use Tasker\Tasks\ITask;
use Tasker\Utils\FileSystem;
use Tasker\Utils\Timer;
use Tasker\Object;
use Tasker\IRunner;
use Tasker\TasksContainer;
use Tasker\ResultSet;

class ThreadsRunner extends Object implements IRunner
{

	/** @var \Tasker\Configuration\ISetting  */
	private $setting;

	/** @var \Tasker\ResultSet  */
	private $resultSet;

	/** @var array|Thread[] */
	private $threads = array();

	/** @var \Tasker\Threading\ResultStorage  */
	private $resultStorage;

	/**
	 * @param ISetting $setting
	 */
	function __construct(ISetting $setting)
	{
		$this->setting = $setting;
		$this->resultStorage = new ResultStorage($setting->getRootPath());
	}

	/**
	 * @param TasksContainer $tasks
	 * @return ResultSet
	 */
	public function run(TasksContainer $tasks)
	{
		$tasks = $tasks->getTasks();
		if(count($tasks)) {
			Timer::d('process');
			$this->getResultSet()->printResult('Running tasks...');

			$this->processTasks($tasks, $this->getResultSet());

			while(!empty($this->threads)) {
				foreach($this->threads as $name => $thread) {
					if(!$thread->isAlive()) {
						unset($this->threads[$name]);
						$this->processTaskResult($name);
						$this->processTasks($tasks);
					}
				}

				$this->pause();
			}

			$this->getResultSet()->printResult('Tasks completed in ' . Timer::convert(Timer::d('process'), Timer::SECONDS) . ' s');
		}else{
			$this->getResultSet()->printResult('No tasks for process.');
		}

		return $this->getResultSet();
	}

	/**
	 * @param ITask $task
	 */
	public function runTask(ITask $task)
	{
		try {
			$result = $task->run($this->setting->getContainer()->getConfig($task->getSectionName()));
			$this->resultStorage->writeSuccess($task->getName(), $result);
		}catch (\Exception $ex) {
			$this->resultStorage->writeError($task->getName(), $ex->getMessage());
		}
	}

	/**
	 * @param $taskName
	 */
	private function processTaskResult($taskName)
	{
		$result = $this->resultStorage->read($taskName);
		if($result[1] !== null) {
			$this->getResultSet()->addResult('Task "'. $taskName . '" completed with result:', Writer::INFO);
			if(is_array($result[1])) {
				$this->getResultSet()->addResults($result[1]);
			}else{
				$this->getResultSet()->addResult($result[1], $result[0]);
			}
		}else{
			$this->getResultSet()->addResult('Task "'. $taskName . '" completed!', Writer::SUCCESS);
		}
	}

	/**
	 * @param array $tasks
	 * @return $this
	 */
	protected function processTasks(array &$tasks)
	{
		$diff = count($this->threads) - $this->setting->getThreadsLimit();
		if($diff < 0) {
			foreach ($tasks as $name => $task) {
				if($diff >= 0) {
					break;
				}

				$this->threads[$name] = $this->createThread($task);
				unset($tasks[$name]);
				$diff++;
			}
		}

		return $this;
	}

	/**
	 * @param ITask $task
	 * @return Thread
	 */
	protected function createThread(ITask $task)
	{
		$this->getResultSet()->addResult('Running task "' . $task->getName() . '"', Writer::INFO);
		$thread = new Thread(array($this, 'runTask'));
		$thread->start($task);
		return $thread;
	}

	/**
	 * @return ResultSet
	 */
	protected function getResultSet()
	{
		if($this->resultSet === null) {
			$this->resultSet = new ResultSet($this->setting->isVerbose());
		}

		return $this->resultSet;
	}

	/**
	 * @return $this
	 */
	private function pause()
	{
		// let the CPU do its work
		sleep($this->setting->getThreadsSleepTime());
		return $this;
	}
}