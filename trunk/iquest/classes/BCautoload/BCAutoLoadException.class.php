<?php
/**
 * 
 * @author kurt van Nieuwenhuyze   <kurt@balancecoding.nl>
 * @package BCcore
 * @subpackage BCautoload
 * @project BCApplication framework
 * @version  1.0
 * @date 2012-08-28
 * @description :
 *  - specific exception for the autoloader
 *
 */
class BCautoload_BCAutoLoadException extends Exception
{
	/**
	 * 
	 * @access public
	 * @return void
	 * @param string $p_sMessage
	 */
	public function __construct($p_sMessage)
	{
		parent::__construct($p_sMessage);
		$this->message = $p_sMessage;
	}
}
