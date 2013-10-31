<?php
/**
 * Class ArrayConfig
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 31.10.13
 */
namespace Tasker\Configs;

use Tasker\Configuration\IConfig;
use Tasker\Object;

class ArrayConfig extends Object implements IConfig
{

	/** @var  array */
	private $config;

	/**
	 * @param array $config
	 */
	function __construct(array $config)
	{
		$this->config = $config;
	}

	/**
	 * @return $this
	 */
	public function loadConfig()
	{
		return $this;
	}

	/**
	 * @return array
	 */
	public function getConfig()
	{
		return $this->config;
	}
}