<?php
/**
 * Class Setting
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 02.11.13
 */
namespace Tasker\Configuration;

use Tasker\Client\Environment;
use Tasker\Object;
use Tasker\InvalidStateException;

class Setting extends Object implements ISetting
{

	/** @var  Container */
	private $container;

	/**
	 * @param Container $container
	 */
	function __construct(Container $container)
	{
		$this->container = $container;
	}

	/**
	 * @return string
	 */
	public function getRootPath()
	{
		return (string) $this->container->getConfig('rootPath', getcwd());
	}

	/**
	 * @return bool
	 */
	public function isVerbose()
	{
		return (bool) $this->container->getConfig('verbose', PHP_SAPI === 'cli');
	}

	/**
	 * @return int
	 * @throws \Tasker\InvalidStateException
	 */
	public function getThreadsLimit()
	{
		$threadsLimit = (int) $this->container->getConfigInNamespace(self::MULTITHREADING, 'limit', 10);
		if($threadsLimit > 10000) {
			throw new InvalidStateException('Treads limit is too high. Set limit smaller then 10000.');
		}

		return $threadsLimit;
	}

	/**
	 * @return bool
	 */
	public function isMultiThreading()
	{
		$isAvailable = Environment::isMultiThreading();
		$allowed = (bool) $this->container->getConfigInNamespace(self::MULTITHREADING, 'allow', $isAvailable);
		if($allowed === true && $isAvailable === false) {
			$allowed = false;
		}

		return $allowed;
	}

	/**
	 * @return int
	 */
	public function getThreadsSleepTime()
	{
		return (int) $this->container->getConfigInNamespace(self::MULTITHREADING, 'sleep', 1);
	}

	/**
	 * @return Container
	 */
	public function getContainer()
	{
		return $this->container;
	}
}