<?php
	session_start();

	include "../common/MYDB.php";
	include "../common/MYCONST.php";
	include "../common/fjcall_comfunc.php";
	include "../common/fjcall_const.php";


	$this_pg ='topmenu_callmeisai.php';
	$this_modname = "着信明細一覧";

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

	//親ﾌｫｰﾑでの選択情報
	$selymd1 = $_GET["selymd1"];
	$selymd2 = $_GET["selymd2"];


	$selclient = $selClient;
	$selenduser = $selEndUser;

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

	<input type="hidden" name="selymd1" Value="<?=$selymd1?>">
	<input type="hidden" name="selymd2" Value="<?=$selymd2?>">

	<center>

	<!-- ﾛｸﾞｲﾝ情報エリア -->

	<table width="410" border="0" style="font-size:9pt;">
		<tr>
			<td width="260" align="left"><?= $companyname ?> 様</td>
			<td align="left" width="200" align="left"><a href="#" onClick="MyComCSV()">CSVダウンロード</a></td>
			<td align="right" width="100"><a href="#" onClick="MyWinClose()">閉じる</a></td>
		</tr>
	</table>

	<hr width="410">



<?php
		//DBｵｰﾌﾟﾝ
		$conn = db_connect();
?>

		<!-- 外側(Header)のTable Start -->
		<table width="410"><tr><td valign="top">
			<!-- 内側のTable Start -->
			<DIV style="width:410px; overflow:auto;">	
			<!--見出し Start-->
			<table id="tblList1" width="410" border="0" cellpadding="0" cellspacing="1" bgcolor="#999999"  style="font-size:9pt;">
				<tr height="20" bgcolor=<? print $GRID_TITLE_BGCOLOR ?> >
					<td width="40"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>No</td>
					<td width="80"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>日付</td>
					<td width="60"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>時刻</td>
					<td width="70"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>通話時間</td>
					<td width="70"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>鳴動時間</td>
					<td width="30"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>G</td>
					<td width="30"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>12</td>
					<td width="30"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>不</td>
				</tr>
				<!--見出し End-->
				<!-- 明細 Start -->
<?
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

?>
					<tr height="20" bgcolor="#FFFFFF" >
						<td width="40"  align="center" bgcolor=<?=$bkcolor_base?>><?= $wRecCnt ?></td>
						<td width="80"  align="center" bgcolor=<?=$bkcolor_base?>><?= $rs["calldate"] ?></td>
						<td width="60"  align="center" bgcolor=<?=$bkcolor_base?>><?= $rs["calltime"] ?></td>
						<td width="70"  align="right"  bgcolor=<?=$bkcolor_base?>><?= $rs["talktime"] ?>&nbsp;</td>
						<td width="70"  align="right"  bgcolor=<?=$bkcolor_base?>><?= $rs["rumblingtime"] ?>&nbsp;</td>
						<td width="30"  align="center" bgcolor=<?=$bkcolor_base?>><?= $markG ?>&nbsp;</td>
						<td width="30"  align="center" bgcolor=<?=$bkcolor_base?>><?= $markG12 ?>&nbsp;</td>
						<td width="30"  align="center" bgcolor=<?=$bkcolor_base?>><?= $markF ?>&nbsp;</td>
					</tr>
<?
				}
				$result = null;
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
// CSV出力ﾎﾞﾀﾝ
/////////////////////////////////////////
function MyComCSV(){

	//日付範囲
	selymd1 = form02.selymd1.value;
	selymd2 = form02.selymd2.value;

	strUrl='topmenu_callmeisai_csvout.php?selymd1='+unescape(selymd1)+'&selymd2='+unescape(selymd2)

	flag = confirm("CSVデータを出力します。よろしいですか？");
	if(flag){
		location.href=strUrl;
	}else{
		return false;
	}
}
//-->
</SCRIPT>
