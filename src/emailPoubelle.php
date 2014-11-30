<?php

//-----------------------------------------------------------
// Title : Email Poubelle
// Licence : GNU GPL v3 : http://www.gnu.org/licenses/gpl.html
// Author : David Mercereau - david [aro] mercereau [.] info
// Home : http://poubelle.zici.fr
// Date : 08/2013
// Version : 1.0
// Depend : Postifx (postmap command) php-pdo
//----------------------------------------------------------- 

// @todo
// 	form ergonomie
// 	sqlite
//	disable time	

//////////////////
// Init & check
//////////////////

define('VERSION', '1.0');


if (DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 'On');
	echo '<div class="highlight-2">'._("Debug activé") .'<br />';
	echo print_r($_REQUEST);
	echo '</div>';
}
echo "<h1>". _("Emails poubelle libre")."</h1>";
echo "<p>". _("Générer des emails poubelle sans contrainte de durée de vie"). "</p>";

if (!defined('DOMAIN') || !defined('DATA') || !defined('DEBUG') || !defined('FICHIERALIAS') || !defined('DB')) {
	echo '<div class="highlight-1">'._("Erreur : Il ne semble pas que le fichier de configuration conf.php soit inclue car les constantes ne sont pas présentes").'.</div>';
// check writable work directory
} else if (!is_writable(DATA)) {
	echo '<div class="highlight-1">'._("Erreur : le répertoire de travail ne peut pas être écrit. Merci de contacter l\'administrateur").'</div>';
// check alias file is_writable 
} else if (!is_writable(FICHIERALIAS)) {
	echo '<div class="highlight-1">'._("Erreur : le fichier d\'alias ne peut pas être écrit. Merci de contacter l\'administrateur").'</div>';
// check blacklist file is_writable
} else if (defined('BLACKLIST') && !is_readable(BLACKLIST)) {
    echo '<div class="highlight-1">'._("Erreur : un fichier de blacklist est renseigné mais n\'est pas lisible. Merci de contacter l\'administrateur").'</div>';
// check aliasdeny file is_writable
} else if (defined('ALIASDENY') && !is_readable(ALIASDENY)) {
    echo '<div class="highlight-1">'._("Erreur : un fichier d\'alias interdit est renseigné mais n\'est pas lisible. Merci de contacter l\'administrateur").'</div>';
// maintenance mod
} else if (MAINTENANCE_MODE == true && MAINTENANCE_IP != $_SERVER["REMOTE_ADDR"]) {
	echo '<div class="highlight-2">'._("Le service est en maintenance.").'</div>';
} else {

if (MAINTENANCE_MODE == true) {
	echo '<div class="highlight-2">'._("Le service est en maintenance.").'</div>';
}

// Connect DB
try {
	if (preg_match('/^sqlite/', DB)) {
		$dbco = new PDO(DB);
	} else {
		$dbco = new PDO(DB, DBUSER, DBPASS);
	}
	$dbco->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch ( PDOException $e ) {
	die('._("Connexion à la base ").'.$e->getMessage());
}
// Create DB if not exists
try {
	// status : 0=not verified - 3=disable - 5=active
	if (preg_match('/^sqlite/', DB)) {
		$create = $dbco->query("CREATE TABLE IF NOT EXISTS ".DBTABLEPREFIX."alias (
								id INTEGER PRIMARY KEY,
								status INTEGER(1) NOT NULL,
								alias CHAR(150) NOT NULL UNIQUE,
								email CHAR(150) NOT NULL,
								dateCreat DATETIME NOT NULL,
								dateExpir DATETIME,
								comment TEXT);");
	} else {
		$create = $dbco->query("CREATE TABLE IF NOT EXISTS ".DBTABLEPREFIX."alias (
								id INTEGER PRIMARY KEY  AUTO_INCREMENT,
								status INTEGER(1) NOT NULL,
								alias CHAR(150) NOT NULL UNIQUE,
								email CHAR(150) NOT NULL,
								dateCreat DATETIME NOT NULL,
								dateExpir DATETIME,
								comment TEXT);");
	}
} catch ( PDOException $e ) {
	echo '<div class="highlight-1">'._("Erreur à l\'initialisation des tables. Merci de contacter l\'administrateur ");
	if (DEBUG) { $e->getMessage(); }
	echo '</div>';
	die();
}

//////////////////
// Start program
//////////////////

// get process "act" (action)
$action = isset($_GET['act']) ? $_GET['act'] : '';
switch ($action) {
	case "validemail" :
		$get_value = urlUnGen($_GET['value']);
		if ($dbco->query("SELECT COUNT(*) FROM ".DBTABLEPREFIX."alias WHERE id = '".$get_value['id']."' AND status = 0")->fetchColumn() != 0) {
			UpdateStatusAlias($get_value['id'], $get_value['alias_full'], 5);
			echo '<div class="highlight-3">'._("Votre email poubelle").' <b>'.$get_value['alias_full'].'</b> '._("est maintenant actif").'</div>';
		} else {
			echo '<div class="highlight-1">'._("Erreur : ID introuvable ou déjà validé").'</div>';
		}
	break;
	case "disable" :
		$get_value = urlUnGen($_GET['value']);
		DisableAlias($get_value['id'], $get_value['alias_full'], null);
	break;
	case "enable" :
		$get_value = urlUnGen($_GET['value']);
		EnableAlias($get_value['id'], $get_value['alias_full'], null);
	break;
	case "delete" :
		$get_value = urlUnGen($_GET['value']);
		DeleteAlias($get_value['id'], $get_value['alias_full']);
	break;
	case "cron" :
		if (CRON) {
			echo '<div class="highlight-2">'._("La tâche planifié est lancé").'</div>';
			LifeExpire();
		} else {
			echo '<div class="highlight-1">'._("Vous n\'avez pas autorisé le lancement par tâche planifié").'</div>';
		}
	break;
}
// Form
if (isset($_POST['username']) && $_POST['username'] != '') { // minimal anti-spam 
	echo 'Hello you';
} else if (isset($_POST['list'])) {
	$email=strtolower(StripCleanToHtml($_POST['email']));
	if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
		echo '<div class="highlight-1">'._("Erreur : Adresse email incorrect").'</div>';
	} else if (! VerifMXemail($email)) {
		echo '<div class="highlight-1">'._("Erreur : Adresse email incorrect (2)").'</div>';
	} else if (ListeAlias($email)) {
		echo '<div class="highlight-3">'._("Un email vient de vous être envoyé").'</div>';
	} else {
		echo '<div class="highlight-1">'._("Erreur : aucun email actif connu").'</div>';
	}
} else if (isset($_POST['email']) && isset($_POST['alias'])) {
	$alias=strtolower(StripCleanToHtml($_POST['alias']));
	$email=strtolower(StripCleanToHtml($_POST['email']));
	$domain=StripCleanToHtml($_POST['domain']);
	$life=$_POST['life'];
	$comment=StripCleanToHtml($_POST['comment']);
	$alias_full=$alias.'@'.$domain;
	// Check form
	if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
		echo '<div class="highlight-1">'._("Erreur : Adresse email incorrect").'</div>';
	} else if (! VerifMXemail($email)) {
		echo '<div class="highlight-1">'._("Erreur : Adresse email incorrect (2)").'</div>';
	} else if (! preg_match('#^[\w.-]+$#',$alias)) {
		echo '<div class="highlight-1">'._("Erreur : Format de l\'email poubelle incorrect").'</div>';
	} else if (! preg_match('#'.$domain.'#',DOMAIN)) {
		echo '<div class="highlight-1">'._("Erreur : ce domain n\'est pas pris en charge").'</div>';
	} else if (AliasDeny($alias)) {
		echo '<div class="highlight-1">'._("Erreur : email poubelle interdit").'</div>';
	} else if (BlacklistEmail($email)) {
		echo '<div class="highlight-1">'._("Erreur : vous avez été blacklisté sur ce service").'</div>';
	// add 
	} elseif (isset($_POST['add'])) {
		if ($dbco->query("SELECT COUNT(*) FROM ".DBTABLEPREFIX."alias WHERE alias = '".$alias_full."'")->fetchColumn() != 0) {
			echo '<div class="highlight-1">'._("Erreur : cet email poubelle est déjà utilisé").'</div>';
		} else {
			if ($dbco->query("SELECT COUNT(*) FROM ".DBTABLEPREFIX."alias WHERE email = '".$email."' AND status > 0")->fetchColumn() != 0) {
				AjouterAlias(5, $alias_full, $email, $life, $comment);
				echo '<div class="highlight-3">'._("Votre email poubelle ").'<b>'.$alias_full.' > '.$email.'</b>'._(" est maintenant actif").'</div>';
			} else {
				$lastId=AjouterAlias(0, $alias_full, $email, $life, $comment);
				$message= _("Confirmation de la création de votre redirection email poubelle :");
				$message= $alias_full.' => '.$email."\n";
				$message= _("Cliquer sur le lien ci-dessous pour confirmer : \n");
				$message.= "\t * ".urlGen('validemail',$lastId,$alias_full)."\n";
				$message.= "\n";
				$message.= _("Pour supprimer cet email poubelle vous pouvez vous rendre sur le lien ci-dessous : \n");
				$message.= "\t * ".urlGen('delete',$lastId,$alias_full)."\n";
				$message.= "\n";
				$message.= _("Après confirmation, vous pourez suspendre temporairement cet email poubelle vous pouvez vous rendre sur le lien ci-dessou : \n");
				$message.= "\t * ".urlGen('disable',$lastId,$alias_full)."\n";
				SendEmail($email,_("Confirmation alias ").$alias,$message);
				echo '<div class="highlight-2">'._("Votre email ").'('.$email.') '._("nous étant inconnu, une confirmation vous a été envoyé par email.").'</div>';
			}
		}
	// delete
	} else if (isset($_POST['del'])) {
		if ($id = $dbco->query("SELECT id FROM ".DBTABLEPREFIX."alias WHERE email = '".$email."' AND alias = '".$alias_full."'")->fetchColumn()) {
			$message= _("Confirmation de la création de votre redirection email poubelle : ");
			$message= $alias_full.' => '.$email."\n";
			$message= _("Cliquer sur le lien ci-dessous pour confirmer la suppression : \n");
			$message.= "\t * ".urlGen('delete',$id,$alias_full)."\n\n";
			$message.= _("Sinon pour suspendre temporairement cet email poubelle vous pouvez vous rendre sur le lien ci-dessou : \n");
			$message.= "\t * ".urlGen('disable',$id,$alias_full)."\n";
			SendEmail($email,_("Suppression de l\'alias ").$alias,$message);
			echo '<div class="highlight-2">'._("Un email de confirmation vient de vous être envoyé").'.</div>';
		} else {
			echo '<div class="highlight-1">'._("Erreur : impossible de trouver cet email poubelle").'</div>';
		}
	// disable
	} else if (isset($_POST['disable'])) {
		DisableAlias(null, $alias_full, $email);
	// enable
	} else if (isset($_POST['enable'])) {
		EnableAlias(null, $alias_full, $email);
	}

	// memory email
	if (isset($_POST['memory'])) {
		setcookie ("email", StripCleanToHtml($email), time() + 31536000);
	} else if (isset($_COOKIE['email'])) {
		unset($_COOKIE['email']);
	}
}

//////////////////
// Printing form
//////////////////

?>

<form action="<?= URLPAGE?>" method="post">
<div id="onglet" style="display: none;">
	<input type="button" value=<?php echo _("Ajouter") ?> id="onglet-add" onClick="ongletChange(this.id)" /> 
	<input type="button" id="onglet-list" value=<?php echo _("Lister") ?> onClick="ongletChange(this.id)" /> 
	<input type="button" id="onglet-del" value=<?php echo _("Supprimer") ?> onClick="ongletChange(this.id)" /> 
	<input type="button" id="onglet-dis" value=<?php echo _("Suspendre") ?> onClick="ongletChange(this.id)" />
	<input type="button" id="onglet-en" value=<?php echo _("Reprendre") ?> onClick="ongletChange(this.id)" />
	<input type="hidden" name="onglet-actif" id="onglet-actif" value="onglet-add" />
</div>
<div id="form-email">
	<label for="email"><?php echo _("Votre email réel") ?> : </label>
	<input type="text" name="email" <?php if (isset($_COOKIE['email'])) { echo 'value="'.$_COOKIE['email'].'"'; } ?> id="input-email" size="24" border="0"  onkeyup="printForm()" onchange="printForm()"  /> 
	<input class="button2" type="submit" name="list" id="button-list" value="Lister" />
	<input type="checkbox" name="memory" id="check-memory" <?php if (isset($_COOKIE['email'])) { echo 'checked="checked" '; } ?>/> <?php echo _("Mémoriser")?>
</div>
<div id="form-alias">
	<label for="alias"><?php echo _("Nom de l'email poubelle")?> : </label>
	<input type="text" name="alias" id="input-alias" size="24" border="0" onkeyup="printForm()" onchange="printForm()" placeholder=<?php echo _("Ex : jean-petiteannonce") ?>/> @<?php
		$domains = explode(';', DOMAIN);
		if (count($domains) == 1) {
			echo DOMAIN.'<input type="hidden" value="'.DOMAIN.'" name="domain" id="input-domain" />';
		} else {
			echo '<select name="domain" id="input-domain">';
			foreach ($domains as $one_domain)  {
				echo '<option value="'.$one_domain.'">'.$one_domain.'</option>';
			}
			echo '</select>';
		}
	?>
	<select name="life" id="input-life">
		<option value="0"><?php echo _("Illimité")?></option>
		<option value="7200"><?php echo _("2 heure")?></option>
		<option value="21600"><?php echo _("6 heures")?></option>
		<option value="86400"><?php echo _("1 jour")?></option>
		<option value="604800"><?php echo _("7 jours")?></option>
		<option value="1296000"><?php echo _("15 jours")?></option>
		<option value="2592000"><?php echo _("30 jours")?></option>
		<option value="7776000"><?php echo _("90 jours")?></option>
	</select>
</div>
<div id="form-comment">
	<label for="comment"><?php echo _("Un commentaire pour l'ajout ? (pour votre mémoire)")?></label>
	<input type="text" name="comment" size="54" placeholder=<?php echo _("Ex : Inscription sur zici.fr") ?>/>
</div>
<div id="form-submit">
	<input class="button" type="submit" id="button-add" name="add" value=<?php echo _("Activer") ?> />
	<input class="button" type="submit" id="button-del" name="del" value=<?php echo _("Supprimer") ?> />
	<input class="button" type="submit" id="button-enable" name="enable" value=<?php echo _("Reprendre") ?> />
	<input class="button" type="submit" id="button-disable" name="disable" value=<?php echo _("Suspendre") ?> />
</div>
<div id="lePecheur" style="display: none;">
	<input name="username" type="text" />
</div>
</form>

<script type="text/javascript">
	function validateEmail(email) { 
		var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		return re.test(email);
	} 
	function printForm() {
		if (validateEmail(document.getElementById('input-email').value) && document.getElementById('input-alias').value != '') {
			document.getElementById('input-alias').disabled = false; 
			document.getElementById('input-domain').disabled = false; 
			document.getElementById('button-list').disabled = false; 
			document.getElementById('button-add').disabled = false; 
			document.getElementById('button-disable').disabled = false; 
			document.getElementById('button-enable').disabled = false; 
			document.getElementById('button-del').disabled = false; 
			document.getElementById('input-life').disabled = false; 
			if (document.getElementById('onglet-actif').value == 'onglet-add') {
				document.getElementById('form-comment').style.display = "block" ;
			}
		} else if (validateEmail(document.getElementById('input-email').value)) {
			document.getElementById('input-alias').disabled = false; 
			document.getElementById('input-domain').disabled = false; 
			document.getElementById('button-list').disabled = false;
			document.getElementById('input-life').disabled = false;
			document.getElementById('form-comment').style.display = "display" ;
			document.getElementById('button-add').disabled = true; 
			document.getElementById('button-disable').disabled = true; 
			document.getElementById('button-enable').disabled = true; 
			document.getElementById('button-del').disabled = true; 
			document.getElementById('input-life').disabled = true;
			document.getElementById('form-comment').style.display = "none" ;
		} else {
			document.getElementById('input-alias').disabled = true; 
			document.getElementById('input-domain').disabled = true; 
			document.getElementById('button-list').disabled = true; 
			document.getElementById('button-add').disabled = true; 
			document.getElementById('button-disable').disabled = true; 
			document.getElementById('button-enable').disabled = true; 
			document.getElementById('button-del').disabled = true; 
			document.getElementById('input-life').disabled = true;
			document.getElementById('form-comment').style.display = "none" ;
		}
	}
	function ongletPrint() {
		var ongletActif = document.getElementById('onglet-actif').value;
		document.getElementById('onglet-add').className = "close" ;
		document.getElementById('onglet-del').className = "close" ;
		document.getElementById('onglet-list').className = "close" ;
		document.getElementById('onglet-en').className = "close" ;
		document.getElementById('onglet-dis').className = "close" ;
		document.getElementById(ongletActif).className = "open" ;
		document.getElementById('input-life').style.display = "none" ;
		document.getElementById('form-alias').style.display = "inline-block" ;
		document.getElementById('button-add').style.display = "none" ;
		document.getElementById('button-del').style.display = "none" ;
		document.getElementById('button-list').style.display = "none" ;
		document.getElementById('button-disable').style.display = "none" ;
		document.getElementById('button-enable').style.display = "none" ;
		if (ongletActif == 'onglet-add') {
			document.getElementById('button-add').style.display = "inline-block" ;
			document.getElementById('input-life').style.display = "inline-block" ;
		} else if (ongletActif == 'onglet-del') {
			document.getElementById('button-del').style.display = "inline-block" ;
		} else if (ongletActif == 'onglet-en') {
			document.getElementById('button-enable').style.display = "inline-block" ;
		} else if (ongletActif == 'onglet-dis') {
			document.getElementById('button-disable').style.display = "inline-block" ;
		} else if (ongletActif == 'onglet-list') {
			document.getElementById('button-list').style.display = "inline-block" ;
			document.getElementById('form-alias').style.display = "none" ;
		}
	}
	function ongletChange(ongletValue) {
		document.getElementById('onglet-actif').value = ongletValue;
		ongletPrint();
	}
	document.getElementById('onglet').style.display = "block" ;
	ongletPrint();
	printForm();
</script>
<p><?php echo _("Version")?> <?= VERSION ?> - <?php echo _("Créé par David Mercereau sous licence GNU GPL v3")?></p>
<p><?php echo _("Télécharger et utiliser ce script sur le site du projet")?> <a target="_blank" href="http://forge.zici.fr/p/emailpoubelle-php/">emailPoubelle.php</a></p>

<?php 
// execute lifeExpir if isn't in crontab
if (!CRON) { LifeExpire(); }
// Close connexion DB
$dbco = null;
// checkupdate
echo CheckUpdate(); 
} // end maintenance mod
?>
