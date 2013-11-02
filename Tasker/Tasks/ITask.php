<?php
/**
 * Class ITask
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 27.08.13
 */
namespace Tasker\Tasks;

use Tasker\Configuration\ISetting;

interface ITask extends ITaskService
{

	/**
	 * @param ISetting $setting
	 * @return $this
	 */
	public function setSetting(ISetting $setting);

	/**
	 * @param string $name
	 * @return $this
	 */
	public function setName($name);

	/**
	 * @return string
	 */
	public function getName();

	/**
	 * @return string
	 */
	public function getSectionName();

	/**
	 * @param string $name
	 * @return $this
	 */
	public function setSectionName($name);
}