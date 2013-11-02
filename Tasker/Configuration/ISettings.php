<?php
/**
 * Class ISettings
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 31.10.13
 */

namespace Tasker\Configuration;


interface ISettings
{

	/**
	 * @return string
	 */
	public function getRootPath();

	/**
	 * @return bool
	 */
	public function isVerbose();

	/**
	 * @return int
	 */
	public function getThreadsLimit();

	/**
	 * @return bool
	 */
	public function isMultithreading();

} 