<?php
/**
 * Class ThreadsRunner
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 27.08.13
 */
namespace Tasker\Threading;

use Tasker\Configuration\ISetting;
use Tasker\IResultSet;
use Tasker\Output\IWriter;
use Tasker\Tasks\ITask;
use Tasker\Object;
use Tasker\IRunner;
use Tasker\Utils\Randomizer;

class ThreadsRunner extends Object implements IRunner
{

	/** @var \Tasker\Configuration\ISetting  */
	private $setting;

	/** @var \Tasker\ResultSet  */
	private $resultSet;

	/** @var array */
	private $threads = array();

	/** @var \Tasker\Threading\ResultStorage  */
	private $resultStorage;

	/**
	 * @param ISetting $setting
	 * @param IResultSet $resultSet
	 */
	function __construct(ISetting $setting, IResultSet $resultSet)
	{
		$this->setting = $setting;
		$this->resultSet = $resultSet;
		$this->resultStorage = new ResultStorage($setting->getThreadsResultStorage());
	}

	/**
	 * @param \Tasker\Tasks\ITask[]|array $tasks
	 * @return \Tasker\IResultSet
	 */
	public function run(array $tasks)
	{
		$this->processTasks($tasks);

		while(!empty($this->threads)) {
			foreach($this->threads as $storage => $item) {
				list($thread, $task) = $item;

				if(!$thread->isAlive()) {
					unset($this->threads[$storage]);
					$this->processTaskResult($task, $storage);
				}
			}

			$this->processTasks($tasks);
			$this->pause();
		}

		return $this->resultSet;
	}

	/**
	 * @param ITask $task
	 * @param string $storage
	 */
	public function runTask(ITask $task, $storage)
	{
		try {
			$result = $task->run($this->setting->getContainer()->getConfig($task->getSectionName()));

			if($result !== null) {
				$this->resultStorage->writeSuccess($storage, $result);
			}
		}catch (\Exception $ex) {
			$this->resultStorage->writeError($storage, $ex->getMessage());
		}
	}

	/**
	 * @param array $tasks
	 */
	protected function processTasks(array &$tasks)
	{
		$diff = count($this->threads) - $this->setting->getThreadsLimit();
		if($diff < 0) {
			foreach ($tasks as $name => $task) {
				if($diff >= 0) {
					break;
				}

				$this->startThread($task);
				unset($tasks[$name]);
				$diff++;
			}
		}
	}

	/**
	 * @param ITask $task
	 */
	protected function startThread(ITask $task)
	{
		$this->resultSet->addResult('Running task "' . $task->getName() . '"', IWriter::INFO);
		$thread = new Thread(array($this, 'runTask'));
		$storage = $this->createStorageName($task->getName());
		$thread->start($task, $storage);
		$this->threads[$storage] = array($thread, $task);
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

	/**
	 * @param ITask $task
	 * @param string $storage
	 */
	private function processTaskResult(ITask $task, $storage)
	{
		$result = $this->resultStorage->read($storage);
		if($result[1] !== null) {
			$this->resultSet->addResult('Task "'. $task->getName() . '" completed with result:', IWriter::INFO);
			if(is_array($result[1])) {
				$this->resultSet->addResults($result[1]);
			}else{
				$this->resultSet->addResult($result[1], $result[0]);
			}
		}else{
			$this->resultSet->addResult('Task "'. $task->getName() . '" completed!', IWriter::SUCCESS);
		}
	}

	/**
	 * @param $taskName
	 * @return string
	 */
	private function createStorageName($taskName)
	{
		return $taskName . '-' . Randomizer::generate(5);
	}
}