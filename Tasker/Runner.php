<?php
/**
 * Class Runner
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 27.08.13
 */
namespace Tasker;

use Tasker\Configuration\Container;
use Tasker\Output\IWriter;
use Tasker\Output\Writer;
use Tasker\Tasks\ITask;
use Tasker\Utils\Timer;
use Tasker\Threading\Memory;
use Tasker\Threading\Thread;

class Runner
{

	const HALF_SECOND = 50000;

	/** @var \Tasker\Configuration\Container  */
	private $config;

	/** @var \Tasker\ResultSet  */
	private $resultSet;

	/** @var array|Thread[] */
	private $threads = array();

	/**
	 * @param Container $config
	 */
	function __construct(Container $config)
	{
		$this->config = $config;
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
			$this->getResultSet()->addResult('Running tasks...', IWriter::NONE);
			Memory::init();

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

			Memory::clear();
			$this->getResultSet()->addResult('Tasks completed in ' . Timer::convert(Timer::d('process'), Timer::SECONDS) . ' s', IWriter::NONE);
		}else{
			$this->getResultSet()->addResult('No tasks for process.', IWriter::NONE);
		}

		return $this->getResultSet();
	}

	/**
	 * @param ITask $task
	 */
	public function runTask(ITask $task)
	{
		try {
			$result = array(IWriter::SUCCESS, $task->run($this->config->getSection($task->getSectionName())));
		}catch (\Exception $ex) {
			$result = array(IWriter::ERROR, $ex->getMessage());
		}

		Memory::set($task->getName(), $result);
	}

	/**
	 * @param $taskName
	 */
	private function processTaskResult($taskName)
	{
		list($type, $result) = (array) Memory::get($taskName);
		if($result !== null) {
			$this->getResultSet()->addResult('Task "'. $taskName . '" completed with result:', Writer::INFO);
			if(is_array($result)) {
				$this->getResultSet()->mergeResults($result);
			}else{
				$this->getResultSet()->addResult($result, $type);
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
		$diff = count($this->threads) - $this->config->getThreadsLimit();
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

	protected function getResultSet()
	{
		if($this->resultSet === null) {
			$this->resultSet = new ResultSet($this->config->isVerbose());
		}

		return $this->resultSet;
	}

	/**
	 * @return $this
	 */
	private function pause()
	{
		// let the CPU do its work
		usleep(self::HALF_SECOND);
		return $this;
	}
}