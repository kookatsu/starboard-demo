<?php
//---------------------------------------------------*
// 共通定数
//---------------------------------------------------*
//--------------------------
// ログインアカウント
//--------------------------
$ACCOUNT_CD0 = 0;					//一般
$ACCOUNT_CD1 = 1;					//管理者

//--------------------------
// ログイン結果
//--------------------------
$LOGIN_DISABLED_TIMES = 10;			//ログイン不可とするログイン失敗回数(12の場合を対象)
$LOGIN_RESULT_CD_OK = 1;
$LOGIN_RESULT_NM_OK = "Login OK";
$LOGIN_RESULT_CD_11 = 11;
$LOGIN_RESULT_NM_11 = "ログインIDが認証できませんでした。";
$LOGIN_RESULT_CD_12 = 12;
$LOGIN_RESULT_NM_12 = "パスワードが正しくありません。";
$LOGIN_RESULT_CD_13 = 13;
$LOGIN_RESULT_NM_13 = "ログインIDが未入力です。";
$LOGIN_RESULT_CD_14 = 14;
$LOGIN_RESULT_NM_14 = "パスワードが未入力です。";
$LOGIN_RESULT_CD_15 = 15;
$LOGIN_RESULT_NM_15 = "所定回数パスワードが正しくなかった為、ユーザIDが無効となっています。";

//--------------------------
// 背景色
//--------------------------
$FIELD_BGCOLOR   = "#476B9A";		//入力項目の見出しの背景色
$FIELD_FTCOLOR   = "#FFFFFF";		//入力項目の見出しの文字色

$GRID_TITLE_BGCOLOR  ="#476B9A";     //背景色(濃紺)
$GRID_TITLE_BGCOLOR2 ="#F2F2F2";     //背景色(薄いグレー)
$GRID_TITLE_FTCOLOR ="#FFFFFF";     //文字色(白)

//2行おきに見やすく
$GRID_MEISAI_COLOR1 ="#FFFFFF";		//奇数行の色
$GRID_MEISAI_COLOR2 ="#E0DFDF";		//偶数行の色
$GRID_MEISAI_COLOR3 ="#F6DDFE";		//紫Ver
$GRID_MEISAI_COLOR4 ="#FFF2CC";		//紫Ver

//曜日の文字色の場合
$YOUBI_COLOR_HEI	= "#000000";    //平日
$YOUBI_COLOR_SAT	= "#003E95";    //土曜日
$YOUBI_COLOR_SUN	= "#E00B0B";    //日祝日

//合計
$SUBTOTAL_BGCOLOR = "#CCFFCC";    //小計行
$TOTAL_BGCOLOR = "#CCFF99";    //合計行

//ヒートグラフ(6色)
$HEAT_COLOR1 = "#92B1DA";
$HEAT_COLOR2 = "#B9CDE8";
$HEAT_COLOR3 = "#FFE082";
$HEAT_COLOR4 = "#FED380";
$HEAT_COLOR5 = "#FDB57A";
$HEAT_COLOR6 = "#FB8F73";

//--------------------------
// ロゴや背景画像
//--------------------------
//ログイン画面
$TOP_TITLE_LOGO   = "./img/StarBoardTitle.jpg";			//StarBoardロゴ

$LOGO_LOGIN_S       = "../img/head_login_s-01.gif";		//ログイン者の前のロゴ(各処理用)

//カレンダー選択用
$SELECT_CAL      = "../img/calbutton1.png";				//日付選択のカレンダーPopUp用

//トップメニュー
$GUIDE_MODNAME_TOP= "../img/modname_logo.gif";	    	//トップメニューの会社名logo
$TOP_BBS0_LOGO    = "../img/topmenu_ivent0.gif";		//TOP画面 右 連絡事項
$TOP_IVENT_MARK   = "../img/topmenu_ivent_mark.gif";	//TOP画面 右 掲示板内見出しﾏｰｸ
$TOP_IVENT_MARK2  = "../img/topmenu_ivent_mark2.gif";	//TOP画面 右 掲示板内見出しﾏｰｸ(新着Gifアニメ)

$MOD_LINKPRE_01   = "../img/mark02.gif";				//ヘッダーとフッダのLINK前の三角
$MOD_LINKPRE_01IVENT  = "../img/mark02_ivent.gif";		//ヘッダーとフッダのLINK前の三角(イベント用)
$TOPMENU_ADDNEW    = "../img/addnew.gif";    //新規登録
$TOP_BOOK01_LOGO  = "../img/book01small.gif";			//TOP画面(青い本)
$TOP_BOOK02_LOGO  = "../img/book02small.gif";			//TOP画面(緑の本)


//各処理内見出し用
$GUIDE_MODNAME1   = "../img/modtitle1.gif";	    		//トップメニュー
$GUIDE_MODNAME2   = "../img/modtitle2.gif";	    		//ユーザ情報
$GUIDE_MODNAME3   = "../img/modtitle3.gif";	    		//状況マスタ
$GUIDE_MODNAME4   = "../img/modtitle4.gif";	    		//POS種別マスタ
$GUIDE_MODNAME5   = "../img/modtitle5.gif";	    		//担当者マスタ
$GUIDE_MODNAME6   = "../img/modtitle6.gif";	    		//ダンプリスト取込
$GUIDE_MODNAME7   = "../img/modtitle7.gif";	    		//トランク一覧
$GUIDE_MODNAME9   = "../img/modtitle9.gif";	    		//アカウント設定


$LOGO_DATATABLE   = "../img/tabledata_title.gif";		//マスタ画面などの右側 "登録状況"
$LOGO_DATATABLE2  = "../img/tabledata_title2.gif";		//マスタ画面などの右側 "取込状況"
$FRAME_FTCOLOR   = "#476B9A";	//詳細情報のタイトル文字色

?>


