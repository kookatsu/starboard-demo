<?php
//2019-02-08 �����̕\����ǉ�

	session_start();

	include "../common/MYDB.php";
	include "../common/MYCONST.php";
	include "../common/fjcall_comfunc.php";
	include "../common/fjcall_const.php";


	$this_pg ='strunk_list.php';
	$this_modname = "�g�����N�ꗗ";

	$companycode = $_SESSION["companycode_call"];//��ЃR�[�h
	$user = $_SESSION['userid_call'];
	$name = $_SESSION['name_call'];
	$level = $_SESSION['levelid_call'];
	$maindeskcode = $_SESSION["maindeskcode_call"];


	//URL���͔r��
	if($user == ""){
		$first="../fjptlksm.php";
		header("location: {$first}");
		exit;
	}

	//�e̫�тł̑I�����
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

	<!-- ۸޲ݏ��G���A -->
	<div id="logininfo">
		<table border="0">
			<tr>
				<td width="80"  align="left"><img src=<?php print $GUIDE_MODNAME_TOP ?> border="0"></td>
				<td width="160" align="left"><img src=<?php print $GUIDE_MODNAME7 ?> border="0"></td>
				<td width="230" align="left"><img src=<? print $LOGO_LOGIN_S ?> border="0">
											<?php print "�y " ; print $name; print " ����z" ?>
				</td>
				<td align="right" width="630">
					<a href="#" onClick="MyGoTopMenu()"><img src=<?= $MOD_LINKPRE_01 ?> border="0" alt="�g�b�v���j���[�ɖ߂�">�g�b�v���j���[�ɖ߂�</a>�b
					<a href="#" onClick="MyWondowLogout()">���O�A�E�g����</a>
				</td>
			</tr>
		</table>
		<hr>
	</div>



<?php
		//DB�����
		$conn = db_connect();
?>

		<!-- �O��(Header)��Table Start -->
		<table width="1100"><tr><td valign="top">
			<!-- ������Table Start -->
			<DIV style="width:1100px; overflow:auto;">	
			<!--���o�� Start-->
			<table id="tblList1" width="1100" border="0" cellpadding="0" cellspacing="1" bgcolor="#999999"  style="font-size:9pt;">
				<tr height="20" bgcolor=<? print $GRID_TITLE_BGCOLOR ?> >
					<td width="40"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>No</td>
					<td width="70"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>�g�����N<br>�ԍ�</td>
					<td width="100" align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>�O���ԍ�</td>
					<td width="140" align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>�O������</td>
					<td width="90"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>���C��<br>�g�����N�ԍ�</td>
					<td width="100" align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>�f�X�N</td>
					<td width="160" align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>�N���C�A���g</td>
					<td width="170" align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>�G���h���[�U</td>
					<td width="230" align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>����</td>
				</tr>
				<!--���o�� End-->
				<!-- ���� Start -->
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

					//���̑�
					if($rs["deskcode"] == 9){
						$bkcolor_base = $GRID_MEISAI_COLOR2;
					}
					//QSC�f�X�N
					if($rs["deskcode"] == 0){
						$bkcolor_base = $GRID_MEISAI_COLOR4;
					}

					//���C���g�����N�ԍ�
					if($rs["maintrunkno"] != 0 ){
						$wDspMain = $rs["maintrunkno"];
					}else{
						$wDspMain = "";
					}

//					$gaisenname = mb_convert_encoding( $rs['gaisenname'], $MOJI_NEW,$MOJI_ORG); //�����R�[�h�ϊ�;
//					$deskname = mb_convert_encoding( $rs['deskname'], $MOJI_NEW,$MOJI_ORG); //�����R�[�h�ϊ�;
//					$clientname = mb_convert_encoding( $rs['clientname'], $MOJI_NEW,$MOJI_ORG); //�����R�[�h�ϊ�;
//					$endusername = mb_convert_encoding( $rs['endusername'], $MOJI_NEW,$MOJI_ORG); //�����R�[�h�ϊ�;
//					$trunkmemo = mb_convert_encoding( $rs['trunkmemo'], $MOJI_NEW,$MOJI_ORG); //�����R�[�h�ϊ�;

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
			<!-- ������Table End -->
		<!-- �O��(Header)��Table End   -->
		</td></tr></table>
<?
		//DB�۰��
		$conn = null;

?>



</center>

</form>
</body>
</html>
<SCRIPT Language="JavaScript">
<!--
/////////////////////////////////////////
// CSV�o������
/////////////////////////////////////////
function MyComCSV(){

	//�I�����
	selymd1 = form02.selymd1.value;
	selymd2 = form02.selymd2.value;
	gvalue = form02.gvalue.value;

	strUrl = 'topmenu_csvout_meisai.php?selymd1=' + unescape(selymd1) + '&selymd2=' + unescape(selymd2) + '&gvalue=' + unescape(gvalue);

	flag = confirm("CSV�f�[�^���o�͂��܂��B(���M���׈ꗗ) ��낵���ł����H");
	if(flag){
		location.href=strUrl;
	}else{
		return false;
	}
}
//-->
</SCRIPT>

<script language="javascript" src="../common/fjcall_ComFunc.js"></script>
