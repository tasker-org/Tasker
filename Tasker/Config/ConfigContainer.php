<?php
/**
 * Class ConfigContainer
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 28.08.13
 */
namespace Tasker\Config;

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
	 * @return array
	 */
	public function getContainer()
	{
		if($this->container === null) {
			if(count($this->configs)) {
				foreach($this->configs as $config) {
					/** @var IConfig $config */
					$content = $config->loadConfig()->getConfig();
					if(!is_array($content)) {
						throw new \InvalidArgumentException;
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
	 * @throws \InvalidArgumentException
	 */
	public function getSection($name)
	{
		$container = $this->getContainer();

		if(isset($container[$name])) {
			return $container[$name];
		}

		throw new \InvalidArgumentException;
	}

	/**
	 * @return array
	 */
	public function getConfigs()
	{
		return $this->configs;
	}
}