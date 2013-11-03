<?php


require __DIR__ . '/../bootstrap.php';

$tasker = new \Tasker\Tasker;
$tasker->addConfig(array('verbose' => true));
for($i = 0; $i < 10; $i++) {
	$tasker->registerTask(function () use ($i) {
		return 'Task result: ' . $i;
	}, $i);
}
$resultSet = $tasker->run();

\Tester\Assert::equal(30, count($resultSet->getResults()));


//Although verbose is set to false, in result set must be the same result
$tasker = new \Tasker\Tasker;
$tasker->addConfig(array('verbose' => false));
for($i = 0; $i < 10; $i++) {
	$tasker->registerTask(function () use ($i) {
		return 'Task result: ' . $i;
	}, $i);
}
$resultSet = $tasker->run();

\Tester\Assert::equal(30, count($resultSet->getResults()));