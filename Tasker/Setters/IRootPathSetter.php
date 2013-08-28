<?php
/**
 * Class IRootPathSetter
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 28.08.13
 */
namespace Tasker\Setters;

interface IRootPathSetter
{

	/**
	 * @param string $root
	 * @return $this
	 */
	public function setRootPath($root);
}