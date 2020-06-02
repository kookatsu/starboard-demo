<?php
	session_start();

	include "../common/MYDB.php";
	include "../common/MYCONST.php";
	include "../common/fjcall_comfunc.php";
	include "../common/fjcall_const.php";


	$this_pg ='message_dsp.php';
	$this_modname = "通達確認";

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


	$conn = db_connect();

	//OKボタン
	if ($_POST["comButton"] !=""){
		//既読処理
		$mid = $_POST["comButton"];
		SqlEdit($conn);
	}

	$midoku = SqlSearch($conn);

    $conn = null;


//////////////////////////////////////////
// 既読処理
//////////////////////////////////////////
function SqlEdit($conn) {

global $Const_DB_SCHEMA;
global $companycode;
global $user;
global $mid;

	$NowYMD = date("Y").date("m").date("d");
	$NowHNS = date("H").date("i").date("s");


    $sql = "UPDATE " . $Const_DB_SCHEMA . "dmessage_suser";
	$sql = $sql . " SET ";
	$sql = $sql . " viewdate=" . $NowYMD;
	$sql = $sql . ",viewtime='" . $NowHNS . "'";
	$sql = $sql . " WHERE companycode ='" . $companycode . "'";
	$sql = $sql . " AND   mid =" .  $mid;
	$sql = $sql . " AND   recieveuser ='" .  $user . "'";
	$result = $conn->prepare($sql);
	$result->execute();
    $result = null;


	
}
//////////////////////////////////////////
// 未読件数取得
//////////////////////////////////////////
function SqlSearch($conn) {

global $Const_DB_SCHEMA;
global $companycode;
global $user;



	$wkensu = 0;

	$sql = "SELECT Count(" . $Const_DB_SCHEMA . "dmessage_suser.mid) AS mid_cnt";
	$sql = $sql . " FROM " . $Const_DB_SCHEMA . "dmessage_suser";
	$sql = $sql . " WHERE (" . $Const_DB_SCHEMA . "dmessage_suser.companycode='" . $companycode . "')";
	$sql = $sql . " AND   (" . $Const_DB_SCHEMA . "dmessage_suser.recieveuser='" . $user . "')";
	$sql = $sql . " AND   (" . $Const_DB_SCHEMA . "dmessage_suser.viewdate=0)";
	$result = $conn->prepare($sql);
	$result->execute();
	if ($rs = $result->fetch(PDO::FETCH_ASSOC)){
		$wkensu = $rs["mid_cnt"];
	}
    $result = null;



	return $wkensu;
}

?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache">
<meta http-equiv="Content-Type" content="text/html; charset=<?= $Const_HTML_CHARSET ?>>
<META NAME="ROBOTS" CONTENT="NOINDEX,NOFOLLOW">
<link rel="stylesheet" type="text/css" href="../common/fjcall_common.css">
<title><? print $this_modname?></title>
</head>
<body bgcolor="<?= $SUBWINDOW_BGCOLOR ?>">
<form name="form01" id="form01" method="POST" action="<?= $this_pg ?>" onsubmit="return false;">

	<input type="hidden" name="comButton" value="">

	<center>

	<!-- ﾛｸﾞｲﾝ情報エリア -->
	<table width="930" border="0" style="font-size:9pt;">
		<tr>
			<td width="210" align="left"><?= $companyname ?> 様</td>
			<td width="140" height="30" align="left" ><img src=<? print $LOGO_LOGIN_S ?> border="0" style="vertical-align: middle;"><?php print "【 " ; print $name; print " さん】" ?></td>
			<td align="left" width="100" align="left">通達一覧</td>
<? if($midoku >0 ){?>
			<td align="left" width="300" align="left"><span style="color:red"><span class="pt14"><?= $midoku ?></span></span> 件の未読の通達があります。</td>
<? }else{ ?>
			<td align="left" width="300" align="left"><?= "未読の通達はありません。"?></td>
<? } ?>
			<td align="right" width="100"><a href="#" onClick="GoToTopMenu()">TOPメニューへ</a></td>
		</tr>
	</table>

	<hr width="930">




<?php
		//DBｵｰﾌﾟﾝ
		$conn = db_connect();
?>

		<!-- 外側(Header)のTable Start -->
		<table width="930" height="650"><tr><td valign="top">
			<!-- 内側のTable Start -->
			<DIV style="width:930px; overflow:auto;">	
			<!--見出し Start-->
			<table id="tblList1" width="930" border="0" cellpadding="0" cellspacing="1" bgcolor="#999999"  style="font-size:9pt;">
				<tr height="20" bgcolor=<? print $GRID_TITLE_BGCOLOR ?> >
					<td width="50"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>No</td>
					<td width="70"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>閲覧</a></td>
					<td width="250" align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>件名</a></td>
					<td width="370" align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>本文</a></td>
					<td width="90"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>通達日</a></td>
					<td width="100" align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>通達者</a></td>
				</tr>
				<!--見出し End-->
				<!-- 明細 Start -->
<?
				$wRecCnt = 0;

				$sql = "SELECT " . $Const_DB_SCHEMA . "dmessage_suser.viewdate, " . $Const_DB_SCHEMA . "dmessage_suser.viewtime,";
				$sql = $sql . $Const_DB_SCHEMA . "dmessage.*, " . $Const_DB_SCHEMA . "suser.username";
				$sql = $sql . " FROM (" . $Const_DB_SCHEMA . "dmessage_suser LEFT JOIN " . $Const_DB_SCHEMA . "dmessage ON (" . $Const_DB_SCHEMA . "dmessage_suser.mid = " . $Const_DB_SCHEMA . "dmessage.mid) AND (" . $Const_DB_SCHEMA . "dmessage_suser.companycode = " . $Const_DB_SCHEMA . "dmessage.companycode))";
				$sql = $sql . " LEFT JOIN " . $Const_DB_SCHEMA . "suser ON (" . $Const_DB_SCHEMA . "dmessage.companycode = " . $Const_DB_SCHEMA . "suser.companycode) AND (" . $Const_DB_SCHEMA . "dmessage.reguser = " . $Const_DB_SCHEMA . "suser.userid)";
				$sql = $sql . " WHERE (" . $Const_DB_SCHEMA . "dmessage_suser.companycode='" . $companycode . "')";
				$sql = $sql . " AND   (" . $Const_DB_SCHEMA . "dmessage_suser.recieveuser='" . $user . "')";
				$sql = $sql . " ORDER BY " . $Const_DB_SCHEMA . "dmessage.regdate DESC , " . $Const_DB_SCHEMA . "dmessage.regtime DESC";

				$result = $conn->prepare($sql);
				$result->execute();
				while ($rs = $result->fetch(PDO::FETCH_ASSOC))
				{
					$wRecCnt = $wRecCnt + 1;


					$bkcolor_base = $GRID_MEISAI_COLOR1;


					if($ENV_MODE == 1){
						$title = mb_convert_encoding( $rs['title'], $MOJI_NEW,$MOJI_ORG); //文字コード変換;
						$body = mb_convert_encoding( $rs['body'], $MOJI_NEW,$MOJI_ORG); //文字コード変換;
						$username = mb_convert_encoding( $rs['username'], $MOJI_NEW,$MOJI_ORG); //文字コード変換;
					}else{
						$title = $rs['title'];
						$body = $rs['body'];
						$username = $rs['username'];
					}

					if($rs["viewdate"]>0){
						$wMess = "既読";
						$bkcolor_base = $GRID_MEISAI_COLOR2;

						$viewY = substr( $rs["viewdate"], 0, 4 );
						$viewM = sprintf("%02d", substr( $rs["viewdate"],4, 2 ));
						$viewD = sprintf("%02d", substr( $rs["viewdate"],6, 2 ));
						$viewH = substr( $rs["viewtime"], 0, 2 );
						$viewN = sprintf("%02d", substr( $rs["viewtime"],2, 2 ));
						$viewS = sprintf("%02d", substr( $rs["viewtime"],4, 2 ));

					}else{
						$wMess = "未読";
						$bkcolor_base = $GRID_MEISAI_COLOR1;
					}

					$fromY = substr( $rs["regdate"], 0, 4 );
					$fromM = sprintf("%02d", substr( $rs["regdate"],4, 2 ));
					$fromD = sprintf("%02d", substr( $rs["regdate"],6, 2 ));
					$fromH = substr( $rs["regtime"], 0, 2 );
					$fromN = sprintf("%02d", substr( $rs["regtime"],2, 2 ));
					$fromS = sprintf("%02d", substr( $rs["regtime"],4, 2 ));




?>
					<tr height="20" bgcolor="#FFFFFF" >
						<td width="50"  align="center" bgcolor=<?=$bkcolor_base?>><?= $wRecCnt ?></td>
<? if($rs["viewdate"]>0){ ?>
						<td width="70"  align="center" bgcolor=<?=$bkcolor_base?>><?= $wMess ?><br><span style="font-size:8pt;"><?= $viewY . "/" . $viewM . "/" . $viewD ?><br><?= $viewH . ":" . $viewN . ":" . $viewS ?></span></td>
<? }else{ ?>
						<td width="70"  align="center" bgcolor=<?=$bkcolor_base?>><a href="javascript:void(0)" target=<?=$rs["mid"]?> Onclick="MyClick(this.target);return false"><?= $wMess ?></a></td>
<? } ?>
						<td width="250" align="left"   bgcolor=<?=$bkcolor_base?>>&nbsp;<?= $title ?></td>
						<td width="370" align="left"   bgcolor=<?=$bkcolor_base?>>&nbsp;<?= nl2br($body) ?></td>
						<td width="90" align="center" bgcolor=<?=$bkcolor_base?>>&nbsp;<?= $fromY . "/" . $fromM . "/" . $fromD ?><br><?= $fromH . ":" . $fromN . ":" . $fromS ?></td>
						<td width="100" align="left"   bgcolor=<?=$bkcolor_base?>>&nbsp;<?= $username ?></td>
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


	<!-- フッダーエリア -->
	<div id="bottom_menu">
		<br>
		<hr>
		<table width="1100" border="0">
			<tr>
				<td width="600" align="left">&nbsp;</td>
				<td width="500" align="right" id="copyright" name="copyright">	Copyright &copy FUJIMOTO CORP. All Rights Reserved.</td>
			</tr>
		</table>
	</div>

</center>

</form>
</body>
</html>
<script language="javascript" src="message_dsp.js"></script>


<SCRIPT Language="JavaScript">
<!--

//////////////////////////////////////////
// ﾃﾞｰﾀ入力ｳｨﾝﾄﾞｳｵｰﾌﾟﾝ
//////////////////////////////////////////
function MyClick(rno)
{

var string1 = rno;

	flag = confirm("既読にしてもよろしいですか？");
	if(flag){
		form01.comButton.value=string1;
		form01.submit();
	}else{
		return false;
	}
}
//-->
</SCRIPT>

