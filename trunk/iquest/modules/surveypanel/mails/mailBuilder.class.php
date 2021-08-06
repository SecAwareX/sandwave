<?php
/**
 *	mailBuilder.class.php :
 *  Author : Kurt Van Nieuwenhuyze <kurt@balancecoding.nl>
 *  Date : 29 aug. 2018
 *  Project : 
 * 	 Package : 
 *  Version : 
 * 
 */

abstract class Modules_surveypanel_mailBuilder
{
    public static function buildMail($p_oMailer,$p_sMail,$p_aData)
    {
        //$p_oMailer->setFrom('iQuestMail@balancecoding.nl', 'balancecoding.nl');
     
        switch($p_sMail)
        {
            case "newUser":
                $p_oMailer->setFrom('iQuestMail@'.MAILHOST, MAILFROMNAME);
                $p_oMailer->addAddress($p_aData["Email"], $p_aData["name"]);
                $p_oMailer->addBCC(DEVEMAIL, "iQuest Systeem - iQuestMail@mareis.nl");
                $p_oMailer->Subject  = 'iQuest systeemmelding - Er is een nieuw account voor u aangemaakt';
                $p_oMailer->sMailTemplate = 'mail_newUser.tpl';
                $p_oMailer->addMailTemplateVars("Name",$p_aData["name"]);
                $p_oMailer->addMailTemplateVars("userName",$p_aData["UserName"]);
                $p_oMailer->addMailTemplateVars("pass",$p_aData["Pass"]);
                $p_oMailer->addMailTemplateVars("URL",$p_aData["URL"]);
             break;
           case "changeUser":
               $p_oMailer->setFrom('iQuestMail@'.MAILHOST, MAILFROMNAME);
               $p_oMailer->addAddress($p_aData["Email"], $p_aData["name"]);
                $p_oMailer->addBCC(DEVEMAIL, "iQuest Systeem - iQuestMail@mareis.nl");
                $p_oMailer->Subject  = 'iQuest systeemmelding - Uw account gegevens zijn gewijzigd';
                $p_oMailer->sMailTemplate = 'mail_changeUser.tpl';
                $p_oMailer->addMailTemplateVars("Name",$p_aData["name"]);
                $p_oMailer->addMailTemplateVars("userName",$p_aData["UserName"]);
                $p_oMailer->addMailTemplateVars("pass",$p_aData["Pass"]);
                $p_oMailer->addMailTemplateVars("URL",$p_aData["URL"]);
                break;
           case "newInvitation":
              
               $p_oMailer->setFrom('iQuestMail@'.MAILHOST, MAILFROMNAME);
               $p_oMailer->addAddress($p_aData["client"]["email"], ucfirst($p_aData["client"]["firstName"])." ".ucfirst($p_aData["client"]["lastName"]));
               $p_oMailer->addBCC(DEVEMAIL, "iQuest Systeem - iQuestMail@mareis.nl");
               $p_oMailer->Subject  = 'Uitnodiging voor het invullen van vragenlijsten';
               $p_oMailer->sMailTemplate = 'mail_Invitation.tpl';
               
               $p_oMailer->addMailTemplateVars("gender",($p_aData["client"]["gender"]=="male"?"heer":"mevrouw"));
               $p_oMailer->addMailTemplateVars("Name",ucfirst($p_aData["client"]["firstName"])." ".ucfirst($p_aData["client"]["lastName"]));
               $p_oMailer->addMailTemplateVars("userName",$p_aData["client"]["email"]);
               $p_oMailer->addMailTemplateVars("pass",$p_aData["credentials"]["Pass"]);
               $p_oMailer->addMailTemplateVars("URL",$p_aData["credentials"]["URL"]);
               $p_oMailer->addMailTemplateVars("appointment",$p_aData["client"]["appointment"] !="0000-00-00"?" ".BChelpers_converters::convertDate("view",$p_aData["client"]["appointment"]):"");
               break;
        }
    }
}