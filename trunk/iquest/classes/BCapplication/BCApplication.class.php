<?php

abstract class BCApplication_BCApplication
{
	
	public static function run()
	{
		try 
			{
			   
				/**
				 * Instantiate the requesthandler And set important settings, its a singleton so we can use the same instance everywhere
				 */
				$oRequest = BChttprequest_BCHttpRequestHandler::getInstance();
				$oRequest->setWebhost(HOST);
				$oRequest->extractParameters(APPLICATIONPATH);
				$aRoute = self::dispatch($oRequest);
				
				//print_r($aRoute);
				
				//Setting the Database
				$oDB = new BCdatabase_BCDB();
				$oDB->setDBName(DBNAME);
				$oDB->setHost(DBHOST);
				$oDB->setPort(DBPORT);
				$oDB->setUser(DBUSER);
				$oDB->setPass(DBPASS);
				$oDB->connect();
				
				$oCrypt = new BCEncryption();
				$oCrypt->setSecretKey(KEY);
				$oCrypt->setVector(Vector);
				
				//There 4 modules in the system so we check wich module is requested, so we can do some pre actions such as checklogin
				if($aRoute["modulename"] == "surveypanel")
				{
				    //Its a request for the admin / surveypannel
				   if(!login_loginHelper::isLoggedIn("Admin"))
				   {    
				      $oRequest->redirect(HOST."/".APPLICATIONPATH."surveypanel/login");
				      
				   }
				    
				        //Checklogin & IP Adres
				}elseif($aRoute["modulename"] == "clientsurvey")
				    {
				        //Its a request for the clientsurvey
				        //Its required to acces the application the firsttime with the unique url
				        if(!login_loginHelper::isLoggedIn("Client"))
				        {
				           // echo "bla bla".$oRequest->getParaMeter(1);exit();
				            $oRequest->redirect(HOST."/".APPLICATIONPATH."iquestclient/login/".$oRequest->getParaMeter(1)."/".$oRequest->getParaMeter(2));
				        }
				        
				    }
				    
				//The modules Login & error doesn't neet a preaction 
				// So execute the request
				    $sControler = $aRoute["controler"];
				    $sAction = $aRoute["action"];
				    
				    //Do some actions
				    $oControler = new $sControler($oRequest, $oDB,$oCrypt);
				    $oControler->$sAction();
				   
				    //Creating the view
				    $oView = new BCView_BCView();
				    $oView->buildScreen($aRoute["modulename"],$oControler,$aRoute["view"]);
				    $oView->renderModuleView($aRoute["modulename"]);
				    $oView->renderView();
				
		}catch(exception $e)
				{
					//echo $e->getMessage();
					throw $e;
				}
		
	}
	
	/**
	 * 
	 * @param object $p_oRequest
	 * @return array with current route
	 */
	private static function dispatch($p_oRequest)
	{
		$aRoute = array();
		$aParameters = $p_oRequest->getParameterArray();
		
		//Include the route File
		include("../iquest/config/routes.php");
		
		if(count($aParameters) == 0)
		{
			$aRoute = $aRoutes["default"];
		}elseif(count($aParameters) == 1)
			{
				if(isset($aRoutes[$p_oRequest->getParaMeter(0)]))
				{
					$aRoute = $aRoutes[$p_oRequest->getParaMeter(0)];
				}else 
					{
						$aRoute = $aRoutes["error/404"];
					}
			}elseif(count($aParameters) >=2)
				{
					if(isset($aRoutes[$p_oRequest->getParaMeter(0)."/".$p_oRequest->getParaMeter(1)]))
					{
						$aRoute = $aRoutes[$p_oRequest->getParaMeter(0)."/".$p_oRequest->getParaMeter(1)];
					}elseif(isset($aRoutes[$p_oRequest->getParaMeter(0)]))
						{
							$aRoute = $aRoutes[$p_oRequest->getParaMeter(0)];
						}else 
							{
								$aRoute = $aRoutes["error/404"];
							}
				}
		
		return $aRoute;
	}
	
	
}