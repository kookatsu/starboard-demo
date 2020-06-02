<?php
//2019-05-09 10分に１回OKボタンを自動でクリック
//2019-05-09 起動時の日付範囲を今日～今日へ変更
//2019-05-13 起動時の日付範囲　9時間足す


	//新着マーク日数
	$NEW_LIMIT =7;



	session_start();

	include "../common/MYDB.php";
	include "../common/MYCONST.php";
	include "../common/fjcall_comfunc.php";
	include "../common/fjcall_const.php";
	include "./topmenu_callcal.php";
	include "./topmenu_callcal_sf.php";

	$this_pg = 'topmenu.php';
	$modname = "デスク別集計";


	//Session変数取得
	$companycode = $_SESSION["companycode_call"];//会社コード
	$companyname = $_SESSION["companyname_call"];
	$selClient = $_SESSION["selclient_call"];
	$selDesk = $_SESSION["seldesk_call"];
	$selEndUser = $_SESSION["selenduser_call"];
	$user = $_SESSION['userid_call'];
	$name = $_SESSION['name_call'];
	$level = $_SESSION['levelid_call'];
	$fujimotoflg = $_SESSION["fujimotoflg_call"];
	//Session変数取得


	//URL直は排除
	if( $user == "" ){
		$first="../" . $Const_LOGIN_PHP; //Login
		header("location: {$first}");
		exit;
	}

	//処理起動時のﾌﾗｸﾞ用
	$mode = $_POST["mode"];


	//OKﾎﾞﾀﾝのﾁｪｯｸ用
	$push_ok = $_POST["push_ok"];

	//グラフの初期表示は日別
	$graphFlg = "g31";





	//起動時の表示後
	if( $mode == "dsp_after" ) {

		if( $_POST["comButtonG"] =="g31"){//日別
			$graphFlg = "g31";
		}
		if( $_POST["comButtonG"] =="g32" ){//時間帯別
			$graphFlg = "g32";
		}
		if( $_POST["comButtonG"] =="g33" ){//月別
			$graphFlg = "g33";
		}


		$seldate1 =  $_POST["colname1"];
		$seldate2 =  $_POST["colname2"];
		$selymd1 = str_replace("/", "", $seldate1);//ｽﾗｯｼｭを外す
		$selymd2 = str_replace("/", "", $seldate2);//ｽﾗｯｼｭを外す

		$talksu = $_POST["talksu"];
		$guidance = $_POST["guidance"];
		$basestorecnt = $_POST["basestorecnt"];
		$notstorecnt = $_POST["notstorecnt"];
		$reportname = $_POST["reportname"];
		$reportnamelastupdate = $_POST["reportnamelastupdate"];
		$reportfilename = $_POST["reportfilename"];

		//データ抽出ボタン
		if ( $_POST["comButton"] =="ok" ){

			$_POST["push_ok"] = "1";

			//日付指定時
			$fromY = substr( $selymd1, 0, 4 );
			$fromM = sprintf("%02d", substr( $selymd1,4, 2 ));
			$fromD = sprintf("%02d", substr(  $selymd1,6, 2 ));
			$toY = substr( $selymd2,0, 4 );
			$toM = sprintf("%02d", substr( $selymd2,4, 2 ));
			$toD = sprintf("%02d", substr(  $selymd2,6, 2 ));
			//(グラフ用開始日)---31日前
			$ad = date("Y-m-d", strtotime( -31 . "day", strtotime($fromY ." -" . $fromM . "-" . $fromD)) );
			$fromY2 = substr( $ad, 0, 4 );
			$fromM2 = sprintf("%02d", substr( $ad,5, 2 ));
			$fromD2 = sprintf("%02d", substr(  $ad,8, 2 ));


			//日数チェック
//			$wday1 = $fromY . "-" . $fromM . "-" . $fromD;
//			$wday2 = $toY . "-" . $toM . "-" . $toD;
//			$date1 =  StrToUnixTime( $wday1 );
//			$date2 =  StrToUnixTime( $wday2 );
//			$wDiff = DateDiff( $date2, $date1 );
//Herokuでは StrToUnixTimeは機能しない
			$wday1 = strtotime($fromY . "-" . $fromM . "-" . $fromD);
			$wday2 = strtotime($toY . "-" . $toM . "-" . $toD);
			$seconddiff = abs($wday2 - $wday1);
			$wDiff = $seconddiff / (60 * 60 * 24);


			$wDiff = $wDiff + 1;
			if($wDiff>31){
				echo "<script type='text/javascript'>";
				echo "alert('日付の最大期間は31日までです！');";
				echo "</script>";
				$_POST["push_ok"] = "0";
			}

			//DBｵｰﾌﾟﾝ
			$conn = db_connect();

			//入力用(wdeskhistory1)にﾃﾞｰﾀ作成
			$recflg = PreMakeWkData( $conn, $selDesk, $selClient, $selEndUser );
			$recflg = PreMakeWkDataSF( $conn, $selDesk,  $selClient, $selEndUser);
			$recflg = PreMakeWkDataSF6Month($conn, $selDesk,  $selClient, $selEndUser);//過去6ヶ月
			//過去全ての未完了件数を取得
			$mikanryosu = Mikanryo( $conn );

			//DBｸﾛｰｽﾞ
			$conn = null;

		}
	}else{
		//起動時 ->自分のデスクを初期表示したい
		$_POST["push_ok"] = "1";

		//終了日(今日)
		$seldate2 = date("Y/m/d", strtotime("+9 hour")); //2019-05-13 システム日付が9時間時差がある
//		$seldate2 = date("Y/m/d", strtotime("-0 days")); //今日
		$_POST["colname2"] = $seldate2;
		$selymd2 = str_replace("/", "", $seldate2);//ｽﾗｯｼｭを外す
		$toY = substr( $selymd2,0, 4 );
		$toM = sprintf("%02d", substr( $selymd2,4, 2 ));
		$toD = sprintf("%02d", substr( $selymd2,6, 2 ));

		//開始日(期初日)
		$in_year = substr( $selymd2, 0, 4 );
		$in_month = substr( $selymd2, 4, 2 );
		$in_day = substr( $selymd2, 6, 2 );

//2019-05-09 MOD
//		list( $fromY, $fromM, $fromD ) = CalcDateStartDay($in_year, $in_month,$in_day , 0 );
		$fromY = $toY;
		$fromM = $toM;
		$fromD = $toD;

		$seldate1 = $fromY. "/" . $fromM. "/" . $fromD;
		$_POST["colname1"] = $seldate1;
		$selymd1 = $fromY. $fromM. $fromD;

		//(グラフ用開始日)---31日前
		$ad = date("Y-m-d", strtotime( -31 . "day", strtotime($fromY ." -" . $fromM . "-" . $fromD)) );
		$fromY2 = substr( $ad, 0, 4 );
		$fromM2 = sprintf("%02d", substr( $ad,5, 2 ));
		$fromD2 = sprintf("%02d", substr(  $ad,8, 2 ));


		//DBｵｰﾌﾟﾝ
		$conn = db_connect();

		//クライアントとデスクとエンドユーザ
		list(  $talksu, $guidance, $basestorecnt, $notstorecnt, $reportname, $reportnamelastupdate, $reportfilename ) = GetKaisenInfo_OPEN( $conn );

		//初期データ作成
		$recflg = PreMakeWkData( $conn, $selDesk,  $selClient, $selEndUser);
		$recflg = PreMakeWkDataSF( $conn, $selDesk,  $selClient, $selEndUser);
		//過去全ての未完了件数を取得
		$mikanryosu = Mikanryo( $conn );

		//DBｸﾛｰｽﾞ
		$conn = null;

	}


?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?= $Const_HTML_CHARSET ?>">
<META NAME="ROBOTS" CONTENT="NOINDEX,NOFOLLOW">
<link rel="icon" href="../img/sb.ico" />
<link rel="shortcut icon" href="../img/sb.ico"  />
<link rel="stylesheet" type="text/css" href="../common/fjcall_common.css">
<title>FJコール受付集計 <? print $modname?></title>

<script type="text/javascript" language="javascript">
<!--
	//600秒(10分)に1回ﾘﾌﾚｯｼｭ
	setTimeout("MyComOK()",600000);

// -->
</script>

</head>

<BODY>

<!--左サイドバー開始-->
<div class="mybox_sidebar">
<div class="mybox_title">▼東京の明日明後日の天気</div>
    <div id="mamewaza_weather" class="mamewaza_weather"></div>
  
  <div class="mybox_title">▼今日の株価</div>
  <div class="mybox" style=" height:400px;"><!--<img src="../img/mybox/kabu.jpg" width="300" height="200">-->
  <!-- TradingView Widget BEGIN -->
<div class="tradingview-widget-container">
  <div class="tradingview-widget-container__widget"></div>
  <div class="tradingview-widget-copyright">TradingView提供の<a href="https://jp.tradingview.com" rel="noopener" target="_blank"><span class="blue-text">マーケットデータ</span></a></div>
  <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-market-overview.js" async>
  {
  "showChart": true,
  "locale": "ja",
  "largeChartUrl": "",
  "isTransparent": false,
  "width": "300",
  "height": "400",
  "plotLineColorGrowing": "rgba(33, 150, 243, 1)",
  "plotLineColorFalling": "rgba(33, 150, 243, 1)",
  "gridLineColor": "rgba(233, 233, 234, 1)",
  "scaleFontColor": "rgba(131, 136, 141, 1)",
  "belowLineFillColorGrowing": "rgba(5, 122, 205, 0.12)",
  "belowLineFillColorFalling": "rgba(5, 122, 205, 0.12)",
  "symbolActiveColor": "rgba(225, 239, 249, 1)",
  "tabs": [
    {
      "title": "指数",
      "symbols": [
        {
          "s": "OANDA:SPX500USD",
          "d": "S&P 500"
        },
        {
          "s": "INDEX:XLY0",
          "d": "Shanghai Composite"
        },
        {
          "s": "FOREXCOM:DJI",
          "d": "Dow 30"
        },
        {
          "s": "INDEX:NKY",
          "d": "Nikkei 225"
        },
        {
          "s": "INDEX:DAX",
          "d": "DAX Index"
        },
        {
          "s": "OANDA:UK100GBP",
          "d": "FTSE 100"
        }
      ],
      "originalTitle": "Indices"
    },
    {
      "title": "商品先物",
      "symbols": [
        {
          "s": "CME_MINI:ES1!",
          "d": "E-Mini S&P"
        },
        {
          "s": "CME:E61!",
          "d": "Euro"
        },
        {
          "s": "COMEX:GC1!",
          "d": "Gold"
        },
        {
          "s": "NYMEX:CL1!",
          "d": "Crude Oil"
        },
        {
          "s": "NYMEX:NG1!",
          "d": "Natural Gas"
        },
        {
          "s": "CBOT:ZC1!",
          "d": "Corn"
        }
      ],
      "originalTitle": "Commodities"
    },
    {
      "title": "国債",
      "symbols": [
        {
          "s": "CME:GE1!",
          "d": "Eurodollar"
        },
        {
          "s": "CBOT:ZB1!",
          "d": "T-Bond"
        },
        {
          "s": "CBOT:UD1!",
          "d": "Ultra T-Bond"
        },
        {
          "s": "EUREX:GG1!",
          "d": "Euro Bund"
        },
        {
          "s": "EUREX:II1!",
          "d": "Euro BTP"
        },
        {
          "s": "EUREX:HR1!",
          "d": "Euro BOBL"
        }
      ],
      "originalTitle": "Bonds"
    },
    {
      "title": "FX",
      "symbols": [
        {
          "s": "FX:EURUSD"
        },
        {
          "s": "FX:GBPUSD"
        },
        {
          "s": "FX:USDJPY"
        },
        {
          "s": "FX:USDCHF"
        },
        {
          "s": "FX:AUDUSD"
        },
        {
          "s": "FX:USDCAD"
        }
      ],
      "originalTitle": "Forex"
    }
  ]
}
  </script>
</div>
<!-- TradingView Widget END --></div>

  <div class="mybox_title">▼Bookmark</div>
  <div class="mybox" style=" padding:10px 0 0 0 ;">
  <div class="mybox_link">公式ホームページ</div>
  <div class="mybox_link">日経</div>
  <div class="mybox_link">財務省</div>
  <div class="mybox_link">facebook</div>
  <div class="mybox_link">交通情報</div>
</div>
+Bookmark設定</div>
<!--左サイドバー終了-->


<!--メイン開始-->
<div style="float:left;">

</div>
<!--メイン要素終了-->

<!--右サイドバー-->
<div class="mybox_sidebar">
<div class="mybox_title">▼twitter</div>
  <div class="mybox"><img src="..//img/mybox/twitter.jpg" width="300" height="200"></div>
  
  <div class="mybox_title">▼今の様子</div>
  <div class="mybox"><!--<img src="../img/mybox/camera.jpg" width="300" height="200">-->
  <iframe width="300" height="200" src="https://www.youtube.com/embed/OBsmLKuNELA?rel=0" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>
  
  <div class="mybox_title">
    ?Mybox設定
  </div>
  <div class="mybox_title">
    ▼背景設定
  </div>
  <div class="mybox"></div>

</div>
<!--右サイドバー終了-->


<!--天気用-->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script type="text/javascript" src="./mamewaza_weather/mamewaza_weather.min.js"></script>
<script type="text/javascript">
$.mamewaza_weather( {
	selector: "#mamewaza_weather",
	region:"130010",
	layout:"vertical",
	when:"2days",
	explanation:"1"
} );
</script>

</body>
</html>



<SCRIPT type="text/javascript" src="../common/jkl-calendar.js" charset="Shift_JIS"></SCRIPT>

<script language="javascript" src="../common/fjcall_ComFunc.js"></script>
<SCRIPT Language="JavaScript">
<!--
var cal1 = new JKL.Calendar("calid","formid","colname1"); //From
var cal2 = new JKL.Calendar("calid","formid","colname2"); //To

/////////////////////////////////////////
// OKボタン
/////////////////////////////////////////
function MyComOK(){


	var labelObj = document.getElementById("wait_label");
	labelObj.innerHTML = "しばらくお待ち下さい。。。";

	form01.comButton.value="ok";
	form01.submit();


}
/////////////////////////////////////////
// 日次報告
/////////////////////////////////////////
function MyCodeClickh5()
{

	//日付範囲
	selymd1=form01.selymd1.value;
	selymd2=form01.selymd2.value;

	window.open('topmenu_sfdailyreport.php?selymd1=' + unescape(selymd1) + '&selymd2=' + unescape(selymd2), '_blank', 'width=1100,height=850,titlebar=no,toolbar=no,scrollbars=yes, top=0,left=0');

}

/////////////////////////////////////////
// 月次定例資料を表示する
/////////////////////////////////////////
function ManuOpen(){

reportfilename = '../manu/' + form01.reportfilename.value ;

	window.open(reportfilename ,'_blank', 'width=1000,height=700,titlebar=no,toolbar=no,scrollbars=yes');

}
/////////////////////////////////////////
// 操作マニュアルを表示する
/////////////////////////////////////////
function ManuOpen2(){

	window.open('../manu/manual_foruser.html', '_blank', 'width=1000,height=770,titlebar=no,toolbar=no,scrollbars=yes');


}
/////////////////////////////////////////
// インシデント総件数クリック
/////////////////////////////////////////
function MyCodeClickh1( wmode )
{
	//日付範囲
	selymd1=form01.selymd1.value;
	selymd2=form01.selymd2.value;

	window.open('topmenu_sfmeisai.php?selymd1=' + unescape(selymd1) + '&selymd2=' + unescape(selymd2)  + '&selmode=' + unescape(wmode), '_blank', 'width=1100,height=850,titlebar=no,toolbar=no,scrollbars=yes, top=' + (screen.availHeight-500)/2 + ',left=' + (screen.availWidth-500)/2);

}
/////////////////////////////////////////
// 総着信数クリック
/////////////////////////////////////////
function MyCodeClickh2(  )
{

	//日付範囲
	selymd1=form01.selymd1.value;
	selymd2=form01.selymd2.value;

	window.open('topmenu_callmeisai.php?selymd1=' + unescape(selymd1) + '&selymd2=' + unescape(selymd2) , '_blank', 'width=570,height=850,titlebar=no,toolbar=no,scrollbars=yes, top=' + (screen.availHeight-500)/2 + ',left=' + (screen.availWidth-500)/2);

}
/////////////////////////////////////////
// 店舗別平均問合せ件数クリック
/////////////////////////////////////////
function MyCodeClickh3( )
{

	//日付範囲
	selymd1=form01.selymd1.value;
	selymd2=form01.selymd2.value;

	window.open('topmenu_sfstore.php?selymd1=' + unescape(selymd1) + '&selymd2=' + unescape(selymd2) , '_blank', 'width=550,height=850,titlebar=no,toolbar=no,scrollbars=yes, top=' + (screen.availHeight-500)/2 + ',left=' + (screen.availWidth-500)/2);

}
/////////////////////////////////////////
// 機種名クリック
/////////////////////////////////////////
function MyCodeClickh4( wmode )
{
	//日付範囲
	selymd1=form01.selymd1.value;
	selymd2=form01.selymd2.value;

	window.open('topmenu_sfkisyumeisai.php?selymd1=' + unescape(selymd1) + '&selymd2=' + unescape(selymd2)  + '&selmode=' + unescape(wmode), '_blank', 'width=1100,height=850,titlebar=no,toolbar=no,scrollbars=yes, top=' + (screen.availHeight-500)/2 + ',left=' + (screen.availWidth-500)/2);

}

/////////////////////////////////////////
// グラフクリック
/////////////////////////////////////////
function MyCodeClickh31( )
{
	form01.comButton.value="ok";
    form01.comButtonG.value="g31";
    form01.submit();
}
/////////////////////////////////////////
// グラフクリック
/////////////////////////////////////////
function MyCodeClickh32( )
{

	form01.comButton.value="ok";
    form01.comButtonG.value="g32";
    form01.submit();
}
/////////////////////////////////////////
// グラフクリック
/////////////////////////////////////////
function MyCodeClickh33( )
{

	form01.comButton.value="ok";
    form01.comButtonG.value="g33";
    form01.submit();
}

//-->
</SCRIPT>

