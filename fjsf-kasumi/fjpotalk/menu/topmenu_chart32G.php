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

	//URL直は排除
	if( $user == "" ){
		exit;
	}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?= $Const_HTML_CHARSET ?>">
<title>時間帯別インシデント件数</title>

<script type="text/javascript" src="https://www.google.com/jsapi"></script>

<script>

// ライブラリのロード
// name:visualization(可視化),version:バージョン(1),packages:パッケージ(corechart)
google.load('visualization', '1', {packages:['corechart']});

// グラフを描画する為のコールバック関数を指定
google.setOnLoadCallback(drawChart);

// データ読み込み
function drawChart() {

var data = google.visualization.arrayToDataTable([
 ['時間帯', '件数' ],
 <?php

	// DBに接続
	$conn = db_connect();

	// SQL文
	$sql = "SELECT * FROM " . $Const_DB_SCHEMA . "wdeskhistory3timefj";
	$sql = $sql . " WHERE userid='" . $Const_COMPANYCODE . $user . "'";
	$sql = $sql . " AND   (recflg=0)";
	$sql = $sql . " ORDER BY time";
	$stmt = $conn->query($sql);
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	// 行の長さ取得
	$length = $stmt->rowCount();
	// カウント
	$no = 0;
	foreach ($result as $value) {
		echo '[\''.$value["time"]."時".'\', '.$value["sf_in_cnt"].']';
		$no++;
		if ($no !== $length) {
			echo ",\n";
		}
	}
	$conn=null;

?>
 ]);
 var options = {
 width:800, height:260, chartArea: { top:10, left:40 },
 fontName: 'メイリオ',backgroundColor:'#EFF4F5', isStacked: true, colors: ['#2E8EF6', '#DA3A29', '#8EC43C'], bar: {groupWidth: 15},colors: ['#7C619D'],
 hAxis:{ title: '',titleTextStyle: { fontName: 'メイリオ',fontSize: 16, italic: false, bold: false, color: '#696969' }, textStyle: { fontSize:12, color: '#696969', fontName: 'メイリオ' },slantedText: false },
 vAxis:{ title: '',titleTextStyle: { fontName: 'メイリオ',fontSize: 16, italic: false, bold: false, color: '#696969' }, textStyle: { fontSize:10, color: '#696969', fontName: 'メイリオ' } },
 legend:'none'
};


// 指定されたIDの要素に棒グラフを作成
var chart = new google.visualization.ColumnChart(document.getElementById('graph1'));

// グラフの描画
chart.draw(data, options);
}
 </script>
</head>
<body>
 <div id="graph1" style="width:100%"></div>
</body>
</html>
