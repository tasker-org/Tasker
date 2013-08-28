<?php
/**
 * Class ConfigContainer
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 28.08.13
 */
namespace Tasker\Config;

use Tasker\InfoException;
use Tasker\InvalidStateException;

class ConfigContainer
{

	/** @var  array */
	private $container;

	/** @var array  */
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
			if(count($this->configs)) {
				foreach($this->configs as $config) {
					/** @var IConfig $config */
					$content = $config->loadConfig()->getConfig();
					if(!is_array($content)) {
						throw new InvalidStateException('Config must be array, ' . gettype($content) . ' given.');
					}
					$this->container = array_merge((array) $this->container, $content);
				}
			}
		}

		return $this->container;
	}

	/**
	 * @param $name
	 * @return mixed
	 * @throws \Tasker\InfoException
	 */
	public function getSection($name)
	{
		$container = $this->getContainer();

		if(isset($container[$name])) {
			return $container[$name];
		}

		throw new InfoException('Configuration for task "' . $name . '" does not exist.');
	}

	/**
	 * @return array
	 */
	public function getConfigs()
	{
		return $this->configs;
	}
}