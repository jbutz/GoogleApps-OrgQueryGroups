#!/usr/bin/php
<?php
// ONLY EDIT THE BELOW LINE!!
define('_GAM_EXEC', "./gam/gam.py");
/// DO NOT EDIT BELOW THIS LINE----------------------------------------
define('_GRP_REGEX', "/Member: (.*) Type: (.*) Direct Member: (.*)/");
define('_ORG_REGEX', "/ (.*)@(.*)/");
function help()
{
   echo "Usage: ".$argv[0]." [--detail] [--noremove] [--noadd] --group GOOGLE_GROUP --org SEARCH_STRING\n\n";
}
if($argc < 5)
{
	echo "Error: Too few arguments\n";
	help();
	return -1;
}
$doAdd		 = true;
$doRemove	 = true;
$detail		 = false;
$googleGroup = "";
$googleOrg   = "";
for($i = 1; $i < $argc; $i++)
{
	$arg = $argv[$i];
	switch($arg)
	{
		case '--noremove':
		{
			$doRemove = false;
		} break;
		case '--noadd':
		{
			$doAdd = false;
		} break;
		case '--detail':
		{
			$detail = true;
		} break;
		case '--group':
		{
			$googleGroup = $argv[ $i + 1 ];
			$i++;
		} break;
		case '--org':
		{
			$googleOrg = $argv[ $i + 1 ];
			$i++;
		} break;
	}
}
if(trim($googleGroup) == "")
{
	echo "Error: No group given\n";
	help();
	return -1;
}
if(trim($googleOrg) == "")
{
	echo "Error: No organization given\n";
	help();
	return -1;
}

/////////////////////////////////////////////////////////////////////////////
$inGoogleGroup = array();
$inGoogleOrg   = array();
$add		   = array();
$remove		= array();

// Get the list of members from gam
$command = _GAM_EXEC." info group ".$googleGroup;
$output  = shell_exec($command);
preg_match_all(_GRP_REGEX, $output, $matches);
foreach($matches[1] as $email)
{
	$inGoogleGroup[] = trim($email);
}

// Get the list of all organizations
$command = _GAM_EXEC." print orgs 2> /dev/null";
$output  = shell_exec($command);
$orgs	= explode("\n",trim($output));
$orgCnt  = 0;
foreach($orgs as $k => $org)
{
	if(strstr($org, $googleOrg) === false)
	{
		unset($orgs[$k]);
	}
	else
	{
		// Get the memebers
		$orgCnt++;
		$command = _GAM_EXEC.' info org "'.$org.'"  2> /dev/null';
		$result = shell_exec($command);
		$r = preg_match_all(_ORG_REGEX, $result, $matches);
		if($r > 0 && $r !== false)
		{
			foreach($matches[0] as $email)
			{
				$inGoogleOrg[] = trim($email);
			}
			$inGoogleOrg = array_unique($inGoogleOrg);
		}
	}
}
if($orgCnt == 0)
{
	echo "Error: No organizations with \"$googleOrg\" were found\n";
	return -1;
}
// Figure out the diff
$add = array_diff($inGoogleOrg,$inGoogleGroup); // In Org but not Group
$remove = array_diff($inGoogleGroup,$inGoogleOrg); // In group but not org

$addCnt = 0;
$remCnt = 0;
// Add
if($doAdd)
{
	foreach($add as $email)
	{
		$command = _GAM_EXEC." update group $googleGroup add member $email 2>&1";
		$t = trim(shell_exec($command));
		$t = $t == "" ? "Added $email to $googleGroup" : $t;
		if($detail)
			echo $t."\n";
		$addCnt++;
	}
}
// Remove
if($doRemove)
{
	foreach($remove as $email)
	{
		$command = _GAM_EXEC." update group $googleGroup remove member $email 2>&1";
		$t = trim(shell_exec($command));
		$t = $t == "" ? "Removed $email from $googleGroup" : $t;
		if($detail)
			echo $t."\n";
		$remCnt++;
	}
}
if($detail)
	echo "Added: $addCnt\nRemoved: $remCnt\n";
?>
