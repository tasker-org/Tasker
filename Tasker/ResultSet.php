<?php
/**
 * Class Results
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 28.08.13
 */
namespace Tasker;

use Tasker\Output\IWriter;
use Tasker\Output\Writer;

class ResultSet implements IResultSet
{

	/** @var bool  */
	public $verbose = true;

	/** @var array  */
	private $results = array();

	/**
	 * @param $verboseMode
	 */
	function __construct($verboseMode)
	{
		$this->verbose = (bool) $verboseMode;
	}

	/**
	 * @return array
	 */
	public function getResults()
	{
		return $this->results;
	}

	/**
	 * @param array $results
	 * @return $this
	 */
	public function mergeResults(array $results)
	{
		if(count($results)) {
			foreach ($results as $type => $result) {
				if(!is_string($type)) {
					$type = IWriter::SUCCESS;
				}

				$this->addResult($result, $type);
			}
		}

		return $this;
	}

	/**
	 * @param $result
	 * @param $type
	 * @return $this
	 */
	public function addResult($result, $type = IWriter::SUCCESS)
	{
		$result = array($result, $type);
		if($this->verbose === true) {
			$this->printResult($result);
		}

		$this->results[] = $result;
		return $this;
	}

	/**
	 * @throws \ErrorException
	 */
	public function dump()
	{
		if($this->verbose === false) {
			if(!count($this->results)) {
				$this->results[] = array('NO TASKS EXECUTED', IWriter::INFO);
			}

			foreach ($this->results as $result) {
				if(!is_array($result)) {
					$result = array($result, IWriter::SUCCESS);
				}
				$this->printResult($result);
			}
		}
	}

	/**
	 * @param array $result
	 */
	protected function printResult(array $result)
	{
		list($message, $type) = $result;
		if($message instanceof \Exception) {
			Writer::writeException($message);
		}else{
			Writer::writeLn($message, $type);
		}
	}
}