#Tasker

Simple & powerful task runner written in php

##Minimal configuration
**Example of usage Tasker with default tasks**
```php

<?php

define('ROOT', realpath(__DIR__ . '/..'));

require ROOT . '/libs-dev/autoload.php';

$tasker = new \Tasker\Tasker;
$tasker->addConfig(ROOT . '/config/files.json')
	//->addConfig(array('rootPath' => ROOT))
	->registerTask(new \Tasker\Copy\CopyFilesTask(), 'copy')
	->registerTask(new \Tasker\Concat\ConcatFilesTask(), 'js');
$tasker->run();

```