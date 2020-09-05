<?php

// Set default timezone to Europe/Paris if PHP 5.4 // Keep this a very first data loaded by PHP
if (@function_exists('date_default_timezone_set') && @function_exists('date_default_timezone_get'))
    @date_default_timezone_set(@date_default_timezone_get());
if (version_compare(PHP_VERSION, '5.4.0') >= 0)
    date_default_timezone_set('Europe/Paris');

require_once 'vendor/autoload.php';
require __DIR__ . '/config/settings.php';

// Benmark start for page load
$time = explode(' ', microtime());
$benchstart = $time[1] + $time[0];

// db settings
$dh = $settings['db']['host'];
$db = $settings['db']['database'];
$du = $settings['db']['username'];
$dp = $settings['db']['password'];
$dt = "loglogins";
$nbl = 500; // nb of inserts
$fields = array(
    ['LOGUSERNAME', 'userName'],
    ['LOGSOURCEIP', 'sourceip'],
    ['LOGRESULT', "randomElement(array('ok','login locked','login incorrect'))"],
	['LOGUPDATEDAT', "dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s')"]
	);

echo "<!doctype html>
<head><title>Faker to fill-in '$dt' table</title>
<meta http-equiv=\"Content-Type\" content=\"text/html;charset=UTF-8\" />
</head><body>";

$connexion = $connexion = mysqli_connect($dh, $du, $dp, $db);
$faker = Faker\Factory::create('fr_FR', 'en_US'); // enable both FR and US data

echo "<H2>Faker to fill in table '$dt' in database '$db'</h2>";

// table definition
echo "<hr><PRE>
DROP TABLE IF EXISTS `loglogins`;
CREATE TABLE IF NOT EXISTS `loglogins` (
  `LOGID` int(8) NOT NULL AUTO_INCREMENT,
  `LOGUSERNAME` varchar(100) NOT NULL,
  `LOGSOURCEIP` varchar(100) NOT NULL,
  `LOGRESULT` varchar(100) NOT NULL,
  `LOGUPDATEDAT` datetime NOT NULL,
  UNIQUE KEY `LOGID` (`LOGID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
</PRE><hr>";

echo "These fileds will be populated :<ul>";
for($n = 0;$n < count($fields); $n++)
	echo "<li>".$fields[$n][0]." = faker->".$fields[$n][1]."</li>";
echo "</ul>";
	
for ($i = 0; $i < $nbl; $i++){
	/*$req = "INSERT INTO $dt VALUES (0,";
	$sep = "";
	for($n=0;$n < count($fields); $n++){
		$fn = $fields[$n][1];
		$req .= "$sep '".addslashes($faker->$fn)."'";
		$sep = ",";
	}
	$ts = date("Y-m-d H:i:s");
	$req .= ", 'System', '$ts');";*/

	$p1 = addslashes($faker->userName);
	$p2 = $faker->ipv4;
	$p3 = $faker->randomElement(array('ok','Access locked','Login incorrect'));
	$p4 = $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'); // anytime from now back to 1 year

	$req = "INSERT INTO $dt VALUES (0,'$p1','$p2','$p3','$p4');";
	//echo "$req<br />";
	mysqli_set_charset($connexion,'utf8');
	$res = mysqli_query($connexion,$req);
}

$req = "SELECT COUNT(*) FROM $dt;";
$res = mysqli_query($connexion,$req);
$row = mysqli_fetch_row($res);

$time = explode(' ', microtime());
$benchend = $time[1] + $time[0];

echo "$nbl rows inserted in ".number_format($benchend - $benchstart,5)."s / $row[0] rows in total";

mysqli_close($connexion);

?>