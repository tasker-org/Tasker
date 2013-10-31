<?php
/**
 * Class IResultSet
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 28.08.13
 */

namespace Tasker;

use Tasker\Output\IWriter;

interface IResultSet
{

	/**
	 * @return array
	 */
	public function getResults();

	/**
	 * @param array $results
	 * @return $this
	 */
	public function addResults(array $results);

	/**
	 * @param $result
	 * @param $type
	 * @return $this
	 */
	public function addResult($result, $type = IWriter::NONE);

	/**
	 * @param string|\Exception $message
	 * @param null $type
	 * @return $this
	 */
	public function printResult($message, $type = IWriter::NONE);
}