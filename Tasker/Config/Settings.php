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

	/** @var  string */
	private $rootPath;

	/** @var  bool */
	private $verboseMode;

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
		$this->rootPath = (string) $root;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getRootPath()
	{
		if($this->rootPath !== null) {
			return $this->rootPath;
		}

		return getcwd();
	}

	/**
	 * @param $verbose
	 * @return $this
	 */
	public function setVerboseMode($verbose)
	{
		$this->verboseMode = (bool) $verbose;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isVerboseMode()
	{
		if($this->verboseMode !== null) {
			return $this->verboseMode;
		}

		return PHP_SAPI === 'cli';
	}
}