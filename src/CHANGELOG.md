## 1.1 (future)

  - support postfix-mysql (not plain text virtual file)
  - admin panel page with statistic
  - limit nb alias / email
  - javascript checkupgrade in admin page
  - add second table in database (for domain/checkupdate/intervale cron)
  - send email for advertisement action ?

## 1.0.1 (2014-01-06)

Bugfixes:

  - fixe ListeAlias no send email if error

## 1.0 (2013-10-02)

Features:

  - add database with PDO (remove plain text) 
		support mysql, sqlite, postgresql...
  - add multi-domain support
  - add memory email (with cookies)
  - add life for alias (optional)
  - add comment for alias (optional)
  - add template for example
  - add javascript in form (noscript compatible)
  - add cron for expir life email
  - add maintenance mode
  - add basic form anti-spam
  - add disable/enable alias function
  - pass UTF-8 encode

## 0.3 (2013-08-08) (without database)

  - add blacklist.txt regex
  - add function "alias list" 
  - add shell statistique script 
  - add readme

## 0.2 (2012-08-05)

Features:

  - add aliasdeny.txt regex
  - migrate to Net_DNS2
  - check email exist with DNS (check MX)

Bugfixes:

   - fixe http://forge.zici.fr/p/emailpoubelle-php/issues/4/

## 0.1b (2012-03-20)

  - start project

