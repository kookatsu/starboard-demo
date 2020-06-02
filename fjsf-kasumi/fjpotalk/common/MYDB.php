<?php
//---------------------------------------------------*
// Mysql�p ���ʊ֐��Q
//---------------------------------------------------*

//require_once("DB.php");

function db_connect(){

$url = parse_url(getenv('DATABASE_URL'));
$dsn = sprintf('pgsql:host=%s;dbname=%s', $url['host'], substr($url['path'], 1));

	try {
		$db = new PDO($dsn, $url['user'], $url['pass']);
	} catch (PDOException $e) {
		exit('�f�[�^�x�[�X�ɐڑ��ł��܂���ł����B' . $e->getMessage());
	}
	return $db;
}

?>