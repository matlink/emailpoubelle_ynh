<?php

//////////////////
// Function 
//////////////////

// Verification des emails
function VerifMXemail($email) {
    if (CHECKMX) {
    	$domaine=explode('@', $email);
    	$r = new Net_DNS2_Resolver(array('nameservers' => array(NS1, NS2)));
    	try {
            $result = $r->query($domaine[1], 'MX');
    	} catch(Net_DNS2_Exception $e) {
    		return false;
    	}
    	if ($result->answer) {
    		return true;
    	} else {
    		return false;
    	}
    } else {
        return true;
    }
}

// postmap command
function UpdateVirtualDB() {
	global $dbco;
	try {
		$selectcmd = $dbco->prepare("SELECT status, alias, email
									FROM ".DBTABLEPREFIX."alias 
									WHERE status > 0
									ORDER BY id ASC");
		$selectcmd->execute();
	} catch ( PDOException $e ) {
		echo "DB error :  ", $e->getMessage();
		die();
	}
	$file_content=null;
	while($alias_db = $selectcmd->fetch()) {
		if ($alias_db['status'] == 5) {
			$file_content .= $alias_db['alias'].' '.$alias_db['email']."\n";
		} else if ($alias_db['status'] == 3) {
			$file_content .= $alias_db['alias']." devnull\n";
		}
	}
	$alias_file=fopen(FICHIERALIAS,'w'); 
	fputs($alias_file, $file_content);
	fclose($alias_file);
	exec(BIN_POSTMAP.' '.FICHIERALIAS,$output,$return);
}

// add new alias
function AjouterAlias($status, $alias,$email, $life, $comment) {
	global $dbco;
	$dateCreat=date('Y-m-d H:i:s');
	if ($life == 0) {
		$dateExpir=NULL;
	} else {
		$dateExpir=date('Y-m-d H:i:s', time()+$life);
	}
	try {
		$insertcmd = $dbco->prepare("INSERT INTO ".DBTABLEPREFIX."alias (status, alias, email, dateCreat, dateExpir, comment) 
										VALUES (:status, :alias, :email, :dateCreat, :dateExpir, :comment)");
		$insertcmd->bindParam('status', $status, PDO::PARAM_INT);
		$insertcmd->bindParam('alias', $alias, PDO::PARAM_STR);
		$insertcmd->bindParam('email', $email, PDO::PARAM_STR);
		$insertcmd->bindParam('dateCreat', $dateCreat, PDO::PARAM_STR);
		$insertcmd->bindParam('dateExpir', $dateExpir, PDO::PARAM_STR);
		$insertcmd->bindParam('comment', $comment, PDO::PARAM_STR);
		$insertcmd->execute();
	} catch ( PDOException $e ) {
		echo "DB error :  ", $e->getMessage();
		die();
	}
	UpdateVirtualDB();
	return $dbco->lastInsertId();
}

// delete alias
function DeleteAlias($id, $alias_full) {
	global $dbco;
	if ($dbco->query("SELECT COUNT(*) FROM ".DBTABLEPREFIX."alias WHERE alias = '".$alias_full."' AND id = ".$id)->fetchColumn() != 0) {
		try {
			$deletecmd = $dbco->prepare("DELETE FROM ".DBTABLEPREFIX."alias WHERE id = :id AND alias =  :alias_full");
			$deletecmd->bindParam('id', $id, PDO::PARAM_INT);
			$deletecmd->bindParam('alias_full', $alias_full, PDO::PARAM_STR);
			$deletecmd->execute();
			echo '<div class="highlight-3">'._("l\'email ").'<b>'.$alias_full.'</b> '._("a bien été supprimé").'</div>';
		} catch ( PDOException $e ) {
			echo "DB error :  ", $e->getMessage();
			die();
		}	
	} else {
		echo '<div class="highlight-1">'._("Erreur : email poubelle inconnu").'</div>';
	}
	UpdateVirtualDB();
}

// enable alias
function EnableAlias($id, $alias_full, $email) {
	global $dbco;
	if ($id == null) {
		$selectcmd = $dbco->prepare("SELECT id,status FROM ".DBTABLEPREFIX."alias WHERE email = :email AND alias = :alias_full");
		$selectcmd->bindParam('email', $email, PDO::PARAM_STR);
	} else {
		$selectcmd = $dbco->prepare("SELECT id,status FROM ".DBTABLEPREFIX."alias WHERE id = :id AND alias = :alias_full");
		$selectcmd->bindParam('id', $id, PDO::PARAM_INT);
	}
	$selectcmd->bindParam('alias_full', $alias_full, PDO::PARAM_STR);
	$selectcmd->execute();
	$alias_fetch = $selectcmd->fetch();
	if (! $alias_fetch) {
		echo '<div class="highlight-1">'._("Erreur : impossible de trouver cet email poubelle").'</div>';
	} else if ($alias_fetch['status'] == 3) {
		UpdateStatusAlias($alias_fetch['id'], $alias_full, 5);
		echo '<div class="highlight-3">'._("La réception sur").' <b>'.$alias_full.'</b> '._("est de nouveau active").'.</div>';
	} else if ($alias_fetch['status'] == 5) {
		echo '<div class="highlight-2">'._("La réception sur").' <b>'.$alias_full.'</b> '._("est déjà active").'.</div>';
	} else if ($alias_fetch['status'] == 0) {
		echo '<div class="highlight-1">'._("La réception sur").' <b>'.$alias_full.'</b '._("n\'à pas été confirmé par email").'.</div>';
	} else {
		echo '<div class="highlight-1">'._("Erreur : status inconnu").'</div>';
	}
	UpdateVirtualDB();
}

// disable alias
function DisableAlias($id, $alias_full, $email) {
	global $dbco;
	if ($id == null) {
		$selectcmd = $dbco->prepare("SELECT id,status FROM ".DBTABLEPREFIX."alias WHERE email = :email AND alias = :alias_full");
		$selectcmd->bindParam('email', $email, PDO::PARAM_STR);
	} else {
		$selectcmd = $dbco->prepare("SELECT id,status FROM ".DBTABLEPREFIX."alias WHERE id = :id AND alias = :alias_full");
		$selectcmd->bindParam('id', $id, PDO::PARAM_INT);
	}
	$selectcmd->bindParam('alias_full', $alias_full, PDO::PARAM_STR);
	$selectcmd->execute();
	$alias_fetch = $selectcmd->fetch();
	if (! $alias_fetch) {
		echo '<div class="highlight-1">'._("Erreur : impossible de trouver cet email poubelle").'</div>';
	} else if ($alias_fetch['status'] == 5) {
		UpdateStatusAlias($alias_fetch['id'], $alias_full, 3);
		echo '<div class="highlight-3">'._("La réception sur").' <b>'.$alias_full.'</b> '._("est maintenant suspendu").'.</div>';
	} else if ($alias_fetch['status'] == 3) {
		echo '<div class="highlight-2">'._("La réception sur").' <b>'.$alias_full.'</b> '._("est déjà suspendu").'.</div>';
	} else if ($alias_fetch['status'] == 0) {
		echo '<div class="highlight-1">'._("La réception sur").' <b>'.$alias_full.'</b> '._("ne peut être suspendu car elle n\'a pas encore été activé").'.</div>';
	} else {
		echo '<div class="highlight-1">'._("Erreur : status inconnu").'</div>';
	}
	UpdateVirtualDB();
}

// update alias status
function UpdateStatusAlias($id, $alias_full, $status) {
	global $dbco;
	try {
		$updatecmd = $dbco->prepare("UPDATE ".DBTABLEPREFIX."alias SET status = $status WHERE id = :id AND alias = :alias_full");
		$updatecmd->bindParam('id', $id, PDO::PARAM_INT);
		$updatecmd->bindParam('alias_full', $alias_full, PDO::PARAM_STR);
		$updatecmd->execute();
	} catch ( PDOException $e ) {
		echo "DB error :  ", $e->getMessage();
		die();
	}
	UpdateVirtualDB();
}

// parse file for blacklist and aliasdeny
function parseFileRegex($file, $chaine) {
    $return=false;
    $handle = fopen($file, 'r');
    while (!feof($handle)) {
        $buffer = fgets($handle);
        $buffer = str_replace("\n", "", $buffer);
        if ($buffer) {
            if (!preg_match('/^(#|$|;)/', $buffer) && preg_match($buffer, $chaine)) {
                $return=true;
                break;
            }
        }
    }
    fclose($handle);
    return $return;
}

// check blacklistemail
function BlacklistEmail($email) {
    if (defined('BLACKLIST')) {
        return parseFileRegex(BLACKLIST, $email);
    } else {
        return false;
    }
}

// check aliasdeny
function AliasDeny($alias) {
    if (defined('ALIASDENY')) {
        return parseFileRegex(ALIASDENY, $alias);
    } else {
        return false;
    }
}

// list alias 
function ListeAlias($email) {
	global $dbco;
	try {
		$selectcmd = $dbco->prepare("SELECT id, status, alias, dateCreat, dateExpir, comment 
									FROM ".DBTABLEPREFIX."alias 
									WHERE email = :email AND status > 0
									ORDER BY status DESC");
		$selectcmd->bindParam('email', $email, PDO::PARAM_STR);
		$selectcmd->execute();
	} catch ( PDOException $e ) {
		echo "DB error :  ", $e->getMessage();
		die();
	}
	$nb_alias=0;
	$nb_alias_disable=0;
	$message= "## "._("Liste de vos redirections poubelles active")." : \n\n";
	while($alias_db = $selectcmd->fetch()) {
		if ($alias_db['status'] == 3 && $nb_alias_disable == 0) {
			$message.= "## "._("Liste de vos redirections poubelles désactivé")." : \n\n";
		} 
		$message.=" * ".$alias_db['alias']._(" Créé le ").$alias_db['dateCreat'];
		if ($alias_db['dateExpir']) {
			$message.=_(" et expire le ").$alias_db['dateExpir'];
		}
		$message.="\n";
		if ($alias_db['comment']) {
			$message.='\t'._("Commentaire : ").$alias_db['comment']."\n";
		}
		if ($alias_db['status'] == 5) {
			$message.='\t'._("Désactiver : ").urlGen('disable',$alias_db['id'],$alias_db['alias'])."\n";
			$nb_alias++;
		} else {
			$message.='\t'._("Activer : ").urlGen('enable',$alias_db['id'],$alias_db['alias'])."\n";
			$nb_alias_disable++;
		}
		$message.='\t'._("Supprimer : ").urlGen('delete',$alias_db['id'],$alias_db['alias'])."\n\n";
	}
	$nb_alias_total = $nb_alias + $nb_alias_disable;
	if ($nb_alias_total == 0) {
		return false;
	} else {
		SendEmail($email,_('Liste de vos ').$nb_alias_total._(' redirection(s) poubelle(s)'),$message);
		return true;
	}
}

function SendEmail($recipient, $sujet, $message) {
	$header = "From: ".EMAILFROM."\n";
	$header.= "MIME-Version: 1.0\n";
	if (preg_match('#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#', $recipient)) {
		$header = str_replace("\n", "\r\n", $header);
		$message = str_replace("\n", "\r\n", $header);
	}
	$message="Bonjour,\n\n".$message."\n\n".
	mail($recipient,EMAILTAGSUJET.' '.$sujet,$message,$header);
}

function urlGen($act,$id,$alias_full) {
	$idUrl=base64_encode($id.';'.$alias_full);
	if (URLREWRITE_START && URLREWRITE_MIDDLE && URLREWRITE_END) {
		return URLREWRITE_START.$act.URLREWRITE_MIDDLE.$idUrl.URLREWRITE_END;
	} else {
		return URLPAGE."?act=".$act."&value=".$idUrl;
	}
}
function urlUnGen($get_value) {
	$explode_get_value = explode(';', base64_decode($get_value));
	$return['id']=$explode_get_value[0];
	$return['alias_full']=$explode_get_value[1];
	return $return;
}

// Source http://css-tricks.com/serious-form-security/
function StripCleanToHtml($s){
		// Restores the added slashes (ie.: " I\'m John " for security in output, and escapes them in htmlentities(ie.:  &quot; etc.)
		// Also strips any <html> tags it may encouter
		// Use: Anything that shouldn't contain html (pretty much everything that is not a textarea)
		return htmlentities(trim(strip_tags(stripslashes($s))), ENT_NOQUOTES, "UTF-8");
}
function CleanToHtml($s){
		// Restores the added slashes (ie.: " I\'m John " for security in output, and escapes them in htmlentities(ie.:  &quot; etc.)
		// It preserves any <html> tags in that they are encoded aswell (like &lt;html&gt;)
		// As an extra security, if people would try to inject tags that would become tags after stripping away bad characters,
		// we do still strip tags but only after htmlentities, so any genuine code examples will stay
		// Use: For input fields that may contain html, like a textarea
		return strip_tags(htmlentities(trim(stripslashes($s))), ENT_NOQUOTES, "UTF-8");
}

//////////////////
// Admin function
//////////////////

function CheckUpdate() {
	if (CHECKUPDATE) {
		if (! is_file(DATA.'/checkupdate') || filemtime(DATA.'/checkupdate') + CHECKUPDATE < time()) {
			$ep_get_version = @file_get_contents('http://poubelle.zici.fr/ep_checkupdate');
			$ep_version_file=fopen(DATA.'/checkupdate','w'); 
			fputs($ep_version_file, $ep_get_version);
			fclose($ep_version_file);
			if (DEBUG) { echo 'ep_checkupdate_downloaded : '.file_get_contents(DATA.'/checkupdate').'\n'; }
		} 
		$file_current_version = trim(file_get_contents(DATA.'/checkupdate'));
		if ($file_current_version != '' && $file_current_version != VERSION) {
			return '<p>'._("Upgrade note : Votre version est en ").VERSION._(' alors que la version en cours est en ').$file_current_version.'</p>';
		} else {
			return false;
		}
	}
}

function LifeExpire() {
	global $dbco;
	try {
		$deletecmd = $dbco->prepare("DELETE FROM ".DBTABLEPREFIX."alias WHERE dateExpir IS NOT NULL AND dateExpir < '".date('Y-m-d H:i:s')."'");
		$deletecmd->execute();
	} catch ( PDOException $e ) {
		echo "DB error :  ", $e->getMessage();
		die();
	}
}
?>
