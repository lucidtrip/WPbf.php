WordPress getUser and XMLRPC BruteForce PoC
===========

> This PoC script relies of a vulnerability in WordPress systems been available from version 3.5 to version 4.0 (included) that allow a brute force attacks through xmlrpc.php file A malicious attacker might to hack a WordPress users using this vulnerability. And use the TALSOFT-2011-0526 vulnerability to get potential authors over the author.php for the BruteForce.

This script is an modification from El3ct71k's [WordPress XMLRPC BruteForce PoC](https://github.com/El3ct71k/WordPress-XMLRPC-BruteForce-PoC)

Why modification? I add the function "wp_getUsers" to better bruteforce users. And fixxed little bugs. Enumerate usernames using [TALSOFT-2011-0526](http://seclists.org/fulldisclosure/2011/May/493) advisory present in WordPress > 3.2-beta2, if no redirect is done try to match username from title of the user's archive page or page content.

## Core Requirements
* PHP with installt PHPcurl

**USAGE:**

`./wpbruteforce.php URL usernames.txt passwords.txt`

`php wpbruteforce.php URL usernames.txt passwords.txt`

`xargs -a wordpress.pot -i -n 1 -P 1 php wpbruteforce.php {} usernames.txt passwords.txt`



### How it works

1. go to authors.php and crawl the usernames
  * [TALSOFT-2011-0526](http://seclists.org/fulldisclosure/2011/May/493)
2. add combine usernames with usernames.txt and passwords.txt
3. bruteforce via xmlrpc.php
  * El3ct71k's [WordPress XMLRPC BruteForce PoC](https://github.com/El3ct71k/WordPress-XMLRPC-BruteForce-PoC)




#### changes
* 11.03.2015
  * bugfix EOL in the user and pass var
