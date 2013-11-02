<?php
/**
 * Class Task
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 30.10.13
 */
namespace Tasker\Tasks;

use Tasker\Object;

abstract class Task extends Object implements ITask
{

	/** @var  string */
	protected $name;

	/** @var  string */
	protected $sectionName;

	function __construct()
	{
		$this->name = self::getClassName();
		$this->sectionName = $this->name;
	}

	/**
	 * @param string $name
	 * @return $this
	 */
	public function setName($name)
	{
		$this->name = $name;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getSectionName()
	{
		return $this->sectionName;
	}

	/**
	 * @param string $sectionName
	 * @return $this
	 */
	public function setSectionName($sectionName)
	{
		$this->sectionName = $sectionName;
		return $this;
	}

	/**
	 * @return string
	 */
	public static function getClassName()
	{
		$name = static::getReflection()->getShortName();
		return lcfirst(str_replace('Task', '', $name));
	}
} 