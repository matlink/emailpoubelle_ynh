#!/usr/bin/php
<?php
include('../conf.php');

# pour migrer du plain-text vers la base de donnée

// Connect DB
try {
	if (preg_match('/^sqlite/', DB)) {
		$dbco = new PDO(DB);
	} else {
		$dbco = new PDO(DB, DBUSER, DBPASS);
	}
	$dbco->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch ( PDOException $e ) {
	die('Connexion à la base '.$e->getMessage());
}

// postmap command
function UpdateVirtualDB_migrateTo10() {
	global $dbco;
	try {
		$selectcmd = $dbco->prepare("SELECT alias, email
									FROM ".DBTABLEPREFIX."alias 
									WHERE status = 5
									ORDER BY id ASC");
		$selectcmd->execute();
	} catch ( PDOException $e ) {
		echo "DB error :  ", $e->getMessage();
		die();
	}
	$file_content=null;
	while($alias_db = $selectcmd->fetch()) {
		$file_content .= $alias_db['alias'].' '.$alias_db['email']."\n";
	}
	$alias_file=fopen(FICHIERALIAS,'w'); 
	fputs($alias_file, $file_content);
	fclose($alias_file);
	exec(BIN_POSTMAP.' '.FICHIERALIAS,$output,$return);
}


// add new alias
function AjouterAlias_migrateTo10($status, $alias,$email, $life, $comment) {
	global $dbco;
	$dateCreat=date('Y-m-d H:i:s', 0);
	$dateExpir=NULL;
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
	return $dbco->lastInsertId();
}

$handle = fopen(FICHIERALIAS, 'r');
while (!feof($handle)) {
	$buffer = fgets($handle);
	if ($buffer) {
		$bufferExplode = explode(' ', $buffer);
		if (!preg_match('/^(#|$|;)/', $buffer)) {
			echo $bufferExplode[0].' -> '.$bufferExplode[1]."\n";
			AjouterAlias_migrateTo10(5, trim($bufferExplode[0]), trim($bufferExplode[1]), null, null);
		}
	}
}
fclose($handle);

UpdateVirtualDB_migrateTo10();

?>
