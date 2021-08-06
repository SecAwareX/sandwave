<?php
/**
 * @author kurt van Nieuwenhuyze   <kurt@balancecoding.nl>
 * @package BClibarys
 * @subpackage BChtprequest
 * @project 
 * @version  1.0
 * @date 2012-09-18
 * @description  : httprequesdt exception
 * **/
class BClibarys_BChttprequest_BCHTTPRequestException extends BCLibarys_BCExceptions_BCException
{
	/**
	 * 
	 * constructor
	 * @param string $p_sMessage
	 */
	public function __construct($p_sMessage)
	{
		parent::__construct($p_sMessage);
	}
}

