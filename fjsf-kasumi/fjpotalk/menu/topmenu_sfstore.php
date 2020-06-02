<?php
	session_start();

	include "../common/MYDB.php";
	include "../common/MYCONST.php";
	include "../common/fjcall_comfunc.php";
	include "../common/fjcall_const.php";


	$this_pg ='topmenu_sfstore.php';
	$this_modname = "店舗別対応件数一覧";

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

	//日付指定時
	$fromY = substr( $selymd1, 0, 4 );
	$fromM = sprintf("%02d", substr( $selymd1,4, 2 ));
	$fromD = sprintf("%02d", substr(  $selymd1,6, 2 ));
	$toY = substr( $selymd2,0, 4 );
	$toM = sprintf("%02d", substr( $selymd2,4, 2 ));
	$toD = sprintf("%02d", substr(  $selymd2,6, 2 ));


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

	<table width="390" border="0" style="font-size:9pt;">
		<tr>
			<td width="260" align="left"><?= $companyname ?> 様</td>
			<td width="130" align="left">&nbsp;</td>
			<td align="left" width="300" align="left"><a href="#" onClick="MyComCSV()">CSVダウンロード</a></td>
			<td align="right" width="100"><a href="#" onClick="MyWinClose()">閉じる</a></td>
		</tr>
	</table>

	<hr width="390">



<?php
		//DBｵｰﾌﾟﾝ
		$conn = db_connect();
?>

		<!-- 外側(Header)のTable Start -->
		<table width="390"><tr><td valign="top">
			<!-- 内側のTable Start -->
			<DIV style="width:390px; overflow:auto;">	
			<!--見出し Start-->
			<table id="tblList1" width="360" border="0" cellpadding="0" cellspacing="1" bgcolor="#999999"  style="font-size:9pt;">
				<tr height="20" bgcolor=<? print $GRID_TITLE_BGCOLOR ?> >
					<td width="40"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>No</td>
					<td width="250" align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>店舗名</td>
					<td width="70"  align="center"><font color=<? print $GRID_TITLE_FTCOLOR ?>>件数</td>
				</tr>
				<!--見出し End-->
				<!-- 明細 Start -->
<?
				$wRecCnt = 0;

				//Salesforce上は、実際の時間の9時間前で記録されている為
				$wFromDate9 = $fromY . "/" . $fromM . "/" . $fromD . " 00:00:00";
				$wFromDate9 = date("Y-m-d H:i:s",strtotime($wFromDate9 . "-9 hour"));

				$wTo9 = $toY . "/" . $toM . "/" . $toD . " 00:00:00";
				$wTo9 = date("Y-m-d H:i:s",strtotime($wTo9 . "+1 day"));
				$wTo9 = date("Y-m-d H:i:s",strtotime($wTo9 . "-9 hour"));

				$sql = "SELECT " . $Const_DB_SCHEMA . "case.shopname__c, Count(" . $Const_DB_SCHEMA . "case.casenumber) AS casenumber_cnt FROM " . $Const_DB_SCHEMA . "case";
				$sql = $sql . " WHERE (" . $Const_DB_SCHEMA . "case.receiotdatetime__c>='" . $wFromDate9 . "'";
				$sql = $sql . " AND    " . $Const_DB_SCHEMA . "case.receiotdatetime__c<='" . $wTo9 . "')";
				$sql = $sql . "   AND (" . $Const_DB_SCHEMA . "case.hq_name__c='" . $Const_HQ_NAME . "')";//MYCONST
				$sql = $sql . " GROUP BY " . $Const_DB_SCHEMA . "case.shopname__c";
				$sql = $sql . " ORDER BY Count(" . $Const_DB_SCHEMA . "case.casenumber) DESC";
				if($ENV_MODE == 1){
					$sql = mb_convert_encoding( $sql, $MOJI_ORG, $MOJI_NEW ); //ここは日本語が混じっているのでUTF-8へ
				}
				$result = $conn->prepare($sql);
				$result->execute();
				while ($rs = $result->fetch(PDO::FETCH_ASSOC))
				{
					$wRecCnt = $wRecCnt + 1;


					$bkcolor_base = $GRID_MEISAI_COLOR1;


					if($ENV_MODE == 1){
						$storename = mb_convert_encoding( $rs['shopname__c'], $MOJI_NEW,$MOJI_ORG); //文字コード変換;
					}else{
						$storename = $rs['shopname__c'];
					}
?>
					<tr height="20" bgcolor="#FFFFFF" >
						<td width="40"  align="center" bgcolor=<?=$bkcolor_base?>><?= $wRecCnt ?></td>
						<td width="250" align="left"   bgcolor=<?=$bkcolor_base?>>&nbsp;<?= $storename ?></td>
						<td width="70"  align="right"   bgcolor=<?=$bkcolor_base?>>&nbsp;<?= $rs["casenumber_cnt"] ?>&nbsp;</td>
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



</center>

</form>
</body>
</html>
<script language="javascript" src="../common/fjcall_ComFunc.js"></script>


<SCRIPT Language="JavaScript">
<!--

//////////////////////////////////////////
// ﾃﾞｰﾀ入力ｳｨﾝﾄﾞｳｵｰﾌﾟﾝ
//////////////////////////////////////////
function MyListOpen(rno)
{

var string1 = rno;

//alert(rno);

	window.open('topmenu_sfmeisai_view.php?datano=' + unescape(string1) , '_blank', 'width=1150,height=600,titlebar=no,toolbar=no,scrollbars=yes, top=' + (screen.availHeight-500)/2 + ',left=' + (screen.availWidth-500)/2);

}
/////////////////////////////////////////
// CSV出力ﾎﾞﾀﾝ
/////////////////////////////////////////
function MyComCSV(){

	//日付範囲
	selymd1 = form02.selymd1.value;
	selymd2 = form02.selymd2.value;

	strUrl='topmenu_sfstore_csvout.php?selymd1='+unescape(selymd1)+'&selymd2='+unescape(selymd2)

	flag = confirm("CSVデータを出力します。よろしいですか？");
	if(flag){
		location.href=strUrl;
	}else{
		return false;
	}
}


//-->
</SCRIPT>
