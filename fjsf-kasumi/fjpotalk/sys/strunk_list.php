<?php
//2019-02-08 メモの表示を追加

	session_start();

	include "../common/MYDB.php";
	include "../common/MYCONST.php";
	include "../common/fjcall_comfunc.php";
	include "../common/fjcall_const.php";


	$this_pg ='strunk_list.php';
	$this_modname = "トランク一覧";

	$companycode = $_SESSION["companycode_call"];//会社コード
	$user = $_SESSION['userid_call'];
	$name = $_SESSION['name_call'];
	$level = $_SESSION['levelid_call'];
	$maindeskcode = $_SESSION["maindeskcode_call"];


	//URL直は排除
	if($user == ""){
		$first="../fjptlksm.php";
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
<meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
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
	<div id="logininfo">
		<table border="0">
			<tr>
				<td width="80"  align="left"><img src=<?php print $GUIDE_MODNAME_TOP ?> border="0"></td>
				<td width="160" align="left"><img src=<?php print $GUIDE_MODNAME7 ?> border="0"></td>
				<td width="230" align="left"><img src=<? print $LOGO_LOGIN_S ?> border="0">
											<?php print "【 " ; print $name; print " さん】" ?>
				</td>
				<td align="right" width="630">
					<a href="#" onClick="MyGoTopMenu()"><img src=<?= $MOD_LINKPRE_01 ?> border="0" alt="トップメニューに戻る">トップメニューに戻る</a>｜
					<a href="#" onClick="MyWondowLogout()">ログアウトする</a>
				</td>
			</tr>
		</table>
		<hr>
	</div>



<?php
		//DBｵｰﾌﾟﾝ
		$conn = db_connect();
?>

		<!-- 外側(Header)のTable Start -->
		<table width="1100"><tr><td valign="top">
			<!-- 内側のTable Start -->
			<DIV style="width:1100px; overflow:auto;">	
			<!--見出し Start-->
			<table id="tblList1" width="1100" border="0" cellpadding="0" cellspacing="1" bgcolor="#999999"  style="font-size:9pt;">
				<tr height="20" bgcolor=<? print $GRID_TITLE_BGCOLOR ?> >
					<td width="40"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>No</td>
					<td width="70"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>トランク<br>番号</td>
					<td width="100" align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>外線番号</td>
					<td width="140" align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>外線名称</td>
					<td width="90"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>メイン<br>トランク番号</td>
					<td width="100" align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>デスク</td>
					<td width="160" align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>クライアント</td>
					<td width="170" align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>エンドユーザ</td>
					<td width="230" align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>メモ</td>
				</tr>
				<!--見出し End-->
				<!-- 明細 Start -->
<?
				$wRecCnt = 0;
				$sql = "SELECT " . $Const_DB_SCHEMA . "mtrunk.*, mdesk.deskname, " . $Const_DB_SCHEMA . "mclient.clientname, " . $Const_DB_SCHEMA . "menduser.endusername";
				$sql = $sql . " FROM ((" . $Const_DB_SCHEMA . "mtrunk LEFT JOIN " . $Const_DB_SCHEMA . "mdesk ON mtrunk.deskcode = " . $Const_DB_SCHEMA . "mdesk.deskcode) LEFT JOIN " . $Const_DB_SCHEMA . "mclient ON " . $Const_DB_SCHEMA . "mtrunk.clientcode = " . $Const_DB_SCHEMA . "mclient.clientcode) LEFT JOIN " . $Const_DB_SCHEMA . "menduser ON (" . $Const_DB_SCHEMA . "mtrunk.endusercode = " . $Const_DB_SCHEMA . "menduser.endusercode) AND (" . $Const_DB_SCHEMA . "mtrunk.clientcode = " . $Const_DB_SCHEMA . "menduser.clientcode)";
				$sql = $sql . " ORDER BY " . $Const_DB_SCHEMA . "mtrunk.trunkno";
//				$sql = mb_convert_encoding( $sql, "SJIS-win", "SJIS-win");
				$result = $conn->prepare($sql);
				$result->execute();
				while ($rs = $result->fetch(PDO::FETCH_ASSOC))
				{
					$wRecCnt = $wRecCnt + 1;


					$bkcolor_base = $GRID_MEISAI_COLOR1;

					//その他
					if($rs["deskcode"] == 9){
						$bkcolor_base = $GRID_MEISAI_COLOR2;
					}
					//QSCデスク
					if($rs["deskcode"] == 0){
						$bkcolor_base = $GRID_MEISAI_COLOR4;
					}

					//メイントランク番号
					if($rs["maintrunkno"] != 0 ){
						$wDspMain = $rs["maintrunkno"];
					}else{
						$wDspMain = "";
					}

//					$gaisenname = mb_convert_encoding( $rs['gaisenname'], $MOJI_NEW,$MOJI_ORG); //文字コード変換;
//					$deskname = mb_convert_encoding( $rs['deskname'], $MOJI_NEW,$MOJI_ORG); //文字コード変換;
//					$clientname = mb_convert_encoding( $rs['clientname'], $MOJI_NEW,$MOJI_ORG); //文字コード変換;
//					$endusername = mb_convert_encoding( $rs['endusername'], $MOJI_NEW,$MOJI_ORG); //文字コード変換;
//					$trunkmemo = mb_convert_encoding( $rs['trunkmemo'], $MOJI_NEW,$MOJI_ORG); //文字コード変換;

					$gaisenname =  $rs['gaisenname'];
					$deskname =  $rs['deskname'];
					$clientname =  $rs['clientname'];
					$endusername =  $rs['endusername'];
					$trunkmemo =  $rs['trunkmemo'];


?>
					<tr height="20" bgcolor="#FFFFFF" >
						<td width="40"  align="center" bgcolor=<?=$bkcolor_base?>><?= $wRecCnt ?></td>
						<td width="70"  align="center" bgcolor=<?=$bkcolor_base?>><?= $rs["trunkno"] ?></td>
						<td width="100" align="center" bgcolor=<?=$bkcolor_base?>><?= $rs["gaisenno"] ?></td>
						<td width="140" align="left"   bgcolor=<?=$bkcolor_base?>>&nbsp;<?= $gaisenname ?></td>
						<td width="90"  align="center" bgcolor=<?=$bkcolor_base?>><?= $wDspMain ?></td>
						<td width="100" align="center" bgcolor=<?=$bkcolor_base?>>&nbsp;<?= $deskname ?></td>
						<td width="160" align="left"   bgcolor=<?=$bkcolor_base?>>&nbsp;<?= $clientname ?></td>
						<td width="170" align="left"   bgcolor=<?=$bkcolor_base?>>&nbsp;<?= $endusername ?></td>
						<td width="230" align="left"   bgcolor=<?=$bkcolor_base?>>&nbsp;<?= $trunkmemo ?></td>
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
