<?php
/**
 * Class ITask
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 27.08.13
 */
namespace Tasker\Tasks;

interface ITask
{

	/**
	 * @return string
	 */
	public function getName();

	/**
	 * @param array $config
	 * @return mixed
	 */
	public function run($config);
}