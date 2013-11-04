<?php
/**
 * Class Container
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 28.08.13
 */
namespace Tasker\Configuration;

use Tasker\ArrayHash;
use Tasker\InvalidStateException;
use Tasker\Object;

class Container extends Object
{

	/** @var  array */
	private $container;

	/** @var bool  */
	private $locked = false;

	/** @var array|IConfig[]  */
	private $configs = array();

	/**
	 * @return $this
	 */
	public function lock()
	{
		$this->locked = true;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isLocked()
	{
		return $this->locked;
	}

	/**
	 * @param IConfig $config
	 * @return $this
	 */
	public function addConfiguration(IConfig $config)
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
		if($this->container === null || !$this->isLocked()) {
			$this->buildContainer();
		}

		return $this->container;
	}

	/**
	 * @param string $name
	 * @param null $default
	 * @return mixed
	 */
	public function getConfig($name, $default = null)
	{
		$container = $this->getContainer();
		if(isset($container[$name])) {
			return $container[$name];
		}

		return $default;
	}

	/**
	 * @param string $name
	 * @return array|null
	 * @throws \Tasker\InvalidStateException
	 */
	public function getNamespace($name)
	{
		$namespace = $this->getConfig($name, array());
		if($namespace !== null && !is_array($namespace) && !is_object($namespace)) {
			throw new InvalidStateException('Configuration for "' . $name . '" must be array.');

		}

		return $namespace;
	}

	/**
	 * @param string $namespace
	 * @param string $configName
	 * @param null $default
	 * @return mixed
	 */
	public function getConfigInNamespace($namespace, $configName, $default = null)
	{
		$namespace = $this->getNamespace($namespace);
		if(isset($namespace[$configName])) {
			return $namespace[$configName];
		}

		return $default;
	}

	/**
	 * @return $this
	 * @throws \Tasker\InvalidStateException
	 */
	public function buildContainer()
	{
		if($this->isLocked()) {
			throw new InvalidStateException('Cannot build container again. Object is locked.');
		}

		foreach($this->configs as $config) {
			$content = $config->loadConfig()->getConfig();
			if(is_array($content) || is_object($content)) {
				$this->container = array_merge((array) $this->container, $content);
			}
		}

		$this->container = ArrayHash::from($this->container);

		return $this;
	}
}