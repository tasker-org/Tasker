<?php
/**
 * Class Config
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 27.08.13
 */
namespace Tasker\Config;

abstract class Config implements IConfig
{

	/** @var  mixed */
	private $config;

	/** @var string  */
	protected $configPath;

	/**
	 * @param $configPath
	 * @throws \InvalidArgumentException
	 */
	function __construct($configPath)
	{
		$this->configPath = (string) $configPath;

		if(!file_exists($this->configPath)) {
			throw new \InvalidArgumentException;
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
	 * @throws \InvalidArgumentException
	 */
	public function getConfig()
	{
		if($this->config === null) {
			throw new \InvalidArgumentException('Please call method loadConfig first');
		}

		return $this->config;
	}

	/**
	 * @param object $obj
	 * @return array
	 */
	protected function objectToArray($obj)
	{
		if(is_object($obj)) {
			$obj = (array) $obj;
		}

		if(is_array($obj)) {
			$new = array();
			foreach($obj as $key => $val) {
				$new[$key] = $this->objectToArray($val);
			}
		} else {
			$new = $obj;
		}

		return $new;
	}
}