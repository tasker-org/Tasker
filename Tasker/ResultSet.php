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
	public $verbose;

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
	public function addResults(array $results)
	{
		foreach ($results as $type => $result) {
			$this->addResult($result, $type);
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
		$this->results[] = array($result, $type);
		if($this->verbose === true) {
			$this->printResult($result, $type);
		}
		return $this;
	}

	/**
	 * @param \Exception|string $message
	 * @param null $type
	 * @return $this
	 */
	public function printResult($message, $type = IWriter::NONE)
	{
		if($message instanceof \Exception) {
			Writer::writeException($message);
		}else{
			Writer::writeLn($message, $type);
		}

		return $this;
	}
}