emailpoubelle
=============
A Yunohost version of Email Poubelle by David Mercereau. All thanks to David. 
http://www.mercereau.info/sortie-de-la-version-1-0-demailpoubelle-php-email-jetable-auto-hebergeable/
=============
For now, it requires a modification by yourself to 2 files : 

add : virtual_alias_maps = hash:/www/emailPoubelle/var/virtual to /etc/postfix/main.cf

echo "devnull:/dev/null" >> /etc/aliases

and do: newaliases && service postfix reload
