<?php
/**
 * Class Object
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 31.10.13
 */
namespace Tasker;

class Object
{

	/**
	 * Access to reflection.
	 *
	 * @return \ReflectionClass
	 */
	public /**/static/**/ function getReflection()
	{
		return new \ReflectionClass(/*5.2*$this*//**/get_called_class()/**/);
	}
} 