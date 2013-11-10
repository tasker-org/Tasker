**Tasker** is simple & powerful task runner written in php

###Minimal configuration
**Example of usage Tasker with default tasks**

```php

<?php

define('ROOT', realpath(__DIR__ . '/..'));

require ROOT . '/libs-dev/autoload.php';

$tasker = new \Tasker\Tasker;
$tasker->addConfig(ROOT . '/config/tasker.json')
	//->addConfig(array('rootPath' => ROOT))
	->registerTask(new \Tasker\Copy\CopyFilesTask(), 'copy')
	->registerTask(new \Tasker\Concat\ConcatFilesTask(), 'js');
$tasker->run();

```

##Available settings
*example of settings from file **ROOT . '/config/tasker.json'***

```json
	{
	  "multithreading": {
	    "allow": true,
	    "limit": 10, //threads limit
	    "sleep": 1, //in seconds, make CPU to do it
	    "storage": "temp/results" //for storing results in diferrent threads
	  },
	  "rootPath": "/path/to/your/repository", //default: getcwd()
	  "verbose": "false", //if you are runnig script from console, value is set to TRUE
	}
```