<?php
/**
 * Class Settings
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 28.08.13
 */
namespace Tasker\Config;

class Settings implements ISettings
{

	/** @var array  */
	private $config = array();

	/**
	 * @param null $root
	 */
	function __construct($root = null)
	{
		if($root !== null) {
			$this->setRootPath($root);
		}
	}

	/**
	 * @param $root
	 * @return $this
	 */
	public function setRootPath($root)
	{
		return $this->set('rootPath', (string) $root);
	}


	/**
	 * @return string
	 */
	public function getRootPath()
	{
		return (string) $this->get('rootPath', getcwd());
	}

	/**
	 * @param $verbose
	 * @return $this
	 */
	public function setVerboseMode($verbose)
	{
		return $this->set('verboseMode', (bool) $verbose);
	}

	/**
	 * @return bool
	 */
	public function isVerboseMode()
	{
		return (bool) $this->get('verboseMode', PHP_SAPI === 'cli');
	}

	/**
	 * @param $name
	 * @param $value
	 * @return $this
	 */
	private function set($name, $value)
	{
		$this->config[$name] = $value;
		return $this;
	}

	/**
	 * @param $name
	 * @param $default
	 * @return mixed
	 */
	private function get($name, $default = null)
	{
		return (isset($this->config[$name])) ? $this->config[$name] : $default;
	}
}