<?php
//////////////////////////////////////////
// データ作成
//<引数>
//  $wSelclientcode:選択したｸﾗｲｱﾝﾄ
//  $wSelendusercode:選択したｴﾝﾄﾞﾕｰｻﾞ
//////////////////////////////////////////
function PreMakeWkData( $conn, $selDesk, $wSelclientcode, $wSelendusercode ) {

global $ENV_MODE;
global $Const_COMPANYCODE;
global $Const_DB_SCHEMA;
global $MOJI_ORG;
global $MOJI_NEW;

global $user;
global $fromY, $fromM, $fromD, $toY, $toM, $toD;
global $fromY2, $fromM2, $fromD2; //グラフ用


	//抽出対象日付を作成
	$wFrom = $fromY . $fromM . $fromD;
	$wFrom += 0;
	$wTo = $toY . $toM . $toD;
	$wTo += 0;
	$wFrom2 = $fromY2 . $fromM2 . $fromD2;
	$wFrom2 += 0;

	$recflg = 0;//対象ﾚｺｰﾄﾞ有り無しﾌﾗｸﾞ


	//前削除(自分)
	$sql = "DELETE FROM " . $Const_DB_SCHEMA . "wdeskhistory1";
	$sql = $sql." WHERE userid ='". $Const_COMPANYCODE . $user."'";//自分
	$result = $conn->prepare($sql);
	$result->execute();
	$result = null;
	//前削除(自分)
	$sql = "DELETE FROM " . $Const_DB_SCHEMA . "wdeskhistory2day";
	$sql = $sql." WHERE userid ='". $Const_COMPANYCODE . $user."'";//自分
	$result = $conn->prepare($sql);
	$result->execute();
	$result = null;

	//前削除(自分)
	$sql = "DELETE FROM " .  $Const_DB_SCHEMA . "wdeskhistory3timefj";
	$sql = $sql." WHERE userid ='". $Const_COMPANYCODE . $user."'";//自分
	$result = $conn->prepare($sql);
	$result->execute();
	$result = null;


	////////////////////////////////////////
	// 1.wdeskhistory1の作成
	////////////////////////////////////////

	//空枠の作成
	$sql = "SELECT " . $Const_DB_SCHEMA . "mclient.deskcode, " . $Const_DB_SCHEMA . "mclient.dspno," . $Const_DB_SCHEMA . "menduser.clientcode";
	$sql = $sql . "," . $Const_DB_SCHEMA .  "menduser.dspno, " . $Const_DB_SCHEMA . "menduser.endusercode, " . $Const_DB_SCHEMA . "mclient.clientname, " . $Const_DB_SCHEMA . "menduser.endusername";
	$sql = $sql . " FROM " . $Const_DB_SCHEMA . "menduser LEFT JOIN " . $Const_DB_SCHEMA . "mclient ON " . $Const_DB_SCHEMA . "menduser.clientcode = " . $Const_DB_SCHEMA . "mclient.clientcode";
	//ｸﾗｲｱﾝﾄの選択
	if($wSelclientcode == "ALL"){
		//選択したデスクの全クライアント
		$sql = $sql . " WHERE " . $Const_DB_SCHEMA . " mclient.deskcode=" . $selDesk;
	}else{
		//クライアントを指定
		$sql = $sql . " WHERE " . $Const_DB_SCHEMA . "menduser.clientcode='" . $wSelclientcode . "'";
	}
	//ｴﾝﾄﾞﾕｰｻﾞの選択
	if($wSelendusercode == "ALL"){
		//全ユーザの場合、条件不要
	}else{
		$sql = $sql . " AND " . $Const_DB_SCHEMA . "menduser.endusercode='" . $wSelendusercode . "'";
	}

	$sql = $sql . " AND " . $Const_DB_SCHEMA . "mclient.endday=0";//2019-02-04 ADD

	$sql = $sql . " ORDER BY " . $Const_DB_SCHEMA . "mclient.deskcode, " . $Const_DB_SCHEMA . "mclient.dspno, " . $Const_DB_SCHEMA . "menduser.clientcode, " . $Const_DB_SCHEMA . "menduser.dspno, " . $Const_DB_SCHEMA . "menduser.endusercode";
	$result = $conn->prepare($sql);
	$result->execute();
	while ($rs = $result->fetch(PDO::FETCH_ASSOC))
	{
		$sql2 = "INSERT INTO " . $Const_DB_SCHEMA . "wdeskhistory1";
		$sql2 = $sql2. "(userid, deskcode, clientcode,endusercode)";
		$sql2 = $sql2. " VALUES(";
		$sql2 = $sql2. "'" . $Const_COMPANYCODE . $user."'";
		$sql2 = $sql2. "," . $rs["deskcode"];
		$sql2 = $sql2. ",'" . $rs["clientcode"]  ."'";
		$sql2 = $sql2. ",'" . $rs["endusercode"] . "'";
		$sql2 = $sql2. ")";
		$result2 = $conn->query($sql2);
		$result2 = null;
	}
	$rs = null;
	$result = null;

	//通話数
	$sql = "SELECT " . $Const_DB_SCHEMA . "mtrunk.clientcode, " . $Const_DB_SCHEMA . "mtrunk.endusercode, Count(" . $Const_DB_SCHEMA . "dcall_datadump.recno) AS recno_cnt, Sum(" . $Const_DB_SCHEMA . "dcall_datadump.talktime) AS talktime_sum";
	$sql = $sql . " FROM ((" . $Const_DB_SCHEMA . "dcall_datadump LEFT JOIN " . $Const_DB_SCHEMA . "mtrunk ON " . $Const_DB_SCHEMA . "dcall_datadump.trunkno = " . $Const_DB_SCHEMA . "mtrunk.trunkno) LEFT JOIN " . $Const_DB_SCHEMA . "mclient ON " . $Const_DB_SCHEMA . "mtrunk.clientcode = " . $Const_DB_SCHEMA . "mclient.clientcode) LEFT JOIN " . $Const_DB_SCHEMA . "menduser ON (" . $Const_DB_SCHEMA . "mtrunk.clientcode = " . $Const_DB_SCHEMA . "menduser.clientcode) AND (" . $Const_DB_SCHEMA . "mtrunk.endusercode = " . $Const_DB_SCHEMA . "menduser.endusercode)";
	$sql = $sql . " WHERE (" . $Const_DB_SCHEMA . "dcall_datadump.calldate>=" . $wFrom . " And " . $Const_DB_SCHEMA . "dcall_datadump.calldate<=" . $wTo . ")";
	$sql = $sql . " AND   (" . $Const_DB_SCHEMA . "dcall_datadump.talkieflg=0 AND " . $Const_DB_SCHEMA . "dcall_datadump.fusyutsuflg=0 AND " . $Const_DB_SCHEMA . "dcall_datadump.guidance1flg=0 AND " . $Const_DB_SCHEMA . "dcall_datadump.guidance2flg=0)";
	$sql = $sql . " GROUP BY " . $Const_DB_SCHEMA . "mtrunk.clientcode, " . $Const_DB_SCHEMA . "mtrunk.endusercode";
	$result = $conn->prepare($sql);
	$result->execute();
	while ($rs = $result->fetch(PDO::FETCH_ASSOC))
	{
		$recflg = 1;

		$sql2 = "UPDATE " . $Const_DB_SCHEMA . "wdeskhistory1 SET";
		$sql2 = $sql2 ." talksu=". $rs["recno_cnt"];
		$sql2 = $sql2 .",talktime=". $rs["talktime_sum"];
		$sql2 = $sql2 ." WHERE userid='". $Const_COMPANYCODE . $user."'";
		$sql2 = $sql2 ." AND   clientcode='". $rs["clientcode"]."'";
		$sql2 = $sql2 ." AND   endusercode='". $rs["endusercode"]."'";
		$result2 = $conn->query($sql2);
		$result2 = null;

	}
	$rs = null;
	$result = null;

	//ガイダンス数
	$sql = "SELECT " . $Const_DB_SCHEMA . "mtrunk.clientcode, " . $Const_DB_SCHEMA . "mtrunk.endusercode, Count(" . $Const_DB_SCHEMA . "dcall_datadump.recno) AS recno_cnt";
	$sql = $sql . " FROM ((" . $Const_DB_SCHEMA . "dcall_datadump LEFT JOIN " . $Const_DB_SCHEMA . "mtrunk ON " . $Const_DB_SCHEMA . "dcall_datadump.trunkno = " . $Const_DB_SCHEMA . "mtrunk.trunkno) LEFT JOIN " . $Const_DB_SCHEMA . "mclient ON " . $Const_DB_SCHEMA . "mtrunk.clientcode = " . $Const_DB_SCHEMA . "mclient.clientcode) LEFT JOIN " . $Const_DB_SCHEMA . "menduser ON (" . $Const_DB_SCHEMA . "mtrunk.clientcode = " . $Const_DB_SCHEMA . "menduser.clientcode) AND (" . $Const_DB_SCHEMA . "mtrunk.endusercode = " . $Const_DB_SCHEMA . "menduser.endusercode)";
	$sql = $sql . " WHERE (" . $Const_DB_SCHEMA . "dcall_datadump.calldate>=" . $wFrom . " And " . $Const_DB_SCHEMA . "dcall_datadump.calldate<=" . $wTo . ")";
	$sql = $sql . " AND   (" . $Const_DB_SCHEMA . "dcall_datadump.talkieflg>0)";
	$sql = $sql . " GROUP BY " . $Const_DB_SCHEMA . "mtrunk.clientcode, " . $Const_DB_SCHEMA . "mtrunk.endusercode";
	$result = $conn->prepare($sql);
	$result->execute();
	while ($rs = $result->fetch(PDO::FETCH_ASSOC))
	{
		$sql2 = "UPDATE " . $Const_DB_SCHEMA . "wdeskhistory1 SET";
		$sql2 = $sql2 ." guidancesu=". $rs["recno_cnt"];
		$sql2 = $sql2 ." WHERE userid='". $Const_COMPANYCODE . $user."'";
		$sql2 = $sql2 ." AND   clientcode='". $rs["clientcode"]."'";
		$sql2 = $sql2 ." AND   endusercode='". $rs["endusercode"]."'";
		$result2 = $conn->query($sql2);
		$result2 = null;
	}
	$rs = null;
	$result = null;

	//ガイダンス12秒数
	$sql = "SELECT  " . $Const_DB_SCHEMA . "mtrunk.clientcode,  " . $Const_DB_SCHEMA . "mtrunk.endusercode, Count( " . $Const_DB_SCHEMA . "dcall_datadump.recno) AS recno_cnt";
	$sql = $sql . " FROM (( " .  $Const_DB_SCHEMA . "dcall_datadump LEFT JOIN  " . $Const_DB_SCHEMA . "mtrunk ON  " . $Const_DB_SCHEMA . "dcall_datadump.trunkno =  " . $Const_DB_SCHEMA . "mtrunk.trunkno) LEFT JOIN  " . $Const_DB_SCHEMA . "mclient ON  " . $Const_DB_SCHEMA . "mtrunk.clientcode =  " . $Const_DB_SCHEMA . "mclient.clientcode) LEFT JOIN  " . $Const_DB_SCHEMA . "menduser ON ( " . $Const_DB_SCHEMA . "mtrunk.clientcode =  " . $Const_DB_SCHEMA . "menduser.clientcode) AND ( " . $Const_DB_SCHEMA . "mtrunk.endusercode =  " . $Const_DB_SCHEMA . "menduser.endusercode)";
	$sql = $sql . " WHERE ( " .  $Const_DB_SCHEMA . "dcall_datadump.calldate>=" . $wFrom . " And  " . $Const_DB_SCHEMA . "dcall_datadump.calldate<=" . $wTo . ")";
	$sql = $sql . " AND   (( " . $Const_DB_SCHEMA . "dcall_datadump.talkieflg=0 AND  " . $Const_DB_SCHEMA . "dcall_datadump.fusyutsuflg=0 AND  " . $Const_DB_SCHEMA . "dcall_datadump.guidance1flg=1)";
	$sql = $sql . " OR     ( " . $Const_DB_SCHEMA . "dcall_datadump.talkieflg=0 AND  " . $Const_DB_SCHEMA . "dcall_datadump.fusyutsuflg=0 AND  " . $Const_DB_SCHEMA . "dcall_datadump.guidance2flg=1))";
	$sql = $sql . " GROUP BY " .  $Const_DB_SCHEMA . "mtrunk.clientcode, " .  $Const_DB_SCHEMA . "mtrunk.endusercode";
	$result = $conn->prepare($sql);
	$result->execute();
	while ($rs = $result->fetch(PDO::FETCH_ASSOC))
	{
		$sql2 = "UPDATE " . $Const_DB_SCHEMA . "wdeskhistory1 SET";
		$sql2 = $sql2 ." guidance12su=". $rs["recno_cnt"];
		$sql2 = $sql2 ." WHERE userid='". $Const_COMPANYCODE . $user."'";
		$sql2 = $sql2 ." AND   clientcode='". $rs["clientcode"]."'";
		$sql2 = $sql2 ." AND   endusercode='". $rs["endusercode"]."'";
		$result2 = $conn->query($sql2);
		$result2 = null;
	}
	$rs = null;
	$result = null;

	//不出呼数
	$sql = "SELECT  " . $Const_DB_SCHEMA . "mtrunk.clientcode, " . $Const_DB_SCHEMA . "mtrunk.endusercode, Count( " . $Const_DB_SCHEMA . "dcall_datadump.recno) AS recno_cnt";
	$sql = $sql . " FROM (( " .  $Const_DB_SCHEMA . "dcall_datadump LEFT JOIN  " . $Const_DB_SCHEMA . "mtrunk ON  " . $Const_DB_SCHEMA . "dcall_datadump.trunkno =  " . $Const_DB_SCHEMA . "mtrunk.trunkno) LEFT JOIN  " . $Const_DB_SCHEMA . "mclient ON " .  $Const_DB_SCHEMA . "mtrunk.clientcode =  " . $Const_DB_SCHEMA . "mclient.clientcode) LEFT JOIN  " . $Const_DB_SCHEMA . "menduser ON ( " . $Const_DB_SCHEMA . "mtrunk.clientcode = " . $Const_DB_SCHEMA . "menduser.clientcode) AND ( " . $Const_DB_SCHEMA . "mtrunk.endusercode =  " . $Const_DB_SCHEMA . "menduser.endusercode)";
	$sql = $sql . " WHERE ( " . $Const_DB_SCHEMA . "dcall_datadump.calldate>=" . $wFrom . " And  " . $Const_DB_SCHEMA . "dcall_datadump.calldate<=" . $wTo . ")";
	$sql = $sql . " AND   ( " . $Const_DB_SCHEMA . "dcall_datadump.talkieflg=0 AND  " . $Const_DB_SCHEMA . "dcall_datadump.fusyutsuflg=1)";
	$sql = $sql . " GROUP BY  " . $Const_DB_SCHEMA . "mtrunk.clientcode,  " . $Const_DB_SCHEMA . "mtrunk.endusercode";
	$result = $conn->prepare($sql);
	$result->execute();
	while ($rs = $result->fetch(PDO::FETCH_ASSOC))
	{
		$sql2 = "UPDATE " . $Const_DB_SCHEMA . "wdeskhistory1 SET";
		$sql2 = $sql2 ." fusyutsusu=". $rs["recno_cnt"];
		$sql2 = $sql2 ." WHERE userid='". $Const_COMPANYCODE . $user."'";
		$sql2 = $sql2 ." AND   clientcode='". $rs["clientcode"]."'";
		$sql2 = $sql2 ." AND   endusercode='". $rs["endusercode"]."'";
		$result2 = $conn->query($sql2);
		$result2 = null;
	}
	$rs = null;
	$result = null;



	///////////////////////
	//通話数-時間帯別
	// ADD 2018-12-03
	///////////////////////
	if ($selDesk == 1 ){
		$sql = "SELECT  " . $Const_DB_SCHEMA . "mtrunk.clientcode,  " . $Const_DB_SCHEMA . "mtrunk.endusercode, Count( " . $Const_DB_SCHEMA . "dcall_datadump.recno) AS recno_cnt,  " . $Const_DB_SCHEMA . "dcall_datadump.timezone";
		$sql = $sql . " FROM (( " . $Const_DB_SCHEMA . "dcall_datadump LEFT JOIN  " . $Const_DB_SCHEMA . "mtrunk ON  " . $Const_DB_SCHEMA . "dcall_datadump.trunkno =  " . $Const_DB_SCHEMA . "mtrunk.trunkno) LEFT JOIN  " . $Const_DB_SCHEMA . "mclient ON  " . $Const_DB_SCHEMA . "mtrunk.clientcode =  " . $Const_DB_SCHEMA . "mclient.clientcode) LEFT JOIN  " . $Const_DB_SCHEMA . "menduser ON ( " . $Const_DB_SCHEMA . "mtrunk.clientcode =  " . $Const_DB_SCHEMA . "menduser.clientcode) AND ( " . $Const_DB_SCHEMA . "mtrunk.endusercode =  " . $Const_DB_SCHEMA . "menduser.endusercode)";
		$sql = $sql . " WHERE ( " . $Const_DB_SCHEMA . "dcall_datadump.calldate>=" . $wFrom . " And  " . $Const_DB_SCHEMA . "dcall_datadump.calldate<=" . $wTo . ")";
		$sql = $sql . " AND   ( " . $Const_DB_SCHEMA . "dcall_datadump.talkieflg=0 AND  " . $Const_DB_SCHEMA . "dcall_datadump.fusyutsuflg=0 AND  " . $Const_DB_SCHEMA . "dcall_datadump.guidance1flg=0 AND  " . $Const_DB_SCHEMA . "dcall_datadump.guidance2flg=0)";
		$sql = $sql . " GROUP BY  " . $Const_DB_SCHEMA . "dcall_datadump.timezone,  " . $Const_DB_SCHEMA . "mtrunk.clientcode,  " . $Const_DB_SCHEMA . "mtrunk.endusercode";
		$result = $conn->prepare($sql);
		$result->execute();
		while ($rs = $result->fetch(PDO::FETCH_ASSOC))
		{
			$recflg = 1;

			$sql2 = "UPDATE " . $Const_DB_SCHEMA . "wdeskhistory1 SET";
			$sql2 = $sql2 ." talksu" . $rs["timezone"] . "=". $rs["recno_cnt"];
			$sql2 = $sql2 ." WHERE userid='". $Const_COMPANYCODE . $user."'";
			$sql2 = $sql2 ." AND   clientcode='". $rs["clientcode"]."'";
			$sql2 = $sql2 ." AND   endusercode='". $rs["endusercode"]."'";
			$result2 = $conn->query($sql2);
			$result2 = null;
		}
		$rs = null;
		$result = null;
	}


	//デスク別合計
	$sql = "SELECT deskcode, Sum(talksu) AS talksu_sum, Sum(talktime) AS talktime_sum, Sum(guidancesu) AS guidancesu_sum, Sum(guidance12su) AS guidance12su_sum, Sum(fusyutsusu) AS fusyutsusu_sum";
	$sql = $sql . ",Sum(talksu0) AS talksu0_sum, Sum(talksu1) AS talksu1_sum, Sum(talksu2) AS talksu2_sum, Sum(talksu3) AS talksu3_sum, Sum(talksu4) AS talksu4_sum";
	$sql = $sql . ",Sum(talksu5) AS talksu5_sum, Sum(talksu6) AS talksu6_sum, Sum(talksu7) AS talksu7_sum, Sum(talksu8) AS talksu8_sum, Sum(talksu9) AS talksu9_sum";
	$sql = $sql . ",Sum(talksu10) AS talksu10_sum, Sum(talksu11) AS talksu11_sum, Sum(talksu12) AS talksu12_sum, Sum(talksu13) AS talksu13_sum, Sum(talksu14) AS talksu14_sum";
	$sql = $sql . ",Sum(talksu15) AS talksu15_sum, Sum(talksu16) AS talksu16_sum, Sum(talksu17) AS talksu17_sum, Sum(talksu18) AS talksu18_sum, Sum(talksu19) AS talksu19_sum";
	$sql = $sql . ",Sum(talksu20) AS talksu20_sum, Sum(talksu21) AS talksu21_sum, Sum(talksu22) AS talksu22_sum, Sum(talksu23) AS talksu23_sum";
	$sql = $sql . " FROM  " . $Const_DB_SCHEMA . "wdeskhistory1";
	$sql = $sql . " WHERE userid='". $Const_COMPANYCODE . $user."'";
	$sql = $sql . " AND   recflg=0";
	$sql = $sql . " GROUP BY deskcode";
	$result = $conn->prepare($sql);
	$result->execute();
	while ($rs = $result->fetch(PDO::FETCH_ASSOC))
	{
		$sql2 = "INSERT INTO " . $Const_DB_SCHEMA . "wdeskhistory1";
		$sql2 = $sql2. "(userid, deskcode, clientcode,clientname,endusercode,endusername,talksu,talktime, guidancesu,guidance12su,fusyutsusu,recflg";
		$sql2 = $sql2. ",talksu0, talksu1, talksu2, talksu3, talksu4, talksu5, talksu6, talksu7, talksu8, talksu9, talksu10, talksu11";
		$sql2 = $sql2. ",talksu12,talksu13, talksu14, talksu15, talksu16, talksu17, talksu18, talksu19, talksu20, talksu21, talksu22, talksu23";
		$sql2 = $sql2. " )VALUES(";
		$sql2 = $sql2. "'" . $Const_COMPANYCODE . $user."'";
		$sql2 = $sql2. "," . $rs["deskcode"];
		$sql2 = $sql2. "," . "'s" . $rs["deskcode"] . "'";
		$sql2 = $sql2. ",''";
		$sql2 = $sql2. "," . "'s" . $rs["deskcode"] . "'";
		$sql2 = $sql2. ",''";
		$sql2 = $sql2. "," . $rs["talksu_sum"];
		$sql2 = $sql2. "," . $rs["talktime_sum"];
		$sql2 = $sql2. "," . $rs["guidancesu_sum"];
		$sql2 = $sql2. "," . $rs["guidance12su_sum"];
		$sql2 = $sql2. "," . $rs["fusyutsusu_sum"];
		$sql2 = $sql2. ",9"; //合計は９
		$sql2 = $sql2. "," . $rs["talksu0_sum"];
		$sql2 = $sql2. "," . $rs["talksu1_sum"];
		$sql2 = $sql2. "," . $rs["talksu2_sum"];
		$sql2 = $sql2. "," . $rs["talksu3_sum"];
		$sql2 = $sql2. "," . $rs["talksu4_sum"];
		$sql2 = $sql2. "," . $rs["talksu5_sum"];
		$sql2 = $sql2. "," . $rs["talksu6_sum"];
		$sql2 = $sql2. "," . $rs["talksu7_sum"];
		$sql2 = $sql2. "," . $rs["talksu8_sum"];
		$sql2 = $sql2. "," . $rs["talksu9_sum"];
		$sql2 = $sql2. "," . $rs["talksu10_sum"];
		$sql2 = $sql2. "," . $rs["talksu11_sum"];
		$sql2 = $sql2. "," . $rs["talksu12_sum"];
		$sql2 = $sql2. "," . $rs["talksu13_sum"];
		$sql2 = $sql2. "," . $rs["talksu14_sum"];
		$sql2 = $sql2. "," . $rs["talksu15_sum"];
		$sql2 = $sql2. "," . $rs["talksu16_sum"];
		$sql2 = $sql2. "," . $rs["talksu17_sum"];
		$sql2 = $sql2. "," . $rs["talksu18_sum"];
		$sql2 = $sql2. "," . $rs["talksu19_sum"];
		$sql2 = $sql2. "," . $rs["talksu20_sum"];
		$sql2 = $sql2. "," . $rs["talksu21_sum"];
		$sql2 = $sql2. "," . $rs["talksu22_sum"];
		$sql2 = $sql2. "," . $rs["talksu23_sum"];
		$sql2 = $sql2. ")";
		$result2 = $conn->query($sql2);
		$result2 = null;
	}
	$rs = null;
	$result = null;








	////////////////////////////////////////
	// 2.wdeskhistory2dayの作成
	// 3.wdeskhistory3timefjの作成
	// クライアントを選択していたならば、日別を作成する
	/////////////////////////////////////////////////////////
	// 2.wdeskhistory2dayの作成
	//////////////////////////////////

//	$targetdate = $wFrom; //開始日
	$targetdate = $wFrom2; //開始日(31日前)
	$endymd = $wTo; //終了日

	//曜日を配列に入れる
	$youbi = array("日","月","火","水","木","金","土");

	while($targetdate <= $endymd) 
	{
		$weekname = "";
		$yb = date('w',strtotime($targetdate) );
		switch ($yb) 
		{
			case 0:
				$weekname = "Sun";
				break;
			case 1:
				$weekname = "Mon";
				break;
			case 2:
				$weekname = "Tue";
				break;
			case 3:
				$weekname = "Wed";
				break;
			case 4:
				$weekname = "Thu";
				break;
			case 5:
				$weekname = "Fri";
				break;
			case 6:
				$weekname = "Sat";
				break;
		}

		//日の切り取り
		$wHiduke = substr("00000000".$targetdate,8);
		$wHidukeD = 0 + substr($wHiduke,6,2);

		//祝日判断
		$wsyukuflg =0;
		$wSyukuName = GetHoliday_OPEN( $conn, substr($wHiduke,0,4), substr($wHiduke,4,2), substr($wHiduke,6,2) );
		if($wSyukuName !=""){
			$wsyukuflg =1;
		}
		$sql2 = "INSERT INTO " . $Const_DB_SCHEMA . "wdeskhistory2day";
		$sql2 = $sql2. "(userid, hiduke,dspday,youbicd,youbimei,syukuflg,recflg)";
		$sql2 = $sql2. " VALUES(";
		$sql2 = $sql2. "'" . $Const_COMPANYCODE . $user."'";
		$sql2 = $sql2. "," . $wHiduke;
		$sql2 = $sql2. "," . $wHidukeD;
		$sql2 = $sql2. "," . $yb;
		$sql2 = $sql2. ",'" . $weekname . "'";
		$sql2 = $sql2. "," . $wsyukuflg;
		$sql2 = $sql2. ",0" ;
		$sql2 = $sql2. ")";
		$result2 = $conn->query($sql2);
		$result2 = null;

		//日付ｶｳﾝﾄｱｯﾌﾟ
		$targetdate = date("Ymd", mktime(0, 0, 0, date("m",strtotime($targetdate)), date("d",strtotime($targetdate)) + 1, date("Y",strtotime($targetdate)) ));
	}

	//通話数
	$sql = "SELECT  " . $Const_DB_SCHEMA . "dcall_datadump.calldate, Count( " . $Const_DB_SCHEMA . "dcall_datadump.recno) AS recno_cnt, Sum( " . $Const_DB_SCHEMA . "dcall_datadump.talktime) AS talktime_sum";
	$sql = $sql. " FROM  " . $Const_DB_SCHEMA . "dcall_datadump LEFT JOIN  " . $Const_DB_SCHEMA . "mtrunk ON  " . $Const_DB_SCHEMA . "dcall_datadump.trunkno =  " . $Const_DB_SCHEMA . "mtrunk.trunkno";
	$sql = $sql . " WHERE (calldate>=" . $wFrom2 . " AND calldate<=" . $endymd . ")";
//	$sql = $sql . " WHERE (calldate>=" . $wFrom . " AND calldate<=" . $endymd . ")";
	if($wSelclientcode == "ALL"){
		$sql = $sql . " AND   ( " . $Const_DB_SCHEMA . "mtrunk.deskcode=" . $selDesk . ")";
	}else{
		$sql = $sql . " AND   ( " . $Const_DB_SCHEMA . "mtrunk.clientcode='" . $wSelclientcode . "')";
	}
	if($wSelendusercode !="ALL"){ //ｴﾝﾄﾞﾕｰｻﾞ選択?
		$sql = $sql . " AND   ( " . $Const_DB_SCHEMA . "mtrunk.endusercode='" . $wSelendusercode . "')";
	}
	$sql = $sql . " AND   ( " . $Const_DB_SCHEMA . "dcall_datadump.talkieflg=0)";
	$sql = $sql . " AND   ( " . $Const_DB_SCHEMA . "dcall_datadump.fusyutsuflg=0)";
	$sql = $sql . " AND   ( " . $Const_DB_SCHEMA . "dcall_datadump.guidance1flg=0)";
	$sql = $sql . " AND   ( " . $Const_DB_SCHEMA . "dcall_datadump.guidance2flg=0)";
	$sql = $sql . " GROUP BY calldate";
	$sql = $sql . " ORDER BY calldate";
	$result = $conn->prepare($sql);
	$result->execute();
	while ($rs = $result->fetch(PDO::FETCH_ASSOC))
	{
		$sql2 = "UPDATE " . $Const_DB_SCHEMA . "wdeskhistory2day SET";
		$sql2 = $sql2 ." talksu=". $rs["recno_cnt"];
		$sql2 = $sql2 .",talktime=". $rs["talktime_sum"];
		$sql2 = $sql2 ." WHERE userid='". $Const_COMPANYCODE . $user."'";
		$sql2 = $sql2 ." AND   hiduke=". $rs["calldate"];
		$result2 = $conn->query($sql2);
		$result2 = null;
	}
	$rs = null;
	$result = null;

	//ガイダンス数
	$sql = "SELECT  " . $Const_DB_SCHEMA . "dcall_datadump.calldate, Count( " . $Const_DB_SCHEMA . "dcall_datadump.recno) AS recno_cnt";
	$sql = $sql . " FROM (( " . $Const_DB_SCHEMA . "dcall_datadump LEFT JOIN  " . $Const_DB_SCHEMA . "mtrunk ON  " . $Const_DB_SCHEMA . "dcall_datadump.trunkno =  " . $Const_DB_SCHEMA . "mtrunk.trunkno) LEFT JOIN  " . $Const_DB_SCHEMA . "mclient ON " . $Const_DB_SCHEMA . "mtrunk.clientcode =  " . $Const_DB_SCHEMA . "mclient.clientcode) LEFT JOIN  " . $Const_DB_SCHEMA . "menduser ON ( " . $Const_DB_SCHEMA . "mtrunk.clientcode =  " . $Const_DB_SCHEMA . "menduser.clientcode) AND ( " . $Const_DB_SCHEMA . "mtrunk.endusercode =  " . $Const_DB_SCHEMA . "menduser.endusercode)";
//	$sql = $sql . " WHERE ( " . $Const_DB_SCHEMA . "dcall_datadump.calldate>=" . $wFrom . " And  " . $Const_DB_SCHEMA . "dcall_datadump.calldate<=" . $wTo . ")";
	$sql = $sql . " WHERE ( " . $Const_DB_SCHEMA . "dcall_datadump.calldate>=" . $wFrom2 . " And  " . $Const_DB_SCHEMA . "dcall_datadump.calldate<=" . $wTo . ")";
	if($wSelclientcode == "ALL"){
		$sql = $sql . " AND   ( " . $Const_DB_SCHEMA . "mtrunk.deskcode=" . $selDesk . ")";
	}else{
		$sql = $sql . " AND   ( " . $Const_DB_SCHEMA . "mtrunk.clientcode='" . $wSelclientcode . "')";
	}
	if($wSelendusercode !="ALL"){ //ｴﾝﾄﾞﾕｰｻﾞ選択?
		$sql = $sql . " AND   ( " . $Const_DB_SCHEMA . "mtrunk.endusercode='" . $wSelendusercode . "')";
	}
	$sql = $sql . " AND   ( " . $Const_DB_SCHEMA . "dcall_datadump.talkieflg>0)";
	$sql = $sql . " GROUP BY calldate";
	$result = $conn->prepare($sql);
	$result->execute();
	while ($rs = $result->fetch(PDO::FETCH_ASSOC))
	{
		$sql2 = "UPDATE  " . $Const_DB_SCHEMA . "wdeskhistory2day SET";
		$sql2 = $sql2 ." guidancesu=". $rs["recno_cnt"];
		$sql2 = $sql2 ." WHERE userid='". $Const_COMPANYCODE . $user."'";
		$sql2 = $sql2 ." AND   hiduke=". $rs["calldate"];
		$result2 = $conn->query($sql2);
		$result2 = null;
	}
	$rs = null;
	$result = null;

	//ガイダンス12秒数
	$sql = "SELECT  " . $Const_DB_SCHEMA . "dcall_datadump.calldate, Count( " . $Const_DB_SCHEMA . "dcall_datadump.recno) AS recno_cnt";
	$sql = $sql . " FROM (( " . $Const_DB_SCHEMA . "dcall_datadump LEFT JOIN  " . $Const_DB_SCHEMA . "mtrunk ON  " . $Const_DB_SCHEMA . "dcall_datadump.trunkno =  " . $Const_DB_SCHEMA . "mtrunk.trunkno) LEFT JOIN  " . $Const_DB_SCHEMA . "mclient ON  " . $Const_DB_SCHEMA . "mtrunk.clientcode =  " . $Const_DB_SCHEMA . "mclient.clientcode) LEFT JOIN  " . $Const_DB_SCHEMA . "menduser ON ( " . $Const_DB_SCHEMA . "mtrunk.clientcode =  " . $Const_DB_SCHEMA . "menduser.clientcode) AND ( " . $Const_DB_SCHEMA . "mtrunk.endusercode =  " . $Const_DB_SCHEMA . "menduser.endusercode)";
//	$sql = $sql . " WHERE ( " . $Const_DB_SCHEMA . "dcall_datadump.calldate>=" . $wFrom . " And  " . $Const_DB_SCHEMA . "dcall_datadump.calldate<=" . $wTo . ")";
	$sql = $sql . " WHERE ( " . $Const_DB_SCHEMA . "dcall_datadump.calldate>=" . $wFrom2 . " And  " . $Const_DB_SCHEMA . "dcall_datadump.calldate<=" . $wTo . ")";
	if($wSelclientcode == "ALL"){
		$sql = $sql . " AND   ( " . $Const_DB_SCHEMA . "mtrunk.deskcode=" . $selDesk . ")";
	}else{
		$sql = $sql . " AND   ( " . $Const_DB_SCHEMA . "mtrunk.clientcode='" . $wSelclientcode . "')";
	}
	if($wSelendusercode !="ALL"){ //ｴﾝﾄﾞﾕｰｻﾞ選択?
		$sql = $sql . " AND   ( " . $Const_DB_SCHEMA . "mtrunk.endusercode='" . $wSelendusercode . "')";
	}
	$sql = $sql . " AND   (( " . $Const_DB_SCHEMA . "dcall_datadump.talkieflg=0 AND  " . $Const_DB_SCHEMA . "dcall_datadump.fusyutsuflg=0 AND  " . $Const_DB_SCHEMA . "dcall_datadump.guidance1flg=1)";
	$sql = $sql . " OR     ( " . $Const_DB_SCHEMA . "dcall_datadump.talkieflg=0 AND  " . $Const_DB_SCHEMA . "dcall_datadump.fusyutsuflg=0 AND  " . $Const_DB_SCHEMA . "dcall_datadump.guidance2flg=1))";
	$sql = $sql . " GROUP BY calldate";
	$result = $conn->prepare($sql);
	$result->execute();
	while ($rs = $result->fetch(PDO::FETCH_ASSOC))
	{
		$sql2 = "UPDATE " . $Const_DB_SCHEMA . "wdeskhistory2day SET";
		$sql2 = $sql2 ." guidance12su=". $rs["recno_cnt"];
		$sql2 = $sql2 ." WHERE userid='". $Const_COMPANYCODE . $user."'";
		$sql2 = $sql2 ." AND   hiduke=". $rs["calldate"];
		$result2 = $conn->query($sql2);
		$result2 = null;
	}
	$rs = null;
	$result = null;

	//不出呼数
	$sql = "SELECT  " . $Const_DB_SCHEMA . "dcall_datadump.calldate, Count( " . $Const_DB_SCHEMA . "dcall_datadump.recno) AS recno_cnt";
	$sql = $sql . " FROM (( " . $Const_DB_SCHEMA . "dcall_datadump LEFT JOIN  " . $Const_DB_SCHEMA . "mtrunk ON  " . $Const_DB_SCHEMA . "dcall_datadump.trunkno =  " . $Const_DB_SCHEMA . "mtrunk.trunkno) LEFT JOIN  " . $Const_DB_SCHEMA . "mclient ON  " . $Const_DB_SCHEMA . "mtrunk.clientcode =  " . $Const_DB_SCHEMA . "mclient.clientcode) LEFT JOIN  " . $Const_DB_SCHEMA . "menduser ON ( " . $Const_DB_SCHEMA . "mtrunk.clientcode =  " . $Const_DB_SCHEMA . "menduser.clientcode) AND ( " . $Const_DB_SCHEMA . "mtrunk.endusercode =  " . $Const_DB_SCHEMA . "menduser.endusercode)";
//	$sql = $sql . " WHERE ( " . $Const_DB_SCHEMA . "dcall_datadump.calldate>=" . $wFrom . " And  " . $Const_DB_SCHEMA . "dcall_datadump.calldate<=" . $wTo . ")";
	$sql = $sql . " WHERE ( " . $Const_DB_SCHEMA . "dcall_datadump.calldate>=" . $wFrom2 . " And  " . $Const_DB_SCHEMA . "dcall_datadump.calldate<=" . $wTo . ")";
	if($wSelclientcode == "ALL"){
		$sql = $sql . " AND   ( " . $Const_DB_SCHEMA . "mtrunk.deskcode=" . $selDesk . ")";
	}else{
		$sql = $sql . " AND   ( " . $Const_DB_SCHEMA . "mtrunk.clientcode='" . $wSelclientcode . "')";
	}
	if($wSelendusercode !="ALL"){ //ｴﾝﾄﾞﾕｰｻﾞ選択?
		$sql = $sql . " AND   ( " . $Const_DB_SCHEMA . "mtrunk.endusercode='" . $wSelendusercode . "')";
	}
	$sql = $sql . " AND   ( " . $Const_DB_SCHEMA . "dcall_datadump.talkieflg=0 AND  " . $Const_DB_SCHEMA . "dcall_datadump.fusyutsuflg=1)";
	$sql = $sql . " GROUP BY calldate";
	$result = $conn->prepare($sql);
	$result->execute();
	while ($rs = $result->fetch(PDO::FETCH_ASSOC))
	{
		$sql2 = "UPDATE " . $Const_DB_SCHEMA . "wdeskhistory2day SET";
		$sql2 = $sql2 ." fusyutsusu=". $rs["recno_cnt"];
		$sql2 = $sql2 ." WHERE userid='". $Const_COMPANYCODE . $user."'";
		$sql2 = $sql2 ." AND   hiduke=". $rs["calldate"];
		$result2 = $conn->query($sql2);
		$result2 = null;
	}
	$rs = null;
	$result = null;


	//合計行の挿入
	$sql = "SELECT Sum(talksu) AS talksu_sum, Sum(talktime) AS talktime_sum, Sum(guidancesu) AS guidancesu_sum, Sum(guidance12su) AS guidance12su_sum, Sum(fusyutsusu) AS fusyutsusu_sum";
	$sql = $sql . " FROM  " . $Const_DB_SCHEMA . "wdeskhistory2day";
	$sql = $sql . " WHERE (userid='" . $Const_COMPANYCODE . $user . "')";
	$result = $conn->prepare($sql);
	$result->execute();
	while ($rs = $result->fetch(PDO::FETCH_ASSOC))
	{
		//合計行追加
		$sql2 = "INSERT INTO " . $Const_DB_SCHEMA . "wdeskhistory2day";
		$sql2 = $sql2. "(userid, hiduke,dspday,talksu,talktime,guidancesu,guidance12su,fusyutsusu,recflg)";
		$sql2 = $sql2. " VALUES(";
		$sql2 = $sql2. "'" . $Const_COMPANYCODE . $user."'";
		$sql2 = $sql2. ",99999999"; //日付
		$sql2 = $sql2. ",99";
		$sql2 = $sql2. "," . $rs["talksu_sum"];
		$sql2 = $sql2. "," . $rs["talktime_sum"];
		$sql2 = $sql2. "," . $rs["guidancesu_sum"];
		$sql2 = $sql2. "," . $rs["guidance12su_sum"];
		$sql2 = $sql2. "," . $rs["fusyutsusu_sum"];
		$sql2 = $sql2. ",9" ; //合計
		$sql2 = $sql2. ")";
		$result2 = $conn->query($sql2);
		$result2 = null;

	}
	$rs = null;
	$result = null;




	//////////////////////////////////
	// 3.wdeskhistory3timefjの作成
	//////////////////////////////////
	//時間枠の作成
	//mtimedsptableから枠を作成するように変更 2019-01-25
	$sql = "Select * From  " . $Const_DB_SCHEMA . "mtimedsptable";
	$result = $conn->prepare($sql);
	$result->execute();
	while ($rs = $result->fetch(PDO::FETCH_ASSOC))
	{
		$sql2 = "INSERT INTO " . $Const_DB_SCHEMA . "wdeskhistory3timefj";
		$sql2 = $sql2. "(userid, time, shiftname, subtotalflg, dspno, recflg)";
		$sql2 = $sql2. " VALUES(";
		$sql2 = $sql2. "'" . $Const_COMPANYCODE . $user."'";
		$sql2 = $sql2. "," . $rs["time"];
		$sql2 = $sql2. ",'" . $rs["shiftname"] . "'";
		$sql2 = $sql2. "," . $rs["subtotalflg"]; //シフトグループ(小計で識別したい)
		$sql2 = $sql2. "," . $rs["dspno"];
		$sql2 = $sql2. ",0" ;
		$sql2 = $sql2. ")";
		$result2 = $conn->query($sql2);
		$result2 = null;
	}
	$rs = null;
	$result = null;

	//通話数
	$sql = "SELECT  " . $Const_DB_SCHEMA . "dcall_datadump.timezone, Count( " . $Const_DB_SCHEMA . "dcall_datadump.recno) AS recno_cnt, Sum( " . $Const_DB_SCHEMA . "dcall_datadump.talktime) AS talktime_sum";
	$sql = $sql . " FROM  " . $Const_DB_SCHEMA . "dcall_datadump LEFT JOIN  " . $Const_DB_SCHEMA . "mtrunk ON " . $Const_DB_SCHEMA . "dcall_datadump.trunkno =  " . $Const_DB_SCHEMA . "mtrunk.trunkno";
	$sql = $sql . " WHERE ( " . $Const_DB_SCHEMA . "dcall_datadump.calldate>=" . $wFrom . " And  " . $Const_DB_SCHEMA . "dcall_datadump.calldate<=" . $wTo . ")";
	if($wSelclientcode == "ALL"){
		$sql = $sql . " AND   ( " . $Const_DB_SCHEMA . "mtrunk.deskcode=" . $selDesk . ")";
	}else{
		$sql = $sql . " AND   ( " . $Const_DB_SCHEMA . "mtrunk.clientcode='" . $wSelclientcode . "')";
	}
	if($wSelendusercode !="ALL"){ //ｴﾝﾄﾞﾕｰｻﾞ選択?
		$sql = $sql . " AND   ( " . $Const_DB_SCHEMA . "mtrunk.endusercode='" . $wSelendusercode . "')";
	}
	$sql = $sql . " AND   ( " . $Const_DB_SCHEMA . "dcall_datadump.talkieflg=0 AND  " . $Const_DB_SCHEMA . "dcall_datadump.fusyutsuflg=0 AND  " . $Const_DB_SCHEMA . "dcall_datadump.guidance1flg=0 AND  " . $Const_DB_SCHEMA . "dcall_datadump.guidance2flg=0)";
	$sql = $sql . " GROUP BY  " . $Const_DB_SCHEMA . "dcall_datadump.timezone";
	$sql = $sql . " ORDER BY  " . $Const_DB_SCHEMA . "dcall_datadump.timezone";
	$result = $conn->prepare($sql);
	$result->execute();
	while ($rs = $result->fetch(PDO::FETCH_ASSOC))
	{
		$sql2 = "UPDATE " . $Const_DB_SCHEMA . "wdeskhistory3timefj SET";
		$sql2 = $sql2 ." talksu=". $rs["recno_cnt"];
		$sql2 = $sql2 .",talktime=". $rs["talktime_sum"];
		$sql2 = $sql2 ." WHERE userid='". $Const_COMPANYCODE . $user."'";
		$sql2 = $sql2 ." AND   time=". $rs["timezone"];
		$result2 = $conn->query($sql2);
		$result2 = null;

	}
	$rs = null;
	$result = null;







	//ガイダンス数
	$sql = "SELECT  " . $Const_DB_SCHEMA . "dcall_datadump.timezone, Count( " . $Const_DB_SCHEMA . "dcall_datadump.recno) AS recno_cnt";
	$sql = $sql . " FROM (( " . $Const_DB_SCHEMA . "dcall_datadump LEFT JOIN  " . $Const_DB_SCHEMA . "mtrunk ON  " . $Const_DB_SCHEMA . "dcall_datadump.trunkno = " . $Const_DB_SCHEMA . "mtrunk.trunkno) LEFT JOIN  " . $Const_DB_SCHEMA . "mclient ON " . $Const_DB_SCHEMA . "mtrunk.clientcode =  " . $Const_DB_SCHEMA . "mclient.clientcode) LEFT JOIN  " . $Const_DB_SCHEMA . "menduser ON ( " . $Const_DB_SCHEMA . "mtrunk.clientcode =  " . $Const_DB_SCHEMA . "menduser.clientcode) AND ( " . $Const_DB_SCHEMA . "mtrunk.endusercode =  " . $Const_DB_SCHEMA . "menduser.endusercode)";
	$sql = $sql . " WHERE ( " . $Const_DB_SCHEMA . "dcall_datadump.calldate>=" . $wFrom . " And  " . $Const_DB_SCHEMA . "dcall_datadump.calldate<=" . $wTo . ")";
	if($wSelclientcode == "ALL"){
		$sql = $sql . " AND   ( " . $Const_DB_SCHEMA . "mtrunk.deskcode=" . $selDesk . ")";
	}else{
		$sql = $sql . " AND   ( " . $Const_DB_SCHEMA . "mtrunk.clientcode='" . $wSelclientcode . "')";
	}
	if($wSelendusercode !="ALL"){ //ｴﾝﾄﾞﾕｰｻﾞ選択?
		$sql = $sql . " AND   ( " . $Const_DB_SCHEMA . "mtrunk.endusercode='" . $wSelendusercode . "')";
	}
	$sql = $sql . " AND   ( " . $Const_DB_SCHEMA . "dcall_datadump.talkieflg>0)";
	$sql = $sql . " GROUP BY timezone";
	$result = $conn->prepare($sql);
	$result->execute();
	while ($rs = $result->fetch(PDO::FETCH_ASSOC))
	{
		$sql2 = "UPDATE " . $Const_DB_SCHEMA . "wdeskhistory3timefj SET";
		$sql2 = $sql2 ." guidancesu=". $rs["recno_cnt"];
		$sql2 = $sql2 ." WHERE userid='". $Const_COMPANYCODE . $user."'";
		$sql2 = $sql2 ." AND   time=". $rs["timezone"];
		$result2 = $conn->query($sql2);
		$result2 = null;
	}
	$rs = null;
	$result = null;

	//ガイダンス12秒数
	$sql = "SELECT  " . $Const_DB_SCHEMA . "dcall_datadump.timezone, Count( " . $Const_DB_SCHEMA . "dcall_datadump.recno) AS recno_cnt";
	$sql = $sql . " FROM (( " . $Const_DB_SCHEMA . "dcall_datadump LEFT JOIN  " . $Const_DB_SCHEMA . "mtrunk ON  " . $Const_DB_SCHEMA . "dcall_datadump.trunkno =  " . $Const_DB_SCHEMA . "mtrunk.trunkno) LEFT JOIN  " . $Const_DB_SCHEMA . "mclient ON  " . $Const_DB_SCHEMA . "mtrunk.clientcode =  " . $Const_DB_SCHEMA . "mclient.clientcode) LEFT JOIN  " . $Const_DB_SCHEMA . "menduser ON ( " . $Const_DB_SCHEMA . "mtrunk.clientcode =  " . $Const_DB_SCHEMA . "menduser.clientcode) AND ( " . $Const_DB_SCHEMA . "mtrunk.endusercode =  " . $Const_DB_SCHEMA . "menduser.endusercode)";
	$sql = $sql . " WHERE ( " . $Const_DB_SCHEMA . "dcall_datadump.calldate>=" . $wFrom . " And  " . $Const_DB_SCHEMA . "dcall_datadump.calldate<=" . $wTo . ")";
	if($wSelclientcode == "ALL"){
		$sql = $sql . " AND   ( " . $Const_DB_SCHEMA . "mtrunk.deskcode=" . $selDesk . ")";
	}else{
		$sql = $sql . " AND   ( " . $Const_DB_SCHEMA . "mtrunk.clientcode='" . $wSelclientcode . "')";
	}
	if($wSelendusercode !="ALL"){ //ｴﾝﾄﾞﾕｰｻﾞ選択?
		$sql = $sql . " AND   ( " . $Const_DB_SCHEMA . "mtrunk.endusercode='" . $wSelendusercode . "')";
	}
	$sql = $sql . " AND   (( " . $Const_DB_SCHEMA . "dcall_datadump.talkieflg=0 AND  " . $Const_DB_SCHEMA . "dcall_datadump.fusyutsuflg=0 AND  " . $Const_DB_SCHEMA . "dcall_datadump.guidance1flg=1)";
	$sql = $sql . " OR     ( " . $Const_DB_SCHEMA . "dcall_datadump.talkieflg=0 AND  " . $Const_DB_SCHEMA . "dcall_datadump.fusyutsuflg=0 AND  " . $Const_DB_SCHEMA . "dcall_datadump.guidance2flg=1))";
	$sql = $sql . " GROUP BY timezone";
	$result = $conn->prepare($sql);
	$result->execute();
	while ($rs = $result->fetch(PDO::FETCH_ASSOC))
	{
		$sql2 = "UPDATE " . $Const_DB_SCHEMA . "wdeskhistory3timefj SET";
		$sql2 = $sql2 ." guidance12su=". $rs["recno_cnt"];
		$sql2 = $sql2 ." WHERE userid='". $Const_COMPANYCODE . $user."'";
		$sql2 = $sql2 ." AND   time=". $rs["timezone"];
		$result2 = $conn->query($sql2);
		$result2 = null;
	}
	$rs = null;
	$result = null;

	//不出呼数
	$sql = "SELECT  " . $Const_DB_SCHEMA . "dcall_datadump.timezone, Count( " . $Const_DB_SCHEMA . "dcall_datadump.recno) AS recno_cnt";
	$sql = $sql . " FROM (( " . $Const_DB_SCHEMA . "dcall_datadump LEFT JOIN  " . $Const_DB_SCHEMA . "mtrunk ON  " . $Const_DB_SCHEMA . "dcall_datadump.trunkno =  " . $Const_DB_SCHEMA . "mtrunk.trunkno) LEFT JOIN  " . $Const_DB_SCHEMA . "mclient ON  " . $Const_DB_SCHEMA . "mtrunk.clientcode =  " . $Const_DB_SCHEMA . "mclient.clientcode) LEFT JOIN  " . $Const_DB_SCHEMA . "menduser ON ( " . $Const_DB_SCHEMA . "mtrunk.clientcode =  " . $Const_DB_SCHEMA . "menduser.clientcode) AND ( " . $Const_DB_SCHEMA . "mtrunk.endusercode =  " . $Const_DB_SCHEMA . "menduser.endusercode)";
	$sql = $sql . " WHERE ( " . $Const_DB_SCHEMA . "dcall_datadump.calldate>=" . $wFrom . " And  " . $Const_DB_SCHEMA . "dcall_datadump.calldate<=" . $wTo . ")";
	if($wSelclientcode == "ALL"){
		$sql = $sql . " AND   ( " . $Const_DB_SCHEMA . "mtrunk.deskcode=" . $selDesk . ")";
	}else{
		$sql = $sql . " AND   ( " . $Const_DB_SCHEMA . "mtrunk.clientcode='" . $wSelclientcode . "')";
	}
	if($wSelendusercode !="ALL"){ //ｴﾝﾄﾞﾕｰｻﾞ選択?
		$sql = $sql . " AND   ( " . $Const_DB_SCHEMA . "mtrunk.endusercode='" . $wSelendusercode . "')";
	}
	$sql = $sql . " AND   ( " . $Const_DB_SCHEMA . "dcall_datadump.talkieflg=0 AND  " . $Const_DB_SCHEMA . "dcall_datadump.fusyutsuflg=1)";
	$sql = $sql . " GROUP BY timezone";
	$result = $conn->prepare($sql);
	$result->execute();
	while ($rs = $result->fetch(PDO::FETCH_ASSOC))
	{
		$sql2 = "UPDATE " . $Const_DB_SCHEMA . "wdeskhistory3timefj SET";
		$sql2 = $sql2 ." fusyutsusu=". $rs["recno_cnt"];
		$sql2 = $sql2 ." WHERE userid='". $Const_COMPANYCODE . $user."'";
		$sql2 = $sql2 ." AND   time=". $rs["timezone"];
		$result2 = $conn->query($sql2);
		$result2 = null;
	}
	$rs = null;
	$result = null;

	////////////////
	// 小計行の挿入
	////////////////
	$sql = "SELECT subtotalflg, Sum(talksu) AS talksu_sum, Sum(talktime) AS talktime_sum, Sum(guidancesu) AS guidancesu_sum, Sum(guidance12su) AS guidance12su_sum, Sum(fusyutsusu) AS fusyutsusu_sum";
	$sql = $sql . " FROM  " . $Const_DB_SCHEMA . "wdeskhistory3timefj";
	$sql = $sql . " WHERE (userid='" . $Const_COMPANYCODE . $user . "')";
	$sql = $sql . " GROUP BY subtotalflg";
	$result = $conn->prepare($sql);
	$result->execute();
	while ($rs = $result->fetch(PDO::FETCH_ASSOC))
	{
		//小計行追加
		$sql2 = "INSERT INTO " . $Const_DB_SCHEMA . "wdeskhistory3timefj";
		$sql2 = $sql2. "(userid,time,subtotalflg,talksu,talktime,guidancesu,guidance12su,fusyutsusu,recflg)";
		$sql2 = $sql2. " VALUES(";
		$sql2 = $sql2. "'" . $Const_COMPANYCODE . $user."'";
		$sql2 = $sql2. "," . ($rs["subtotalflg"]+90); //時間(90+SubTotalFlg ==> 91とか92)
		$sql2 = $sql2. "," . $rs["subtotalflg"];
		$sql2 = $sql2. "," . $rs["talksu_sum"];
		$sql2 = $sql2. "," . $rs["talktime_sum"];
		$sql2 = $sql2. "," . $rs["guidancesu_sum"];
		$sql2 = $sql2. "," . $rs["guidance12su_sum"];
		$sql2 = $sql2. "," . $rs["fusyutsusu_sum"];
		$sql2 = $sql2. ",2" ; //小計
		$sql2 = $sql2. ")";
		$result2 = $conn->query($sql2);
		$result2 = null;
	}
	$rs = null;
	$result = null;

	////////////////
	// 合計行の挿入
	////////////////
	$sql = "SELECT Sum(talksu) AS talksu_sum, Sum(talktime) AS talktime_sum, Sum(guidancesu) AS guidancesu_sum, Sum(guidance12su) AS guidance12su_sum, Sum(fusyutsusu) AS fusyutsusu_sum";
	$sql = $sql . " FROM  " . $Const_DB_SCHEMA . "wdeskhistory3timefj";
	$sql = $sql . " WHERE (userid='" . $Const_COMPANYCODE . $user . "')";
	$sql = $sql . " AND   (recflg=0)";
	$result = $conn->prepare($sql);
	$result->execute();
	while ($rs = $result->fetch(PDO::FETCH_ASSOC))
	{
		//合計行追加
		$sql2 = "INSERT INTO " . $Const_DB_SCHEMA . "wdeskhistory3timefj";
		$sql2 = $sql2. "(userid,time,subtotalflg,talksu,talktime,guidancesu,guidance12su,fusyutsusu,recflg)";
		$sql2 = $sql2. " VALUES(";
		$sql2 = $sql2. "'" . $Const_COMPANYCODE . $user."'";
		$sql2 = $sql2. ",99"; //時間
		$sql2 = $sql2. ",9"; //subtotalflg
		$sql2 = $sql2. "," . $rs["talksu_sum"];
		$sql2 = $sql2. "," . $rs["talktime_sum"];
		$sql2 = $sql2. "," . $rs["guidancesu_sum"];
		$sql2 = $sql2. "," . $rs["guidance12su_sum"];
		$sql2 = $sql2. "," . $rs["fusyutsusu_sum"];
		$sql2 = $sql2. ",9" ; //合計
		$sql2 = $sql2. ")";
		$result2 = $conn->query($sql2);
		$result2 = null;

	}
	$rs = null;
	$result = null;


	return $recflg;


}

?>
