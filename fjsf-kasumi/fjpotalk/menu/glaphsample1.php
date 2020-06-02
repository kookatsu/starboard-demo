<?php

	session_start();

	include "../common/MYDB.php";
	include "../common/MYCONST.php";
	include "../common/fjcall_comfunc.php";
	include "../common/fjcall_const.php";
	include "./topmenu_callcal.php";
	include "./topmenu_callcal_sf.php";

	//Session変数取得
	$companycode = $_SESSION["companycode_call"];//会社コード
	$companyname = $_SESSION["companyname_call"];
	$selClient = $_SESSION["selclient_call"];
	$selDesk = $_SESSION["seldesk_call"];
	$selEndUser = $_SESSION["selenduser_call"];
	$user = $_SESSION['userid_call'];
	$name = $_SESSION['name_call'];
	$level = $_SESSION['levelid_call'];
	$fujimotoflg = $_SESSION["fujimotoflg_call"];
	//Session変数取得

?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?= $Const_HTML_CHARSET ?>">
 <title>棒グラフ</title>
 <style>
 h1 {
 color: #696969;
 }
 </style>
 <script type="text/javascript" src="https://www.google.com/jsapi"></script>
 <script>
google.load('visualization', '1', {packages:['corechart']});
google.setOnLoadCallback(drawChart);

// データ読み込み

function drawChart() {

var data = google.visualization.arrayToDataTable([
 ['日付(月単位)', 'A' ],
 <?php

	// DBに接続
	$conn = db_connect();

	// SQL文
	$sql = "SELECT * FROM " .  $Const_DB_SCHEMA . "wdeskhistory2day";
	$sql = $sql . " WHERE " .  $Const_DB_SCHEMA . "wdeskhistory2day.userid='" . $Const_COMPANYCODE . $user . "'";
	$sql = $sql . " AND   (" . $Const_DB_SCHEMA . "wdeskhistory2day.dspday<>99)"; //合計は不要
	$sql = $sql . " ORDER BY " . $Const_DB_SCHEMA . "wdeskhistory2day.hiduke";
	$stmt = $conn->query($sql);
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	// 行の長さ取得
	$length = $stmt->rowCount();
	// カウント
	$no = 0;
	foreach ($result as $value) {
		echo '[\''.$value["hiduke"].'\', '.$value["sf_in_cnt"].']';
		$no++;
		if ($no !== $length) {
			echo ",\n";
		}
	}
	$conn=null;

?>
 ]);
 var options = {
 width:900,
 height:600,
 chartArea: {
 top:80,
 left:200
 },
 fontName: 'メイリオ',
backgroundColor:'#EFF4F5',
 isStacked: true,
 colors: ['#2E8EF6', '#DA3A29', '#8EC43C'],
 bar: {groupWidth: 20},
 hAxis:{
 title:'日付(月単位)',
titleTextStyle: {
 fontName: 'メイリオ',
fontSize: 16,
 italic: false,
 bold: false,
 color: '#696969'
 },
 textStyle: {
 fontSize:12,
 color: '#696969',
 fontName: 'メイリオ'
 },
slantedText: true
 },
 vAxis:{
 title: '合計',
titleTextStyle: {
 fontName: 'メイリオ',
fontSize: 16,
 italic: false,
 bold: false,
 color: '#696969'
 },
 textStyle: {
 fontSize:12,
 color: '#696969',
 fontName: 'メイリオ'
 }
 },
legend:{
 position: 'top',
 textStyle:{
 fontName: 'メイリオ',
color: '#696969',
 fontSize: 16
 }
 }
 };
 var chart = new google.visualization.ColumnChart(document.getElementById('graph1'));
 chart.draw(data, options);
 }
 </script>
</head>
<body>
 <h1>サンプル棒グラフ</h1>
 <div id="graph1"></div>
</body>
</html>
