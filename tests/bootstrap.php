<?php

if (!defined('V_TEST_NOW')) define('V_TEST_NOW', true);

if (!defined('VESSEL_DIR_VESSEL')) define('VESSEL_DIR_VESSEL', dirname(__DIR__).DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'Hokeo'.DIRECTORY_SEPARATOR.'Vessel');
if (!defined('VESSEL_DIR_SRC'))    define('VESSEL_DIR_SRC',    dirname(dirname(VESSEL_DIR_VESSEL)));
if (!defined('VESSEL_DIR_MAIN'))   define('VESSEL_DIR_MAIN',   dirname(VESSEL_DIR_SRC));

$cwd = getcwd();
$nudir = dirname(dirname(dirname($cwd)));
print 'Changing to '.$nudir."\n";
chdir($nudir);

print "Writing database configuration for testing environment...\n";

if (!is_dir($nudir.'/app/config/testing'))
{
	mkdir($nudir.'/app/config/testing', 0755, true);
}

if (file_exists(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'is_travis_test'))
{
	define('IS_VESSEL_TRAVIS_TEST', true);
	file_put_contents($nudir.'/app/config/testing/database.php', "<?php return array('connections' => array('mysql' => array('database' => 'vessel_testing', 'username' => 'root', 'password' => '')));");
}
else
{
	define('IS_VESSEL_TRAVIS_TEST', false);
	file_put_contents($nudir.'/app/config/testing/database.php', "<?php return array('connections' => array('mysql' => array('database' => 'vessel_testing', 'username' => 'root', 'password' => 'root')));");
}

// add service provider

$app_file = $nudir.'/app/config/app.php';

if (file_exists($app_file))
{
	$app_file_contents = file_get_contents($app_file);

	if ($app_file_contents)
	{
		$app_file_addition = "'Hokeo\Vessel\VesselServiceProvider',";

		if (strpos($app_file_contents, $app_file_addition) === false)
		{
			print "Adding Hokeo\Vessel\VesselServiceProvider to app/config/app.php...\n";

			file_put_contents($app_file, str_replace(
				"'Illuminate\Workbench\WorkbenchServiceProvider',",
				"'Illuminate\Workbench\WorkbenchServiceProvider', ".$app_file_addition,
			$app_file_contents));
		}
	}
}

// clear test db and seed

print "Running artisan database commands for testing...\n";

if (!IS_VESSEL_TRAVIS_TEST) passthru('php artisan migrate:reset --env=testing');
passthru('php artisan migrate --bench=hokeo/vessel --env=testing');
passthru('php artisan db:seed --class=Hokeo\\\Vessel\\\Seeds\\\TestVesselSeeder --env=testing');
passthru('php artisan dump-autoload');

print 'Changing back to '.$cwd."\n";
chdir($cwd);

// The below is a terrible hack to circumvent function redeclaration errors of the crypt_random_string() function in the below file
// , which for whatever reason they don't want to surround with function_exists().

$evil_file = __DIR__.'/../vendor/phpseclib/phpseclib/phpseclib/Crypt/Random.php';

print 'Hackily-editing: '.$evil_file."\n";

if (file_exists($evil_file))
{
	$evil_file_contents = file_get_contents($evil_file);
	$evil_file_nustring = '<?php /* VESSEL TESTING HACK (from tests/bootstrap.php) */ if (defined("CRYPT_RANDOM_IS_WINDOWS") || function_exists("crypt_random_string")) { return; }';

	if (strpos($evil_file_contents, $evil_file_nustring) === false)
	{
		// find first occurrence of <?php and replace with our $nustring
		file_put_contents($evil_file, implode($evil_file_nustring, explode('<?php', $evil_file_contents, 2)));
	}
}

print 'Requiring autoloader...'."\n";

// actual bootstrap:

require_once __DIR__.'/../vendor/autoload.php';