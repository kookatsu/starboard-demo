<?php
//**********************************************************************
//【処理】一覧CSV出力(POST先の処理) (クライアントPCに直接保存する)
//**********************************************************************

	session_start();

	include "../common/MYDB.php";
	include "../common/MYCONST.php";
	include "../common/fjcall_comfunc.php";

	$this_pg = "topmenu_sfstore_csvout.php";

	$companycode = $_SESSION["companycode_call"];//会社コード
	$companyname = $_SESSION["companyname_call"];
	$selClient = $_SESSION["selclient_call"];
	$selDesk = $_SESSION["seldesk_call"];
	$selEndUser = $_SESSION["selenduser_call"];
	$user = $_SESSION['userid_call'];
	$name = $_SESSION['name_call'];
	$level = $_SESSION['levelid_call'];

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
	$outdate = $outdate."_storelist";
	header("Content-Disposition: attachment; filename=$outdate.csv");

	//ヘッダの作成
	$csvHead = "\"" . "No." . "\",\"";
	$csvHead = $csvHead .  "店舗名" . "\",\"";
	$csvHead = $csvHead .  "件数";
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

	$sql = "SELECT " . $Const_DB_SCHEMA . "case.shopname__c, Count(" . $Const_DB_SCHEMA . "case.casenumber) AS casenumber_cnt FROM " . $Const_DB_SCHEMA . "case";
	$sql = $sql . " WHERE (" . $Const_DB_SCHEMA . "case.receiotdatetime__c>='" . $wFromDate9 . "'";
	$sql = $sql . " AND    " . $Const_DB_SCHEMA . "case.receiotdatetime__c<='" . $wTo9 . "')";
	$sql = $sql . "   AND (" . $Const_DB_SCHEMA . "case.hq_name__c='" . $Const_HQ_NAME . "')";//MYCONST
	$sql = $sql . " GROUP BY " . $Const_DB_SCHEMA . "case.shopname__c";
	$sql = $sql . " ORDER BY Count(" . $Const_DB_SCHEMA . "case.casenumber) DESC";
	if($ENV_MODE == 1){
		$sql = mb_convert_encoding( $sql, $MOJI_ORG, $MOJI_NEW ); //ここは日本語が混じっているのでUTF-8へ
	}
	$result = $conn->prepare($sql);
	$result->execute();
	while ($rs = $result->fetch(PDO::FETCH_ASSOC))
	{
		$wRecCnt = $wRecCnt + 1;


		if($ENV_MODE == 1){
			$storename = mb_convert_encoding( $rs['shopname__c'], $MOJI_NEW,$MOJI_ORG); //文字コード変換;
		}else{
			$storename = $rs['shopname__c'];
		}



		$contents = "\"" . $wRecCnt . "\",\"" 
                         . $storename . "\",\"" 
                         . $rs["casenumber_cnt"] . "\"\n";
		print $contents;

	}
	$rs = null;
	$result = null;
	$conn = null;

?>
