<?php
/* <one line to give the program's name and a brief idea of what it does.>
 * Copyright (C) 2015 ATM Consulting <support@atm-consulting.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * \file    class/actions_marque.class.php
 * \ingroup marque
 * \brief   This file is an example hook overload class file
 *          Put some comments here
 */

/**
 * Class ActionsMarque
 */
class ActionsMarque
{
	/**
	 * @var array Hook results. Propagated to $hookmanager->resArray for later reuse
	 */
	public $results = array();

	/**
	 * @var string String displayed by executeHook() immediately after return
	 */
	public $resprints;

	/**
	 * @var array Errors
	 */
	public $errors = array();

	/**
	 * Constructor
	 */
	public function __construct()
	{
	}

	/**
	 * Overloading the doActions function : replacing the parent's function with the one below
	 *
	 * @param   array()         $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    &$object        The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          &$action        Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	function doActions($parameters, &$object, &$action, $hookmanager)
	{
		$TContext = explode(':', $parameters['context']);
		if (in_array('globalcard',$TContext))
		{
		  	global $langs,$db,$conf,$mysoc;
			if(!empty($langs)) $langs->load('marque@marque');
			
			if($action == 'builddoc' && $object->array_options['options_entity_marque']>0 && $object->array_options['options_entity_marque']!=$conf->entity) {
			
				$sourcecompany = &$mysoc;
				
				dol_include_once('/multicompany/class/dao_multicompany.class.php');
				
				$dao = new DaoMulticompany($db);
				$dao->fetch($object->array_options['options_entity_marque']);
				
				$conf->mycompany->dir_output= DOL_DATA_ROOT;
				if($object->array_options['options_entity_marque']>1)$conf->mycompany->dir_output.='/'.$object->array_options['options_entity_marque'].'/mycompany';
				else $conf->mycompany->dir_output.='/mycompany'; 
					
				$sourcecompany->nom = $sourcecompany->name = $dao->MAIN_INFO_SOCIETE_NOM;
				$sourcecompany->town = $dao->MAIN_INFO_SOCIETE_TOWN;
				$sourcecompany->zip = $dao->MAIN_INFO_SOCIETE_ZIP;
				$sourcecompany->state = $dao->MAIN_INFO_SOCIETE_STATE;
				$sourcecompany->logo = $dao->MAIN_INFO_SOCIETE_LOGO;
				$sourcecompany->logo_small = $dao->MAIN_INFO_SOCIETE_LOGO_SMALL;
				$sourcecompany->logo_mini = $dao->MAIN_INFO_SOCIETE_LOGO_MINI;
				
				$sourcecompany->address = $dao->MAIN_INFO_SOCIETE_ADDRESS;
				$sourcecompany->phone = $dao->MAIN_INFO_SOCIETE_TEL;
				$sourcecompany->fax = $dao->MAIN_INFO_SOCIETE_FAX;
				$sourcecompany->managers = $dao->MAIN_INFO_SOCIETE_MANAGERS;
				$sourcecompany->capital = $dao->MAIN_INFO_CAPITAL;
				$sourcecompany->typent_id = $dao->MAIN_INFO_SOCIETE_FORME_JURIDIQUE;
				$sourcecompany->idprof1 = $dao->MAIN_INFO_SIREN;
				$sourcecompany->idprof2 = $dao->MAIN_INFO_SIRET;
				
			
			}
			
		}

	}
	
	function pdf_build_address(&$parameters, &$null, &$action, $hookmanager)
	{
		global $conf,$user,$langs,$db,$mysoc,$object;
		$TContext = explode(':', $parameters['context']);
		
		if (in_array('pdfgeneration',$TContext))
		{
					
			
			
			
					
		}
		

	}
}