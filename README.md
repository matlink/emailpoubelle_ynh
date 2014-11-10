emailpoubelle
=============
A Yunohost version of Email Poubelle by David Mercereau. All thanks to David. 
http://www.mercereau.info/sortie-de-la-version-1-0-demailpoubelle-php-email-jetable-auto-hebergeable/
=============

TODO : 
------
Test it ! (and check if that doesn't interfer with postfix and its aliases)

English traduction (at least)

cron job to remove redirections (0 */2 * * * /usr/bin/wget -q -t 1 -T 7200 -O /dev/null 'https://domain/poubelle/index.php?act=cron' >/dev/null 2>&1) conflict with SSOWAT + non-public app
