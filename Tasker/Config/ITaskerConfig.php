<?php
/**
 * Class ITaskerConfig
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 28.08.13
 */
namespace Tasker\Config;

interface ITaskerConfig extends IConfig
{

	/**
	 * @return string
	 */
	public function getRootPath();

	/**
	 * @return bool
	 */
	public function getVerboseMode();

	/**
	 * @return int
	 */
	public function getThreadsLimit();
}