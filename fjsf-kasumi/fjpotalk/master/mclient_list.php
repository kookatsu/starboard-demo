<?php

	include "../common/fjcall_comfunc.php";
	include "../common/fjcall_const.php";
	include "../common/MYDB.php";

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
<link rel="stylesheet" type="text/css" href="../common/fjcall_common.css">
<title></title>
</head>
<body leftMargin="1" topMargin="1">

	<table width="480" border="0" cellpadding="2" cellspacing="1" bgcolor="#999999"  style="font-size:9pt;">
		<tr bgcolor=<? print $GRID_TITLE_BGCOLOR ?> >
			<td width="40"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>No.</font></td>
			<td width="70"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>コード</font></td>
			<td width="300" align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>お名前</font></td>
			<td width="70"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>表示順</font></td>
		</tr>

<?php
		$Reccnt = 0;

		$conn = db_connect();

		$sql = "SELECT * FROM mclient";
		$sql = $sql . " ORDER By dspno,clientcode";
EOS;
		$sql = mb_convert_encoding( $sql, "SJIS-win", "SJIS-win");
		$result = $conn->prepare($sql);
		$result->execute();
		while ($rs = $result->fetch(PDO::FETCH_ASSOC))
		{
			//No用ｶｳﾝﾀｰ
			$Reccnt = $Reccnt + 1;

			$wTcd=$rs['clientcode'];

?>
			<tr bgcolor="#FFFFFF">
				<td width="40"  align="center"><?php print $Reccnt?></td>
				<td width="70"  align="center"><a href="javascript:void(0)" target=<?=$wTcd?> Onclick="SelCd(this.target);return false"><?=$wTcd?></td>
				<td width="300" align="left"><?=$rs['clientname']?></td>
				<td width="70"  align="center"><?=$rs['dspno']?></td>
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
// 選択したｺｰﾄﾞのｾｯﾄ
/////////////////////////////////////////
function SelCd(TargetCode){

var string2 = TargetCode;

	if(window.parent.document.form01.skubun[0].checked == true){ //新規の場合のみ
		//修正を選択
	    window.parent.document.form01.skubun[1].checked = true;
	}

    window.parent.document.form01.bcd.value=string2;
    window.parent.document.form01.comok.click();

}
//-->
</SCRIPT>


