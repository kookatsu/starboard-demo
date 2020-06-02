<?php
//**********************************************************************
//【処理】一覧CSV出力(POST先の処理) (クライアントPCに直接保存する)
//**********************************************************************

	session_start();

	include "../common/MYDB.php";
	include "../common/MYCONST.php";
	include "../common/fjcall_comfunc.php";

	$this_pg = "topmenu_csvout2.php";

	$companycode = $_SESSION["companycode_call"];//会社コード
	$user = $_SESSION['userid_call'];
	$name = $_SESSION['name_call'];
	$level = $_SESSION['levelid_call'];
	$maindeskcode = $_SESSION["maindeskcode_call"];

	//親ﾌｫｰﾑでの選択情報
	$selclient = $_GET["selclient"];
	$selenduser = $_GET["selenduser"];

	//DBｵｰﾌﾟﾝ
	$conn = db_connect();

	//クライアント名の取得
	if($selclient =="ALL"){
		$clientname = "全てのお客様";
	}else{
		$sql = "Select clientname " . $Const_DB_SCHEMA . "From mclient";
		$sql = $sql . " WHERE clientcode='" . $selclient . "'";
		$sql = mb_convert_encoding( $sql, "SJIS-win", "SJIS-win");
		$result = $conn->prepare($sql);
		$result->execute();
		if ($rs = $result->fetch(PDO::FETCH_ASSOC)){
			$clientname = $rs["clientname"];
		}
		$rs = null;
		$result = null;
	}
	//エンドユーザ名の取得
	if($selenduser =="ALL"){
		$endusername = "全てのユーザ";
	}else{
		$endusername = GetEndUserName( $conn, $selclient, $selenduser ); //fjcall_comfunc
	}

	//ﾍｯﾀﾞｰ情報
	header("Content-Type: application/octet-stream");
	$outdate = date("Ymd_His");
	$outdate = $outdate."_dailylist";
	header("Content-Disposition: attachment; filename=$outdate.csv");


	//クライアントとエンドユーザの出力
	$csvHead = "\"" . $clientname . "\",\"";
	$csvHead = $csvHead .  $endusername . "\",\"";
	$csvHead = $csvHead . "\"\n";
	//ヘッダの出力
	print $csvHead;


	//ヘッダの作成
	$csvHead = "\"" . "日" . "\",\"";
	$csvHead = $csvHead .  "曜日" . "\",\"";
	$csvHead = $csvHead .  "総着信数" . "\",\"";
	$csvHead = $csvHead .  "通話数" . "\",\"";
	$csvHead = $csvHead .  "通話％" . "\",\"";
	$csvHead = $csvHead .  "通話秒" . "\",\"";
	$csvHead = $csvHead .  "ガイダンス数" . "\",\"";
	$csvHead = $csvHead .  "ガイダンス％" . "\",\"";
	$csvHead = $csvHead .  "12秒ガイダンス数" . "\",\"";
	$csvHead = $csvHead .  "12秒ガイダンス％" . "\",\"";
	$csvHead = $csvHead .  "不出呼数" . "\",\"";
	$csvHead = $csvHead .  "不出呼数％";
	$csvHead = $csvHead . "\"\n";

	//ヘッダの出力
	print $csvHead;

	//データ出力
	$sql = "SELECT * FROM " . $Const_DB_SCHEMA . "wdeskhistory2day";
	$sql = $sql . " WHERE userid='" . $Const_COMPANYCODE. $user . "'";
	$sql = $sql . " ORDER BY hiduke";
	$sql = mb_convert_encoding( $sql, "SJIS-win", "SJIS-win");
	$result = $conn->prepare($sql);
	$result->execute();
	while ($rs = $result->fetch(PDO::FETCH_ASSOC))
	{
		//総着信数
		$wTotalDaySu = $rs["talksu"] +  $rs["guidanceSu"] + $rs["guidance12Su"] + $rs["fusyutsuSu"];

		//通話数%
		if($wTotalDaySu!=0){
			$talkP = ($rs["talksu"] / $wTotalDaySu) *100;
		}else{
			$talkP = 0;
		}
		//ガイダンス%
		if($wTotalDaySu!=0){
			$guidanceP = ($rs["guidanceSu"] / $wTotalDaySu) *100;
		}else{
			$guidanceP = 0;
		}
		//ガイダンス12%
		if($wTotalDaySu!=0){
			$guidance12P = ($rs["guidance12Su"] / $wTotalDaySu) *100;
		}else{
			$guidance12P = 0;
		}
		//不出呼数%
		if($wTotalDaySu!=0){
			$fusyutsuP = ($rs["fusyutsuSu"] / $wTotalDaySu) *100;
		}else{
			$fusyutsuP = 0;
		}

		//日付表示用(合計行は空欄にしたい)
		if($rs["RecFlg"] == 9){
			$dspDay2 = "";
		}else{
			$dspDay2 = $rs["dspday"];
		}

		$contents = "\"" . $dspDay2 . "\",\"" 
                         . $rs["youbimei"] . "\",\"" 
                         . number_format($wTotalDaySu,0) . "\",\"" 
                         . number_format($rs["talksu"],0) . "\",\"" 
                         . number_format($talkP,1) . "\",\"" 
                         . number_format($rs["talktime"],0) . "\",\"" 
                         . number_format($rs["guidanceSu"],0) . "\",\"" 
                         . number_format($guidanceP,1) . "\",\"" 
                         . number_format($rs["guidance12Su"],0) . "\",\"" 
                         . number_format($guidance12P,1) . "\",\"" 
                         . number_format($rs["fusyutsuSu"],0) . "\",\"" 
                         . number_format($fusyutsuP,1) . "\"\n";
		print $contents;

	}
	$rs = null;
	$result = null;
	$conn = null;
?>
