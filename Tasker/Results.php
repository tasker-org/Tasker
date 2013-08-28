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

class Results
{

	/** @var array  */
	private $results = array();

	/**
	 * @param array $results
	 */
	function __construct(array $results = array())
	{
		$this->results = $results;
	}

	/**
	 * @param $result
	 * @param $type
	 * @return $this
	 */
	public function addResult($result, $type = Writer::SUCCESS)
	{
		$this->results[] = array($result, $type);
		return $this;
	}

	/**
	 * @return void
	 */
	public function dump()
	{
		if(!count($this->results)) {
			$this->results[] = array('NO TASKS EXECUTED', Writer::SUCCESS);
		}

		foreach ($this->results as $result) {
			list($message, $type) = $result;
			if($message instanceof \Exception) {
				Writer::writeException($message);
			}else{
				Writer::writeLn($message, $type);
			}
		}
	}
}