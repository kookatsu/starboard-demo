<?php
//**********************************************************************
//【処理】一覧CSV出力(POST先の処理) (クライアントPCに直接保存する)
//**********************************************************************

	session_start();

	include "../common/MYDB.php";
	include "../common/MYCONST.php";
	include "../common/fjcall_comfunc.php";

	$this_pg = "topmenu_sfdailyreport_csvout.php";


	$companycode = $_SESSION["companycode_call"];//会社コード
	$companyname = $_SESSION["companyname_call"];
	$selClient = $_SESSION["selclient_call"];
	$selDesk = $_SESSION["seldesk_call"];
	$selEndUser = $_SESSION["selenduser_call"];
	$user = $_SESSION['userid_call'];
	$name = $_SESSION['name_call'];
	$level = $_SESSION['levelid_call'];

	//URL直は排除
	if( $user == "" ){
		$first="../" . $Const_LOGIN_PHP; //Login
		header("location: {$first}");
		exit;
	}

	//親ﾌｫｰﾑでの選択情報
	$selymd1 = $_GET["selymd1"];
	$selymd2 = $_GET["selymd2"];

	//日付指定時
	$fromY = substr( $selymd1, 0, 4 );
	$fromM = sprintf("%02d", substr( $selymd1,4, 2 ));
	$fromD = sprintf("%02d", substr(  $selymd1,6, 2 ));
	$toY = substr( $selymd2,0, 4 );
	$toM = sprintf("%02d", substr( $selymd2,4, 2 ));
	$toD = sprintf("%02d", substr(  $selymd2,6, 2 ));


	//ﾍｯﾀﾞｰ情報
	header("Content-Type: application/octet-stream");
	$outdate = date("Ymd_His");
	$outdate = $outdate."_dailyreport";
	header("Content-Disposition: attachment; filename=$outdate.csv");

	//ヘッダの作成
	$csvHead =  "No." . ",\"";
	$csvHead = $csvHead .  "日付" . "\",\"";
	$csvHead = $csvHead .  "時間" . "\",\"";
	$csvHead = $csvHead .  "ｲﾝｼﾃﾞﾝﾄNo" . "\",\"";
	$csvHead = $csvHead .  "店舗名" . "\",\"";
	$csvHead = $csvHead .  "ご担当者" . "\",\"";
	$csvHead = $csvHead .  "受付者" . "\",\"";
	$csvHead = $csvHead .  "ステータス" . "\",\"";
	$csvHead = $csvHead .  "機種" . "\",\"";
	$csvHead = $csvHead .  "機器番号" . "\",\"";
	$csvHead = $csvHead .  "問合せ内容" . "\",\"";
	$csvHead = $csvHead .  "対応内容" . "\",\"";
	$csvHead = $csvHead .  "対応依頼先" . "\",\"";
	$csvHead = $csvHead .  "クローズ日付" . "\",\"";
	$csvHead = $csvHead .  "クローズ時間" . "\",\"";
	$csvHead = $csvHead .  "クローズ者" . "\",\"";
	$csvHead = $csvHead .  "完了理由";
	$csvHead = $csvHead . "\"\n";

	//ヘッダの出力
	print $csvHead;

	//DBｵｰﾌﾟﾝ
	$conn = db_connect();

	//データ出力
	$wRecCnt = 0;

	//Salesforce上は、実際の時間の9時間前で記録されている為
	$wFromDate9 = $fromY . "/" . $fromM . "/" . $fromD . " 00:00:00";
	$wFromDate9 = date("Y-m-d H:i:s",strtotime($wFromDate9 . "-9 hour"));

	$wTo9 = $toY . "/" . $toM . "/" . $toD . " 00:00:00";
	$wTo9 = date("Y-m-d H:i:s",strtotime($wTo9 . "+1 day"));
	$wTo9 = date("Y-m-d H:i:s",strtotime($wTo9 . "-9 hour"));

	$sql = "SELECT * From " . $Const_DB_SCHEMA . "case";
	$sql = $sql . " WHERE (" . $Const_DB_SCHEMA . "case.receiotdatetime__c>='" . $wFromDate9 . "'";
	$sql = $sql . " AND    " . $Const_DB_SCHEMA . "case.receiotdatetime__c<='" . $wTo9 . "')";
	$sql = $sql . " AND   (" . $Const_DB_SCHEMA . "case.hq_name__c='" . $Const_HQ_NAME . "')";//MYCONST
	$sql = $sql . " ORDER BY " . $Const_DB_SCHEMA  . "case.receiotdatetime__c"; //日時
	if($ENV_MODE == 1){
		$sql = mb_convert_encoding( $sql, $MOJI_ORG, $MOJI_NEW ); //ここは日本語が混じっているのでUTF-8へ
	}
	$result = $conn->prepare($sql);
	$result->execute();
	while ($rs = $result->fetch(PDO::FETCH_ASSOC))
	{
		$wRecCnt = $wRecCnt + 1;


		$receiotdatetime__c = date("Y-m-d H:i:s",strtotime($rs["receiotdatetime__c"] . "+9 hour")); //ここで9時間足す
		$createdateYMD = substr($receiotdatetime__c,0,10);
		$createdateHNS = substr($receiotdatetime__c,11);
		if($rs["closeddate"]>0){
			$closeddate = date("Y-m-d H:i:s",strtotime($rs["closeddate"] . "+9 hour")); //ここで9時間足す
			$closeddateYMD = substr($closeddate,0,10);
			$closeddateHNS = substr($closeddate,11);
		}else{
			$closeddateYMD = "";
			$closeddateHNS = "";
		}
		if($ENV_MODE == 1){
			$storename = mb_convert_encoding( $rs['shopname__c'], $MOJI_NEW,$MOJI_ORG); //文字コード変換;
			$storetan = mb_convert_encoding( $rs['customerstaff__c'], $MOJI_NEW,$MOJI_ORG); //文字コード変換;
			$laststaff = mb_convert_encoding( $rs['laststaff__c'], $MOJI_NEW,$MOJI_ORG); //文字コード変換;
			$status =mb_convert_encoding( $rs['status'], $MOJI_NEW,$MOJI_ORG); //文字コード変換;
			$kisyu = mb_convert_encoding( $rs['inquirycategory2__c'], $MOJI_NEW,$MOJI_ORG); //文字コード変換;
			$naiyou = mb_convert_encoding( $rs['inquirycategory3__c'], $MOJI_NEW,$MOJI_ORG); //文字コード変換;
			$machine = mb_convert_encoding( $rs['machinenumber__c'], $MOJI_NEW,$MOJI_ORG); //文字コード変換;
			$description = mb_convert_encoding( $rs['description'], $MOJI_NEW,$MOJI_ORG); //文字コード変換;
			$lastreport = mb_convert_encoding( $rs['lastreport__c'], $MOJI_NEW,$MOJI_ORG); //文字コード変換;
			$taiousaki  = mb_convert_encoding( $rs['taiouirai__c'], $MOJI_NEW,$MOJI_ORG); //文字コード変換;
			$closereson =mb_convert_encoding( $rs['closereson__c'], $MOJI_NEW,$MOJI_ORG); //文字コード変換;
		}else{
			$storename = $rs['shopname__c'];
			$storetan = $rs['customerstaff__c'];
			$laststaff = $rs['laststaff__c'];
			$status =$rs['status'];
			$kisyu = $rs['inquirycategory2__c'];
			$naiyou = $rs['inquirycategory3__c'];
			$machine = $rs['machinenumber__c'];
			$description = $rs['description'];
			$lastreport = $rs['lastreport__c'];
			$taiousaki  = $rs["taiouirai__c"];
						$closereson =$rs['closereson__c'];
		}


		$contents = "\"" . $wRecCnt . "\",\"" 
                         . $createdateYMD . "\",\"" 
                         . $createdateHNS . "\",\"" 
                         . $rs["casenumber"] . "\",\"" 
                         . $storename . "\",\"" 
                         . $storetan . "\",\"" 
                         . $laststaff . "\",\"" 
                         . $status . "\",\"" 
                         . $kisyu . "\",\"" 
                         . $machine . "\",\"" 
                         . $description . "\",\"" 
                         . $lastreport . "\",\"" 
                         . $taiousaki . "\",\"" 
                         . $closeddateYMD . "\",\"" 
                         . $closeddateHNS . "\",\"" 
                         . $laststaff . "\",\"" 
                         . $closereson . "\"\n";
		print $contents;

	}
	$rs = null;
	$result = null;
	$conn = null;

?>
