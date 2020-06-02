<?php
//---------------------------------------------------*
// Mysql用 共通関数群
//---------------------------------------------------*

//require_once("DB.php");

function db_connect(){

$url = parse_url(getenv('DATABASE_URL'));
$dsn = sprintf('pgsql:host=%s;dbname=%s', $url['host'], substr($url['path'], 1));

	try {
		$db = new PDO($dsn, $url['user'], $url['pass']);
	} catch (PDOException $e) {
		exit('データベースに接続できませんでした。' . $e->getMessage());
	}
	return $db;
}

?>