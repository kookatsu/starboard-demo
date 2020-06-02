<?php
	session_start();

	include "./common/fjcall_comfunc.php";
	include "./common/fjcall_const.php";
    include "./common/MYDB.php";
    include "./common/MYCONST.php";

	$this_pg = $Const_LOGIN_PHP; //カスミ専用
	$mode   = $_POST["mode"];


	//IPｱﾄﾞﾚｽ
//	$aIPaddress = $_SERVER["REMOTE_ADDR"];
	$aIPaddress = getIpAddress();

	//ﾎｽﾄ名
	$aHOSTname = "";//gethostbyaddr($aIPaddress);
	//ﾌﾞﾗｳｻﾞ(MSIE.FireFox.iPad...)
	$agent = getenv("HTTP_USER_AGENT");


	$loginbtn_tooltip = "パスワードを続けて".$LOGIN_DISABLED_TIMES."回間違えますと、ユーザIDは無効になります。";


	if($mode == "log_in") {
		//結果ｴﾘｱ初期化
		$err_msg = "";

		if($_POST["u_id"]) {
			$u_id   = $_POST["u_id"];
       		$u_pass   = $_POST["u_pass"];

			if( strpos($u_id,'\'') !== false ){//'が含まれている場合
				$reccnt = 0;
			}elseif( strpos($u_id,' ') !== false ){//' 'が含まれている場合
				$reccnt = 0;
			}else{
				//ﾃﾞｰﾀﾍﾞｰｽを検索
				list($reccnt,$l_name, $l_pass,$l_levellid,$l_blsu, $l_fujimotoflg) = SqlSearch($u_id);
			}

			//現状のログイン失敗回数
			$login_false_times = $l_blsu;

			if( $login_false_times >=  $LOGIN_DISABLED_TIMES ) {
				//所定パスワード入力エラーの為、ログイン不可
        		$err_msg = "<font color=\"#ff3333\">$LOGIN_RESULT_NM_15($LOGIN_DISABLED_TIMES 回)</font>\n";
				AddLoginlog( $aIPaddress , $aHOSTname , $u_id , $u_pass , $LOGIN_RESULT_CD_15 ,$agent);//ログインログへ追加
			}else{
	    		if($reccnt == 0 ) {
					//ユーザID認証エラー
	        		$err_msg = "<font color=\"#ff3333\">$LOGIN_RESULT_NM_11</font>\n";
					AddLoginlog( $aIPaddress , $aHOSTname , $u_id , $u_pass , $LOGIN_RESULT_CD_11 ,$agent);//ログインログへ追加
	      		}else{
	          		if($_POST["u_pass"]) {
	            		if($u_pass != $l_pass) {
							//パスワードが異なる
	              			$err_msg = "<font color=\"#ff3333\">$LOGIN_RESULT_NM_12</font>\n";
							AddLoginlog( $aIPaddress , $aHOSTname , $u_id , $u_pass , $LOGIN_RESULT_CD_12 ,$agent);//ログインログへ追加
							SqlEdit($u_id , $aIPaddress , 1 ); //ログイン情報をsusertableへ更新(ログイン失敗回数＋)
	            		}
	          		}else{
						//パスワード未入力
	            		$err_msg = "<font color=\"#ff3333\">$LOGIN_RESULT_NM_14</font>\n";
						AddLoginlog( $aIPaddress , $aHOSTname , $u_id , $u_pass , $LOGIN_RESULT_CD_14,$agent );//ログインログへ追加
	      			}
				}
			}
  		}else{
			//ユーザID未入力(JavaScriptでガードしているので来ないでしょう)
			$err_msg = "<font color=\"#ff3333\">$LOGIN_RESULT_NM_13</font>\n";
			AddLoginlog( $aIPaddress , $aHOSTname , $u_id , $u_pass , $LOGIN_RESULT_CD_13,$agent );
  		}
  		if ($err_msg == "") {
			//OK
			SqlEdit($u_id , $aIPaddress , 0 ); //ログイン情報をsusertableへ更新(ログイン成功)
			AddLoginlog( $aIPaddress , $aHOSTname , $u_id , $u_pass , $LOGIN_RESULT_CD_OK ,$agent); //ログインログへ追加

			list($companyname, $selClient, $selDesk, $selEndUser) = GetCompanyInfo1();

			//ｾｯｼｮﾝ変数にｾｯﾄ
			$_SESSION["companycode_call"] = $Const_COMPANYCODE;//会社コード
			$_SESSION["companyname_call"] = $companyname;//会社名
			$_SESSION["selclient_call"] = $selClient;//クライアントコード
			$_SESSION["seldesk_call"] = $selDesk;//デスクコード
			$_SESSION["selenduser_call"] = $selEndUser;//エンドユーザーコード

			$_SESSION["userid_call"] = $u_id;
			$_SESSION["name_call"] = $l_name;
			$_SESSION["levelid_call"] = $l_levellid;//権限
			$_SESSION["fujimotoflg_call"] = $l_fujimotoflg;

			$first="./menu/topmenu.php";
//通達経由
//			$first="./message/message_dsp.php";
			header("location: {$first}");
			exit;

    	}
  	}

//////////////////////////////////////////
function getIpAddress() {
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ipAddresses = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim(end($ipAddresses));
    }
    else {
        return $_SERVER['REMOTE_ADDR'];
    }
}
//////////////////////////////////////////
// UserInfoからﾃﾞｰﾀを取得
// <引数>
//   $a_id;ユーザID
//////////////////////////////////////////
function SqlSearch( $a_id ) {

global $ENV_MODE;
global $MOJI_ORG;
global $MOJI_NEW;
global $Const_COMPANYCODE;
global $Const_DB_SCHEMA;

	$wk_reccnt = 0;

	$conn = db_connect();

	$sql = "SELECT * FROM " . $Const_DB_SCHEMA . "suser";
	$sql = $sql . " WHERE companycode ='" . $Const_COMPANYCODE . "'";
	$sql = $sql . " AND   userid ='" . $a_id . "'";
	$result = $conn->prepare($sql);
	$result->execute();
	if ($rs = $result->fetch(PDO::FETCH_ASSOC)){
		$wk_reccnt = 1;
		$l_pass = $rs['password'];
		if( $ENV_MODE == 1){
			$l_name = mb_convert_encoding( $rs['username'], $MOJI_NEW,$MOJI_ORG); //文字コード変換;
		}else{
			$l_name = $rs['username'];
		}
		$l_levelid = $rs['levelid'];
		$l_blsu = $rs['loginFalseTimes'];
		$l_fujimotoflg =  $rs['fujimotoflg'];
	}
	$result = null;
	$conn = null;

	return array($wk_reccnt,$l_name,$l_pass,$l_levelid,$l_blsu,$l_fujimotoflg);
}
//////////////////////////////////////////
// susertableにログイン日時を更新
// <引数>
//   $a_id;ユーザID
//   $a_ip;アクセスIPアドレス
//   $editmode;0:ログイン成功 1:パスワード入力エラー
//////////////////////////////////////////
function SqlEdit( $a_id,$a_ip,$editmode ) {

global $Const_COMPANYCODE;
global $Const_DB_SCHEMA;

	$NowYMD=date("Y").date("m").date("d");
	$NowHNS=date("H").date("i").date("s");

	$conn = db_connect();


//パスワード入力エラー
if ($editmode == 1 ){
    $sql = <<<EOS
      UPDATE suser
		SET
          lastloginday       = '$NowYMD',
          lastlogintime      = '$NowHNS',
          lastloginIPaddress = '$a_ip',
          loginFalseTimes = loginFalseTimes + 1
        WHERE 
          userid = '$a_id'
EOS;

//ログイン成功
}else{
    $sql = "UPDATE " . $Const_DB_SCHEMA . "suser";
	$sql = $sql . " SET ";
	$sql = $sql . " lastloginday=" . "'" . $NowYMD . "'";
	$sql = $sql . ",lastlogintime=" . "'" . $NowHNS . "'";
	$sql = $sql . ",lastloginipaddress=" . "'" . $a_ip . "'";
	$sql = $sql . ",loginfalsetimes=0";
	$sql = $sql . " WHERE userid =" .  "'" . $a_id . "'";
}
	$result = $conn->prepare($sql);
	$result->execute();

	$result = null;
	$conn = null;
}
//////////////////////////////////////////
// lloginlogにログイン情報を追加
// <引数>
//   $a_ip    :アクセスIPアドレス
//   $a_host  :アクセスホスト名
//   $a_id    :ユーザID
//   $a_pwd   :パスワード
//   $a_result:結果
//////////////////////////////////////////
function AddLoginlog( $a_ip , $a_host , $a_id , $a_pwd , $a_result ,$a_agent) {

global $Const_COMPANYCODE;
global $Const_DB_SCHEMA;


	$NowYMD=date("Y").date("m").date("d");
	$NowHNS=date("H").date("i").date("s");

	if(ini_get('magic_quotes_gpc') == "1") {
		$wa_agent = stripslashes($a_agent);
	}else{
		$wa_agent = $a_agent;
	}

	$conn = db_connect();


$sql = <<<EOS
	INSERT INTO $Const_DB_SCHEMA dloginlog 
		(
        accessdate,
        accesstime,
        accessipaddress,
        accesshostname,
        accessuserid,
        accesspassword,
        accessresult,
        accessagent,
		companycode
		)VALUES(
        '$NowYMD',
        '$NowHNS',
        '$a_ip',
        '$a_host',
        '$a_id',
        '$a_pwd',
         $a_result,
        '$wa_agent',
        '$Const_COMPANYCODE'
		);
EOS;
	$result = $conn->prepare($sql);
	$result->execute();

    $result = null;
    $conn = null;
}

?>



<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?= $Const_HTML_CHARSET ?>">
<META NAME="ROBOTS" CONTENT="NOINDEX,NOFOLLOW">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<link rel="stylesheet" type="text/css" href="./common/fjcall_common.css">
<link rel="icon" href="./img/sb.ico" type="image/vnd.microsoft.icon" />
<link rel="shortcut icon" href="./img/sb.ico" type="image/vnd.microsoft.icon" />
<title>StarBoard　ログイン</title>
</head>


<!--入力フォームの背景色定義と各inputフォームのスタイル定義 -->
<style type="text/css">
<!--
.bkcolor_off {BACKGROUND:#FFFFFF;}
.bkcolor_on  {BACKGROUND:#F1C3DA;}
#u_id {height:20;border-width:2px;border-style:inset;ime-mode:disabled;}
#u_pass {height:20;border-width:2px;border-style:inset;ime-mode:disabled;}
-->
</style>


<!-- フォーカス制御 -->
<body bgcolor="#ffffff" OnLoad="form01.u_id.focus()">
<form id="form01" name="form01" method="POST" action="<?= $this_pg ?>">
	<input type="hidden" name="mode" value="log_in" >

	<center>

	<br>
	<br>


	<!-- ロゴエリア -->
	<table width="640" border="0">
		<tr>
			<td align="left"><img src=<? print $TOP_TITLE_LOGO ?> border="0" alt="StarBoard"></td>
		</tr>
	</table>

<br>


	<!-- 入力エリア -->
	<div id="login_input">
	<table width="340" border="0">
		<tr>
			<td>

				<table width="340" border="0" cellpadding="4" >
					<tr valign="middle">
						<td align="left">StarBoard　ログイン<hr></td>
					</tr>

					<tr valign="middle">
						<td  align="center" valign="middle">
							<table width="340" border="0" cellpadding="4">

								<tr valign="middle">
									<td width="120" align="left" bgcolor="#E6E6E6">ログインID</td>
									<td width="220" bgcolor="#E6E6E6"><input name="u_id" type="text" id="u_id" maxlength="100" style="width:200;" onkeydown=u_id_down(event) onfocus=u_id_fcs() onblur=u_id_fcsout() Value="<?=$_POST["u_id"]?>"></td>
								</tr>

								<tr valign="middle">
									<td width="120" align="left" bgcolor="#E6E6E6">パスワード</td>
									<td width="220" bgcolor="#E6E6E6"><input name="u_pass" type="password" id="u_pass" maxlength="16" style="width:200;" onkeydown=u_pass_down(event) onfocus=u_pass_fcs() onblur=u_pass_fcsout()></td>
								</tr>

								<tr align="center">
									<td colspan="2">
										<input type="button" name="Submit" id="btn1" onClick="GoLogin()" value="ログイン" style="height:30;width:150"  >
									</td>
								</tr>

							</table>
						</td>
					</tr>
				</table>

			</td>
		</tr>
	</table>
	</div>


	<!-- メッセージエリア -->
	<table width="340" height="80" border="0">
		<tr>
			<td  align="center" valign="middle"><?= $err_msg ?></td>
		</tr>
	</table>






	<!-- フッダーエリア -->
	<div id="bottom_menu">
		<hr>
		<?php ShowFooter(); ?>
	</div>

	</center>

</form>
</body>
</html>

<script type="text/javascript" src="fjptlksm.js" charset="Shift_JIS"></script>


