<?php
/**
 * Class IRunner
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 02.11.13
 */

namespace Tasker;

interface IRunner
{

	/**
	 * @param \Tasker\Tasks\ITask[]|array $tasks
	 * @return IResultSet
	 */
	public function run(array $tasks);
} 