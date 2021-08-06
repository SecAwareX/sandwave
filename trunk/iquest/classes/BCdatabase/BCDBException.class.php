<?php
/**
 * @author kurt van Nieuwenhuyze   <kurt@balancecoding.nl>
 * @package BClibarys
 * @subpackage BCdatabase
 * @project 
 * @version  1.0
 * @date 2012-09-18
 * @description  : Databse exception
 * **/
class BCdatabase_BCDBException extends exception
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