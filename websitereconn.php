<?php
// PHP Web information Gathering Tool
require 'functions.php';

$white = "\e[97m";
$black = "\e[30m\e[1m";
$yellow = "\e[93m";
$orange = "\e[38;5;208m";
$blue   = "\e[34m";
$lblue  = "\e[36m";
$cln    = "\e[0m";
$green  = "\e[92m";
$fgreen = "\e[32m";
$red    = "\e[91m";
$magenta = "\e[35m";
$bluebg = "\e[44m";
$lbluebg = "\e[106m";
$greenbg = "\e[42m";
$lgreenbg = "\e[102m";
$yellowbg = "\e[43m";
$lyellowbg = "\e[103m";
$redbg = "\e[101m";
$grey = "\e[37m";
$cyan = "\e[36m";
$bold   = "\e[1m";
	$banner = $red."
	    :::     ::::    :::  ::::::::  ::::    ::: :::   ::: ::::    ::::   ::::::::  :::    :::  ::::::::  
  :+: :+:   :+:+:   :+: :+:    :+: :+:+:   :+: :+:   :+: +:+:+: :+:+:+ :+:    :+: :+:    :+: :+:    :+: 
 +:+   +:+  :+:+:+  +:+ +:+    +:+ :+:+:+  +:+  +:+ +:+  +:+ +:+:+ +:+ +:+    +:+ +:+    +:+ +:+        
+#++:++#++: +#+ +:+ +#+ +#+    +:+ +#+ +:+ +#+   +#++:   +#+  +:+  +#+ +#+    +:+ +#+    +:+ +#++:++#++ 
+#+     +#+ +#+  +#+#+# +#+    +#+ +#+  +#+#+#    +#+    +#+       +#+ +#+    +#+ +#+    +#+        +#+ 
#+#     #+# #+#   #+#+# #+#    #+# #+#   #+#+#    #+#    #+#       #+# #+#    #+# #+#    #+# #+#    #+# 
###     ### ###    ####  ########  ###    ####    ###    ###       ###  ########   ########   ########  
:::       ::: :::::::::: :::::::::       :::::::::  :::::::::: ::::::::   ::::::::  ::::    :::         
:+:       :+: :+:        :+:    :+:      :+:    :+: :+:       :+:    :+: :+:    :+: :+:+:   :+:         
+:+       +:+ +:+        +:+    +:+      +:+    +:+ +:+       +:+        +:+    +:+ :+:+:+  +:+         
+#+  +:+  +#+ +#++:++#   +#++:++#+       +#++:++#:  +#++:++#  +#+        +#+    +:+ +#+ +:+ +#+         
+#+ +#+#+ +#+ +#+        +#+    +#+      +#+    +#+ +#+       +#+        +#+    +#+ +#+  +#+#+#         
 #+#+# #+#+#  #+#        #+#    #+#      #+#    #+# #+#       #+#    #+# #+#    #+# #+#   #+#+#         
  ###   ###   ########## #########       ###    ### ########## ########   ########  ###    ####               
	".$white;
	print($banner. "\n\n");
	
	echo "Enter a target or domain : ";
	$url = trim(fgets(STDIN, 1024));
	if(substr($url, -1) === "/")
		$url = substr($url,0,-1);
	if(empty($url))
		die("\nSpecify target domain! Empty field..");
	if(strpos($url, "http") === false && strpos($url, "https") === false)
		die("\nPlease Include HTTP/HTTPS !!");
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_FRESH_CONNECT, TRUE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_NOBODY, TRUE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_HEADER, TRUE);
	curl_exec($ch);
	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	if($httpCode !== 200)
	{
		echo $red."\n\nTarget Domain may be down, check if you typed the link properly or you are not connected to Internet !!";
		echo "\nResponse code : ".$httpCode.$white;
		die();
	}
	
	//Main functions start here 
	$header = get_headers($url);
	echo $green;
	echo "\n\nHEADER INFORMATIONS";
	echo "\n==============================================\n";
	for( $x=0; $x<count($header); $x++ )
		echo $yellow.$header[$x]. "\n";
	echo $green."================================================\n";
	
	//Get robots.txt File
	$robourl = $url."/robots.txt";
	$handle = curl_init($robourl);
	curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);
	curl_exec($handle);
	$statusCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
	curl_close($handle);
	if($statusCode !== 200){
		echo $red."\nRobots.txt Not found !\n".$green;
	}
	else {
		$con = file_get_contents($robourl);
		echo "\nRobots.txt Found !";
		echo "\n================================================\n";
		echo $yellow.$con. "\n".$green;
		echo "================================================\n";
	}
	
	//Detect CMS
	$sc = file_get_contents($url);
	$cms = "Not detected";
	
	echo "\nDetecting Content Management System";
	echo "\n===============================================\n";
	if(strpos($sc, "/wp-content/") !== false || strpos($sc, "/wp-admin/") !== false)
		$cms = "Wordpress";
	if(strpos($sc, "Joomla") !== false)
		$cms = "Joomla";
	if(strpos($sc, "/skin/frontend/") !== false)
		$cms = "Magneto";
	echo "\nCMS : ".$yellow.$cms. "\n".$green;
	echo "===============================================\n";
	
	Echo "\nDo you want to perform admin panel bruteforce? This can take a while..";
	echo "\n1. Yes\n2. No\nYour choice : ";
	flush();
	$choice = fgets(STDIN, 1024);
	if($choice == 1){
	//Crawl for login pages
		if(substr($url, -1) !== "/")
			$url = $url."/";
		$get_directories_file = file_get_contents("directories.txt");
		$array = explode(",", $get_directories_file);
		$headers = [
			'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/97.0.4692.71 Safari/537.36'
		];
		echo "\nBruteforcing Directories and Admin Login panels\n";
		echo "===============================================";
		foreach($array as $directory){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url ."/". $directory);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_exec($ch);
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			if($httpCode === 200 || $httpCode === 302)
				echo $green."\nFound : ". $orange. $url . $directory;
			else
				echo $red."\nNot Found : ". $white. $url . $directory;
		}
		echo "\n===============================================\n";
	}
	$http = "http://";
	$https = "https://";
	$worldwideweb = "www.";
	if(strpos($url, $http) !== false)
		$URL = str_replace($http,"", $url);
	if(strpos($url, $https) !== false)
		$URL = str_replace($https, "", $url);
	$ip = gethostbyname($URL);
	echo "\nBASIC SITE INFORMATIONS";
	echo $green."\n============================================";
	echo $yellow."\n[IP] : ". $ip;
	echo "\n[SITE TITLE] : ". getTitle($url);
	echo $green."\n============================================";
	
	if($cms === "Wordpress"){
		$uploads_url = $url. "/wp-content/uploads";
		echo "\n\nGathering Wordpress Site Info";
		echo "\n============================================";
		if(getReadmeFile($url) === true)
			echo $yellow. "\nReadme file Found ! Link : ".$url."/readme.html".$green;
		 else
			echo $red. "\nReadme file not Found !";
		if(getlicensefile($url) === true)
			echo $yellow. "\nLicense file Found ! Link : ".$url."/license.txt".$green;
		else
			echo $red. "\nLicense file not Found !";
		$hd = get_headers($uploads_url, 1);
		if($hd[0] === "HTTP/1.1 200 OK"){
			$navigator = file_get_contents($uploads_url);
			if(strpos($navigator,"Index of /wp-content/uploads") !== false)
				echo $yellow.$url."/wp-content/uploads is Browseable";
		}else echo "\n". $red. $url. "/wp-content/uploads is not Browseable!".$white;
		if(getWordspressVersion($url)){
			//first method to find wordpress version !!
			echo $green."\nWordPress Version : ".$yellow.getWordspressVersion($url).$white;
		}else if(!getWordspressVersion($url)){
			echo $green."\nWordPress Version : ".$yellow.getWordspressVersionTwo($url).$white;
		}
	}
	echo "\n";
	echo "\n";
	echo $cyan;
	echo "=============================================================";
	echo "\n";
	echo "\tS Q L	  V U L N E R A B I L I T Y   S C A N N E R";
	echo "\n";
	echo "=============================================================";
	echo "\n";
	echo $green;
	$html = file_get_contents($url);
	$dom = new DOMDocument();
	@$dom->loadHtml($html);
	$links = $dom->getElementsByTagName('a');
	foreach($links as $link){
		$lol = $link->getAttribute('href');
		if(strpos($lol, "?") !== false){
			echo "\n";
			//echo "[-] Searching for Sql injections";
			$sqlerrors = file_get_contents("sqlerrors.txt", false);
			$sqllst = explode(",", $sqlerrors);
			if(strpos($lol, "://") !== false)
				$sqlurl = $lol . "'";
			else
				$sqlurl = $url . "/" . $lol . "'";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $sqlurl);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_exec($ch);
			$codeHTTP = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			if($codeHTTP == 200 || $codeHTTP == 301 || $codeHTTP == 302) {
				$sqlscrd = file_get_contents($sqlurl);
				$sqlvn = $red. "\nNot Found";
				foreach($sqllst as $sqllink){
					if(strpos($sqlscrd, $sqllink) !== false)
						$message = $green . "URL : " . $lol. " might be vulnerable to SQLInjection !".$green;
					else
						$message = $red . "URL : " . $lol. " might not be vulnerable to SQLInjection !".$green;
				}
				echo $message;
			}else echo $red."\nNot a Valid Url! Response code {$codeHTTP}";
		}else{
			echo "\n";
			echo $red."URL with no parameters : ".$lol;
		}
	}
	echo "\n";
	$url = $url . "/";
	echo $magenta;
	echo "\n\n====================================================";
	echo "\n";
	echo "\tC R A W L I N G  F O R  O T H E R  P A G E S";
	echo "\n";
	echo "=====================================================";
	echo "\n\n";
	echo $cyan;
	$content = file_get_contents("others.txt");
	$all = explode(",", $content);
	$user_agent= [
		'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/97.0.4692.71 Safari/537.36'
	];
	foreach($all as $dir){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url .$dir);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $user_agent);
		curl_exec($ch);
		$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		if($status === 200 || $status === 302 || $status === 301)
			echo $green."\n\nFound : ". $orange. $url . $dir. "\nResponse code : ".$status;
		else
			echo $red."\n\nNot Found : ". $white. $url . $dir. "\nResponse code : ".$status;
	}
?>
