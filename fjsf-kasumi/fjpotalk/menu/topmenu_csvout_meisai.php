<?php
//**********************************************************************
//【処理】一覧CSV出力(POST先の処理) (クライアントPCに直接保存する)
//**********************************************************************

	session_start();

	include "../common/MYDB.php";
	include "../common/MYCONST.php";
	include "../common/fjcall_comfunc.php";

	$this_pg = "topmenu_csvout_meisai.php";

	$companycode = $_SESSION["companycode_call"];//会社コード
	$user = $_SESSION['userid_call'];
	$name = $_SESSION['name_call'];
	$level = $_SESSION['levelid_call'];
	$maindeskcode = $_SESSION["maindeskcode_call"];

	//親ﾌｫｰﾑでの選択情報
	$selymd1 = $_GET["selymd1"];
	$selymd2 = $_GET["selymd2"];
	$gvalue = $_GET["gvalue"];

	$selclient = mb_substr($gvalue, 0, 3);
	$selenduser = mb_substr($gvalue, 3, 3);

	//DBｵｰﾌﾟﾝ
	$conn = db_connect();

	$endusername = GetEndUserName( $conn, $selclient, $selenduser ); //fjcall_comfunc

	//ﾍｯﾀﾞｰ情報
	header("Content-Type: application/octet-stream");
	$outdate = date("Ymd_His");
	$outdate = $outdate."_timezonelist";
	header("Content-Disposition: attachment; filename=$outdate.csv");


	//クライアントとエンドユーザの出力
	$csvHead = "\"" . $endusername . "\",\"";
	$csvHead = $csvHead . "\"\n";
	//ヘッダの出力
	print $csvHead;


	//ヘッダの作成
	$csvHead = "\"" . "No" . "\",\"";
	$csvHead = $csvHead .  "日付" . "\",\"";
	$csvHead = $csvHead .  "時刻" . "\",\"";
	$csvHead = $csvHead .  "トランク番号" . "\",\"";
	$csvHead = $csvHead .  "外線番号" . "\",\"";
	$csvHead = $csvHead .  "外線名称" . "\",\"";
	$csvHead = $csvHead .  "通話時間" . "\",\"";
	$csvHead = $csvHead .  "内線" . "\",\"";
	$csvHead = $csvHead .  "鳴動時間" . "\",\"";
	$csvHead = $csvHead .  "G" . "\",\"";
	$csvHead = $csvHead .  "12" . "\",\"";
	$csvHead = $csvHead .  "不";
	$csvHead = $csvHead . "\"\n";

	//ヘッダの出力
	print $csvHead;

	//データ出力
	$wRecCnt = 0;
	$sql = "SELECT " . $Const_DB_SCHEMA . "dcall_datadump.* FROM " . $Const_DB_SCHEMA . "dcall_datadump LEFT JOIN " . $Const_DB_SCHEMA . "mtrunk ON " . $Const_DB_SCHEMA . "dcall_datadump.trunkno = " . $Const_DB_SCHEMA . "mtrunk.trunkno";
	$sql = $sql . " WHERE (" . $Const_DB_SCHEMA . "dcall_datadump.calldate>=" . $selymd1 . " And " . $Const_DB_SCHEMA . "dcall_datadump.calldate<=" . $selymd2 . ")";
	$sql = $sql . "   AND (" . $Const_DB_SCHEMA . "mtrunk.clientcode='" . $selclient . "' AND " . $Const_DB_SCHEMA . "mtrunk.endusercode='" . $selenduser . "')";
	$sql = $sql . " ORDER BY " . $Const_DB_SCHEMA . "dcall_datadump.calldate, " . $Const_DB_SCHEMA . "dcall_datadump.calltime";
	$sql = mb_convert_encoding( $sql, "SJIS-win", "SJIS-win");
	$result = $conn->prepare($sql);
	$result->execute();
	while ($rs = $result->fetch(PDO::FETCH_ASSOC))
	{
		$wRecCnt = $wRecCnt + 1;
		$markG = ""; $markG12 = ""; $markF = "";

		//不出呼数
		if($rs["fusyutsuFLG"] == 1){
			$bkcolor_base = $GRID_MEISAI_COLOR2;
			$markF = "●";
		}
		if( $rs["guidance1FLG"] == 1 || $rs["guidance2FLG"] == 1 ){
			$bkcolor_base = $GRID_MEISAI_COLOR3;
			$markG12 = "●";
		}
		if( $rs["talkieFLG"] != 0 ){
			$bkcolor_base = $GRID_MEISAI_COLOR4;
			$markG = "●";
		}


		$contents = "\"" . $wRecCnt . "\",\"" 
                         . $rs["calldate"] . "\",\"" 
                         . $rs["calltime"] . "\",\"" 
                         . $rs["trunkno"] . "\",\"" 
                         . $rs["telno"] . "\",\"" 
                         . $rs["telname"] . "\",\"" 
                         . $rs["talktime"] . "\",\"" 
                         . $rs["idnaisen"] . "\",\"" 
                         . $rs["rumblingtime"] . "\",\"" 
                         . $markG . "\",\"" 
                         . $markG12 . "\",\"" 
                         . $markF . "\"\n";
		print $contents;

	}
	$rs = null;
	$result = null;
	$conn = null;
?>
