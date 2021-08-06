<?php
/**
 *	iquestFormFactory.class.php :
 *  Author : Kurt Van Nieuwenhuyze <kurt@balancecoding.nl>
 *  Date : 12 sep. 2018
 *  Project : 
 * 	 Package : 
 *  Version : 
 * 
 */

abstract class surveypanel_models_iquestforms_iquestFormFactory
{
    public static function createForm($p_sType)
    { 
        switch(strtolower($p_sType))
        {
            case "simple" :
                return new surveypanel_models_iquestforms_iquestForm(); 
            break;
            case "medium" :
                return new surveypanel_models_iquestforms_iquestFormMedium(); 
            break;
            case "complex" :
                return new surveypanel_models_iquestforms_iquestFormComplex(); 
            break;
            default : return null;
        }
    }
}