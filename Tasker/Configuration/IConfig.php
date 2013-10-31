<?php
/**
 * Class IConfig
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 27.08.13
 */

namespace Tasker\Configuration;


interface IConfig
{

	/**
	 * @return $this
	 */
	public function loadConfig();

	/**
	 * @return array
	 */
	public function getConfig();

}