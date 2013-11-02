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

class Runner extends Object implements IRunner
{

	/** @var  ISetting */
	private $setting;

	/** @var  ResultSet */
	private $resultSet;

	/**
	 * @param ISetting $setting
	 * @param IResultSet $resultSet
	 */
	function __construct(ISetting $setting, IResultSet $resultSet)
	{
		$this->setting = $setting;
		$this->resultSet = $resultSet;
	}

	/**
	 * @param \Tasker\Tasks\ITask[]|array $tasks
	 * @return IResultSet
	 */
	public function run(array $tasks)
	{
		foreach ($tasks as $task) {
			$this->resultSet->addResult('Running task "' . $task->getName() . '"', IWriter::INFO);
			$this->processTaskResult($task, $this->runTask($task));
		}

		return $this;
	}

	/**
	 * @param ITask $task
	 * @param $result
	 */
	protected function processTaskResult(ITask $task, $result)
	{
		if($result !== null) {
			$this->resultSet->addResult('Task "'. $task->getName() . '" completed with result:', IWriter::INFO);
			if(is_array($result)) {
				$this->resultSet->addResults($result);
			}else{
				$this->resultSet->addResult($result);
			}
		}else{
			$this->resultSet->addResult('Task "'. $task->getName() . '" completed!', IWriter::SUCCESS);
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
}