<?php
/**
 * Class Config
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 27.08.13
 */
namespace Tasker\Config;

use Tasker\InvalidArgumentException;
use Tasker\InvalidStateException;

abstract class Config implements IConfig
{

	/** @var  mixed */
	private $config;

	/** @var string  */
	protected $configPath;

	/**
	 * @param $configPath
	 * @throws InvalidArgumentException
	 */
	function __construct($configPath)
	{
		$this->configPath = (string) $configPath;

		if(!file_exists($this->configPath)) {
			throw new InvalidArgumentException('Given path "' . $this->configPath . '" does not exist.');
		}
	}

	/**
	 * @return $this
	 */
	public function loadConfig()
	{
		$this->config = file_get_contents($this->configPath);
		return $this;
	}

	/**
	 * @return mixed
	 * @throws \Tasker\InvalidStateException
	 */
	public function getConfig()
	{
		if($this->config === null) {
			throw new InvalidStateException('Please call method loadConfig first');
		}

		return $this->config;
	}
}