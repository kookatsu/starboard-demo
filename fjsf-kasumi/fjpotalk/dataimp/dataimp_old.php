<?php

$IMPORT_DIR = "../../dataimp/";	//テキスト置き場

	define( GUI12_1 , "839" );
	define( GUI12_2 , "839*2" );

	session_start();


	include "../common/fjcall_comfunc.php";
	include "../common/MYDB.php";
	include "../common/fjcall_const.php";

	$this_pg='dataimp.php';
	$modname = "ダンプリスト取込";

	$user = $_SESSION['userid_call'];
	$name = $_SESSION['name_call'];
	$level = $_SESSION['levelid_call'];
	$face = $_SESSION["face_call"];
	$birth = $_SESSION["birth_call"];
	$maindeskcode = $_SESSION["maindeskcode_call"];

	//URL直は排除
	if($user == ""){
		$first="../fcprt.php";
		header("location: {$first}");
		exit;
	}


//	$dump_full_path = getcwd() . "\\tmp\\" . "dumplist.TXT"; // →カレントのﾌﾙﾊﾟｽ+「tmp」 Windows7版
	$dump_full_path = $IMPORT_DIR . "dumplist.TXT"; // 

	//取込ボタン
	if ($_POST["comButton"] == "comadd"){
		if ( file_exists( $dump_full_path ) ) {

			//ﾃｷｽﾄ→ﾒﾓﾘ
			$l_data = GetTxtRead( $dump_full_path );

			//ﾒﾓﾘ→ﾃﾞｰﾀﾍﾞｰｽ
			PreMakeWkData( $l_data );

			//ﾛｰｶﾙ上のﾌｧｲﾙは不要の為、削除する
			unlink( $dump_full_path );

			print "<script language=javascript>alert('取込完了しました！')</script>";

		}
	}else{

	}


	//取込対象のファイルが存在するかのチェック
	$wExitFlg = 0;
	if (file_exists($dump_full_path)) {
        //タイムスタンプ取得
        $updateDate = filemtime($dump_full_path);
        //書式整形
        $updateDate = date('Y-m-d H:i:s', $updateDate);
		$exit_mess  ="取込対象のダンプリストが存在します。"  . "[" . $updateDate . "]";
		$wExitFlg = 1;
	} else {
		$exit_mess  ="取込対象のダンプリストが存在しません。。。";
		$wExitFlg = 0;
	}


//////////////////////////////////////////
// ダンプリストをﾒﾓﾘへ
//////////////////////////////////////////
function GetTxtRead( $r_fullfilename ) {

	//配列初期化
	$memData = array();

	$contents = @file($r_fullfilename);

	//ファイル→メモリ
	foreach($contents as $line){
		//配列格納
		$memData[] = explode(',',$line);
	}

	return $memData;

}
//////////////////////////////////////////
// データベースへ更新する
// ﾒﾓﾘ→wimport1→dcall_datadump
//////////////////////////////////////////
function PreMakeWkData( $l_data ) {

	global $user;


	//DBｵｰﾌﾟﾝ
	$conn = db_connect();

	//ﾜｰｸ前削除(wimport1)
	$sql = "DELETE FROM wimport1";
	$sql = $sql." WHERE userid ='". $user."'";//自分
	$sql = mb_convert_encoding( $sql, "SJIS-win", "SJIS-win");
	$result = $conn->prepare($sql);
	$result->execute();
	$result = null;

	//ﾒﾓﾘ→ﾃﾞｰﾀﾍﾞｰｽ(wimport1)
	//1行目はﾍｯﾀﾞｰの為、取り込まない
	$memCnt = sizeof( $l_data );
	if( $memCnt > 1 ){ //2行以上ある

		//ﾚｺｰﾄﾞ数分ﾙｰﾌﾟ
		for ( $i = 1; $i < $memCnt; $i++ ) { //2行目から読み込む
			//日時の分解
			$wHiduke = substr( $l_data[$i][0], 0, 4 ) . substr( $l_data[$i][0], 5, 2 ). substr( $l_data[$i][0], 8, 2 );
			$wJikan = substr( $l_data[$i][0], 11, 2 ) . substr( $l_data[$i][0], 14, 2 );

			$sql2 = "INSERT INTO wimport1";
			$sql2 = $sql2. "(userid, recno, calldatetime,calldate,calltime,syubetsu,trunkno,talktime,idnaisen,dialno,rumblingtime)";
			$sql2 = $sql2. " VALUES(";
			$sql2 = $sql2. "'" . $user."'";
			$sql2 = $sql2. "," . $i;
			$sql2 = $sql2. ",'" . $wHiduke . $wJikan . "'";
			$sql2 = $sql2. "," . $wHiduke;
			$sql2 = $sql2. ",'" . $wJikan ."'";
			$sql2 = $sql2. ",'" . $l_data[$i][1] ."'";
			$sql2 = $sql2. "," . $l_data[$i][3];
			$sql2 = $sql2. "," . $l_data[$i][4];
			$sql2 = $sql2. ",'" . rtrim($l_data[$i][5]) ."'";
			$sql2 = $sql2. ",'" . rtrim($l_data[$i][6]) ."'";
			$sql2 = $sql2. "," . $l_data[$i][7];
			$sql2 = $sql2. ")";
			$sql2 = mb_convert_encoding( $sql2, "SJIS-win", "SJIS-win");
			$result2 = $conn->prepare($sql2);
			$result2->execute();
			$result2 = null;

		}
	}

	//取込内容をキープしておく
	$wMaxDate = 0; $wMinDate = 0; $wRecCnt = 0;
	$sql = "SELECT Max(calldatetime) AS calldatetime_MAX, Min(calldatetime) AS calldatetime_MIN, Count(recno) AS recno_CNT";
	$sql = $sql . " FROM wimport1";
	$sql = $sql . " WHERE userid='" . $user . "'";
	$sql = mb_convert_encoding( $sql, "SJIS-win", "SJIS-win");
	$result = $conn->prepare($sql);
	$result->execute();
	while ($rs = $result->fetch(PDO::FETCH_ASSOC))
	{
		$wMaxDate = $rs["calldatetime_MAX"]; //最大日時
		$wMinDate = $rs["calldatetime_MIN"]; //最小日時
		$wRecCnt = $rs["recno_CNT"]; //取込件数
	}
	$rs = null;
	$result = null;

	$wTargetFromDate = substr($wMinDate,0,8);
	$wTargetFromTime = substr($wMinDate,8,4);
	$wTargetToDate = substr($wMaxDate,0,8);
	$wTargetToTime = substr($wMaxDate,8,4);
	$wTargetCnt = $wRecCnt;

	//前削除
	$sql = "Delete From dcall_datadump";
	$sql = $sql  ." WHERE calldate>=" . $wTargetFromDate;
	$sql = $sql  ." AND   calldate<=" . $wTargetToDate;
	$sql = mb_convert_encoding( $sql, "SJIS-win", "SJIS-win");
	$result = $conn->prepare($sql);
	$result->execute();
	$result = null;


	//////////////////////////////////////
	// wimport1→dcall_datadump
	//////////////////////////////////////
	//最大RecNoの取得
	$wMaxNo = 0;
	$sql = "SELECT Max(RecNo) AS RecNo_MAX  FROM dcall_datadump";
	$sql = mb_convert_encoding( $sql, "SJIS-win", "SJIS-win");
	$result = $conn->prepare($sql);
	$result->execute();
	while ($rs = $result->fetch(PDO::FETCH_ASSOC))
	{
		$wMaxNo = $rs["RecNo_MAX"];
	}
	$rs = null;
	$result = null;


	//dcall_datadumpへ
	$sql = "SELECT wimport1.*, mtrunk.gaisenno, mtrunk.gaisenname, mtrunk.maintrunkno";
	$sql = $sql . " FROM wimport1 LEFT JOIN mtrunk ON wimport1.trunkno = mtrunk.trunkno";
	$sql = $sql . " WHERE wimport1.userid='" . $user . "'";
	$sql = $sql . " ORDER BY wimport1.recno";
	$sql = mb_convert_encoding( $sql, "SJIS-win", "SJIS-win");
	$result = $conn->prepare($sql);
	$result->execute();
	while ($rs = $result->fetch(PDO::FETCH_ASSOC))
	{
		$wMaxNo = $wMaxNo + 1;

		//時間帯
		$wTimeZone = substr( $rs["calltime"],0,2);
		$wTimeZone +=0;

		$sql2 = "INSERT INTO dcall_datadump";
		$sql2 = $sql2. "(recno, calldate,calltime,timezone,syubetsu,trunkno,telno,telname,talktime,idnaisen,dialno,rumblingtime,fusyutsuFLG,guidance1FLG,guidance2FLG,talkieFLG)";
		$sql2 = $sql2. " VALUES(";
		$sql2 = $sql2. $wMaxNo;
		$sql2 = $sql2. "," . $rs["calldate"];
		$sql2 = $sql2. ",'" . $rs["calltime"] . "'";
		$sql2 = $sql2. "," . $wTimeZone;
		$sql2 = $sql2. ",'" . $rs["syubetsu"] . "'";
		$sql2 = $sql2. "," . $rs["trunkno"];
		$sql2 = $sql2. ",'" . $rs["gaisenno"] . "'";
		$sql2 = $sql2. ",'" . $rs["gaisenname"] . "'";
		$sql2 = $sql2. "," . $rs["talktime"];
		$sql2 = $sql2. ",'" . $rs["idnaisen"] . "'";
		$sql2 = $sql2. ",'" . $rs["dialno"] . "'";
		$sql2 = $sql2. "," . $rs["rumblingtime"];
		$sql2 = $sql2. ",0";
		$sql2 = $sql2. ",0";
		$sql2 = $sql2. ",0";
		$sql2 = $sql2. "," . $rs["maintrunkno"];
		$sql2 = $sql2. ")";
		$sql2 = mb_convert_encoding( $sql2, "SJIS-win", "SJIS-win");
		$result2 = $conn->prepare($sql2);
		$result2->execute();
		$result2 = null;

	}
	$rs = null;
	$result = null;


	//通話鳴動時間がどちらも０を削除
	$sql = "DELETE FROM dcall_datadump";
//	$sql = $sql ." WHERE (talktime=0) AND (rumblingtime=0) AND (talkieFLG=0)"; //親回線のみ
	$sql = $sql ." WHERE (talktime=0) AND (rumblingtime=0)"; //全部
	$sql = $sql ." AND   (calldate>=" . $wTargetFromDate ." AND calldate<=" . $wTargetToDate . ")"; //今回取込日付のみ
	$sql = mb_convert_encoding( $sql, "SJIS-win", "SJIS-win");
	$result = $conn->prepare($sql);
	$result->execute();
	$result = null;

	//沖縄災害着信の削除(トランク3～8)
	$sql = "DELETE FROM dcall_datadump";
	$sql = $sql ." WHERE (trunkno>=3 And trunkno<=8)";
	$sql = $sql ." AND   (calldate>=" . $wTargetFromDate ." AND calldate<=" . $wTargetToDate . ")"; //今回取込日付のみ
	$sql = mb_convert_encoding( $sql, "SJIS-win", "SJIS-win");
	$result = $conn->prepare($sql);
	$result->execute();
	$result = null;

	//不出呼数Flgの更新
	$sql = "UPDATE dcall_datadump SET dcall_datadump.fusyutsuFLG = 1";
	$sql = $sql . " WHERE (talktime=0)";
	$sql = $sql . " AND   (calldate>=" . $wTargetFromDate ." AND calldate<=" . $wTargetToDate . ")"; //今回取込日付のみ
	$sql = mb_convert_encoding( $sql, "SJIS-win", "SJIS-win");
	$result = $conn->prepare($sql);
	$result->execute();
	$result = null;

	//12秒ガイダンスFlg(839)
	$sql = "UPDATE dcall_datadump SET dcall_datadump.guidance1FLG = 1";
	$sql = $sql . " WHERE (idnaisen='" . GUI12_1 . "' AND talktime>0)";
	$sql = $sql . " AND   (calldate>=" . $wTargetFromDate ." AND calldate<=" . $wTargetToDate . ")"; //今回取込日付のみ
	$sql = mb_convert_encoding( $sql, "SJIS-win", "SJIS-win");
	$result = $conn->prepare($sql);
	$result->execute();
	$result = null;

	//12秒ガイダンスFlg(839*2)
	$sql = "UPDATE dcall_datadump SET dcall_datadump.guidance2FLG = 1";
	$sql = $sql . " WHERE (idnaisen='" . GUI12_2 . "' AND talktime>0)";
	$sql = $sql . " AND   (calldate>=" . $wTargetFromDate ." AND calldate<=" . $wTargetToDate . ")"; //今回取込日付のみ
	$sql = mb_convert_encoding( $sql, "SJIS-win", "SJIS-win");
	$result = $conn->prepare($sql);
	$result->execute();
	$result = null;


	//取込ログの追加更新
	//最終更新日用のｼｽﾃﾑ日付
	$NowYMD = date("Y").date("m").date("d");
	$NowHIS = date("His");

	$sql = "INSERT INTO dimportlog";
	$sql = $sql. "(updateDate, updateTime,datafrom,timefrom,datato,timeto,importcnt,modifyuserid)";
	$sql = $sql. " VALUES(";
	$sql = $sql. "'" . $NowYMD . "'";
	$sql = $sql. ",'" . $NowHIS . "'";
	$sql = $sql. "," . $wTargetFromDate;
	$sql = $sql. ",'" . $wTargetFromTime . "'";
	$sql = $sql. "," . $wTargetToDate;
	$sql = $sql. ",'" . $wTargetToTime . "'";
	$sql = $sql. "," . $wTargetCnt;
	$sql = $sql. ",'" . $user . "'";
	$sql = $sql. ")";
	$sql = mb_convert_encoding( $sql, "SJIS-win", "SJIS-win");
	$result = $conn->prepare($sql);
	$result->execute();
	$result = null;



	//DBｸﾛｰｽﾞ
	$conn = null;

}
?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
<META NAME="ROBOTS" CONTENT="NOINDEX,NOFOLLOW">
<link rel="stylesheet" type="text/css" href="../common/fjcall_common.css">
<title><? print $modname?></title>
</head>


<body OnLoad="form01.btn.focus();">

<form name="form01" method="POST" action="<?= $this_pg ?>"  onsubmit="return false;">
<input type="hidden" name="comButton" value="">

<center>

	<!-- ﾛｸﾞｲﾝ情報エリア -->
	<div id="logininfo">
		<table border="0">
			<tr>
				<td width="80"  align="left"><img src=<?php print $GUIDE_MODNAME_TOP ?> border="0"></td>
				<td width="160" align="left"><img src=<?php print $GUIDE_MODNAME6 ?> border="0"></td>
				<td width="230" align="left"><img src=<? print $LOGO_LOGIN_S ?> border="0">
											<?php print "【 " ; print $name; print " さん】" ?>
				</td>
				<td align="right" width="630">
					<a href="#" onClick="MyGoTopMenu()"><img src=<?= $MOD_LINKPRE_01 ?> border="0" alt="アカウント設定">トップメニューに戻る</a>｜
					<a href="#" onClick="MyWondowLogout()">ログアウトする</a>
				</td>
			</tr>
		</table>
		<hr>
	</div>

<?
	//ファイルが存在しない場合はボタン無効
	if($wExitFlg == 1){
		$addEna = "";
	}else{
		$addEna = "disabled";
	}
?>

	<!-- 取込ボタンエリア -->
	<table width="910" border="0">
		<tr>
			<td width="200"><input type="button" name="btn" value="ダンプリスト取込" onClick="MyComAdd()" style="height:40px;width:160px" <?= $addEna ?>></td>
			<td width="510"><font color="red" style="font-size:8pt;"><label id="exit_label"><?= $exit_mess ?> &nbsp;</label></font></td>
			<td width="200"><font color="red" style="font-size:8pt;"><label id="wait_label"><?= $wait_mess ?> &nbsp;</label></font></td>

		</tr>
	</table>

	<!-- 取込ログエリア -->
	<table width="910" height="700"  border="0">
		<tr>
			<td><img src=<? print $LOGO_DATATABLE2 ?> border="0" alt=<? print $modname?>></td>
		</tr>
		<tr>
			<td><iframe src="dataimp_list.php" name="RowMasterList" width="705" height="650" frameborder="1"></iframe></td>
		</tr>
	</table>

	<!-- フッダーエリア -->
	<div id="bottom_menu">
		<br>
		<hr>
		<?php ShowFooter(); ?>
	</div>

</center>

</form>
</body>
</html>


<SCRIPT Language="JavaScript" type="text/javascript" src="../common/fjcall_ComFunc.js"></script>
<SCRIPT Language="JavaScript" type="text/javascript" src="dataimp.js"></script>
