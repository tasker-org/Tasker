<?php
/**
 * Class Settings
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 28.08.13
 */
namespace Tasker\Config;

use Tasker\InvalidArgumentException;

class Settings implements ISettings
{

	/** @var  string */
	private $rootPath;

	/** @var  bool */
	private $verboseMode;
	
	/** @var int  */
	private $threadsLimit = 10;

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

	/**
	 * @param $threadsLimit
	 * @return $this
	 * @throws \Tasker\InvalidArgumentException
	 */
	public function setThreadsLimit($threadsLimit)
	{
		if($threadsLimit > 10000) {
			throw new InvalidArgumentException('Treads limit is too high. Set limit smaller then 10000');
		}

		$this->threadsLimit = (int) $threadsLimit;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getThreadsLimit()
	{
		return $this->threadsLimit;
	}
}