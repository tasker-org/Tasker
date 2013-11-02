<?php

if (@!include __DIR__ . '/../libs/autoload.php') {
        echo 'Install Nette Tester using `composer update --dev`';
        exit(1);
}

// configure environment
Tester\Environment::setup();
class_alias('Tester\Assert', 'Assert');
date_default_timezone_set('Europe/Prague');


// create temporary directory
define('TEMP_DIR', __DIR__ . '/../tmp/' . getmypid());
@mkdir(dirname(TEMP_DIR)); // @ - directory may already exist
Tester\Helpers::purge(TEMP_DIR);


$_SERVER = array_intersect_key($_SERVER, array_flip(array('PHP_SELF', 'SCRIPT_NAME', 'SERVER_ADDR', 'SERVER_SOFTWARE', 'HTTP_HOST', 'DOCUMENT_ROOT', 'OS', 'argc', 'argv')));
$_SERVER['REQUEST_TIME'] = 1234567890;
$_ENV = $_GET = $_POST = array();


if (extension_loaded('xdebug')) {
        xdebug_disable();
        Tester\CodeCoverage\Collector::start(__DIR__ . '/coverage.dat');
}


function id($val) {
        return $val;
}

/**
 * @param callable $callable
 * @param array $params
 * @return mixed
 * @throws Exception
 */
function fetchOutput($callable, array $params = array()) {
	if(!is_callable($callable)) {
		throw new \Exception('Invalid callback given.');
	}

	ob_start();
	try {
		call_user_func_array($callable, $params);
	} catch (\Exception $e) {
		ob_end_clean();
		throw $e;
	}

	return ob_get_clean();
}