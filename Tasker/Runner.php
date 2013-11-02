<?php
/**
 * Class Runner
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 02.11.13
 */
namespace Tasker;

class Runner extends Object implements IRunner
{

	/**
	 * @param TasksContainer $tasks
	 * @return IResultSet
	 */
	public function run(TasksContainer $tasks)
	{
		throw new InvalidStateException('Not implemented.');
	}
}