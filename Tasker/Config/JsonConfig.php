<?php
/**
 * Class JsonConfig
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 27.08.13
 */
namespace Tasker\Config;

class JsonConfig extends Config
{

	/**
	 * @return array
	 */
	public function getConfig()
	{
		return $this->objectToArray(json_decode(parent::getConfig()));
	}

	/**
	 * @param string $name
	 * @return mixed
	 * @throws \InvalidArgumentException
	 */
	public function getConfigSection($name)
	{
		$config = $this->getConfig();
		if(isset($config->$name)) {
			return $config->$name;
		}

		throw new \InvalidArgumentException;
	}

}