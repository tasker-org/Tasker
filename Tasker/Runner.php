<?php
/**
 * Class Runner
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 27.08.13
 */
namespace Tasker;

use Tasker\Config\ConfigContainer;
use Tasker\Output\IWriter;
use Tasker\Output\Writer;
use Tasker\Threading\Memory;
use Tasker\Threading\Thread;

class Runner
{

	/** @var \Tasker\Config\ConfigContainer  */
	private $config;

	/** @var \Tasker\TasksContainer  */
	private $tasks;

	/** @var array|Thread[] */
	private $threads = array();

	/**
	 * @param ConfigContainer $config
	 * @param TasksContainer $tasks
	 */
	function __construct(ConfigContainer $config, TasksContainer $tasks)
	{
		$this->config = $config;
		$this->tasks = $tasks;
	}

	/**
	 * @param IResultSet $set
	 * @return IResultSet
	 */
	public function run(IResultSet $set)
	{
		$tasks = $this->tasks->getTasksName();
		if(count($tasks)) {
			Memory::init();

			foreach ($tasks as $taskName) {
				if($set->isVerboseMode()) {
					$set->addResult('Running task "' . $taskName . '"', Writer::INFO);
				}

				$thread = new Thread(array($this, 'runTask'));
				$thread->start($taskName);
				$this->threads[$taskName] = $thread;
			}

			$this->cleanThreads($set);

			Memory::release();
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
	 */
	private function cleanThreads(IResultSet $set)
	{
		while( !empty($this->threads) ) {
			foreach($this->threads as $name => $thread ) {
				if(!$thread->isAlive()) {
					unset($this->threads[$name]);
					list($type, $result) = (array) Memory::get($name);
					if($result !== null) {
						$set->addResult('Task '. $name . ' completed with result:', Writer::INFO);
						if(is_array($result)) {
							$set->mergeResults($result);
						}else{
							$set->addResult($result, $type);
						}
					}else{
						$set->addResult('Task '. $name . ' completed!', Writer::INFO);
					}
				}
			}

			// let the CPU do its work
			sleep(1);
		}
	}
}