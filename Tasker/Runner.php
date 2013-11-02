<?php
/**
 * Class Runner
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 02.11.13
 */
namespace Tasker;

use Tasker\Configuration\ISetting;
use Tasker\Tasks\ITask;
use Tasker\Output\IWriter;
use Tasker\Utils\Timer;

class Runner extends Object implements IRunner
{

	/** @var  ISetting */
	private $setting;

	/** @var  ResultSet */
	private $resultSet;

	/**
	 * @param ISetting $setting
	 */
	function __construct(ISetting $setting)
	{
		$this->setting = $setting;
	}

	/**
	 * @param \Tasker\Tasks\ITask[]|array $tasks
	 * @return IResultSet
	 */
	public function run(array $tasks)
	{
		if(count($tasks)) {
			Timer::d('process');
			$this->getResultSet()->printResult('Running tasks...');

			foreach ($tasks as $task) {
				$this->getResultSet()->addResult('Running task "' . $task->getName() . '"', IWriter::INFO);
				$this->processTaskResult($task, $this->runTask($task));
			}

			$this->getResultSet()->printResult('Tasks completed in ' . Timer::convert(Timer::d('process'), Timer::SECONDS) . ' s');
		}else{
			$this->getResultSet()->printResult('No tasks for process.');
		}

		return $this->getResultSet();
	}

	/**
	 * @param ITask $task
	 * @param $result
	 */
	protected function processTaskResult(ITask $task, $result)
	{
		if($result !== null) {
			$this->getResultSet()->addResult('Task "'. $task->getName() . '" completed with result:', IWriter::INFO);
			if(is_array($result)) {
				$this->getResultSet()->addResults($result);
			}else{
				$this->getResultSet()->addResult($result);
			}
		}else{
			$this->getResultSet()->addResult('Task "'. $task->getName() . '" completed!', IWriter::SUCCESS);
		}
	}

	/**
	 * @param ITask $task
	 * @return \Exception|mixed
	 */
	protected function runTask(ITask $task)
	{
		try {
			$result = $task->run($this->setting->getContainer()->getConfig($task->getSectionName()));
		}catch (\Exception $ex) {
			$result = $ex;
		}

		return $result;
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
}