<?php
/**
 * Class ISettings
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 31.10.13
 */

namespace Tasker\Configuration;


interface ISetting
{

	const MULTITHREADING = 'multithreading';

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
	public function isMultiThreading();

	/**
	 * @return int
	 */
	public function getThreadsSleepTime();

	/**
	 * @return Container
	 */
	public function getContainer();

} 