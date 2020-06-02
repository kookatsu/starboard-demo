<?php
//引数のselmode -> データの抽出条件が変わる
//1:インシデント-総件数
//11～20:インシデント内容ごとのNO

//51:過去未完了件数全ての総件数


// ソート順[sortflg]
// 0:インシデントNo.(起動時)
// 1:日時
// 2:店舗
// 3:内容
// 4:機種
// 5:ステータス
// 6:完了理由


	session_start();

	include "../common/MYDB.php";
	include "../common/MYCONST.php";
	include "../common/fjcall_comfunc.php";
	include "../common/fjcall_const.php";


	$this_pg ='topmenu_sfmeisai.php';
	$this_modname = "インシデント明細一覧";

	$companycode = $_SESSION["companycode_call"];//会社コード
	$companyname = $_SESSION["companyname_call"];
	$selClient = $_SESSION["selclient_call"];
	$selDesk = $_SESSION["seldesk_call"];
	$selEndUser = $_SESSION["selenduser_call"];
	$user = $_SESSION['userid_call'];
	$name = $_SESSION['name_call'];
	$level = $_SESSION['levelid_call'];


	//URL直は排除
	if($user == ""){
		$first="../" . $Const_LOGIN_PHP;
		header("location: {$first}");
		exit;
	}

	$wait_mess = "";


	//処理起動時のﾌﾗｸﾞ用
	$mode = $_POST["mode"];
	//起動時の表示後
	if( $mode == "dsp_after" ) {
		$selymd1 = $_POST["selymd1"];
		$selymd2 = $_POST["selymd2"];
		$selmode = $_POST["selmode"];
		$sortflg =  $_POST["sortflg"];
	}else{
		//親ﾌｫｰﾑでの選択情報
		$selymd1 = $_GET["selymd1"];
		$selymd2 = $_GET["selymd2"];
		$selmode = $_GET["selmode"];
		$sortflg = 0;//インシデントNo
	}

	//日付指定時
	$fromY = substr( $selymd1, 0, 4 );
	$fromM = sprintf("%02d", substr( $selymd1,4, 2 ));
	$fromD = sprintf("%02d", substr(  $selymd1,6, 2 ));
	$toY = substr( $selymd2,0, 4 );
	$toM = sprintf("%02d", substr( $selymd2,4, 2 ));
	$toD = sprintf("%02d", substr(  $selymd2,6, 2 ));


	//Salesforce抽出用日時の作成
	//Salesforce上は、実際の時間の9時間前で記録されている為
	$wFromDate9 = $fromY . "/" . $fromM . "/" . $fromD . " 00:00:00";
	$wFromDate9 = date("Y-m-d H:i:s",strtotime($wFromDate9 . "-9 hour"));
	$wTo9 = $toY . "/" . $toM . "/" . $toD . " 00:00:00";
	$wTo9 = date("Y-m-d H:i:s",strtotime($wTo9 . "+1 day"));
	$wTo9 = date("Y-m-d H:i:s",strtotime($wTo9 . "-9 hour"));


	//起動時の表示後
	if( $mode != "dsp_after" ) {
		PreMakeWkData();
		$_POST["combostorecode"]="ALL";
		$_POST["combohardcode"]="ALL";

	}

//////////////////////////////////////////
// 店舗ｺﾝﾎﾞの作成
// 予め店舗をグルーピングして選択に使う
//////////////////////////////////////////
function PreMakeWkData() {

global $ENV_MODE;
global $Const_COMPANYCODE;
global $Const_HQ_NAME;
global $Const_DB_SCHEMA;
global $MOJI_ORG;
global $MOJI_NEW;
global $user;
global $this_pg;
global $selmode;
global $wFromDate9;
global $wTo9;


	//最終更新日用のｼｽﾃﾑ日付
	$NowYMD = date("Y").date("m").date("d");

	//DBｵｰﾌﾟﾝ
	$conn = db_connect();

	///////////////////////
	// wselstore前削除(自分)
	///////////////////////
	$sql = "DELETE FROM " . $Const_DB_SCHEMA . "wselstore";
	$sql = $sql." WHERE userid ='". $Const_COMPANYCODE . $user."'";//自分
	$result = $conn->prepare($sql);
	$result->execute();
	$result = null;

	///////////////////////
	// wselstoreの作成
	///////////////////////
	//過去全ての未完了件数(51)と期間指定の未完了件数(52)
	if($selmode==51 || $selmode==52){
		$sql = "SELECT " . $Const_DB_SCHEMA . "case.shopname__c" . "," . $Const_DB_SCHEMA . "case.inquirycategory2__c FROM " . $Const_DB_SCHEMA . "case";
		$sql = $sql . " WHERE ((" . $Const_DB_SCHEMA . "case.closeddate) Is Null)";
		$sql = $sql . "   AND (" . $Const_DB_SCHEMA . "case.hq_name__c='" . $Const_HQ_NAME . "')";//MYCONST
		if( $selmode == 52){ //期間指定
			$sql = $sql . " AND (" . $Const_DB_SCHEMA . "case.receiotdatetime__c>='" . $wFromDate9 . "'";
			$sql = $sql . " AND    " . $Const_DB_SCHEMA . "case.receiotdatetime__c<='" . $wTo9 . "')";
		}
		$sql = $sql . " GROUP BY " . $Const_DB_SCHEMA . "case.shopname__c" . "," . $Const_DB_SCHEMA . "case.inquirycategory2__c";
		$sql = $sql . " ORDER BY " . $Const_DB_SCHEMA . "case.shopname__c" . "," . $Const_DB_SCHEMA . "case.inquirycategory2__c";
	}else{
		//総件数・インシデント別
		$sql = "SELECT " . $Const_DB_SCHEMA . "case.shopname__c" . "," . $Const_DB_SCHEMA . "case.inquirycategory2__c FROM " . $Const_DB_SCHEMA . "case";
		$sql = $sql . " WHERE (" . $Const_DB_SCHEMA . "case.receiotdatetime__c>='" . $wFromDate9 . "'";
		$sql = $sql . " AND    " . $Const_DB_SCHEMA . "case.receiotdatetime__c<='" . $wTo9 . "')";
		$sql = $sql . " AND   (" . $Const_DB_SCHEMA . "case.hq_name__c='" . $Const_HQ_NAME . "')";//MYCONST
		if( $selmode>=11 && $selmode <=20 ){//インシデント別
			$selname = SfCloseresonNoToName( $selmode ); //fjcall_comfunc.php
			$sql = $sql . "   AND (" . $Const_DB_SCHEMA . "case.closereson__c='" . $selname . "')";//MYCONST
		}
		$sql = $sql . " GROUP BY " . $Const_DB_SCHEMA . "case.shopname__c" . "," . $Const_DB_SCHEMA . "case.inquirycategory2__c";
		$sql = $sql . " ORDER BY " . $Const_DB_SCHEMA . "case.shopname__c" . "," . $Const_DB_SCHEMA . "case.inquirycategory2__c";
	}
	if($ENV_MODE == 1){
		$sql = mb_convert_encoding( $sql, $MOJI_ORG, $MOJI_NEW ); //ここは日本語が混じっているのでUTF-8へ
	}
	$result = $conn->prepare($sql);
	$result->execute();
	while ($rs = $result->fetch(PDO::FETCH_ASSOC))
	{
		$wstodecode = substr($rs["shopname__c"],0,3);
		$whardcode = $rs["inquirycategory2__c"];

		$sql2="Insert Into  " . $Const_DB_SCHEMA . "wselstore";
		$sql2 = $sql2. "(userid, execphp, storecode,hardcode,storename,hardname,lastupdateday)";
		$sql2 = $sql2. " VALUES(";
		$sql2 = $sql2. "'" . $Const_COMPANYCODE . $user."'";
		$sql2 = $sql2. ",'" . $this_pg ."'";
		$sql2 = $sql2. ",'" . $rs["shopname__c"] ."'";
		$sql2 = $sql2. ",'" . $whardcode ."'";
		$sql2 = $sql2. ",'" . $rs["shopname__c"] ."'";
		$sql2 = $sql2. ",'" . $whardcode ."'";
		$sql2 = $sql2. "," . $NowYMD;
		$sql2 = $sql2. ")";
		$result2 = $conn->query($sql2);
		$result2 = null;
	}
	$result = null;
	$conn = null;

}

//////////////////////////////////////////
//<説明>
// 店舗のｺﾝﾎﾞﾎﾞｯｸｽ作成
// $wDefStoreCode:表示したい店舗
//////////////////////////////////////////
function SetStoreCombo($wDefStoreCode) {

global $ENV_MODE;
global $Const_COMPANYCODE;
global $Const_HQ_NAME;
global $Const_DB_SCHEMA;
global $MOJI_ORG;
global $MOJI_NEW;
global $user;
global $this_pg;

	$conn = db_connect();

	print "<option value='ALL' selected>" . "▼全ての店舗" .  "</option>"; 

	$sql = "SELECT " . $Const_DB_SCHEMA . "wselstore.storename FROM " . $Const_DB_SCHEMA . "wselstore";
	$sql = $sql . " WHERE userid='". $Const_COMPANYCODE . $user."'";
	$sql = $sql . " AND   execphp='". $this_pg ."'";
	$sql = $sql . " GROUP By " . $Const_DB_SCHEMA . "wselstore.storename";
	$sql = $sql . " ORDER By " . $Const_DB_SCHEMA . "wselstore.storename";
	if($ENV_MODE == 1){
		$sql = mb_convert_encoding( $sql, $MOJI_ORG, $MOJI_NEW ); //ここは日本語が混じっているのでUTF-8へ
	}
   	$result = $conn->prepare($sql);
  	$result->execute();
	while ($rs = $result->fetch(PDO::FETCH_ASSOC))
	{
		if($ENV_MODE == 1){
			$storename = mb_convert_encoding( $rs['storename'], $MOJI_NEW,$MOJI_ORG); //文字コード変換
		}else{
			$storename = $rs['storename'];
		}
		//ｺﾝﾎﾞの初期表示
		if ($wDefStoreCode == $storename){
			print "<option value='" . $storename . "' selected>" . $storename .  "</option>"; 
		}else{
			print "<option value='" . $storename . "'>" . $storename .  "</option>"; 
		}	
	}
	$result = null;
	$conn = null;
}
//////////////////////////////////////////
//<説明>
// 機種のｺﾝﾎﾞﾎﾞｯｸｽ作成
// $wDefStoreCode:表示したい機種
//////////////////////////////////////////
function SetHardCombo($wDefHardCode) {

global $ENV_MODE;
global $Const_COMPANYCODE;
global $Const_HQ_NAME;
global $Const_DB_SCHEMA;
global $MOJI_ORG;
global $MOJI_NEW;
global $user;
global $this_pg;

	$conn = db_connect();

	print "<option value='ALL' selected>" . "▼全ての機種" .  "</option>"; 

	$sql = "SELECT " . $Const_DB_SCHEMA . "wselstore.hardname FROM " . $Const_DB_SCHEMA . "wselstore";
	$sql = $sql . " WHERE userid='". $Const_COMPANYCODE . $user."'";
	$sql = $sql . " AND   execphp='". $this_pg ."'";
	$sql = $sql . " GROUP By " . $Const_DB_SCHEMA . "wselstore.hardname";
	$sql = $sql . " ORDER By " . $Const_DB_SCHEMA . "wselstore.hardname";
	if($ENV_MODE == 1){
		$sql = mb_convert_encoding( $sql, $MOJI_ORG, $MOJI_NEW ); //ここは日本語が混じっているのでUTF-8へ
	}
   	$result = $conn->prepare($sql);
  	$result->execute();
	while ($rs = $result->fetch(PDO::FETCH_ASSOC))
	{
		if($ENV_MODE == 1){
			$hardname = mb_convert_encoding( $rs['hardname'], $MOJI_NEW,$MOJI_ORG); //文字コード変換
		}else{
			$hardname = $rs['hardname'];
		}
		//ｺﾝﾎﾞの初期表示
		if ($wDefHardCode == $hardname){
			print "<option value='" . $hardname . "' selected>" . $hardname .  "</option>"; 
		}else{
			print "<option value='" . $hardname . "'>" . $hardname .  "</option>"; 
		}	
	}
	$result = null;
	$conn = null;
}

?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache">
<meta http-equiv="Content-Type" content="text/html; charset=<?= $Const_HTML_CHARSET ?>">
<META NAME="ROBOTS" CONTENT="NOINDEX,NOFOLLOW">
<link rel="stylesheet" type="text/css" href="../common/fjcall_common.css">
<title><? print $this_modname?></title>
</head>
<body bgcolor="<?= $SUBWINDOW_BGCOLOR ?>">
<form name="form02" id="form02" method="POST" action="<?= $this_pg ?>" onsubmit="return false;">

	<input type="hidden" name="mode" value="dsp_after" >
	<input type="hidden" name="selymd1" Value="<?=$selymd1?>">
	<input type="hidden" name="selymd2" Value="<?=$selymd2?>">
	<input type="hidden" name="selmode" Value="<?=$selmode?>">
	<input type="hidden" name="sortflg" Value="<?=$sortflg?>">


	<center>

	<!-- ﾛｸﾞｲﾝ情報エリア -->

	<table width="1060" border="0" style="font-size:9pt;">
		<tr>
			<td width="260" align="left"><?= $companyname ?> 様</td>
			<td width="380" align="left"><font color="red" style="font-size:8pt;"><label id="wait_label"><?= $wait_mess ?> &nbsp;</label></font></td>
			<td align="left" width="320" align="left"><a href="#" onClick="MyComCSV()">CSVダウンロード</a></td>
			<td align="right" width="100"><a href="#" onClick="MyWinClose()">閉じる</a></td>
		</tr>
	</table>

	<hr width="1060">



<?php
		//DBｵｰﾌﾟﾝ
		$conn = db_connect();
?>

		<!-- 外側(Header)のTable Start -->
		<table width="1060"><tr><td valign="top">
			<!-- 内側のTable Start -->
			<DIV style="width:1060px; overflow:auto;">	
			<!--見出し Start-->
			<table id="tblList1" width="1060" border="0" cellpadding="0" cellspacing="1" bgcolor="#999999"  style="font-size:9pt;">
				<tr height="20" bgcolor=<? print $GRID_TITLE_BGCOLOR2 ?> >
					<td width="40"  align="center" rowspan="2"><font color=<? print $GRID_TITLE_FTCOLOR ?>>No</td>
					<td width="160" align="center" rowspan="2"><font color=<? print $GRID_TITLE_FTCOLOR ?>><a href="javascript:void(0)" target="" Onclick="MySortClick(1);return false">日時</a></td>
					<td width="240" align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>><a href="javascript:void(0)" target="" Onclick="MySortClick(2);return false">店舗名</a></td>
					<td width="70"  align="center" rowspan="2"><font color=<? print $GRID_TITLE_FTCOLOR ?>><a href="javascript:void(0)" target="" Onclick="MySortClick(0);return false">No</a></td>
					<td width="70"  align="center" rowspan="2"><font color=<? print $GRID_TITLE_FTCOLOR ?>><a href="javascript:void(0)" target="" Onclick="MySortClick(3);return false">内容</a></td>
					<td width="180" align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>><a href="javascript:void(0)" target="" Onclick="MySortClick(4);return false">機種</a></td>
					<td width="60"  align="center"  rowspan="2"><font color=<? print $GRID_TITLE_FTCOLOR ?>><a href="javascript:void(0)" target="" Onclick="MySortClick(7);return false">機器番号</a></td>
					<td width="110" align="center"  rowspan="2"><font color=<? print $GRID_TITLE_FTCOLOR ?>><a href="javascript:void(0)" target="" Onclick="MySortClick(5);return false">ステータス</a></td>
					<td width="130" align="center"  rowspan="2"><font color=<? print $GRID_TITLE_FTCOLOR ?>><a href="javascript:void(0)" target="" Onclick="MySortClick(6);return false">完了理由</a></td>
				</tr>
				<tr height="20" bgcolor=<? print $GRID_TITLE_BGCOLOR2 ?> >
					<td width="240"><select name="combostorecode" onChange="MyCodeClick()" onkeydown=EnterToTab(event) ><?php SetStoreCombo( $_POST["combostorecode"] ); ?></select></td>
					<td width="180"><select name="combohardcode" onChange="MyCodeClick()" onkeydown=EnterToTab(event) ><?php SetHardCombo( $_POST["combohardcode"] ); ?></select></td>
				</tr>
				<!--見出し End-->
				<!-- 明細 Start -->
<?
				$wRecCnt = 0;


				//インシデント->総件数
				if($selmode==1){
					$sql = "SELECT * From " . $Const_DB_SCHEMA . "case";
					$sql = $sql . " WHERE (" . $Const_DB_SCHEMA . "case.receiotdatetime__c>='" . $wFromDate9 . "'";
					$sql = $sql . " AND    " . $Const_DB_SCHEMA . "case.receiotdatetime__c<='" . $wTo9 . "')";
					$sql = $sql . " AND   (" . $Const_DB_SCHEMA . "case.hq_name__c='" . $Const_HQ_NAME . "')";//MYCONST
					//店舗選択あり
					if($_POST["combostorecode"] !="ALL"){
						$sql = $sql . " AND   (" . $Const_DB_SCHEMA . "case.shopname__c='" . $_POST["combostorecode"] . "')";
					}
					//機種選択あり
					if($_POST["combohardcode"] !="ALL"){
						$sql = $sql . " AND   (" . $Const_DB_SCHEMA . "case.inquirycategory2__c='" . $_POST["combohardcode"] . "')";
					}
					if( $sortflg == 1 ){
						$sql = $sql . " ORDER BY " . $Const_DB_SCHEMA  . "case.receiotdatetime__c"; //日時
					}elseif( $sortflg == 2 ){
						$sql = $sql . " ORDER BY " . $Const_DB_SCHEMA  . "case.shopname__c"; //店舗名
					}elseif( $sortflg == 3 ){
						$sql = $sql . " ORDER BY convert_to(" . $Const_DB_SCHEMA  . "case.inquirycategory3__c,'UTF-8')"; //内容
					}elseif( $sortflg == 4 ){
						$sql = $sql . " ORDER BY convert_to(" . $Const_DB_SCHEMA  . "case.inquirycategory2__c,'UTF-8')"; //機種
					}elseif( $sortflg == 5 ){
						$sql = $sql . " ORDER BY convert_to(" . $Const_DB_SCHEMA  . "case.status,'UTF-8')"; //ステータス
					}elseif( $sortflg == 6 ){
						$sql = $sql . " ORDER BY convert_to(" . $Const_DB_SCHEMA  . "case.closereson__c,'UTF-8')"; //完了理由
					}elseif( $sortflg == 7 ){
						$sql = $sql . " ORDER BY convert_to(" . $Const_DB_SCHEMA  . "case.machinenumber__c,'UTF-8')"; //機器番号
					}else{
						$sql = $sql . " ORDER BY " . $Const_DB_SCHEMA  . "case.casenumber"; //インシデントNO
					}
					if($ENV_MODE == 1){
						$sql = mb_convert_encoding( $sql, $MOJI_ORG, $MOJI_NEW ); //ここは日本語が混じっているのでUTF-8へ
					}
				}
				//インシデント別
				if( $selmode>=11 && $selmode <=20 ){
					$selname = SfCloseresonNoToName( $selmode ); //fjcall_comfunc.php

					$sql = "SELECT * From " . $Const_DB_SCHEMA . "case";
					$sql = $sql . " WHERE (" . $Const_DB_SCHEMA . "case.receiotdatetime__c>='" . $wFromDate9 . "'";
					$sql = $sql . " AND    " . $Const_DB_SCHEMA . "case.receiotdatetime__c<='" . $wTo9 . "')";
					$sql = $sql . "   AND (" . $Const_DB_SCHEMA . "case.hq_name__c='" . $Const_HQ_NAME . "')";//MYCONST
					$sql = $sql . "   AND (" . $Const_DB_SCHEMA . "case.closereson__c='" . $selname . "')";//MYCONST
					//店舗選択あり
					if($_POST["combostorecode"] !="ALL"){
						$sql = $sql . " AND   (" . $Const_DB_SCHEMA . "case.shopname__c='" . $_POST["combostorecode"] . "')";
					}
					//機種選択あり
					if($_POST["combohardcode"] !="ALL"){
						$sql = $sql . " AND   (" . $Const_DB_SCHEMA . "case.inquirycategory2__c='" . $_POST["combohardcode"] . "')";
					}

					if( $sortflg == 1 ){
						$sql = $sql . " ORDER BY " . $Const_DB_SCHEMA  . "case.receiotdatetime__c"; //日時
					}elseif( $sortflg == 2 ){
						$sql = $sql . " ORDER BY " . $Const_DB_SCHEMA  . "case.shopname__c"; //店舗名
					}elseif( $sortflg == 3 ){
						$sql = $sql . " ORDER BY convert_to(" . $Const_DB_SCHEMA  . "case.inquirycategory3__c,'UTF-8')"; //内容
					}elseif( $sortflg == 4 ){
						$sql = $sql . " ORDER BY convert_to(" . $Const_DB_SCHEMA  . "case.inquirycategory2__c,'UTF-8')"; //機種
					}elseif( $sortflg == 5 ){
						$sql = $sql . " ORDER BY convert_to(" . $Const_DB_SCHEMA  . "case.status,'UTF-8')"; //ステータス
					}elseif( $sortflg == 6 ){
						$sql = $sql . " ORDER BY convert_to(" . $Const_DB_SCHEMA  . "case.closereson__c,'UTF-8')"; //完了理由
					}elseif( $sortflg == 7 ){
						$sql = $sql . " ORDER BY convert_to(" . $Const_DB_SCHEMA  . "case.machinenumber__c,'UTF-8')"; //機器番号
					}else{
						$sql = $sql . " ORDER BY " . $Const_DB_SCHEMA  . "case.casenumber"; //インシデントNO
					}
					if($ENV_MODE == 1){
						$sql = mb_convert_encoding( $sql, $MOJI_ORG, $MOJI_NEW ); //ここは日本語が混じっているのでUTF-8へ
					}
				}
				//過去全ての未完了件数(51)と期間指定の未完了件数(52)
				if($selmode==51 || $selmode==52){
					$sql = "SELECT * From " . $Const_DB_SCHEMA . "case";
					$sql = $sql . " WHERE ((" . $Const_DB_SCHEMA . "case.closeddate) Is Null)";
					$sql = $sql . "   AND (" . $Const_DB_SCHEMA . "case.hq_name__c='" . $Const_HQ_NAME . "')";//MYCONST
					if( $selmode == 52){ //期間指定
						$sql = $sql . " AND (" . $Const_DB_SCHEMA . "case.receiotdatetime__c>='" . $wFromDate9 . "'";
						$sql = $sql . " AND    " . $Const_DB_SCHEMA . "case.receiotdatetime__c<='" . $wTo9 . "')";
					}
					//店舗選択あり
					if($_POST["combostorecode"] !="ALL"){
						$sql = $sql . " AND   (" . $Const_DB_SCHEMA . "case.shopname__c='" . $_POST["combostorecode"] . "')";
					}
					//機種選択あり
					if($_POST["combohardcode"] !="ALL"){
						$sql = $sql . " AND   (" . $Const_DB_SCHEMA . "case.inquirycategory2__c='" . $_POST["combohardcode"] . "')";
					}

					if( $sortflg == 1 ){
						$sql = $sql . " ORDER BY " . $Const_DB_SCHEMA  . "case.receiotdatetime__c"; //日時
					}elseif( $sortflg == 2 ){
						$sql = $sql . " ORDER BY " . $Const_DB_SCHEMA  . "case.shopname__c"; //店舗名
					}elseif( $sortflg == 3 ){
						$sql = $sql . " ORDER BY convert_to(" . $Const_DB_SCHEMA  . "case.inquirycategory3__c,'UTF-8')"; //内容
					}elseif( $sortflg == 4 ){
						$sql = $sql . " ORDER BY convert_to(" . $Const_DB_SCHEMA  . "case.inquirycategory2__c,'UTF-8')"; //機種
					}elseif( $sortflg == 5 ){
						$sql = $sql . " ORDER BY convert_to(" . $Const_DB_SCHEMA  . "case.status,'UTF-8')"; //ステータス
					}elseif( $sortflg == 6 ){
						$sql = $sql . " ORDER BY convert_to(" . $Const_DB_SCHEMA  . "case.closereson__c,'UTF-8')"; //完了理由
					}elseif( $sortflg == 7 ){
						$sql = $sql . " ORDER BY convert_to(" . $Const_DB_SCHEMA  . "case.machinenumber__c,'UTF-8')"; //機器番号
					}else{
						$sql = $sql . " ORDER BY " . $Const_DB_SCHEMA  . "case.casenumber"; //インシデントNO
					}
					if($ENV_MODE == 1){
						$sql = mb_convert_encoding( $sql, $MOJI_ORG, $MOJI_NEW ); //ここは日本語が混じっているのでUTF-8へ
					}
				}

				$result = $conn->prepare($sql);
				$result->execute();
				while ($rs = $result->fetch(PDO::FETCH_ASSOC))
				{
					$wRecCnt = $wRecCnt + 1;


					$bkcolor_base = $GRID_MEISAI_COLOR1;


					$receiotdatetime__c = date("Y-m-d H:i:s",strtotime($rs["receiotdatetime__c"] . "+9 hour")); //ここで9時間足す
//					$createdateYMD = substr($receiotdatetime__c,0,10);
//					$createdateHNS = substr($receiotdatetime__c,11);

					if($ENV_MODE == 1){
						$storename = mb_convert_encoding( $rs['shopname__c'], $MOJI_NEW,$MOJI_ORG); //文字コード変換;
						$naiyou = mb_convert_encoding( $rs['inquirycategory3__c'], $MOJI_NEW,$MOJI_ORG); //文字コード変換;
						$kisyu = mb_convert_encoding( $rs['inquirycategory2__c'], $MOJI_NEW,$MOJI_ORG); //文字コード変換;
						$machine = mb_convert_encoding( $rs['machinenumber__c'], $MOJI_NEW,$MOJI_ORG); //文字コード変換;
						$status =mb_convert_encoding( $rs['status'], $MOJI_NEW,$MOJI_ORG); //文字コード変換;
						$taiou =mb_convert_encoding( $rs['closereson__c'], $MOJI_NEW,$MOJI_ORG); //文字コード変換;
					}else{
						$storename = $rs['shopname__c'];
						$naiyou = $rs['inquirycategory3__c'];
						$kisyu = $rs['inquirycategory2__c'];
						$machine = $rs['machinenumber__c'];
						$status =$rs['status'];
						$taiou =$rs['closereson__c'];
					}
?>
					<tr height="20" bgcolor="#FFFFFF" >
						<td width="40"  align="center" bgcolor=<?=$bkcolor_base?>><?= $wRecCnt ?></td>
						<td width="160"  align="center" bgcolor=<?=$bkcolor_base?>><?= $receiotdatetime__c ?></td>
						<td width="210" align="left"   bgcolor=<?=$bkcolor_base?>>&nbsp;<?= $storename ?></td>
						<td width="70"  align="center" bgcolor=<?=$bkcolor_base?>><a href="javascript:void(0)" target=<?=$rs["casenumber"]?> onClick="MyListOpen(this.target);return false"><?=$rs["casenumber"]?></a></td>
						<td width="70"  align="left"   bgcolor=<?=$bkcolor_base?>>&nbsp;<?= $naiyou ?></td>
						<td width="180" align="left"   bgcolor=<?=$bkcolor_base?>>&nbsp;<?= $kisyu ?></td>
						<td width="60 " align="left"   bgcolor=<?=$bkcolor_base?>>&nbsp;<?= $machine ?></td>
						<td width="110" align="left"   bgcolor=<?=$bkcolor_base?>>&nbsp;<?= $status ?></td>
						<td width="130" align="left"   bgcolor=<?=$bkcolor_base?>>&nbsp;<?= $taiou ?></td>
					</tr>
<?
				}
				$result=null;
?>
			</table>
			</DIV>
			<!-- 内側のTable End -->
		<!-- 外側(Header)のTable End   -->
		</td></tr></table>
<?
		//DBｸﾛｰｽﾞ
		$conn = null;

?>



</center>

</form>
</body>
</html>
<script language="javascript" src="../common/fjcall_ComFunc.js"></script>


<SCRIPT Language="JavaScript">
<!--

/////////////////////////////////////////
// 店舗Comboクリック
/////////////////////////////////////////
function MyCodeClick( sortflg )
{
	var labelObj = document.getElementById("wait_label");
	labelObj.innerHTML = "しばらくお待ち下さい。。。";

	form02.submit();

}

MyCodeClick
/////////////////////////////////////////
// 表題(ソート)クリック
/////////////////////////////////////////
function MySortClick( sortflg )
{
	var labelObj = document.getElementById("wait_label");
	labelObj.innerHTML = "しばらくお待ち下さい。。。";

	form02.sortflg.value = sortflg;
	form02.submit();

}

/////////////////////////////////////////
// CSV出力ﾎﾞﾀﾝ
/////////////////////////////////////////
function MyComCSV(){

	//日付範囲
	selymd1 = form02.selymd1.value;
	selymd2 = form02.selymd2.value;
	selmode = form02.selmode.value;

	strUrl='topmenu_sfmeisai_csvout.php?selymd1='+unescape(selymd1)+'&selymd2='+unescape(selymd2)+'&selmode='+unescape(selmode)

	flag = confirm("CSVデータを出力します。よろしいですか？");
	if(flag){
		location.href=strUrl;
	}else{
		return false;
	}
}

//////////////////////////////////////////
// ﾃﾞｰﾀ入力ｳｨﾝﾄﾞｳｵｰﾌﾟﾝ
//////////////////////////////////////////
function MyListOpen(rno)
{

var string1 = rno;

//alert(rno);

	window.open('topmenu_sfmeisai_view.php?datano=' + unescape(string1) , '_blank', 'width=1150,height=600,titlebar=no,toolbar=no,scrollbars=yes, top=' + (screen.availHeight-500)/2 + ',left=' + (screen.availWidth-500)/2);

}
//-->
</SCRIPT>
