<?php
function PreMakeWkDataSF( $conn, $selDesk, $wSelclientcode, $wSelendusercode ) {

global $ENV_MODE;
global $Const_COMPANYCODE;
global $Const_HQ_NAME;
global $Const_DB_SCHEMA;
global $MOJI_ORG;
global $MOJI_NEW;

global $user;
global $fromY, $fromM, $fromD, $toY, $toM, $toD;
global $fromY2, $fromM2, $fromD2;



		//日別件数のカウント→wdeskhistory2day
		//時間帯別件数のカウント→salesforce_wdeskhistory3timefj


		//Salesforce上は、実際の時間の9時間前で記録されている為
		$wFromDate9 = $fromY . "/" . $fromM . "/" . $fromD . " 00:00:00";
		$wFromDate9 = date("Y-m-d H:i:s",strtotime($wFromDate9 . "-9 hour"));

		$wFromDate92 = $fromY2 . "/" . $fromM2 . "/" . $fromD2 . " 00:00:00";
		$wFromDate92 = date("Y-m-d H:i:s",strtotime($wFromDate92 . "-9 hour"));

		$wTo9 = $toY . "/" . $toM . "/" . $toD . " 00:00:00";
		$wTo9 = date("Y-m-d H:i:s",strtotime($wTo9 . "+1 day"));
		$wTo9 = date("Y-m-d H:i:s",strtotime($wTo9 . "-9 hour"));


		$wcnt = 0;
		$sf_in_cnt=0;
		$sql = "SELECT * From " . $Const_DB_SCHEMA . "case";
		$sql = $sql . " WHERE (" . $Const_DB_SCHEMA . "case.receiotdatetime__c>='" . $wFromDate9 . "'";
		$sql = $sql . " AND    " . $Const_DB_SCHEMA . "case.receiotdatetime__c<='" . $wTo9 . "')";
		$sql = $sql . "   AND (" . $Const_DB_SCHEMA . "case.hq_name__c='" . $Const_HQ_NAME . "')";//MYCONST
		$sql = $sql . " ORDER BY " . $Const_DB_SCHEMA  . "case.casenumber";
		if($ENV_MODE == 1){
			$sql = mb_convert_encoding( $sql, $MOJI_ORG, $MOJI_NEW ); //ここは日本語が混じっているのでUTF-8へ
		}
		$result = $conn->prepare($sql);
		$result->execute();
		while ($rs = $result->fetch(PDO::FETCH_ASSOC))
		{
			//日別件数の更新
			$receiotdatetime__c = date("Y-m-d H:i:s",strtotime($rs["receiotdatetime__c"] . "+9 hour")); //ここで9時間足す
			$createdateYMD = substr($receiotdatetime__c,0,10);
			$createdateYMD2 = str_replace("-", "", $createdateYMD);//ｽﾗｯｼｭを外す

			//時間帯別件数の更新(小計、合計は計算不要)
			$createdateH = substr($receiotdatetime__c,11,2);
			$createdateH +=0;

			$sql2 = "UPDATE " . $Const_DB_SCHEMA . "wdeskhistory3timefj SET";
			$sql2 = $sql2 ." sf_in_cnt=sf_in_cnt+1";
			$sql2 = $sql2 ." WHERE userid='". $Const_COMPANYCODE . $user."'";
			$sql2 = $sql2 ." AND   time=". $createdateH;
			$result2 = $conn->query($sql2);
			$result2 = null;

			$wcnt =$wcnt+1;

		}
		$result = null;


		$sf_in_cnt=0;
		$sql = "SELECT * From " . $Const_DB_SCHEMA . "case";
		$sql = $sql . " WHERE (" . $Const_DB_SCHEMA . "case.receiotdatetime__c>='" . $wFromDate92 . "'";
		$sql = $sql . " AND    " . $Const_DB_SCHEMA . "case.receiotdatetime__c<='" . $wTo9 . "')";
		$sql = $sql . "   AND (" . $Const_DB_SCHEMA . "case.hq_name__c='" . $Const_HQ_NAME . "')";//MYCONST
		$sql = $sql . " ORDER BY " . $Const_DB_SCHEMA  . "case.casenumber";
		if($ENV_MODE == 1){
			$sql = mb_convert_encoding( $sql, $MOJI_ORG, $MOJI_NEW ); //ここは日本語が混じっているのでUTF-8へ
		}
		$result = $conn->prepare($sql);
		$result->execute();
		while ($rs = $result->fetch(PDO::FETCH_ASSOC))
		{

			//日別件数の更新
			$receiotdatetime__c = date("Y-m-d H:i:s",strtotime($rs["receiotdatetime__c"] . "+9 hour")); //ここで9時間足す
			$createdateYMD = substr($receiotdatetime__c,0,10);
			$createdateYMD2 = str_replace("-", "", $createdateYMD);//ｽﾗｯｼｭを外す

			$sql2 = "UPDATE " . $Const_DB_SCHEMA . "wdeskhistory2day SET";
			$sql2 = $sql2 ." sf_in_cnt=sf_in_cnt+1";
			$sql2 = $sql2 ." WHERE userid='". $Const_COMPANYCODE . $user."'";
			$sql2 = $sql2 ." AND   hiduke=". $createdateYMD2;
			$result2 = $conn->query($sql2);
			$result2 = null;

			$wcnt =$wcnt+1;

		}
		$result = null;




	return $wcnt;

}
//////////////////////////////////////////
//<説明>
//  過去全ての未完了件数を取得する(期間指定関係なし)
//////////////////////////////////////////
function Mikanryo($conn) {

global $ENV_MODE;
global $Const_HQ_NAME;
global $Const_DB_SCHEMA;
global $MOJI_ORG;
global $MOJI_NEW;

	$mikensu = 0;

	$sql = "SELECT Count(" . $Const_DB_SCHEMA . "case.id) AS id_cnt FROM " . $Const_DB_SCHEMA . "case";
	$sql = $sql . " WHERE ((" . $Const_DB_SCHEMA . "case.closeddate) Is Null)";
	$sql = $sql . " AND   ( " . $Const_DB_SCHEMA . "case.hq_name__c='" . $Const_HQ_NAME . "')";
	if($ENV_MODE == 1){
		$sql = mb_convert_encoding( $sql, $MOJI_ORG, $MOJI_NEW ); //ここは日本語が混じっているのでUTF-8へ
	}
	$result = $conn->prepare($sql);
	$result->execute();
	while ($rs = $result->fetch(PDO::FETCH_ASSOC))
	{
		$mikensu = $rs["id_cnt"];
	}
	$result = null;

	return $mikensu;


}
function PreMakeWkDataSF6Month( $conn, $selDesk, $wSelclientcode, $wSelendusercode ) {

global $ENV_MODE;
global $Const_COMPANYCODE;
global $Const_HQ_NAME;
global $Const_DB_SCHEMA;
global $MOJI_ORG;
global $MOJI_NEW;

global $user;
global $fromY, $fromM, $fromD, $toY, $toM, $toD;



		//件数初期化
		$kensu0 = 0; $kensu1 = 0; $kensu2 = 0; $kensu3 = 0; $kensu4 = 0; $kensu5 = 0; $kensu6 = 0;


		//Salesforce上は、実際の時間の9時間前で記録されている為
		$wFromDate9 = $fromY . "/" . $fromM . "/" . $fromD . " 00:00:00";
		$wFromDate9 = date("Y-m-d H:i:s",strtotime($wFromDate9 . "-9 hour"));

		$wTo9 = $toY . "/" . $toM . "/" . $toD . " 00:00:00";
		$wTo9 = date("Y-m-d H:i:s",strtotime($wTo9 . "+1 day"));
		$wTo9 = date("Y-m-d H:i:s",strtotime($wTo9 . "-9 hour"));


		//////////////////////////
		//当月(0ヶ月前)
		//////////////////////////
		$sql = "SELECT Count(" . $Const_DB_SCHEMA . "case.casenumber) AS casenumber_cnt FROM " . $Const_DB_SCHEMA . "case";
		$sql = $sql . " WHERE (" . $Const_DB_SCHEMA . "case.receiotdatetime__c>='" . $wFromDate9 . "'";
		$sql = $sql . " AND    " . $Const_DB_SCHEMA . "case.receiotdatetime__c<='" . $wTo9 . "')";
		$sql = $sql . " AND   (" . $Const_DB_SCHEMA . "case.hq_name__c='" . $Const_HQ_NAME . "')";//MYCONST
		if($ENV_MODE == 1){
			$sql = mb_convert_encoding( $sql, $MOJI_ORG, $MOJI_NEW ); //ここは日本語が混じっているのでUTF-8へ
		}
		$result = $conn->prepare($sql);
		$result->execute();
		if ($rs = $result->fetch(PDO::FETCH_ASSOC)){
			$kensu0 = $rs["casenumber_cnt"];
		}
		$result= null;

		//////////////////////////
		//1ヶ月前
		//////////////////////////
		$wFromDate9 = date("Y-m-d H:i:s",strtotime($wFromDate9 . "-1 month"));
		$wTo9 = date("Y-m-d H:i:s",strtotime($wFromDate9 . "+1 month"));
		$wTo9 = date("Y-m-d H:i:s",strtotime($wTo9 . "-1 day"));

		$sql = "SELECT Count(" . $Const_DB_SCHEMA . "case.casenumber) AS casenumber_cnt FROM " . $Const_DB_SCHEMA . "case";
		$sql = $sql . " WHERE (" . $Const_DB_SCHEMA . "case.receiotdatetime__c>='" . $wFromDate9 . "'";
		$sql = $sql . " AND    " . $Const_DB_SCHEMA . "case.receiotdatetime__c<='" . $wTo9 . "')";
		$sql = $sql . " AND   (" . $Const_DB_SCHEMA . "case.hq_name__c='" . $Const_HQ_NAME . "')";//MYCONST
		if($ENV_MODE == 1){
			$sql = mb_convert_encoding( $sql, $MOJI_ORG, $MOJI_NEW ); //ここは日本語が混じっているのでUTF-8へ
		}
		$result = $conn->prepare($sql);
		$result->execute();
		if ($rs = $result->fetch(PDO::FETCH_ASSOC)){
			$kensu1 = $rs["casenumber_cnt"];
		}
		$result= null;


		//////////////////////////
		//2ヶ月前
		//////////////////////////
		$wFromDate9 = date("Y-m-d H:i:s",strtotime($wFromDate9 . "-1 month"));
		$wTo9 = date("Y-m-d H:i:s",strtotime($wFromDate9 . "+1 month"));
		$wTo9 = date("Y-m-d H:i:s",strtotime($wTo9 . "-1 day"));

		$sql = "SELECT Count(" . $Const_DB_SCHEMA . "case.casenumber) AS casenumber_cnt FROM " . $Const_DB_SCHEMA . "case";
		$sql = $sql . " WHERE (" . $Const_DB_SCHEMA . "case.receiotdatetime__c>='" . $wFromDate9 . "'";
		$sql = $sql . " AND    " . $Const_DB_SCHEMA . "case.receiotdatetime__c<='" . $wTo9 . "')";
		$sql = $sql . " AND   (" . $Const_DB_SCHEMA . "case.hq_name__c='" . $Const_HQ_NAME . "')";//MYCONST
		if($ENV_MODE == 1){
			$sql = mb_convert_encoding( $sql, $MOJI_ORG, $MOJI_NEW ); //ここは日本語が混じっているのでUTF-8へ
		}
		$result = $conn->prepare($sql);
		$result->execute();
		if ($rs = $result->fetch(PDO::FETCH_ASSOC)){
			$kensu2 = $rs["casenumber_cnt"];
		}
		$result= null;

		//////////////////////////
		//3ヶ月前
		//////////////////////////
		$wFromDate9 = date("Y-m-d H:i:s",strtotime($wFromDate9 . "-1 month"));
		$wTo9 = date("Y-m-d H:i:s",strtotime($wFromDate9 . "+1 month"));
		$wTo9 = date("Y-m-d H:i:s",strtotime($wTo9 . "-1 day"));

		$sql = "SELECT Count(" . $Const_DB_SCHEMA . "case.casenumber) AS casenumber_cnt FROM " . $Const_DB_SCHEMA . "case";
		$sql = $sql . " WHERE (" . $Const_DB_SCHEMA . "case.receiotdatetime__c>='" . $wFromDate9 . "'";
		$sql = $sql . " AND    " . $Const_DB_SCHEMA . "case.receiotdatetime__c<='" . $wTo9 . "')";
		$sql = $sql . " AND   (" . $Const_DB_SCHEMA . "case.hq_name__c='" . $Const_HQ_NAME . "')";//MYCONST
		if($ENV_MODE == 1){
			$sql = mb_convert_encoding( $sql, $MOJI_ORG, $MOJI_NEW ); //ここは日本語が混じっているのでUTF-8へ
		}
		$result = $conn->prepare($sql);
		$result->execute();
		if ($rs = $result->fetch(PDO::FETCH_ASSOC)){
			$kensu3 = $rs["casenumber_cnt"];
		}
		$result= null;


		//////////////////////////
		//4ヶ月前
		//////////////////////////
		$wFromDate9 = date("Y-m-d H:i:s",strtotime($wFromDate9 . "-1 month"));
		$wTo9 = date("Y-m-d H:i:s",strtotime($wFromDate9 . "+1 month"));
		$wTo9 = date("Y-m-d H:i:s",strtotime($wTo9 . "-1 day"));

		$sql = "SELECT Count(" . $Const_DB_SCHEMA . "case.casenumber) AS casenumber_cnt FROM " . $Const_DB_SCHEMA . "case";
		$sql = $sql . " WHERE (" . $Const_DB_SCHEMA . "case.receiotdatetime__c>='" . $wFromDate9 . "'";
		$sql = $sql . " AND    " . $Const_DB_SCHEMA . "case.receiotdatetime__c<='" . $wTo9 . "')";
		$sql = $sql . " AND   (" . $Const_DB_SCHEMA . "case.hq_name__c='" . $Const_HQ_NAME . "')";//MYCONST
		if($ENV_MODE == 1){
			$sql = mb_convert_encoding( $sql, $MOJI_ORG, $MOJI_NEW ); //ここは日本語が混じっているのでUTF-8へ
		}
		$result = $conn->prepare($sql);
		$result->execute();
		if ($rs = $result->fetch(PDO::FETCH_ASSOC)){
			$kensu4 = $rs["casenumber_cnt"];
		}
		$result= null;


		//////////////////////////
		//5ヶ月前
		//////////////////////////
		$wFromDate9 = date("Y-m-d H:i:s",strtotime($wFromDate9 . "-1 month"));
		$wTo9 = date("Y-m-d H:i:s",strtotime($wFromDate9 . "+1 month"));
		$wTo9 = date("Y-m-d H:i:s",strtotime($wTo9 . "-1 day"));

		$sql = "SELECT Count(" . $Const_DB_SCHEMA . "case.casenumber) AS casenumber_cnt FROM " . $Const_DB_SCHEMA . "case";
		$sql = $sql . " WHERE (" . $Const_DB_SCHEMA . "case.receiotdatetime__c>='" . $wFromDate9 . "'";
		$sql = $sql . " AND    " . $Const_DB_SCHEMA . "case.receiotdatetime__c<='" . $wTo9 . "')";
		$sql = $sql . " AND   (" . $Const_DB_SCHEMA . "case.hq_name__c='" . $Const_HQ_NAME . "')";//MYCONST
		if($ENV_MODE == 1){
			$sql = mb_convert_encoding( $sql, $MOJI_ORG, $MOJI_NEW ); //ここは日本語が混じっているのでUTF-8へ
		}
		$result = $conn->prepare($sql);
		$result->execute();
		if ($rs = $result->fetch(PDO::FETCH_ASSOC)){
			$kensu5 = $rs["casenumber_cnt"];
		}
		$result= null;

		//////////////////////////
		//6ヶ月前
		//////////////////////////
		$wFromDate9 = date("Y-m-d H:i:s",strtotime($wFromDate9 . "-1 month"));
		$wTo9 = date("Y-m-d H:i:s",strtotime($wFromDate9 . "+1 month"));
		$wTo9 = date("Y-m-d H:i:s",strtotime($wTo9 . "-1 day"));

		$sql = "SELECT Count(" . $Const_DB_SCHEMA . "case.casenumber) AS casenumber_cnt FROM " . $Const_DB_SCHEMA . "case";
		$sql = $sql . " WHERE (" . $Const_DB_SCHEMA . "case.receiotdatetime__c>='" . $wFromDate9 . "'";
		$sql = $sql . " AND    " . $Const_DB_SCHEMA . "case.receiotdatetime__c<='" . $wTo9 . "')";
		$sql = $sql . " AND   (" . $Const_DB_SCHEMA . "case.hq_name__c='" . $Const_HQ_NAME . "')";//MYCONST
		if($ENV_MODE == 1){
			$sql = mb_convert_encoding( $sql, $MOJI_ORG, $MOJI_NEW ); //ここは日本語が混じっているのでUTF-8へ
		}
		$result = $conn->prepare($sql);
		$result->execute();
		if ($rs = $result->fetch(PDO::FETCH_ASSOC)){
			$kensu6 = $rs["casenumber_cnt"];
		}
		$result= null;


		//前削除(自分)
		$sql = "DELETE FROM " . $Const_DB_SCHEMA . "wcasehistory1";
		$sql = $sql." WHERE userid ='". $Const_COMPANYCODE . $user."'";//自分
		$result = $conn->prepare($sql);
		$result->execute();
		$result = null;


		$sql2 = "INSERT INTO " . $Const_DB_SCHEMA . "wcasehistory1";
		$sql2 = $sql2. "(userid, month, sf_in_cnt)";
		$sql2 = $sql2. " VALUES(";
		$sql2 = $sql2. "'" . $Const_COMPANYCODE . $user."'";
		$sql2 = $sql2. ",0";
		$sql2 = $sql2. "," . $kensu0;
		$sql2 = $sql2. ")";
		$result = $conn->prepare($sql2);
		$result->execute();
		$result = null;

		$sql2 = "INSERT INTO " . $Const_DB_SCHEMA . "wcasehistory1";
		$sql2 = $sql2. "(userid, month, sf_in_cnt)";
		$sql2 = $sql2. " VALUES(";
		$sql2 = $sql2. "'" . $Const_COMPANYCODE . $user."'";
		$sql2 = $sql2. ",1";
		$sql2 = $sql2. "," . $kensu1;
		$sql2 = $sql2. ")";
		$result = $conn->prepare($sql2);
		$result->execute();
		$result = null;

		$sql2 = "INSERT INTO " . $Const_DB_SCHEMA . "wcasehistory1";
		$sql2 = $sql2. "(userid, month, sf_in_cnt)";
		$sql2 = $sql2. " VALUES(";
		$sql2 = $sql2. "'" . $Const_COMPANYCODE . $user."'";
		$sql2 = $sql2. ",2";
		$sql2 = $sql2. "," . $kensu2;
		$sql2 = $sql2. ")";
		$result = $conn->prepare($sql2);
		$result->execute();
		$result = null;

		$sql2 = "INSERT INTO " . $Const_DB_SCHEMA . "wcasehistory1";
		$sql2 = $sql2. "(userid, month, sf_in_cnt)";
		$sql2 = $sql2. " VALUES(";
		$sql2 = $sql2. "'" . $Const_COMPANYCODE . $user."'";
		$sql2 = $sql2. ",3";
		$sql2 = $sql2. "," . $kensu3;
		$sql2 = $sql2. ")";
		$result = $conn->prepare($sql2);
		$result->execute();
		$result = null;

		$sql2 = "INSERT INTO " . $Const_DB_SCHEMA . "wcasehistory1";
		$sql2 = $sql2. "(userid, month, sf_in_cnt)";
		$sql2 = $sql2. " VALUES(";
		$sql2 = $sql2. "'" . $Const_COMPANYCODE . $user."'";
		$sql2 = $sql2. ",4";
		$sql2 = $sql2. "," . $kensu4;
		$sql2 = $sql2. ")";
		$result = $conn->prepare($sql2);
		$result->execute();
		$result = null;

		$sql2 = "INSERT INTO " . $Const_DB_SCHEMA . "wcasehistory1";
		$sql2 = $sql2. "(userid, month, sf_in_cnt)";
		$sql2 = $sql2. " VALUES(";
		$sql2 = $sql2. "'" . $Const_COMPANYCODE . $user."'";
		$sql2 = $sql2. ",5";
		$sql2 = $sql2. "," . $kensu5;
		$sql2 = $sql2. ")";
		$result = $conn->prepare($sql2);
		$result->execute();
		$result = null;

		$sql2 = "INSERT INTO " . $Const_DB_SCHEMA . "wcasehistory1";
		$sql2 = $sql2. "(userid, month, sf_in_cnt)";
		$sql2 = $sql2. " VALUES(";
		$sql2 = $sql2. "'" . $Const_COMPANYCODE . $user."'";
		$sql2 = $sql2. ",6";
		$sql2 = $sql2. "," . $kensu6;
		$sql2 = $sql2. ")";
		$result = $conn->prepare($sql2);
		$result->execute();
		$result = null;



		return $kensu0;

}

?>
