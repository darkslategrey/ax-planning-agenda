<?php
/* Copyright (C) 2008-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2009 Regis Houssin        <regis@dolibarr.fr>
 * Copyright (C) 2011	   Juanjo Menent        <jmenent@2byte.es>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 * or see http://www.gnu.org/
 */

/**
 *  \file		htdocs/axplanning/lib/axplanning.lib.php
 *  \brief		Set of function for the Axplanning module
 */


/*
 * ================================================================= 
 * Purpose: Utility function to translate Dolibarr event data structure into
 *          FullCalendar compatible data structure.
 *          Ex. of Dolibarr event json structure: 
 * {"1355698800":[{"element":"action","table_element":"actioncomm","table_rowid":"id","id":"1","type_id":null,"type_code":"AC_OTH_AUTO","type":null,"label":null,"date":null,"datec":null,"datem":null,"author":{"id":"1"},"usermod":{},"datep":1355702228,"datef":1355702228,"dateend":null,"durationp":-1,"fulldayevent":"0","punctual":1,"percentage":"-1","location":"","priority":"0","note":null,"usertodo":{"id":null},"userdone":{"id":"1"},"societe":{"id":"1"},"contact":{"id":null},"fk_project":null,"fk_element":null,"elementtype":null,"icalname":null,"icalcolor":null,"actions":[],"error":null,"errors":null,"canvas":null,"lastname":null,"firstname":null,"name":null,"nom":null,"civility_id":null,"array_options":[],"libelle":"Soci\u00e9t\u00e9 DEV_COMPANY ajout\u00e9e dans Dolibarr","date_start_in_calendar":1355702228,"date_end_in_calendar":1355702228,"ponctuel":1}]}
 *
 * Input:   Dolibarr Event Json Format
 * Author:  Grégory Faruch
 * Licence: GPL
 * @param	Json structure            $doli_events             What Dolibarr found as events
 * @return      Json structure            $cal_events              Fullcalendar translated events
 * ==================================================================
 */
function translate_to_full_callendar($doli_events) {

  // dol_syslog(print_r($doli_events, true), LOG_DEBUG);
  // TODO put limit as configuration params.
  
  $limit = 5;

  $cal_events = Array();
  foreach($doli_events as $event_ts => $d_events) {
    $i = 0;
    foreach($d_events as $e) {
      // $e = $event[0];
      dol_syslog("libelle <".$e->libelle.">", LOG_DEBUG);
      dol_syslog("date_start_in_calendar <".$e->date_start_in_calendar.">", LOG_DEBUG);
      dol_syslog("date_end_in_calendar <".$e->date_end_in_calendar.">", LOG_DEBUG);

      if (! empty($e->contact->id) && $e->contact->id > 0) {
	// $contact_name = $e->contact->civilite . ' ' . $e->contact->firstname . ' ' . $e->contact->name;
	$contact_name = $e->contact->nomurl;
	// $contact_name = $e->contact->civilite;
	// dol_syslog("greg contact : <".print_r($e->contact, true).">");
      }

      $event_arr = array('id' => $e->id, 'title' => $e->libelle,
			 'start' => $e->date_start_in_calendar, 
			 'allDay' => false,
			 'contact_id' => $e->contact->id,
			 'contact_name' => $contact_name,
			 'nomurl' => $e->contact->nomurl,
			 'url'   => '/comm/action/fiche.php?id='.$e->id,
			 'end'   => $e->date_end_in_calendar);
   
      array_push($cal_events, $event_arr);
      if($i > $limit) { break; }
      $i += 1;
    }
  }
  // dol_syslog("ICI : ".print_r($doli_events['1355698800'], true), LOG_DEBUG);
  dol_syslog("ICI 2 : ".print_r($cal_events, true), LOG_DEBUG);
  // dol_syslog($doli_events, LOG_DEBUG); 
  return $cal_events;
}

/*
 * ================================================================= 
 * Purpose: Manage filters and provide filtered infos
 * Input:   Filters
 * Author:  Grégory Faruch
 * Licence: GPL
 * @param	Object	$form			Form object
 * @param	int		$canedit		Can edit filter fields
 * @param	int		$status			Status
 * @param 	int		$year			Year
 * @param 	int		$month			Month
 * @param 	int		$day			Day
 * @param 	int		$showbirthday	Show birthday
 * @param 	string	$filtera		Filter on create by user
 * @param 	string	$filtert		Filter on assigned to user
 * @param 	string	$filterd		Filter of done by user
 * @param 	int		$pid			Product id
 * @param 	int		$socid			Third party id
 * @param	array	$showextcals	Array with list of external calendars, or -1 to show no legend
 * @param       
 * Input format 
{
  "1355698800": [
    {
      "element": "action",
      "table_element": "actioncomm",
      "table_rowid": "id",
      "id": "1",
      "type_id": null,
      "type_code": "AC_OTH_AUTO",
      "type": null,
      "label": null,
      "date": null,
      "datec": null,
      "datem": null,
      "author": {
        "id": "1"
      },
      "usermod": {
      },
      "datep": 1355702228,
      "datef": 1355702228,
      "dateend": null,
      "durationp": -1,
      "fulldayevent": "0",
      "punctual": 1,
      "percentage": "-1",
      "location": "",
      "priority": "0",
      "note": null,
      "usertodo": {
        "id": null
      },
      "userdone": {
        "id": "1"
      },
      "societe": {
        "id": "1"
      },
      "contact": {
        "id": null
      },
      "fk_project": null,
      "fk_element": null,
      "elementtype": null,
      "icalname": null,
      "icalcolor": null,
      "actions": [

      ],
      "error": null,
      "errors": null,
      "canvas": null,
      "lastname": null,
      "firstname": null,
      "name": null,
      "nom": null,
      "civility_id": null,
      "array_options": [

      ],
      "libelle": "Société DEV_COMPANY ajoutée dans Dolibarr",
      "date_start_in_calendar": 1355702228,
      "date_end_in_calendar": 1355702228,
      "ponctuel": 1
    }
  ]
}
 * ==================================================================
 */

function ajax_filter_calls() {
  dol_syslog("greg inside ajax_filter_calls()");
  // $form,$canedit,$status,$year,$month,$day,$showbirthday,$filtera,$filtert,$filterd,$pid,$socid,$showextcals=array()) {
    $out = '<script type="text/javascript">
               $(document).ready(function() {
                    projectid = $("select[name=projectid]");
                    $("#userasked,#usertodo,#userdone,#actioncode,select[name=projectid]").change(function() { 
                       userasked = $("#userasked").val();
                       usertodo = $("#usertodo").val(); 
                       userdone = $("#userdone").val(); 
                       actioncode = $("#actioncode").val(); 
                       projectid  = $("select[name=projectid]").val(); 
                       $("#calendar").fullCalendar( "refetchEvents" );
                    });
                });
             </script>';
    $out .= "\n";
    return $out;
}
 
             
/**
 * Show filter form in agenda view
 *
 * @param	Object	$form			Form object
 * @param	int		$canedit		Can edit filter fields
 * @param	int		$status			Status
 * @param 	int		$year			Year
 * @param 	int		$month			Month
 * @param 	int		$day			Day
 * @param 	int		$showbirthday	Show birthday
 * @param 	string	$filtera		Filter on create by user
 * @param 	string	$filtert		Filter on assigned to user
 * @param 	string	$filterd		Filter of done by user
 * @param 	int		$pid			Product id
 * @param 	int		$socid			Third party id
 * @param	array	$showextcals	Array with list of external calendars, or -1 to show no legend
 * @return	void
 */
function print_actions_filter($form,$canedit,$status,$year,$month,$day,$showbirthday,$filtera,$filtert,$filterd,$pid,$socid,$showextcals=array())
{
	global $conf,$user,$langs,$db;

	// Filters
	if ($canedit || ! empty($conf->projet->enabled))
	{
		print '<form name="listactionsfilter" class="listactionsfilter" action="'.$_SERVER["PHP_SELF"].'" method="POST">';
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		print '<input type="hidden" name="status" value="'.$status.'">';
		print '<input type="hidden" name="year" value="'.$year.'">';
		print '<input type="hidden" name="month" value="'.$month.'">';
		print '<input type="hidden" name="day" value="'.$day.'">';
		print '<input type="hidden" name="showbirthday" value="'.$showbirthday.'">';
		print '<table class="nobordernopadding" width="100%">';
		if ($canedit || ! empty($conf->projet->enabled))
		{
			print '<tr><td nowrap="nowrap">';

			print '<table class="nobordernopadding">';

			if ($canedit)
			{
				print '<tr>';
				print '<td nowrap="nowrap">';
				print $langs->trans("ActionsAskedBy");
				print ' &nbsp;</td><td nowrap="nowrap">';
				print $form->select_dolusers($filtera,'userasked',1,'',!$canedit);
				print '</td>';
				print '</tr>';

				print '<tr>';
				print '<td nowrap="nowrap">';
				print $langs->trans("or").' '.$langs->trans("ActionsToDoBy");
				print ' &nbsp;</td><td nowrap="nowrap">';
				print $form->select_dolusers($filtert,'usertodo',1,'',!$canedit);
				print '</td></tr>';

				print '<tr>';
				print '<td nowrap="nowrap">';
				print $langs->trans("or").' '.$langs->trans("ActionsDoneBy");
				print ' &nbsp;</td><td nowrap="nowrap">';
				print $form->select_dolusers($filterd,'userdone',1,'',!$canedit);
				print '</td></tr>';

				include_once DOL_DOCUMENT_ROOT.'/core/class/html.formactions.class.php';
				$formactions=new FormActions($db);
				print '<tr>';
				print '<td nowrap="nowrap">';
				print $langs->trans("Type");
				print ' &nbsp;</td><td nowrap="nowrap">';

				// print $formactions->select_type_actions(GETPOST('actioncode'), "actioncode");
				print $formactions->select_type_actions(GETPOST('actioncode')?GETPOST('actioncode'):'manual', "actioncode", '', (empty($conf->global->AGENDA_USE_EVENT_TYPE)?1:0));

				print '</td></tr>';
			}

			if (! empty($conf->projet->enabled) && $user->rights->projet->lire)
			{
				print '<tr>';
				print '<td nowrap="nowrap">';
				print $langs->trans("Project").' &nbsp; ';
				print '</td><td nowrap="nowrap">';
				select_projects($socid?$socid:-1, $pid, 'projectid', 64);
				print '</td></tr>';
			}

			print '</table>';
			print '</td>';

			// Legend
			if ($conf->use_javascript_ajax && is_array($showextcals))
			{
    			print '<td align="center" valign="middle" nowrap="nowrap">';
                print '<script type="text/javascript">'."\n";
                print 'jQuery(document).ready(function () {'."\n";
                print 'jQuery("#check_mytasks").click(function() { jQuery(".family_mytasks").toggle(); jQuery(".family_other").toggle(); });'."\n";
                print 'jQuery("#check_birthday").click(function() { jQuery(".family_birthday").toggle(); });'."\n";
                print 'jQuery(".family_birthday").toggle();'."\n";
                print '});'."\n";
                print '</script>'."\n";
                print '<table>';
                if (! empty($conf->global->MAIN_JS_SWITCH_AGENDA))
                {
                    if (count($showextcals) > 0)
                    {
                        print '<tr><td><input type="checkbox" id="check_mytasks" name="check_mytasks" checked="true" disabled="disabled"> '.$langs->trans("LocalAgenda").'</td></tr>';
                        foreach($showextcals as $val)
                        {
                            $htmlname=dol_string_nospecial($val['name']);
                            print '<script type="text/javascript">'."\n";
                            print 'jQuery(document).ready(function () {'."\n";
                            print 'jQuery("#check_'.$htmlname.'").click(function() { jQuery(".family_'.$htmlname.'").toggle(); });'."\n";
                            print '});'."\n";
                            print '</script>'."\n";
                            print '<tr><td><input type="checkbox" id="check_'.$htmlname.'" name="check_'.$htmlname.'" checked="true"> '.$val['name'].'</td></tr>';
                        }
                    }
                }
                print '<tr><td><input type="checkbox" id="check_birthday" name="check_birthday checked="false"> '.$langs->trans("AgendaShowBirthdayEvents").'</td></tr>';
                print '</table>';
                print '</td>';
			}

			print '</tr>';
		}
		print '</table>';
		print '</form>';
	}
}


/**
 *  Show actions to do array
 *
 *  @param	int		$max		Max nb of records
 *  @return	void
 */
function show_array_actions_to_do($max=5)
{
	global $langs, $conf, $user, $db, $bc, $socid;

	$now=dol_now();

	include_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
	include_once DOL_DOCUMENT_ROOT.'/societe/class/client.class.php';

	$sql = "SELECT a.id, a.label, a.datep as dp, a.datep2 as dp2, a.fk_user_author, a.percent,";
	$sql.= " c.code, c.libelle,";
	$sql.= " s.nom as sname, s.rowid, s.client";
	$sql.= " FROM (".MAIN_DB_PREFIX."c_actioncomm as c,";
	$sql.= " ".MAIN_DB_PREFIX."actioncomm as a";
	if (!$user->rights->societe->client->voir && !$socid) $sql.= ", ".MAIN_DB_PREFIX."societe_commerciaux as sc";
	$sql.= ")";
    $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."societe as s ON a.fk_soc = s.rowid";
	$sql.= " WHERE c.id = a.fk_action";
	$sql.= " AND a.entity = ".$conf->entity;
    $sql.= " AND ((a.percent >= 0 AND a.percent < 100) OR (a.percent = -1 AND a.datep2 > '".$db->idate($now)."'))";
	if (!$user->rights->societe->client->voir && !$socid) $sql.= " AND s.rowid = sc.fk_soc AND sc.fk_user = " .$user->id;
	if ($socid) $sql.= " AND s.rowid = ".$socid;
	$sql.= " ORDER BY a.datep DESC, a.id DESC";
	$sql.= $db->plimit($max, 0);

	$resql=$db->query($sql);
	if ($resql)
	{
	    $num = $db->num_rows($resql);

	    print '<table class="noborder" width="100%">';
	    print '<tr class="liste_titre"><td colspan="2">'.$langs->trans("LastActionsToDo",$max).'</td>';
		print '<td colspan="2" align="right"><a href="'.DOL_URL_ROOT.'/comm/action/listactions.php?status=todo">'.$langs->trans("FullList").'</a>';
		print '</tr>';

		$var = true;
	    $i = 0;

		$staticaction=new ActionComm($db);
	    $customerstatic=new Client($db);

        while ($i < $num)
        {
            $obj = $db->fetch_object($resql);
            $var=!$var;

            print '<tr '.$bc[$var].'>';

            $staticaction->type_code=$obj->code;
            $staticaction->libelle=$obj->label;
            $staticaction->id=$obj->id;
            print '<td>'.$staticaction->getNomUrl(1,34).'</td>';

           // print '<td>'.dol_trunc($obj->label,22).'</td>';

            print '<td>';
            if ($obj->rowid > 0)
            {
            	$customerstatic->id=$obj->rowid;
            	$customerstatic->name=$obj->sname;
            	$customerstatic->client=$obj->client;
            	print $customerstatic->getNomUrl(1,'',16);
            }
            print '</td>';

            $datep=$db->jdate($obj->dp);
            $datep2=$db->jdate($obj->dp2);

            // Date
			print '<td width="100" align="right">'.dol_print_date($datep,'day').'&nbsp;';
			$late=0;
			if ($obj->percent == 0 && $datep && $datep < time()) $late=1;
			if ($obj->percent == 0 && ! $datep && $datep2 && $datep2 < time()) $late=1;
			if ($obj->percent > 0 && $obj->percent < 100 && $datep2 && $datep2 < time()) $late=1;
			if ($obj->percent > 0 && $obj->percent < 100 && ! $datep2 && $datep && $datep < time()) $late=1;
			if ($late) print img_warning($langs->trans("Late"));
			print "</td>";

			// Statut
			print "<td align=\"right\" width=\"14\">".$staticaction->LibStatut($obj->percent,3)."</td>\n";

			print "</tr>\n";

            $i++;
        }
	    print "</table><br>";

	    $db->free($resql);
	}
	else
	{
	    dol_print_error($db);
	}
}


/**
 *  Show last actions array
 *
 *  @param	int		$max		Max nb of records
 *  @return	void
 */
function show_array_last_actions_done($max=5)
{
	global $langs, $conf, $user, $db, $bc, $socid;

	$now=dol_now();

	$sql = "SELECT a.id, a.percent, a.datep as da, a.datep2 as da2, a.fk_user_author, a.label,";
	$sql.= " c.code, c.libelle,";
	$sql.= " s.rowid, s.nom as sname, s.client";
	$sql.= " FROM (".MAIN_DB_PREFIX."c_actioncomm as c,";
	$sql.= " ".MAIN_DB_PREFIX."actioncomm as a";
	if (!$user->rights->societe->client->voir && !$socid) $sql.= ", ".MAIN_DB_PREFIX."societe_commerciaux as sc";
	$sql.=")";
    $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."societe as s ON a.fk_soc = s.rowid";
	$sql.= " WHERE c.id = a.fk_action";
	$sql.= " AND a.entity = ".$conf->entity;
    $sql.= " AND (a.percent >= 100 OR (a.percent = -1 AND a.datep2 <= '".$db->idate($now)."'))";
	if (!$user->rights->societe->client->voir && !$socid) $sql.= " AND s.rowid = sc.fk_soc AND sc.fk_user = " .$user->id;
    if ($socid) $sql.= " AND s.rowid = ".$socid;
	$sql .= " ORDER BY a.datep2 DESC";
	$sql .= $db->plimit($max, 0);

	$resql=$db->query($sql);
	if ($resql)
	{
		$num = $db->num_rows($resql);

		print '<table class="noborder" width="100%">';
		print '<tr class="liste_titre"><td colspan="2">'.$langs->trans("LastDoneTasks",$max).'</td>';
		print '<td colspan="2" align="right"><a href="'.DOL_URL_ROOT.'/comm/action/listactions.php?status=done">'.$langs->trans("FullList").'</a>';
		print '</tr>';
		$var = true;
		$i = 0;

	    $staticaction=new ActionComm($db);
	    $customerstatic=new Societe($db);

		while ($i < $num)
		{
			$obj = $db->fetch_object($resql);
			$var=!$var;

			print '<tr '.$bc[$var].'>';

			$staticaction->type_code=$obj->code;
			$staticaction->libelle=$obj->label;
			$staticaction->id=$obj->id;
			print '<td>'.$staticaction->getNomUrl(1,34).'</td>';

            //print '<td>'.dol_trunc($obj->label,24).'</td>';

			print '<td>';
			if ($obj->rowid > 0)
			{
                $customerstatic->id=$obj->rowid;
                $customerstatic->name=$obj->sname;
                $customerstatic->client=$obj->client;
			    print $customerstatic->getNomUrl(1,'',24);
			}
			print '</td>';

			// Date
			print '<td width="100" align="right">'.dol_print_date($db->jdate($obj->da2),'day');
			print "</td>";

			// Statut
			print "<td align=\"right\" width=\"14\">".$staticaction->LibStatut($obj->percent,3)."</td>\n";

			print "</tr>\n";
			$i++;
		}
		// TODO Ajouter rappel pour "il y a des contrats a mettre en service"
		// TODO Ajouter rappel pour "il y a des contrats qui arrivent a expiration"
		print "</table><br>";

		$db->free($resql);
	}
	else
	{
		dol_print_error($db);
	}
}


/**
 * Prepare array with list of tabs
 *
 * @return  array				Array of tabs to shoc
 */
function agenda_prepare_head()
{
	global $langs, $conf, $user;
	$h = 0;
	$head = array();

	$head[$h][0] = DOL_URL_ROOT."/admin/agenda.php";
	$head[$h][1] = $langs->trans("AutoActions");
	$head[$h][2] = 'autoactions';
	$h++;

	$head[$h][0] = DOL_URL_ROOT."/admin/agenda_xcal.php";
	$head[$h][1] = $langs->trans("ExportCal");
	$head[$h][2] = 'xcal';
	$h++;

	$head[$h][0] = DOL_URL_ROOT."/admin/agenda_extsites.php";
	$head[$h][1] = $langs->trans("ExtSites");
	$head[$h][2] = 'extsites';
	$h++;

	complete_head_from_modules($conf,$langs,$object,$head,$h,'agenda_admin');
	
	$head[$h][0] = DOL_URL_ROOT."/admin/agenda_extrafields.php";
	$head[$h][1] = $langs->trans("ExtraFields");
	$head[$h][2] = 'attributes';
	$h++;

	complete_head_from_modules($conf,$langs,$object,$head,$h,'agenda_admin','remove');


	return $head;
}

/**
 * Prepare array with list of tabs
 *
 * @param   Object	$object		Object related to tabs
 * @return  array				Array of tabs to shoc
 */
function actions_prepare_head($object)
{
	global $langs, $conf, $user;

	$h = 0;
	$head = array();

	$head[$h][0] = DOL_URL_ROOT.'/comm/action/fiche.php?id='.$object->id;
	$head[$h][1] = $langs->trans("CardAction");
	$head[$h][2] = 'card';
	$h++;

	if (! empty($conf->global->AGENDA_USE_SEVERAL_CONTACTS))
	{
		$head[$h][0] = DOL_URL_ROOT.'/comm/action/contact.php?id='.$object->id;
		$head[$h][1] = $langs->trans("Contacts");
		$head[$h][2] = 'contact';
		$h++;
	}

	$head[$h][0] = DOL_URL_ROOT.'/comm/action/document.php?id='.$object->id;
	$head[$h][1] = $langs->trans('Documents');
	$head[$h][2] = 'documents';
	$h++;

	$head[$h][0] = DOL_URL_ROOT.'/comm/action/info.php?id='.$object->id;
	$head[$h][1] = $langs->trans('Info');
	$head[$h][2] = 'info';
	$h++;

	return $head;
}


/**
 *  Define head array for tabs of agenda setup pages
 *
 *  @param	string	$param		Parameters to add to url
 *  @return array			    Array of head
 */
function calendars_prepare_head($param)
{
    global $langs, $conf, $user;

    $h = 0;
    $head = array();

    $head[$h][0] = DOL_URL_ROOT.'/comm/action/index.php'.($param?'?'.$param:'');
    $head[$h][1] = $langs->trans("Agenda");
    $head[$h][2] = 'card';
    $h++;

	$object=(object) array();

    // Show more tabs from modules
    // Entries must be declared in modules descriptor with line
    // $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
    // $this->tabs = array('entity:-tabname);   												to remove a tab
    complete_head_from_modules($conf,$langs,$object,$head,$h,'agenda');

    complete_head_from_modules($conf,$langs,$object,$head,$h,'agenda','remove');

    return $head;
}

?>

