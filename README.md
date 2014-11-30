emailpoubelle
=============
A Yunohost version of Email Poubelle by David Mercereau. All thanks to David. 
http://www.mercereau.info/sortie-de-la-version-1-0-demailpoubelle-php-email-jetable-auto-hebergeable/
=============

WARNING
=========
Using this will cause to disable the Yunohost ldap aliases ! Once installed, you won't be able to use the aliasses settings that you can see when you modify your personnal settings in the SSOWAT pannel.
Of course, once uninstalled, everything get back to normal. 

TODO : 
------
Test it ! (and check if that doesn't interfer with postfix and its aliases)

Test Translate !
Translate it to other languages !

cron job to remove redirections (0 */2 * * * /usr/bin/wget -q -t 1 -T 7200 -O /dev/null 'https://domain/poubelle/index.php?act=cron' >/dev/null 2>&1) conflict with SSOWAT + non-public app
