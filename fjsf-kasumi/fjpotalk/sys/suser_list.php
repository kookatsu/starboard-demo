<?php
	include "../common/fjcall_const.php";
	include "../common/MYDB.php";
	include "../common/fjcall_comfunc.php";

	//���ѐݒ�(�������)
	list($sysLevel0Name,$sysLevel1Name) = GetSystemLevel($sysLevel0Name,$sysLevel1Name);

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
<link rel="stylesheet" type="text/css" href="../common/fjcall_common.css">
<title>�A�J�E���g���X�g</title>
</head>
<body leftMargin="1" topMargin="1">
<table width="520" border="0" cellpadding="2" cellspacing="1" bgcolor="#999999"  style="font-size:9pt;">
  <tr bgcolor=<? print $GRID_TITLE_BGCOLOR ?> >
    <td width="40"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>No.</font></td>
    <td width="120" align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>���[�UID</font></td>
    <td width="140" align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>���[�U��</font></td>
    <td width="140" align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>�p�X���[�h</font></td>
    <td width="80"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>�A�N�Z�X����</font></td>
  </tr>



<?php
	$Reccnt=0;

	$conn = db_connect();

    $sql = "SELECT * FROM suser";
	$sql = $sql . " ORDER By levelid DESC,userid";
EOS;
	$sql = mb_convert_encoding( $sql, "SJIS-win", "SJIS-win");
	$result = $conn->prepare($sql);
	$result->execute();
	while ($rs = $result->fetch(PDO::FETCH_ASSOC))
	{
			//No�p�����
			$Reccnt = $Reccnt + 1;

			$wTcd=$rs['userid'];

			//���x���̕\���p
			if ($rs['levelid']=="1"){
				$dflg = $sysLevel1Name;
			}else{
				$dflg = $sysLevel0Name;
			}

?>
  		<tr bgcolor="#FFFFFF">
    		<td width="40"  align="center"><?php print $Reccnt?></td>
    		<td width="120" align="center"><a href="javascript:void(0)" target=<?=$wTcd?> Onclick="SelCd(this.target);return false"><?=$wTcd?></td>
    		<td width="140" align="left"><?=$rs['username']?></td>
    		<td width="140" align="left"><?=$rs['password']?></td>
    		<td width="80"  align="center"><?=$dflg?></td>
  		</tr>
<?php
		}
?>
</table>

<?php
	$result = null;
	$conn = null;
?>

</body>
</html>



<SCRIPT Language="JavaScript">
<!--

/////////////////////////////////////////
// �I���������ނ̾��
/////////////////////////////////////////
function SelCd(TargetCode){

var string2 = TargetCode;

	if(window.parent.document.form01.skubun[0].checked == true){ //�V�K�̏ꍇ�̂�
		//�C����I��
	    window.parent.document.form01.skubun[1].checked = true;
	}

    window.parent.document.form01.bcd.value=string2;
    window.parent.document.form01.comok.click();

}



//-->
</SCRIPT>


