<?php
//**********************************************************************
//【処理】一覧CSV出力(POST先の処理) (クライアントPCに直接保存する)
//**********************************************************************

	session_start();

	include "../common/MYDB.php";
	include "../common/MYCONST.php";
	include "../common/fjcall_comfunc.php";

	$this_pg = "topmenu_callmeisai_csvout.php";

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


	$selclient = $selClient;
	$selenduser = $selEndUser;


	//ﾍｯﾀﾞｰ情報
	header("Content-Type: application/octet-stream");
	$outdate = date("Ymd_His");
	$outdate = $outdate."_calllist";
	header("Content-Disposition: attachment; filename=$outdate.csv");

	//ヘッダの作成
	$csvHead = "\"" . "No." . "\",\"";
	$csvHead = $csvHead .  "日付" . "\",\"";
	$csvHead = $csvHead .  "時刻" . "\",\"";
	$csvHead = $csvHead .  "通話時間" . "\",\"";
	$csvHead = $csvHead .  "鳴動時間" . "\",\"";
	$csvHead = $csvHead .  "ガイダンス" . "\",\"";
	$csvHead = $csvHead .  "12秒ガイダンス" . "\",\"";
	$csvHead = $csvHead .  "不出個数";
	$csvHead = $csvHead . "\"\n";

	//ヘッダの出力
	print $csvHead;

	//DBｵｰﾌﾟﾝ
	$conn = db_connect();

	//データ出力
	$wRecCnt = 0;

	$sql = "SELECT " . $Const_DB_SCHEMA . "dcall_datadump.* FROM " . $Const_DB_SCHEMA . "dcall_datadump LEFT JOIN " . $Const_DB_SCHEMA . "mtrunk ON " . $Const_DB_SCHEMA . "dcall_datadump.trunkno = " . $Const_DB_SCHEMA . "mtrunk.trunkno";
	$sql = $sql . " WHERE (" . $Const_DB_SCHEMA . "dcall_datadump.calldate>=" . $selymd1 . " And " . $Const_DB_SCHEMA . "dcall_datadump.calldate<=" . $selymd2 . ")";
	$sql = $sql . "   AND (" . $Const_DB_SCHEMA . "mtrunk.clientcode='" . $selclient . "' AND " . $Const_DB_SCHEMA . "mtrunk.endusercode='" . $selenduser . "')";
	$sql = $sql . " ORDER BY " . $Const_DB_SCHEMA . "dcall_datadump.calldate, " . $Const_DB_SCHEMA . "dcall_datadump.calltime";
	$result = $conn->prepare($sql);
	$result->execute();
	while ($rs = $result->fetch(PDO::FETCH_ASSOC))
	{
		$wRecCnt = $wRecCnt + 1;

		$markG = ""; $markG12 = ""; $markF = "";

		$bkcolor_base = $GRID_MEISAI_COLOR1;

		//不出呼数
		if($rs["fusyutsuflg"] == 1){
			$bkcolor_base = $GRID_MEISAI_COLOR2;
			$markF = "●";
		}
		if( $rs["guidance1flg"] == 1 || $rs["guidance2flg"] == 1 ){
			$bkcolor_base = $GRID_MEISAI_COLOR3;
			$markG12 = "●";
		}
		if( $rs["talkieflg"] != 0 ){
			$bkcolor_base = $GRID_MEISAI_COLOR4;
			$markG = "●";
		}

		if($ENV_MODE == 1){
			$telname = mb_convert_encoding( $rs['telname'], $MOJI_NEW,$MOJI_ORG); //文字コード変換;
		}else{
			$telname = $rs['telname'];
		}

		$contents = "\"" . $wRecCnt . "\",\"" 
                         . $rs["calldate"] . "\",\"" 
                         . $rs["calltime"] . "\",\"" 
                         . $rs["talktime"] . "\",\"" 
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
