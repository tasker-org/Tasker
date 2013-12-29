**Tasker** is simple & powerful task runner written in php

**With support of "multi threading"** - make your tasks done very fast.

![Tasker](http://tasker.jsifalda.name/screens/tasker.png "Terminal")

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
*example of settings from file ROOT . '/config/tasker.json'*

```json
	{
	  "multithreading": {
	    "allow": true,
	    "limit": 10,
	    "sleep": 1,
	    "storage": "temp/results"
	  },
	  "rootPath": "/path/to/your/repository",
	  "verbose": "false",
	}
```

**Details:**
```
multithreading[sleep]: value in seconds, make CPU to do it
multithreading[storage]: for storing results in diferrent threads
rootPath: default is *getcwd()*
verbose: if you are runnig script from console, value is set to TRUE
```