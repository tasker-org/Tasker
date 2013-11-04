<?php
/**
 * Class ResultStorage
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 02.11.13
 */
namespace Tasker\Threading;

use Tasker\Object;
use Tasker\Output\IWriter;
use Tasker\Utils\FileSystem;

class ResultStorage extends Object
{

	/** @var  string */
	private $storage;

	/**
	 * @param $storage
	 */
	function __construct($storage)
	{
		$this->storage = (string) $storage;
		FileSystem::cleanDir($this->storage, false);
	}


	/**
	 * @param $taskName
	 * @param $result
	 * @return $this
	 */
	public function writeSuccess($taskName, $result)
	{
		$this->write($taskName, $result, IWriter::SUCCESS);
		return $this;
	}

	/**
	 * @param $taskName
	 * @param $result
	 * @return $this
	 */
	public function writeError($taskName, $result)
	{
		$this->write($taskName, $result, IWriter::ERROR);
		return $this;
	}

	/**
	 * @param $taskName
	 * @return array
	 */
	public function read($taskName)
	{
		if(file_exists($file = $this->getFilePath($taskName))) {
			$content = FileSystem::read($file);
			return eval('return ' . $content . ';');
		}

		return array(null, null);
	}

	/**
	 * @param $taskName
	 * @param $result
	 * @param $type
	 * @return int
	 */
	protected function write($taskName, $result, $type)
	{
		$result = array($type, $result);
		return FileSystem::write($this->getFilePath($taskName), var_export($result, true));
	}

	/**
	 * @param $taskName
	 * @return string
	 */
	protected function getFilePath($taskName)
	{
		return $this->storage . '/task-' . (string) $taskName . '.txt';
	}
} 