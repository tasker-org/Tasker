<?php
/**
 * Class ISettings
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 28.08.13
 */
namespace Tasker\Config;

interface ISettings
{

	/**
	 * @return string
	 */
	public function getRootPath();

	/**
	 * @return bool
	 */
	public function isVerboseMode();

	/**
	 * @return int
	 */
	public function getThreadsLimit();
}