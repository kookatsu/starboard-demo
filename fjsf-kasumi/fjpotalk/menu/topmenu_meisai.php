<?php
	session_start();

	include "../common/MYDB.php";
	include "../common/MYCONST.php";
	include "../common/fjcall_comfunc.php";
	include "../common/fjcall_const.php";


	$this_pg ='topmenu_meisai.php';
	$this_modname = "着信明細一覧";

	$companycode = $_SESSION["companycode_call"];//会社コード
	$user = $_SESSION['userid_call'];
	$name = $_SESSION['name_call'];
	$level = $_SESSION['levelid_call'];
	$maindeskcode = $_SESSION["maindeskcode_call"];


	//URL直は排除
	if($user == ""){
		$first="../" . $Const_LOGIN_PHP;
		header("location: {$first}");
		exit;
	}

	//親ﾌｫｰﾑでの選択情報
	$selymd1 = $_GET["selymd1"];
	$selymd2 = $_GET["selymd2"];
	$gvalue = $_GET["gvalue"];


	$selclient = mb_substr($gvalue, 0, 3);
	$selenduser = mb_substr($gvalue, 3, 3);

	$conn = db_connect();
	$endusername = GetEndUserName( $conn, $selclient, $selenduser ); //fjcall_comfunc
	$conn = null;

?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<META NAME="ROBOTS" CONTENT="NOINDEX,NOFOLLOW">
<link rel="stylesheet" type="text/css" href="../common/fjcall_common.css">
<title><? print $this_modname?></title>
</head>
<body bgcolor="<?= $SUBWINDOW_BGCOLOR ?>">
<form name="form02" id="form02" method="POST" action="<?= $this_pg ?>" onsubmit="return false;">

	<input type="hidden" name="selymd1" Value="<?=$selymd1?>">
	<input type="hidden" name="selymd2" Value="<?=$selymd2?>">
	<input type="hidden" name="gvalue" Value="<?=$gvalue?>">

	<center>

	<!-- ﾛｸﾞｲﾝ情報エリア -->

	<table width="840" border="0" style="font-size:9pt;">
		<tr>
			<td width="390" align="left"><?= $endusername ?></td>
			<td width="150" align="left">&nbsp;</td>
			<td align="right" width="300"><a href="#" onClick="MyWinClose()">閉じる</a></td>
		</tr>
	</table>

	<hr width="840">



<?php
		//DBｵｰﾌﾟﾝ
		$conn = db_connect();
?>

		<!-- 外側(Header)のTable Start -->
		<table width="840"><tr><td valign="top">
			<!-- 内側のTable Start -->
			<DIV style="width:840px; overflow:auto;">	
			<!--見出し Start-->
			<table id="tblList1" width="840" border="0" cellpadding="0" cellspacing="1" bgcolor="#999999"  style="font-size:9pt;">
				<tr height="20" bgcolor=<? print $GRID_TITLE_BGCOLOR ?> >
					<td width="40"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>No</td>
					<td width="80"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>日付</td>
					<td width="60"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>時刻</td>
					<td width="90"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>トランク番号</td>
					<td width="90"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>外線番号</td>
					<td width="160" align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>外線名称</td>
					<td width="70"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>通話時間</td>
					<td width="90"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>内線</td>
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
//				$sql = mb_convert_encoding( $sql, "SJIS-win", "SJIS-win");
				$result = $conn->prepare($sql);
				$result->execute();
				while ($rs = $result->fetch(PDO::FETCH_ASSOC))
				{
					$wRecCnt = $wRecCnt + 1;

					$markG = ""; $markG12 = ""; $markF = "";

					$bkcolor_base = $GRID_MEISAI_COLOR1;

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

//					$telname = mb_convert_encoding( $rs['telname'], $MOJI_NEW,$MOJI_ORG); //文字コード変換;
					$telname =  $rs['telname'];

?>
					<tr height="20" bgcolor="#FFFFFF" >
						<td width="40"  align="center" bgcolor=<?=$bkcolor_base?>><?= $wRecCnt ?></td>
						<td width="80"  align="center" bgcolor=<?=$bkcolor_base?>><?= $rs["calldate"] ?></td>
						<td width="60"  align="center" bgcolor=<?=$bkcolor_base?>><?= $rs["calltime"] ?></td>
						<td width="90"  align="center" bgcolor=<?=$bkcolor_base?>><?= $rs["trunkno"] ?></td>
						<td width="90"  align="center" bgcolor=<?=$bkcolor_base?>><?= $rs["telno"] ?></td>
						<td width="160" align="left"   bgcolor=<?=$bkcolor_base?>>&nbsp;<?= $telname ?></td>
						<td width="70"  align="right"  bgcolor=<?=$bkcolor_base?>><?= $rs["talktime"] ?>&nbsp;</td>
						<td width="90"  align="center" bgcolor=<?=$bkcolor_base?>><?= $rs["idnaisen"] ?></td>
						<td width="70"  align="right"  bgcolor=<?=$bkcolor_base?>><?= $rs["rumblingtime"] ?>&nbsp;</td>
						<td width="30"  align="center" bgcolor=<?=$bkcolor_base?>><?= $markG ?>&nbsp;</td>
						<td width="30"  align="center" bgcolor=<?=$bkcolor_base?>><?= $markG12 ?>&nbsp;</td>
						<td width="30"  align="center" bgcolor=<?=$bkcolor_base?>><?= $markF ?>&nbsp;</td>
					</tr>
<?
				}
				$result;
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

	<table width="840" border="0" style="font-size:9pt;">
		<tr>
			<td width="150" align="left"><input type="button" value="CSV出力"   onClick="MyComCSV()" style="width:150"></td>
		</tr>
	</table>



</center>

</form>
</body>
</html>
<SCRIPT Language="JavaScript">
<!--
/////////////////////////////////////////
// CSV出力ﾎﾞﾀﾝ
/////////////////////////////////////////
function MyComCSV(){

	//選択情報
	selymd1 = form02.selymd1.value;
	selymd2 = form02.selymd2.value;
	gvalue = form02.gvalue.value;

	strUrl = 'topmenu_csvout_meisai.php?selymd1=' + unescape(selymd1) + '&selymd2=' + unescape(selymd2) + '&gvalue=' + unescape(gvalue);

	flag = confirm("CSVデータを出力します。(着信明細一覧) よろしいですか？");
	if(flag){
		location.href=strUrl;
	}else{
		return false;
	}
}
//-->
</SCRIPT>

<script language="javascript" src="../common/fjcall_ComFunc.js"></script>
