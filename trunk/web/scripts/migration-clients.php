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

echo "<center><h1>Migration Mycrypt to Openssl : clients</h1></center><hr>";

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


$aCompanys = getClients($oDB,$oCrypt,$oCryptOSSL);
echo "<pre>";
print_r($aCompanys);
echo "</pre>";
echo "<pre>";
saveClients($oDB, $aCompanys);
echo "</pre>";
echo "<pre>";
print_r(checkClients($oDB,$oCrypt,$oCryptOSSL));
echo "</pre>";

/**
 * Get Company Rows
 * @param object $p_oDB
 * @return array
 */
function getClients($p_oDB,$p_oCrypt,$p_oCryptOSSL)
{
    $sSQL = "SELECT clientID,gender,firstName,lastName,email,dateOfBirth FROM clients ";
    $oQuery = $p_oDB->prepare($sSQL);
    $oQuery->execute();
    $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
    
    $iNrRecords = count($aResult);
    
    for($i=0;$i<$iNrRecords;$i++)
    {
        $aResult[$i]["gender-encr"] = $aResult[$i]["gender"];
        $aResult[$i]["gender"] = $p_oCrypt->decrypt($aResult[$i]["gender"]);
        $aResult[$i]["gender-decrypt"] = $p_oCryptOSSL->encrypt($p_oCrypt->decrypt($aResult[$i]["gender"]));
        
        $aResult[$i]["firstName-encr"] = $aResult[$i]["firstName"];
        $aResult[$i]["firstName"] = $p_oCrypt->decrypt($aResult[$i]["firstName"]);
        $aResult[$i]["firstName-decrypt"] = $p_oCryptOSSL->encrypt($p_oCrypt->decrypt($aResult[$i]["firstName"]));
        
        $aResult[$i]["lastName-encr"] = $aResult[$i]["lastName"];
        $aResult[$i]["lastName"] = $p_oCrypt->decrypt($aResult[$i]["lastName"]);
        $aResult[$i]["lastName-decrypt"] = $p_oCryptOSSL->encrypt($p_oCrypt->decrypt($aResult[$i]["lastName"]));
        
        $aResult[$i]["email-encr"] = $aResult[$i]["email"];
        $aResult[$i]["email"] = $p_oCrypt->decrypt($aResult[$i]["email"]);
        $aResult[$i]["email-decrypt"] = $p_oCryptOSSL->encrypt($p_oCrypt->decrypt($aResult[$i]["email"]));
        
        $aResult[$i]["dateOfBirth-encr"] = $aResult[$i]["dateOfBirth"];
        $aResult[$i]["dateOfBirth"] = $p_oCrypt->decrypt($aResult[$i]["dateOfBirth"]);
        $aResult[$i]["dateOfBirth-decrypt"] = $p_oCryptOSSL->encrypt($p_oCrypt->decrypt($aResult[$i]["dateOfBirth"]));
       
    }
    
    return $aResult;
}

function saveClients($p_oDB,$p_aUsers)
{
    
    $iCount = count($p_aUsers);
    $iRows = 0;
    for($i=0;$i<$iCount;$i++)
    {
       
        $ID =  $p_aUsers[$i]["clientID"];
        $sGender =  $p_aUsers[$i]["gender-decrypt"];
        $sName =  $p_aUsers[$i]["firstName-decrypt"];
        $sLast =  $p_aUsers[$i]["lastName-decrypt"];
        $sMail=  $p_aUsers[$i]["email-decrypt"];
        $sDate =  $p_aUsers[$i]["dateOfBirth-decrypt"];
    
        $sSQL = "UPDATE clients SET gender=:gender,firstName=:name,lastName=:lastname,email=:mail,dateOfBirth=:date WHERE clientID=:ID";
        $oQuery = $p_oDB->prepare($sSQL);
        $oQuery->bindParam(":ID", $ID);
        $oQuery->bindParam(":gender", $sGender);
        $oQuery->bindParam(":name", $sName);
        $oQuery->bindParam(":lastname", $sLast);
        $oQuery->bindParam(":mail", $sMail);
        $oQuery->bindParam(":date", $sDate);
        $oQuery->execute();
        
        $iRows= $iRows+$oQuery->rowCount();
    }
}

/**
 * check Company Rows
 * @param object $p_oDB
 * @return array
 */
function checkClients($p_oDB,$p_oCrypt,$p_oCryptOSSL)
{
    $sSQL = "SELECT clientID,gender,firstName,lastName,email,dateOfBirth FROM clients ";
    $oQuery = $p_oDB->prepare($sSQL);
    $oQuery->execute();
    $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
    
    $iNrRecords = count($aResult);
    
    for($i=0;$i<$iNrRecords;$i++)
    {
        $aResult[$i]["gender-ecrypt"] = $aResult[$i]["gender"];
        $aResult[$i]["gender"] = $p_oCryptOSSL->decrypt($aResult[$i]["gender"]);
        
        $aResult[$i]["firstName-ecrypt"] = $aResult[$i]["firstName"];
        $aResult[$i]["firstName"] = $p_oCryptOSSL->decrypt($aResult[$i]["firstName"]);
        
        $aResult[$i]["lastName-ecrypt"] = $aResult[$i]["lastName"];
        $aResult[$i]["lastName"] = $p_oCryptOSSL->decrypt($aResult[$i]["lastName"]);
        
        $aResult[$i]["email-ecrypt"] = $aResult[$i]["email"];
        $aResult[$i]["email"] = $p_oCryptOSSL->decrypt($aResult[$i]["email"]);
        
        $aResult[$i]["dateOfBirth-ecrypt"] = $aResult[$i]["dateOfBirth"];
        $aResult[$i]["dateOfBirth"] = $p_oCryptOSSL->decrypt($aResult[$i]["dateOfBirth"]);
        
        
    }
    
    return $aResult;
}
