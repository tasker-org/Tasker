<?php
/**
 * Class ITaskService
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 28.08.13
 */
namespace Tasker\Tasks;

interface ITaskService
{

	/**
	 * @param array $config
	 * @return mixed
	 */
	public function run(array $config);
}