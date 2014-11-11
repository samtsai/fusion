<?php

class Redirect {
    
    public static function refreshPage() {
        header("location: http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
        exit();
    }
    
    public static function gotoPage($page) {
   		
        header("location: http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/".$page);
        exit();
    }

    public static function gotoHome() {
        header("location: http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/index.php");
        exit();
    }
    
    public static function gotoSite($site) {
        if (!eregi("^http://[0-9a-zA-Z~_\-./%+?=]+$", $site)) {
            $external_site = "http://".$site;
        }
        else { $external_site = $site; }
        header("Location: ".$external_site);
        exit();
    }
    
}

?>