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
use Tasker\Threading\Thread;

class Runner
{

	/** @var \Tasker\Config\ConfigContainer  */
	private $config;

	/** @var \Tasker\TasksContainer  */
	private $tasks;

	/** @var array|Thread[] */
	private $threads = array();

	/** @var  string */
	private $sharedMemory;

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
			$sem_id = sem_get(1);
			sem_acquire($sem_id);
			$this->sharedMemory = shm_attach(2, 5500);


			foreach ($tasks as $taskName) {
				if($set->isVerboseMode()) {
					$set->addResult('Running task "' . $taskName . '"', Writer::INFO);
				}

				$thread = new Thread(array($this, 'runTask'));
				$thread->start($taskName, $this->sharedMemory);
				$this->threads[$taskName] = $thread;
			}

			$this->cleanThreads($set);
		}

		return $set;
	}

	/**
	 * @param $name
	 * @param $memoryId
	 */
	public function runTask($name, $memoryId)
	{
		try {
			$task = $this->tasks->getTask($name);
			$result = array(IWriter::SUCCESS, $task->run($this->config->getSection($task->getSectionName())));
		}catch (\Exception $ex) {
			$result = array(IWriter::ERROR, $ex->getMessage());
		}

		shm_put_var($memoryId, $name, $result);
	}

	private function cleanThreads(IResultSet $set)
	{
		while( !empty($this->threads) ) {
			foreach($this->threads as $name => $thread ) {
				if(!$thread->isAlive()) {
					unset($this->threads[$name]);
					$set->addResult('Task '. $name . ' completed!', Writer::INFO);
					list($type, $result) = (array) shm_get_var($this->sharedMemory, $name);
					if($result !== null) {
						if(is_array($result)) {
							$set->mergeResults($result);
						}else{

							$set->addResult($result, $type);
						}
					}
				}
			}


			// let the CPU do its work
			sleep(1);
		}
	}
}