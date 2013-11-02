<?php


require __DIR__ . '/../bootstrap.php';

$tasker = new \Tasker\Tasker;
$tasker->addConfig(array('verbose' => true));
for($i = 0; $i < 10; $i++) {
	$tasker->registerTask(function () use ($i) {
		return 'Task result: ' . $i;
	}, $i);
}
$tasker->run();
\Tester\Assert::true(true);