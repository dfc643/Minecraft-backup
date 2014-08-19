<?php
// MINECRAFT 数据备份工具
// @ by Norckon

date_default_timezone_set('PRC');

// 【请先设置】备份文件路径与MC服务器路径设置等相关设置
//   BAKPATH: 你想在何处存储备份文件
//   SRCPATH: MC服务器被安装在何处
//   GMAP   : 你所要备份的地图
//   GMODE  : 备份模式参照下面的说明
//   GTOKEN : 百度开发者的 ACCESS TOKEN
//   GAPPID : 你的PCS的的应用名称，即网盘apps文件夹中的文件夹名

$bakpath = "C:\\mcbkp";
$srcpath = "C:\\cauldron";
$gmap    = "world";
$gmode   = 1;
$gtoken  = "你的ACCESSTOKEN";
$gappid  = "你的PCS的的应用名称";
// 百度网盘 API 使用方法 ：http://www.fcsys.us/webapp/wordpress/?p=1292

// 创建压缩档案
//   SRC: 想要压缩哪一些目录
//   DST: 何处存储压缩档
//   MAPNAME: 想要备份MC服务器中的哪一张地图
//   MODE: 怎么备份
//      1: 只备份该地图
//      2: 备份地图与MOD
//      3: 备份所有服务器文件
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
                        echo "I don't known which mode you want.";
        }
}

// 删除陈旧备份
function RemoveOld ($path) {
        system("del /f /s /q $path\\mcbkp_*.mcbw");
}

// 上传至百度网盘
function bdpcsUpload($accesstoken, $path ,$appname ,$filename) {
        system('curl -# -k -L -F "file=@'.$path.'" "https://c.pcs.baidu.com/rest/2.0/pcs/file?method=upload&access_token='.$accesstoken.'&path=/apps/'.$appname.'/'.$filename.'"');
}

// Main function
function FMain() {
        global $bakpath,$srcpath,$gmap,$gmode,$gtoken,$gappid;
        // Remove all older backups
        RemoveOld($bakpath);
        // Create new backup
        $bkparc = $bakpath."\\mcbkp_".date("Y_m_d_H_i_s").".mcbw";
        CreateArchive($srcpath, $bkparc, $gmap, $gmaode);
        // Upload to baidudisk
        bdpcsUpload($gtoken, $bkparc, $gappid, "/mcbkp_".date("Y_m_d_H_i_s").".mcbw");
}

FMain();
?>