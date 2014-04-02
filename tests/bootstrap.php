<?php

$cwd = getcwd();
$nudir = dirname(dirname(dirname($cwd)));
print 'Changing to '.$nudir."\n";
chdir($nudir);


if (!is_dir($nudir.'/app/config/testing'))
{
	mkdir($nudir.'/app/config/testing', 0755, true);
}

if (file_exists(__DIR__.DIRECTORY_SEPARATOR.'is_travis_test'))
{
	define('IS_VESSEL_TRAVIS_TEST', true);
	file_put_contents($nudir.'/app/config/testing/database.php', "<?php return array('connections' => array('mysql' => array('database' => 'vessel_testing', 'username' => 'root', 'password' => '')));");
}
else
{
	define('IS_VESSEL_TRAVIS_TEST', false);
	file_put_contents($nudir.'/app/config/testing/database.php', "<?php return array('connections' => array('mysql' => array('database' => 'vessel_testing', 'username' => 'root', 'password' => 'root')));");
}

exec('php artisan migrate:reset --env=testing');
exec('php artisan migrate --bench=hokeo/vessel --env=testing');
exec('php artisan db:seed --class=Hokeo\\\Vessel\\\Seeds\\\TestVesselSeeder --env=testing');

print 'Changing back to '.$cwd."\n";
chdir($cwd);

require_once __DIR__.'/../vendor/autoload.php';

return array('connections' => array('mysql' => array('database' => 'vessel_testing', 'username' => 'root', 'password' => '')));