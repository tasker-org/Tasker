<?php
/**
 * Class Task
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 30.10.13
 */
namespace Tasker\Tasks;

use Tasker\Object;
use Tasker\Configuration\ISetting;

abstract class Task extends Object implements ITask
{

	/** @var  ISetting */
	protected $setting;

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
	 * @param \Tasker\Configuration\ISetting $setting
	 * @return $this
	 */
	public function setSetting(ISetting $setting)
	{
		$this->setting = $setting;
		return $this;
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