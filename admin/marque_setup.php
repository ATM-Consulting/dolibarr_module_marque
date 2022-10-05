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
 * 	\file		admin/marque.php
 * 	\ingroup	marque
 * 	\brief		This file is an example module setup page
 * 				Put some comments here
 */
// Dolibarr environment
$res = @include("../../main.inc.php"); // From htdocs directory
if (! $res) {
    $res = @include("../../../main.inc.php"); // From "custom" directory
}

// Libraries
require_once DOL_DOCUMENT_ROOT . "/core/lib/admin.lib.php";
require_once '../lib/marque.lib.php';

/** @var DoliDB $db */
/** @var Translate $langs */
/** @var User $user */

// Translations
$langs->load("marque@marque");

// Access control
if (! $user->admin) {
    accessforbidden();
}

// Parameters
$action = GETPOST('action', 'alpha');

/*
 * Actions
 */
// SPÉ Koesio: marque par défaut sur les dossiers de financement
if ($action === 'set_MARQUE_DEFAUT_DOSSIER') {
	$entity = intval(GETPOST('entity', 'int'));
	$value = GETPOST('value', 'alpha');
	if ($value === '') {
		dolibarr_del_const($db, 'MARQUE_DEFAUT_DOSSIER', $entity);
	} elseif (is_numeric($value)) {
		dolibarr_set_const($db, 'MARQUE_DEFAUT_DOSSIER', intval($value), 'int', 0, '', $entity);
	}
}
// END SPÉ Koesio
elseif (preg_match('/set_(.*)/',$action,$reg))
{
	$code=$reg[1];
	$value = GETPOST($code);
	if(is_array($value))$value = implode(',',$value);

	if (dolibarr_set_const($db, $code, $value, 'chaine', 0, '', $conf->entity) > 0)
	{
		header("Location: ".$_SERVER["PHP_SELF"]);
		exit;
	}
	else
	{
		dol_print_error($db);

	}
}

elseif (preg_match('/del_(.*)/',$action,$reg))
{
	$code=$reg[1];
	if (dolibarr_del_const($db, $code, 0) > 0)
	{
		Header("Location: ".$_SERVER["PHP_SELF"]);
		exit;
	}
	else
	{
		dol_print_error($db);
	}
}


/*
 * View
 */
$page_name = "MarqueSetup";
llxHeader('', $langs->trans($page_name));

// Subheader
$linkback = '<a href="' . DOL_URL_ROOT . '/admin/modules.php">'
    . $langs->trans("BackToModuleList") . '</a>';
print_fiche_titre($langs->trans($page_name), $linkback);

// Configuration header
$head = marqueAdminPrepareHead();
dol_fiche_head(
    $head,
    'settings',
    $langs->trans("Module104015Name"),
    0,
    "marque@marque"
);

// Setup page goes here
$form=new Form($db);
$var=false;
print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Parameters").'</td>'."\n";
print '<td align="center" width="20">&nbsp;</td>';
print '<td align="center" width="100">'.$langs->trans("Value").'</td>'."\n";


$var=!$var;
print '<tr '.$bc[$var].'>';
print '<td>'.$langs->trans("set_MARQUE_ENTITIES_LINKED").'</td>';
print '<td align="center" width="20">&nbsp;</td>';
print '<td align="right" width="300">';
print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="set_MARQUE_ENTITIES_LINKED_'.$conf->entity.'">';

$TEntities=array();
dol_include_once('/multicompany/class/dao_multicompany.class.php');
$dao = new DaoMulticompany($db);
$dao->getEntities();
foreach($dao->entities as &$e) {

	$TEntities[$e->id] = $e->label;

}

print $form->multiselectarray('MARQUE_ENTITIES_LINKED_'.$conf->entity, $TEntities, explode(',',$conf->global->{'MARQUE_ENTITIES_LINKED_'.$conf->entity}));
print '<input type="submit" class="button" value="'.$langs->trans("Modify").'">';
print '</form>';
print '</td></tr>';

foreach ($TEntities as $id => $label) {
	formDefaultMarqueForEntity($id);
}

print '</table>';

llxFooter();

$db->close();


//function getMarqueEntitiesLinked() {
//	$sql = /* @lang SQL */
//		'SELECT c.value FROM ' . MAIN_DB_PREFIX . 'const AS c'
//		.' WHERE c.name = CONCAT("MARQUE_ENTITIES_LINKED_", c.entity);';
//}

/**
 * Affiche une ligne de tableau avec un formulaire permettant de définir la marque par défaut
 * pour les dossiers de financement de l'entité passée en paramètre.
 * @param int $entityId
 * @return void
 */
function formDefaultMarqueForEntity($entityId) {
	global $db, $bc, $var, $conf, $form, $TEntities;
	$var=!$var;
	$selectable0 = dolibarr_get_const($db, 'MARQUE_ENTITIES_LINKED_' . $entityId, $entityId);
	$currentValue = dolibarr_get_const($db, 'MARQUE_DEFAUT_DOSSIER', $entityId);
//	$confName = 'MARQUE_ENTITIES_LINKED_' . $entityId;
//	$selectable0 = $conf->global->{$confName} ?? null;
	if ($selectable0) {
		$selectable = array_filter($TEntities, function($i) use ($selectable0) {
			return preg_match("/\b{$i}\b/", $selectable0);
		}, ARRAY_FILTER_USE_KEY);
	} else {
		$selectable = $TEntities;
	}

	?>
	<tr <?php echo $bc[$var] ?>>
		<td>Marque par défaut pour les dossiers de l'entité <?php echo $TEntities[$entityId] ?></td>
		<td> </td>
		<td style="text-align: right; min-width: 25%">
			<form method="post"
				action="<?php echo dol_buildpath('/marque/admin/marque_setup.php', 1) ?>">
			<select class="marque-par-defaut"
					name="value">
				<option value="">–</option>
			<?php
			foreach ($selectable as $s => $label) {
				$isSelected = $currentValue == $s;
				$option = '<option value="%s" %s>%s</option>';
				printf($option, $s, $isSelected ? 'selected' : '', $label);
			}
			?>
			</select>
			<input type="hidden" name="entity" value="<?php echo $entityId ?>">
			<button type="submit" name="action" value="set_MARQUE_DEFAUT_DOSSIER" class="button">Modifier</button>
			</form>
		</td>
	</tr>
	<?php
}
