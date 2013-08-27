<?php
/**
 * Class Results
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 28.08.13
 */
namespace Tasker;

class Results
{

	/** @var array  */
	private $results = array();

	/**
	 * @param $result
	 * @return $this
	 */
	public function addResult($result)
	{
		$this->results[] = $result;
		return $this;
	}

	/**
	 * @return void
	 */
	public function dump()
	{
		if(count($this->results)) {
			foreach ($this->results as $result) {
				echo $result . PHP_EOL;
			}
		}else{
			echo 'NO TASKS EXECUTED';
		}
	}
}