<?php
/**
 *	migration.php :
 *  Author : Kurt Van Nieuwenhuyze <kurt@balancecoding.nl>
 *  Date : 28 nov. 2019
 *  Project : 
 * 	 Package : 
 *  Version : 
 * 
 */

error_reporting(E_ALL ^ E_DEPRECATED);
ini_set("display_errors", 1);


require_once("../../iquest/classes/BCdatabase/BCDB.class.php");
require_once("../../iquest/classes/BCEncryption/BCEncryption.class.php");
require_once("../../iquest/classes/BCEncryption/BCEncryptionOSSL.class.php");

/**
define("DBHOST","localhost");
define("DBPORT",3306);
define("DBNAME","iquest");
define("DBUSER","root");
define("DBPASS","");**/


define("DBHOST","db.mareis.nl");
define("DBPORT",3306);
define("DBNAME","md205816db445922");
define("DBUSER","md205816db445922");
define("DBPASS","Mareis#1978@iquest!");

/**
 * Secret Keys
 */
define("KEY", "648744c598d01eb06B7AA7471D75796B");
define("Vector", "44xieHnurWq7e9gK8Jjqf+EvbHHVp2UKleld72ldXUo=");

echo "<center><h1>Migration Mycrypt to Openssl : users</h1></center><hr>";

//Connect to DB
$oDB = new BCdatabase_BCDB();
$oDB->setDBName(DBNAME);
$oDB->setHost(DBHOST);
$oDB->setPort(DBPORT);
$oDB->setUser(DBUSER);
$oDB->setPass(DBPASS);
$oDB->connect();

//Init Crypt
$oCrypt = new BCEncryption();
$oCrypt->setSecretKey(KEY);
$oCrypt->setVector(Vector);

//Init OpenSSL Crypt
$oCryptOSSL = new BCEncryptionOSSL();
//echo "KEY : ".$oCryptOSSL->createSecretKey();
//echo "<br>";
//echo "VECTOR : ".$oCryptOSSL->createVector();
$oCryptOSSL->setSecretKey("YmE1NDczMzAxNjUxYTIxNjczMWQ2ZTVjMzlkOTU5ODZiY2EzY2M3YQ==");
$oCryptOSSL->setVector("K+2FZ9ACOpHDRyyHHUNEHQ==");


$aCompanys = getUsers($oDB,$oCrypt,$oCryptOSSL);
echo "<pre>";
print_r($aCompanys);
echo "</pre>";
echo "<pre>";
saveUsers($oDB, $aCompanys);
echo "</pre>";
echo "<pre>";
print_r(checkUsers($oDB,$oCrypt,$oCryptOSSL));
echo "</pre>";

/**
 * Get Company Rows
 * @param object $p_oDB
 * @return array
 */
function getUsers($p_oDB,$p_oCrypt,$p_oCryptOSSL)
{
    $sSQL = "SELECT userID,userScreenname,userPass FROM users ";
    $oQuery = $p_oDB->prepare($sSQL);
    $oQuery->execute();
    $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
    
    $iNrRecords = count($aResult);
    
    for($i=0;$i<$iNrRecords;$i++)
    {
       $aResult[$i]["userScreenname-encr"] = $aResult[$i]["userScreenname"];
       $aResult[$i]["userScreenname"] = $p_oCrypt->decrypt($aResult[$i]["userScreenname"]);
       
       $aResult[$i]["userPass-encr"] = $aResult[$i]["userPass"];
       $aResult[$i]["userPass"] = $p_oCrypt->decrypt($aResult[$i]["userPass"]);
       
       $aResult[$i]["userScreenname-decrypt"] = $p_oCryptOSSL->encrypt($p_oCrypt->decrypt($aResult[$i]["userScreenname"]));
       $aResult[$i]["userPass-decrypt"] = $p_oCryptOSSL->encrypt($p_oCrypt->decrypt($aResult[$i]["userPass"]));
    }
    
    return $aResult;
}

function saveUsers($p_oDB,$p_aUsers)
{
    
    $iCount = count($p_aUsers);
    $iRows = 0;
    for($i=0;$i<$iCount;$i++)
    {
       
        $ID =  $p_aUsers[$i]["userID"];
        $sName =  $p_aUsers[$i]["userScreenname-decrypt"];
        $sPass =  $p_aUsers[$i]["userPass-decrypt"];
    
        $sSQL = "UPDATE users SET userScreenname=:name,userPass=:pass WHERE userID=:ID";
        $oQuery = $p_oDB->prepare($sSQL);
        $oQuery->bindParam(":ID", $ID);
        $oQuery->bindParam(":name", $sName);
        $oQuery->bindParam(":pass", $sPass);
        $oQuery->execute();
        
        $iRows= $iRows+$oQuery->rowCount();
    }
}

/**
 * check Company Rows
 * @param object $p_oDB
 * @return array
 */
function checkUsers($p_oDB,$p_oCrypt,$p_oCryptOSSL)
{
    $sSQL = "SELECT userID,userScreenname,userPass FROM users ";
    $oQuery = $p_oDB->prepare($sSQL);
    $oQuery->execute();
    $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
    
    $iNrRecords = count($aResult);
    
    for($i=0;$i<$iNrRecords;$i++)
    {
        $aResult[$i]["userScreenname-ecrypt"] = $aResult[$i]["userScreenname"];
        $aResult[$i]["userScreenname"] = $p_oCryptOSSL->decrypt($aResult[$i]["userScreenname"]);
        
        $aResult[$i]["userPass-ecrypt"] = $aResult[$i]["userPass"];
        $aResult[$i]["userPass"] = $p_oCryptOSSL->decrypt($aResult[$i]["userPass"]);
    }
    
    return $aResult;
}
