<?php

abstract class BCApplication_BCApplication
{
	
	public static function run()
	{
		try 
			{
				
				/**
				 * Instantiate the requesthandler And set important settings, its a singleton so we can the same instance everywhere
				 */
				$oRequest = BChttprequest_BCHttpRequestHandler::getInstance();
				$oRequest->setWebhost(HOST);
				$oRequest->extractParameters(APPLICATIONPATH);
				
				//Setting the Database
				$oDB = new BCdatabase_BCDB();
				$oDB->setDBName(DBNAME);
				$oDB->setHost(DBHOST);
				$oDB->setPort(DBPORT);
				$oDB->setUser(DBUSER);
				$oDB->setPass(DBPASS);
				$oDB->connect();
				
				//Include the route File
				include("../iquest/config/routes.php");
				
				if(count($oRequest->getParameterArray()) > 0)
				{
					//Check if the module exists
					//If not redirect to a error page
					if(isset($aRoutes[$oRequest->getParaMeter(0)]))
					{
						//Sets the module name
						$sModule = $oRequest->getParaMeter(0);
						
						//Get The module Routes
						$aModuleRoutes = $aRoutes[$oRequest->getParaMeter(0)];
						
						//Check if the user is ingelogd
						if(!BClogin_BCLoginHelper::isLoggedIn())
						{
							//The first Part of the loginVArs is the same, so we can check universal'
							//Instantiate the login controler
							$sControler = $aModuleRoutes["login"]["controler"];
							$sAction = $aModuleRoutes["login"]["action"];
							
							echo "Go To Inloggen<br />";
						}else 
							{
								//Instantiate ModduleControler
								echo "GO to MOdule";
							}
							
						$oControler = new $sControler();
						$oControler->$sAction();
						
					}else 
						{
							echo "Module Not Found!!";
						}
						
				}else 
					{
						//There is no module, lets check if er is a cuurent session Or the IP adres is Known
						//So we can redirect to specific application parts
						$oRequest->redirect(HOST."/".APPLICATIONPATH."/dashboard");
					}
				
				
				
				
				
				
				//self::runModule($oDispatcher);
				
				
		}catch(Core_Config_MVCConfigException $e)
				{
					//echo $e->getMessage();
					throw $e;
				}
		
	}
	
	
}