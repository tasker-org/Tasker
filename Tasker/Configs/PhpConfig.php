<?php
/**
 * Class PhpConfig
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 04.11.13
 */
namespace Tasker\Configs;

use Tasker\Configuration\Config;

class PhpConfig extends Config
{

	const EXTENSION = 'php';

	/**
	 * @return $this
	 */
	public function loadConfig()
	{
		$this->config = include $this->configPath;
		return $this;
	}
}