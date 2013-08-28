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
	 * @return bool
	 */
	public function isVerboseMode();

	/**
	 * @return array
	 */
	public function getResults();

	/**
	 * @param $result
	 * @param $type
	 * @return $this
	 */
	public function addResult($result, $type = IWriter::SUCCESS);

	/**
	 * @return void
	 */
	public function dump();
}