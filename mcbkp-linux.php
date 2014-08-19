<?php
// MINECRAFT data backup tool
// @ by Norckon

date_default_timezone_set('PRC');

// Backup filename and source path
//   BAKPATH: Where want you storage backup archive.
//   SRCPATH: Where Minecraft server installed.
$bakpath = "/root/mcbackups";
$srcpath = "/root/cauldron";

// Create Zip Archive
//   SRC: Which directories want to compress
//   DST: Where storage Zip Archive
//   MAPNAME: Which map want you backup in Minecraft
//   MODE: How to backup
//      1: Current map only
//      2: Current map and mods
//      3: All the server and data 
function CreateArchive($src, $dst, $mapname, $mode) {
	// Create new zip archive file
	switch($mode) {
		case 1:
			system("zip -qr $dst $src/$mapname/");
			break;
		case 2:
			system("zip -qr $dst $src/$mapname/ $src/mods/");
			break;
		case 3:
			system("zip -qr $dst $src/");
			break;
		default:
			echo "I don't known witch mode you want.";
	}
}

// Remove older backups
function RemoveOld ($path) {
	system("rm -f $path/mcbkp_*.mcb");
}

// Upload to Baidu netdisk
function bdpcsUpload($accesstoken, $path ,$filename) {
	system('curl -k -L -F "file=@'.$path.'" "https://c.pcs.baidu.com/rest/2.0/pcs/file?method=upload&access_token='.$accesstoken.'&path=/apps/fmcbackups/'.$filename.'"');
}

// Main function
function FMain() {
	global $bakpath,$srcpath;
	// Remove all older backups
	RemoveOld($bakpath);
	// Create new backup
	$bkparc = $bakpath."/mcbkp_".date("Y_m_d_H_i_s").".mcb";
	CreateArchive($srcpath, $bkparc, "world", 1);
	// Upload to baidudisk
	// 百度网盘 API 使用方法 ：http://www.fcsys.us/webapp/wordpress/?p=1292
	bdpcsUpload("YOUR_ACCESS_TOKEN", $bkparc, "/mcbkp_".date("Y_m_d_H_i_s").".mcb");
}

FMain();
?>