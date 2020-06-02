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

	<table width="680" border="0" cellpadding="2" cellspacing="1" bgcolor="#999999"  style="font-size:9pt;">
		<tr bgcolor=<? print $GRID_TITLE_BGCOLOR ?> >
			<td width="60"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>No.</font></td>
			<td width="150"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>取込日時</font></td>
			<td width="90"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>取込ユーザ</font></td>
			<td width="190" align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>データ開始日時</font></td>
			<td width="190" align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>データ終了日時</font></td>
		</tr>

<?php
		$Reccnt = 0;

		$conn = db_connect();

		$sql = "SELECT * FROM dimportlog";
		$sql = $sql . " ORDER By updatedate DESC,updatetime DESC";
EOS;

		$sql = mb_convert_encoding( $sql, "SJIS-win", "SJIS-win");
		$result = $conn->prepare($sql);
		$result->execute();
		while ($rs = $result->fetch(PDO::FETCH_ASSOC))
		{
			//No用ｶｳﾝﾀｰ
			$Reccnt = $Reccnt + 1;

			//取込日時
			$wImpDate = mb_substr($rs["updatedate"], 0, 4) . "/" . mb_substr($rs["updatedate"], 4, 2) . "/" . mb_substr($rs["updatedate"], -2);
			$wImpTime = mb_substr($rs["updatetime"], 0, 2) . ":" . mb_substr($rs["updatetime"], 2, 2) . ":" . mb_substr($rs["updatetime"], -2);

			//データ開始日時
			$wDataFromDate = mb_substr($rs["datafrom"], 0, 4) . "/" . mb_substr($rs["datafrom"], 4, 2) . "/" . mb_substr($rs["datafrom"], -2);
			$wDataFromTime = mb_substr($rs["timefrom"], 0, 2) . ":" . mb_substr($rs["timefrom"], 2, 2) . ":" . mb_substr($rs["timefrom"], -2);
			//データ終了日時
			$wDataToDate = mb_substr($rs["datato"], 0, 4) . "/" . mb_substr($rs["datato"], 4, 2) . "/" . mb_substr($rs["datato"], -2);
			$wDataToTime = mb_substr($rs["timeto"], 0, 2) . ":" . mb_substr($rs["timeto"], 2, 2) . ":" . mb_substr($rs["timeto"], -2);

?>
			<tr bgcolor="#FFFFFF">
				<td width="60"  align="center"><?php print $Reccnt?></td>
				<td width="150" align="center"><?=$wImpDate . " " . $wImpTime?></td>
				<td width="90"  align="center"><?=$rs['modifyuserid']?></td>
				<td width="190" align="center"><?=$wDataFromDate . " " . $wDataFromTime?></td>
				<td width="190" align="center"><?=$wDataToDate . " " . $wDataToTime?></td>
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
