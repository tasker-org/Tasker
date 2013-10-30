<?php
/**
 * Class Memory
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 30.10.13
 */
namespace Tasker\Threading;

use Nette\Object;
use Tasker\InvalidStateException;

class Memory extends Object
{

	const SIZE = 5512;
	const SEMKEY = 1;
	const SHMKEY = 2;

	/** @var  resource */
	public static $semId;

	/** @var  resource */
	public static $shmId;

	/**
	 * @return void
	 */
	public static function init()
	{
		static::getSem();
		static::acquireSem();
		static::attachShm();
	}

	/**
	 * @throws \Tasker\InvalidStateException
	 */
	public static function getSem()
	{
		static::$semId = sem_get(static::SEMKEY);

		if(static::$semId === false) {
			throw new InvalidStateException('Failed to create semaphore');
		}

		static::$semId;
	}

	/**
	 * @throws \Tasker\InvalidStateException
	 */
	public static function acquireSem()
	{
		if(!sem_acquire(static::$semId)) {
			static::removeSem();
			throw new InvalidStateException('Failed to acquire semaphore ' . static::$semId);
		}
	}

	/**
	 * @return mixed
	 * @throws \Tasker\InvalidStateException
	 */
	public static function attachShm()
	{
		static::$shmId = shm_attach(static::SEMKEY, static::SIZE);

		if(static::$shmId === false) {
			static::removeSem();
			throw new InvalidStateException('Fail to attach shared memory');
		}

		return static::$shmId;
	}

	/**
	 * @return bool
	 */
	public static function removeSem()
	{
		return sem_remove(static::$semId);
	}

	/**
	 * @return bool
	 */
	public static function removeShm()
	{
		return shm_remove(static::$shmId);
	}

	/**
	 * @param $name
	 * @param $var
	 */
	public static function set($name, $var)
	{
		if(!shm_put_var(static::$shmId, $name, $var)) {
			static::removeSem();
			static::removeShm();
		}
	}

	/**
	 * @param $name
	 * @return mixed
	 */
	public static function get($name)
	{
		return shm_get_var(static::$shmId, $name);
	}

	/**
	 * @return bool
	 */
	public static function releaseSem()
	{
		return sem_release(static::$semId);
	}

	/**
	 * @return void
	 */
	public static function release()
	{
		static::releaseSem();
		static::removeShm();
		static::removeSem();
	}

} 