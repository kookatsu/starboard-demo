<?php
//**********************************************************************
//【処理】一覧CSV出力(POST先の処理) (クライアントPCに直接保存する)
//**********************************************************************

	session_start();

	include "../common/MYDB.php";
	include "../common/MYCONST.php";
	include "../common/fjcall_comfunc.php";

	$this_pg = "topmenu_csvout1.php";

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

	//ﾍｯﾀﾞｰ情報
	header("Content-Type: application/octet-stream");
	$outdate = date("Ymd_His");
	$outdate = $outdate."_totallist";
	header("Content-Disposition: attachment; filename=$outdate.csv");

	//ヘッダの作成
	$csvHead = "\"" . "お客様" . "\",\"";
	$csvHead = $csvHead .  "エンドユーザ" . "\",\"";
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
	$sql = "SELECT * From " . $Const_DB_SCHEMA . "wdeskhistory1";
	$sql = $sql . " WHERE userid ='" .  $Const_COMPANYCODE . $user . "'";
	$sql = $sql . " ORDER BY deskcode,clientcode,endusercode";
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

		$contents = "\"" . $rs["clientname"] . "\",\"" 
                         . $rs["endusername"] . "\",\"" 
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
