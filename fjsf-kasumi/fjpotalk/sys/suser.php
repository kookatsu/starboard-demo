<?php

	session_start();

	include "../common/fjcall_comfunc.php";
	include "../common/MYDB.php";
	include "../common/fjcall_const.php";

	$this_pg='suser.php';
	$modname = "アカウント設定";

	$user = $_SESSION['userid_call'];
	$name = $_SESSION['name_call'];
	$level = $_SESSION['levelid_call'];
	$face = $_SESSION["face_call"];
	$birth = $_SESSION["birth_call"];
	$maindeskcode = $_SESSION["maindeskcode_call"];

	//処理起動時のﾌﾗｸﾞ用
	$mode   = $_POST["mode"];

	//OKﾎﾞﾀﾝのﾁｪｯｸ用
	$push_ok   = $_POST["push_ok"];
	
	//ｴﾗｰﾒｯｾｰｼﾞの初期化
	$err_msg = "";

	if($mode == "dsp_after") {

		//OKボタン
		if ($_POST["comButton"] =="comok"){
			$bcd = trim($_POST["bcd"]);

			$_POST["bcd"] = $bcd;

			if($bcd == "" && $_POST["skubun"]=="add"){
				$err_msg = "ユーザIDは１文字以上を入力して下さい。";
			}

			//ﾃﾞｰﾀﾍﾞｰｽを検索
			list($reccnt, $l_bname, $l_bpass, $l_ukubun, $l_maindeskcode, $l_lastloginDay, $l_lastloginTime, $l_blsu) = SqlSearch($bcd);

			//修正＆データなし
			if($reccnt == 0 && $_POST["skubun"]=="mod"){
				$err_msg = "登録されていません。";
			}
			//削除＆データなし
			if($reccnt == 0 && $_POST["skubun"]=="del"){
				$err_msg = "登録されていません。";
	      	}
			//登録＆データあり
	    	if($reccnt <> 0 && $_POST["skubun"]=="add"){
				$err_msg = "既に登録されています。";
			}
	  		if ($err_msg == "") {
				$_POST["bname"] =$l_bname;
				$_POST["bpass"] = $l_bpass;
				$_POST["ukubun"] = $l_levelid;
				$_POST["dkubun"] = $l_maindeskcode;
				$_POST["push_ok"]="1";
				if($l_lastloginDay==""){
					$lastloginDay="";
				}else{
					$lastloginDay = substr($l_lastloginDay,0,4).'/'.substr($l_lastloginDay,4,2).'/'.substr($l_lastloginDay,6,2);
				}
				if($l_lastloginTime==""){
					$lastloginTime = "";
				}else{
					$lastloginTime = substr($l_lastloginTime,0,2).':'.substr($l_lastloginTime,2,2).':'.substr($l_lastloginTime,4,2);
				}
				$_POST["blsu"] = $l_blsu;

			}
			else{
				$_POST["bname"]="";
				$_POST["bpass"]= "";
				$_POST["ukubun"]= "0";
				$_POST["blsu"] = 0;
				$_POST["push_ok"]="0";
			}

		}
		//登録ボタン
		elseif ($_POST["comButton"] == "comadd"){
			if ($_POST["push_ok"]=="1"){

				list($ek, $Name) = ch_in_String($_POST["bname"], 1, 30);
				if (!is_null($ek)){
					$err_msg="ユーザ名 : ".$ek;
				} 
				if ($err_msg==""){

	
					if ($_POST["skubun"]=="add"){
						//追加
						SqlAddnew($_POST["bcd"], $_POST["bname"], $_POST["bpass"], $_POST["ukubun"], $_POST["dkubun"], $_POST["blsu"], $user);
						print "<script language=javascript>alert('登録しました！')</script>";
					}elseif ($_POST["skubun"]=="mod"){
						//更新
						SqlEdit($_POST["bcd"], $_POST["bname"], $_POST["bpass"], $_POST["ukubun"], $_POST["dkubun"], $_POST["blsu"], $user);
						print "<script language=javascript>alert('更新しました！')</script>";
					}elseif ($_POST["skubun"]=="del"){
						if($user == $_POST["bcd"]){
							$err_msg="ログイン中のユーザは削除できません。";
						}else{
							//削除
							SqlDelete($_POST["bcd"]);
							print "<script language=javascript>alert('削除しました！')</script>";
						}
					}else{
					}
					$_POST["push_ok"]="0";
				}

			}else{
	        	$err_msg = "ユーザIDが未入力です。";
			}

		}else{
			$_POST["bname"]="";
			$_POST["bpass"]= "";
			$_POST["ukubun"]= "0";
			$_POST["blsu"] = 0;
		}

	}

	//ｼｽﾃﾑ設定(権限情報)
	list($sysLevel0Name,$sysLevel1Name) = GetSystemLevel($sysLevel0Name,$sysLevel1Name);

	//ｵﾌﾟｼｮﾝﾎﾞﾀﾝの初期表示
	if ($_POST["skubun"]=="mod"){
		$check_mod = ("checked");
	}elseif ($_POST["skubun"]=="del"){
		$check_del = ("checked");
	}else{
		$check_add = ("checked");
	}
	//ｵﾌﾟｼｮﾝﾎﾞﾀﾝの初期表示(権限)
	if ($_POST["ukubun"]=="0"){
		$check_lev0 = ("checked");
	}elseif ($_POST["ukubun"]=="1"){
		$check_lev1 = ("checked");
	}else{
		$check_lev0 = ("checked");
	}
	//ｵﾌﾟｼｮﾝﾎﾞﾀﾝの初期表示(デスク)
	if ($_POST["dkubun"]=="0"){
		$check_dkubun0 = ("checked");
	}elseif ($_POST["dkubun"]=="1"){
		$check_dkubun1 = ("checked");
	}elseif ($_POST["dkubun"]=="999"){
		$check_dkubun999 = ("checked");
	}else{
		$check_dkubun0 = ("checked");
	}


//////////////////////////////////////////
// suserからﾃﾞｰﾀを取得
//////////////////////////////////////////
function SqlSearch( $search_key ) {

	$wk_reccnt = 0;

	$conn = db_connect();

    $sql = <<<EOS
      SELECT * FROM suser 
        WHERE
         userid = '$search_key'
EOS;
	$sql = mb_convert_encoding( $sql, "SJIS-win", "SJIS-win");
	$result = $conn->prepare($sql);
	$result->execute();
	if ($rs = $result->fetch(PDO::FETCH_ASSOC)){
		$wk_reccnt = 1;
		$l_bname = $rs['username'];
		$l_bpass = $rs['password'];
		$l_levelid = $rs['levelid'];
		$l_maindeskcode = $rs['maindeskcode'];
		$l_lastloginDay = $rs['lastloginday'];
		$l_lastloginTime = $rs['lastlogintime'];
		$l_blsu = $rs['loginFalseTimes'];
	}
	$result = null;
	$conn = null;

	return array($wk_reccnt, $l_bname, $l_bpass, $l_levelid, $l_maindeskcode, $l_lastloginDay, $l_lastloginTime, $l_blsu);

}

//////////////////////////////////////////
// suserへﾃﾞｰﾀを挿入
//////////////////////////////////////////
function SqlAddnew( $search_key, $b_name, $b_pass, $levelid, $maindeskcode, $b_blsu, $userid ) {

	//最終更新日用のｼｽﾃﾑ日付
	$NowYMD=date("Y").date("m").date("d");

	$b_blsu +=0;
	$maindeskcode +=0;

	$conn = db_connect();

    $sql = <<<EOS
	INSERT INTO suser 
		(
        userid,
        username,
        password,
        levelid,
        maindeskcode,
        loginFalseTimes,
		insertday,
		lastupdateday,
		modifyuserid
		)VALUES(
        '$search_key',
        '$b_name',
        '$b_pass',
         $levelid,
         $maindeskcode,
         $b_blsu,
         $NowYMD,
         $NowYMD,
		 '$userid'
		);

EOS;
	$sql = mb_convert_encoding( $sql, "SJIS-win", "SJIS-win");
	$result = $conn->prepare($sql);
	$result->execute();

	$result = null;
	$conn = null;
}

//////////////////////////////////////////
// userinfoへﾃﾞｰﾀを更新
//////////////////////////////////////////
function SqlEdit( $search_key, $b_name, $b_pass, $levelid, $maindeskcode, $b_blsu, $userid ) {

	//最終更新日用のｼｽﾃﾑ日付
	$NowYMD=date("Y").date("m").date("d");

	$b_blsu +=0;

	$conn = db_connect();

    $sql = <<<EOS
	UPDATE suser 
	SET
        username = '$b_name',
        password = '$b_pass',
        levelid = $levelid,
        maindeskcode = $maindeskcode,
        loginFalseTimes = $b_blsu,
        lastupdateday = $NowYMD,
		modifyuserid = '$userid'

	WHERE 
        userid = '$search_key'

EOS;

	$sql = mb_convert_encoding( $sql, "SJIS-win", "SJIS-win");
	$result = $conn->prepare($sql);
	$result->execute();

	$result = null;
	$conn = null;
}
//////////////////////////////////////////
// userinfoからﾃﾞｰﾀを削除
//////////////////////////////////////////
function SqlDelete( $search_key ) {

	$conn = db_connect();

	$sql = <<<EOS
	DELETE FROM suser WHERE userid = '$search_key'

EOS;
	$sql = mb_convert_encoding( $sql, "SJIS-win", "SJIS-win");
	$result = $conn->prepare($sql);
	$result->execute();

	$result = null;
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


<?
//ﾌｫｰｶｽ制御
if($_POST["push_ok"]=="1"){
?>
  <body OnLoad="form01.bname.focus();form01.bname.select()">
<?
}else{
?>
  <body OnLoad="form01.bcd.focus();form01.bcd.select()">
<?
}
?>

<form name="form01" method="POST" action="<?= $this_pg ?>"  onsubmit="return false;">

  <input type="hidden" name="mode" value="dsp_after" >
  <input type="hidden" name="push_ok" Value="<?=$_POST["push_ok"]?>">
  <input type="hidden" name="comButton" value="">

<center>

	<!-- ﾛｸﾞｲﾝ情報エリア -->
	<div id="logininfo">
		<table border="0">
			<tr>
				<td width="80"  align="left"><img src=<?php print $GUIDE_MODNAME_TOP ?> border="0"></td>
				<td width="160" align="left"><img src=<?php print $GUIDE_MODNAME9 ?> border="0"></td>
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

	<table width="950"height="700"  border="0">
		<tr>
			<td align="center" valign="top">
				<table width="400" border="0" cellpadding="2" style="font-size:9pt;">
					<tr>
						<td width="100" align="center" bgcolor=<?print $FIELD_BGCOLOR;?>><font color=<? print $FIELD_FTCOLOR ?>>処理区分</font></td>
						<td width="300">
							<input type="radio" name="skubun" value="add" <?=$check_add?> onClick="MyModClick('sk0')" >新規　
							<input type="radio" name="skubun" value="mod" <?=$check_mod?> onClick="MyModClick('sk1')" >修正　
							<input type="radio" name="skubun" value="del" <?=$check_del?> onClick="MyModClick('sk2')" >削除
						</td>
					</tr>

					<tr>
						<td width="100" align="center" bgcolor=<?print $FIELD_BGCOLOR;?>><font color=<? print $FIELD_FTCOLOR ?>>ユーザID</font></td>
						<td width="300">
							<input name="bcd" type="text" maxlength="16" Value="<?=$_POST["bcd"]?>" onFocus="MyCodeClick()" onkeydown=EnterToTab(event) style="width:120;ime-mode:disabled">
							<input name="comok" type="button" value=" OK " onClick="MyComOK()">
						</td>
					</tr>

					<tr>
						<td align="center">&nbsp;</td>
						<td width="300"><font color="#ff3333\"><?= $err_msg ?></font></td>
					</tr>

					<tr>
						<td width="100" align="center" bgcolor=<?print $FIELD_BGCOLOR;?>><font color=<? print $FIELD_FTCOLOR ?>>ユーザ名</font></td>
						<td width="300"><input name="bname" type="text" maxlength="30" Value="<?=$_POST["bname"]?>" onkeydown=EnterToTab(event) style="width:150;ime-mode:active"></td>
					</tr>

					<tr>
						<td width="100" align="center" bgcolor=<?print $FIELD_BGCOLOR;?>><font color=<? print $FIELD_FTCOLOR ?>>パスワード</font></td>
						<td width="300"><input name="bpass" type="text" maxlength="16" Value="<?=$_POST["bpass"]?>" onkeydown=EnterToTab(event) style="width:150;ime-mode:disabled"></td>
					</tr>

					<tr>
						<td width="100" align="center" bgcolor=<?print $FIELD_BGCOLOR;?>><font color=<? print $FIELD_FTCOLOR ?>>アクセス権限</font></td>
						<td width="300">
							<input type="radio" name="ukubun" value="0" <?=$check_lev0?> onkeydown=EnterToTab(event)><?print $sysLevel0Name ?> 
							<input type="radio" name="ukubun" value="1" <?=$check_lev1?> onkeydown=EnterToTab(event)><?print $sysLevel1Name ?>
						</td>
					</tr>

					<tr>
						<td width="100" align="center" bgcolor=<?print $FIELD_BGCOLOR;?>><font color=<? print $FIELD_FTCOLOR ?>>デスク</font></td>
						<td width="300">
							<input type="radio" name="dkubun" value="0" <?=$check_dkubun0?> onkeydown=EnterToTab(event)>QSC 
							<input type="radio" name="dkubun" value="1" <?=$check_dkubun1?> onkeydown=EnterToTab(event)>FJ
							<input type="radio" name="dkubun" value="999" <?=$check_dkubun999?> onkeydown=EnterToTab(event)>なし
						</td>
					</tr>


					<tr>
						<td align="center">&nbsp;</td>
						<td width="300"></td>
					</tr>

					<tr>
						<td width="100" align="center" bgcolor=<?print $FIELD_BGCOLOR;?>><font color=<? print $FIELD_FTCOLOR ?>>現在の<br>連続ログイン<br>失敗回数</font></td>
						<td width="300"><input name="blsu" type="text" maxlength="2" Value="<?=$_POST["blsu"]?>" onkeydown=EnterToTab(event) style="width:30;ime-mode:disabled;text-align:right"> 回</td>
					</tr>

					<tr>
						<td align="center">&nbsp;</td>
						<td width="300"></td>
					</tr>

					<tr>
						<td width="100" align="center" bgcolor="#CCCCCC">最終ログイン</td>
						<td width="300"> <?= $lastloginDay ?> <?= $lastloginTime ?>
					</tr>

				</table>

				<br>

				<table width="400" border="0" cellpadding="2">
					<tr align="left">
<?php
if($_POST["skubun"]=="add"){
	$btncap = "登録";
}elseif($_POST["skubun"]=="del"){
	$btncap = "削除";
}else{
	$btncap = "更新";
}
?>

<?php
if ($_POST["push_ok"] =="1"){
?>
						<td><input type="button" name="btn" value=<?= $btncap ?> onClick="MyComAdd()" style="width:150"></td>
<?php
}else{
?>
						<td><input type="button" name="btn" value=<?= $btncap ?>  style="width:150" disabled></td>
<?php
}
?>
						<td width="250" align="center">&nbsp;</td>

					</tr>
				</table>
			</td>

			<!-- 登録状況 -->
			<td width="550" valign="top"><img src=<? print $LOGO_DATATABLE ?> border="0" alt=<? print $modname?>>
				<iframe src="suser_list.php" name="RowMasterList" width="545" height="650" frameborder="1"></iframe>
			</td>

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


<script language="javascript" src="../common/fjcall_ComFunc.js"></script>

<SCRIPT Language="JavaScript" type="text/javascript" src="suser.js">

<!--
//-->
</SCRIPT>

