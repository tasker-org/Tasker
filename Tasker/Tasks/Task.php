<?php
/**
 * Class Task
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 30.10.13
 */
namespace Tasker\Tasks;

use Nette\Object;

abstract class Task extends Object implements ITask
{

	/** @var  string */
	public static $className;

	/**
	 * @return string
	 */
	public function getSectionName()
	{
		return $this->getName();
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return self::getClassName();
	}

	/**
	 * @return string
	 */
	public static function getClassName()
	{
		if(static::$className === null) {
			$name = self::getReflection()->getShortName();
			static::$className = lcfirst(str_replace('Task', '', $name));
		}

		return static::$className;
	}

} 