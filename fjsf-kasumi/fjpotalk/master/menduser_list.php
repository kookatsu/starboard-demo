<?php

	include "../common/fjcall_comfunc.php";
	include "../common/MYDB.php";
	include "../common/fjcall_const.php";


	//�e̫�тł̑I�������N���C�A���g���
	$selbcd = $_GET["selgbumoncd"];

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
<link rel="stylesheet" type="text/css" href="../common/fjcall_common.css">
<title></title>
</head>
<body leftMargin="1" topMargin="1">

<table width="600" border="0" cellpadding="2" cellspacing="1" bgcolor="#999999"  style="font-size:10pt;">
	<tr bgcolor=<? print $GRID_TITLE_BGCOLOR ?> >
		<td width="60"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>No.</font></td>
		<td width="240" align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>�N���C�A���g</font></td>
		<td width="60"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>�R�[�h</font></td>
		<td width="240" align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>�G���h���[�U��</font></td>
	</tr>

<?php
	$Reccnt=0;

	//DB�����
	$conn = db_connect();

	$sql = "SELECT menduser.clientcode, mclient.clientname, menduser.endusercode, menduser.endusername";
	$sql = $sql . " FROM menduser LEFT JOIN mclient ON menduser.clientcode = mclient.clientcode";
	if($selbcd !="ALL"){
		$sql = $sql . " WHERE menduser.clientcode='" . $selbcd . "'";
	}
	$sql = $sql . " ORDER BY mclient.dspno, mclient.clientcode, menduser.dspno, menduser.clientcode";
	$sql = mb_convert_encoding( $sql, "SJIS-win", "SJIS-win");
	$result = $conn->prepare($sql);
	$result->execute();
	while ($rs = $result->fetch(PDO::FETCH_ASSOC))
	{
		//No�p�����
		$Reccnt = $Reccnt + 1;

		$wTcd1 = $rs['clientcode'];
		$wTcd2 = $rs['endusercode'];
?>
		<tr bgcolor="#FFFFFF">
			<td width="60"  align="center"><?php print $Reccnt?></td>
			<td width="240" align="left"  ><?=$rs['clientname']?></td>
			<td width="60"  align="center"><a href="javascript:void(0)" target=<?=$wTcd1.$wTcd2?> Onclick="SelCd(this.target);return false"><?=$wTcd2?></td>
			<td width="240" align="left"  ><?=$rs['endusername']?></td>
		</tr>


<?php
	}
	$result = null;
	$conn = null;
?>
</table>
</body>
</html>



<SCRIPT Language="JavaScript">
<!--

/////////////////////////////////////////
// �I���������ނ̾��
/////////////////////////////////////////
function SelCd(TargetCode){

var string1 = TargetCode.substring( 0, 3 ); //����R�[�h
var string2 = TargetCode.substring( 3 );//������R�[�h

	if(window.parent.document.form01.skubun[0].checked == true){ //�V�K�̏ꍇ�̂�
		//�C����I��
	    window.parent.document.form01.skubun[1].checked = true;
	}

	window.parent.document.form01.clientcode.value = string1;
	window.parent.document.form01.ecd.value = string2;
	window.parent.document.form01.comok.click();

}

//-->
</SCRIPT>
