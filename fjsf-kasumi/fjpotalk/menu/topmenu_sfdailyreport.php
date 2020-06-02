<?php
	session_start();

	include "../common/MYDB.php";
	include "../common/MYCONST.php";
	include "../common/fjcall_comfunc.php";
	include "../common/fjcall_const.php";


	$this_pg ='topmenu_sfdailyreport.php';
	$this_modname = "日次報告";

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


	//親ﾌｫｰﾑでの選択情報
	$selymd1 = $_GET["selymd1"];
	$selymd2 = $_GET["selymd2"];
	$selmode = $_GET["selmode"];
	$sortflg = 0;//インシデントNo


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

	<hr width="1390">



<?php
		//DBｵｰﾌﾟﾝ
		$conn = db_connect();
?>

		<!-- 外側(Header)のTable Start -->
		<table width="1060"><tr><td valign="top">
			<!-- 内側のTable Start -->
			<DIV style="width:1390px; overflow:auto;">
			<!--見出し Start-->
			<table id="tblList1"  border="0" cellpadding="0" cellspacing="1" bgcolor="#999999"  style="font-size:8pt;">
				<tr height="20" bgcolor=<? print $GRID_TITLE_BGCOLOR ?> >
					<td width="30"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>No</td>
					<td width="80"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>日時</td>
					<td width="60"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>ｲﾝｼﾃﾞﾝﾄNo</td>
					<td width="180" align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>店舗名</td>
					<td width="70"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>ご担当者</td>
					<td width="60"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>受付者</td>
					<td width="60"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>ステータス</td>
					<td width="90"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>機種</td>
					<td width="60"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>機器番号</td>
					<td width="180" align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>問合せ内容</td>
					<td width="180" align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>対応内容</td>
					<td width="120" align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>対応依頼先</td>
					<td width="80"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>クローズ日時</td>
					<td width="60"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>クローズ者</td>
					<td width="80"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>完了理由</td>

				</tr>
				<!--見出し End-->
				<!-- 明細 Start -->
<?
				$wRecCnt = 0;

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


					//1行おきに色(基本の背景色)
					if( $wRecCnt%2 == 0 ){
						$bkcolor_base = $GRID_MEISAI_COLOR2;//グレー
					}else{
						$bkcolor_base = $GRID_MEISAI_COLOR1;//白
					}



					$receiotdatetime__c = date("Y-m-d H:i:s",strtotime($rs["receiotdatetime__c"] . "+9 hour")); //ここで9時間足す
					if($rs["closeddate"]>0){
						$closeddate = date("Y-m-d H:i:s",strtotime($rs["closeddate"] . "+9 hour")); //ここで9時間足す
					}else{
						$closeddate = "";
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
?>
					<tr height="20" bgcolor="#FFFFFF" >
						<td width="30"  align="center" bgcolor=<?=$bkcolor_base?>><?= $wRecCnt ?></td>
						<td width="80"  align="center" bgcolor=<?=$bkcolor_base?>><?= $receiotdatetime__c ?></td>
						<td width="60"  align="center" bgcolor=<?=$bkcolor_base?>><?=$rs["casenumber"]?></td>
						<td width="180" align="left"   bgcolor=<?=$bkcolor_base?>>&nbsp;<?= $storename ?></td>
						<td width="70"  align="left"   bgcolor=<?=$bkcolor_base?>>&nbsp;<?= $storetan ?></td>
						<td width="60"  align="left"   bgcolor=<?=$bkcolor_base?>>&nbsp;<?= $laststaff ?></td>
						<td width="60"  align="left"   bgcolor=<?=$bkcolor_base?>>&nbsp;<?= $status ?></td>
						<td width="90"  align="left"   bgcolor=<?=$bkcolor_base?>>&nbsp;<?= $kisyu ?></td>
						<td width="60 " align="left"   bgcolor=<?=$bkcolor_base?>>&nbsp;<?= $machine ?></td>
						<td width="180" align="left"   bgcolor=<?=$bkcolor_base?>>&nbsp;<?= $description ?></td>
						<td width="180" align="left"   bgcolor=<?=$bkcolor_base?>>&nbsp;<?= $lastreport ?></td>
						<td width="120" align="left"   bgcolor=<?=$bkcolor_base?>>&nbsp;<?= $taiousaki ?></td>
						<td width="80"  align="center" bgcolor=<?=$bkcolor_base?>><?= $closeddate ?></td>
						<td width="60"  align="left"   bgcolor=<?=$bkcolor_base?>>&nbsp;<?= $laststaff ?></td>
						<td width="80"  align="left"   bgcolor=<?=$bkcolor_base?>>&nbsp;<?= $closereson ?></td>
					</tr>
<?
				}
				$result=null;
?>
				<!-- 最終行にも見出し -->
				<tr height="20" bgcolor=<? print $GRID_TITLE_BGCOLOR ?> >
					<td width="30"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>No</td>
					<td width="80"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>日時</td>
					<td width="60"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>ｲﾝｼﾃﾞﾝﾄNo</td>
					<td width="180" align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>店舗名</td>
					<td width="70"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>ご担当者</td>
					<td width="60"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>受付者</td>
					<td width="60"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>ステータス</td>
					<td width="90"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>機種</td>
					<td width="60"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>機器番号</td>
					<td width="180" align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>問合せ内容</td>
					<td width="180" align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>対応内容</td>
					<td width="120" align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>対応依頼先</td>
					<td width="80"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>クローズ日時</td>
					<td width="60"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>クローズ者</td>
					<td width="80"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>完了理由</td>

				</tr>
				<!--最終行にも見出し End-->

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
// CSV出力ﾎﾞﾀﾝ
/////////////////////////////////////////
function MyComCSV(){

	//日付範囲
	selymd1 = form02.selymd1.value;
	selymd2 = form02.selymd2.value;

	strUrl='topmenu_sfdailyreport_csvout.php?selymd1='+unescape(selymd1)+'&selymd2='+unescape(selymd2)

	flag = confirm("CSVデータを出力します。よろしいですか？");
	if(flag){
		location.href=strUrl;
	}else{
		return false;
	}
}
//-->
</SCRIPT>
