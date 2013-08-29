Tasker
======

Tasks runner written in php

##Minimal configuration
**Example of usage Tasker with default tasks**
```sh
#!/usr/bin/env php

<?php

define('ROOT', realpath(__DIR__ . '/..'));

require ROOT . '/libs-dev/autoload.php';

/**
 * @return bool
 */
function isLocal()
{
	$opts = getopt('l:');
	if(isset($opts['l']) && (bool) $opts['l'] === true) {
		return true;
	}

	return false;
}

$tasker = new \Tasker\Tasker;
$tasker->addConfig(ROOT . '/config/files.json')
	->registerTask(new \Tasker\Copy\CopyFilesTask(), 'copy')
	->registerTask(new \Tasker\Concat\ConcatFilesTask(), 'js');

if(isLocal()) {
	$tasker->registerTask(new \Tasker\Concat\ConcatFilesTask(), 'css');
}else{
	$tasker->registerTask(new \Tasker\Minify\MinifyCssTask(), 'css');
}

$tasker->run()->dump();

```