<?php
/**
 * Class Container
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 28.08.13
 */
namespace Tasker\Configuration;

use Tasker\InvalidStateException;
use Tasker\Object;
use Tasker\Threading\Threading;

class Container extends Object implements ISettings
{

	/** @var  array */
	private $container;

	/** @var array|IConfig[]  */
	private $configs = array();

	/**
	 * @param IConfig $config
	 * @return $this
	 */
	public function addConfig(IConfig $config)
	{
		$this->configs[] = $config;
		return $this;
	}

	/**
	 * @return array|null
	 * @throws \Tasker\InvalidStateException
	 */
	public function getContainer()
	{
		if($this->container === null) {
			$this->buildContainer();
		}

		return $this->container;
	}

	/**
	 * @return $this
	 * @throws \Tasker\InvalidStateException
	 */
	public function buildContainer()
	{
		if(count($this->configs)) {
			foreach($this->configs as $config) {
				$content = $config->loadConfig()->getConfig();
				if(is_array($content)) {
					$this->container = array_merge((array) $this->container, $content);
				}else if($content !== null && !is_array($content)) {
					throw new InvalidStateException('Config must be array, ' . gettype($content) . ' given.');
				}

			}
		}

		return $this;
	}

	/**
	 * @param $name
	 * @return mixed
	 */
	public function getSection($name)
	{
		$container = $this->getContainer();
		if(isset($container[$name])) {
			return $container[$name];
		}
	}

	/**
	 * @return array|IConfig[]
	 */
	public function getConfigs()
	{
		return $this->configs;
	}

	/**
	 * @return string
	 */
	public function getRootPath()
	{
		return (string) $this->getGlobalConfig('rootPath', getcwd());
	}

	/**
	 * @return bool
	 */
	public function isVerbose()
	{
		return (bool) $this->getGlobalConfig('verbose', PHP_SAPI === 'cli');
	}

	/**
	 * @return int
	 * @throws \Tasker\InvalidStateException
	 */
	public function getThreadsLimit()
	{
		$threadsLimit = (int) $this->getGlobalConfig('threadsLimit', 10);
		if($threadsLimit > 10000) {
			throw new InvalidStateException('Treads limit is too high. Set limit smaller then 10000.');
		}

		return $threadsLimit;
	}

	/**
	 * @return bool
	 */
	public function isMultithreading()
	{
		return (bool) $this->getGlobalConfig('multithreading', Threading::isAvailable());
	}

	/**
	 * @param string $name
	 * @param null $default
	 * @return mixed
	 */
	protected function getGlobalConfig($name, $default = null)
	{
		$container = $this->getContainer();
		if(isset($container[$name])) {
			return $container[$name];
		}

		return $default;
	}
}