<?php
header("Content-Type: text/html; charset=UTF-8");
mysql_query("SET NAMES 'utf8'"); 
mysql_query('SET CHARACTER SET utf8');
mysql_query("SET NAMES utf8");

// this function creates random string for the referal link

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

	
    //We make sure that the user has logged in before attempting to use the Steam API to avoid warnings and wasted resources.
    if(isset($_SESSION['steamid'])){

    	include("settings.php");
        if (empty($_SESSION['steam_uptodate']) or $_SESSION['steam_uptodate'] == false or empty($_SESSION['steam_personaname'])) {
            //We mute alerts from the following line because we do not want to give away our API key in case file_get_contents() throws a warning.
            @ $url = file_get_contents("http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=".$steamauth['apikey']."&steamids=".$_SESSION['steamid']);
            if($url === FALSE) { die('Error: failed to fetch content form Steam. It may be down. Please, try again later.'); }
            $content = json_decode($url, true);
            $_SESSION['steam_steamid'] = $content['response']['players'][0]['steamid'];
            $_SESSION['steam_communityvisibilitystate'] = $content['response']['players'][0]['communityvisibilitystate'];
            $_SESSION['steam_profilestate'] = $content['response']['players'][0]['profilestate'];
            $_SESSION['steam_personaname'] = $content['response']['players'][0]['personaname'];
            $_SESSION['steam_lastlogoff'] = $content['response']['players'][0]['lastlogoff'];
            $_SESSION['steam_profileurl'] = $content['response']['players'][0]['profileurl'];
            $_SESSION['steam_avatar'] = $content['response']['players'][0]['avatar'];
            $_SESSION['steam_avatarmedium'] = $content['response']['players'][0]['avatarmedium'];
            $_SESSION['steam_avatarfull'] = $content['response']['players'][0]['avatarfull'];
            $_SESSION['steam_personastate'] = $content['response']['players'][0]['personastate'];
            if (isset($content['response']['players'][0]['realname'])) { 
    	           $_SESSION['steam_realname'] = $content['response']['players'][0]['realname'];
    	       } else {
    	           $_SESSION['steam_realname'] = "Real name not given";
            }
            $_SESSION['steam_primaryclanid'] = $content['response']['players'][0]['primaryclanid'];
            $_SESSION['steam_timecreated'] = $content['response']['players'][0]['timecreated'];
            $_SESSION['steam_uptodate'] = true;
        }
        
        $steamprofile['steamid'] = $_SESSION['steam_steamid'];
        $steamprofile['communityvisibilitystate'] = $_SESSION['steam_communityvisibilitystate'];
        $steamprofile['profilestate'] = $_SESSION['steam_profilestate'];
        $steamprofile['personaname'] = $_SESSION['steam_personaname'];
        $steamprofile['lastlogoff'] = $_SESSION['steam_lastlogoff'];
        $steamprofile['profileurl'] = $_SESSION['steam_profileurl'];
        $steamprofile['avatar'] = $_SESSION['steam_avatar'];
        $steamprofile['avatarmedium'] = $_SESSION['steam_avatarmedium'];
        $steamprofile['avatarfull'] = $_SESSION['steam_avatarfull'];
        $steamprofile['personastate'] = $_SESSION['steam_personastate'];
        $steamprofile['realname'] = $_SESSION['steam_realname'];
        $steamprofile['primaryclanid'] = $_SESSION['steam_primaryclanid'];
        $steamprofile['timecreated'] = $_SESSION['steam_timecreated'];
		
		$steamprofile['personaname']=mysql_real_escape_string($steamprofile['personaname']);
		$steamprofile['personaname'] = htmlspecialchars($steamprofile['personaname']);
		
		$steamprofile['realname'] =mysql_real_escape_string($steamprofile['realname'] );
		$steamprofile['realname']  = htmlspecialchars($steamprofile['realname'] );
		
		
		
		
		$usid = checkdbforuser($_SESSION['steamid']); 
		if($usid == "0")
		{
			$_SESSION['insertuserflag'] = "1";
		}
		else
		{
			$_SESSION['insertuserflag'] = "0";
			// do nothing here;		
		}
		if($_SESSION['insertuserflag'] == "1")
		{
            
             $has_csgo=false;
                $url=file_get_contents("http://api.steampowered.com/IPlayerService/GetOwnedGames/v0001/?key=9C9870D24C1A54325CFAAA11A50E4288&steamid=".$_SESSION['steamid']."&format=json");
                $games_owned=json_decode($url);
                
                foreach( $games_owned->response->games as $obj)
                {
                    if(intval($obj->appid)==730)
                    {
                        $has_csgo=true;
                        break;
                    }
                }
            
            
            
			$qinsert = "insert into user (usteamid,communityvisibilitystate,profilestate,personname,lastlogoff,profileurl,avatar,avatarmedium,avatarfull,personastate,realname,primaryclanid,timecreated,referal,points) values ('".$steamprofile['steamid']."','".$steamprofile['communityvisibilitystate']."','".$steamprofile['profilestate']."','".$steamprofile['personaname']."','".$steamprofile['lastlogoff']."','".$steamprofile['profileurl']."','".$steamprofile['avatar']."','".$steamprofile['avatarmedium']."','".$steamprofile['avatarfull']."','".$steamprofile['personastate']."','".$steamprofile['realname']."','".$steamprofile['primaryclanid']."','".$steamprofile['timecreated']."', '".generateRandomString()."',".(($_SESSION['ref'] != '' && $has_csgo==true) ? 5 : 0).")";
            
            if(isset($_SESSION['ref']) && $_SESSION['ref']!='')
            {
               
                
                if($has_csgo==true)
                {
                   $x=mysql_fetch_assoc(mysql_query("SELECT * FROM user where referal='".$_SESSION['ref']."'"));
                   $x['points']=intval($x['points']);
                   $x['referred']=intval($x['referred']);
                   $total=$x['points']+5;
                   
                   $x['referred']++;
                   mysql_query("UPDATE user SET points=".$total.", referred=".$x['referred']." WHERE referal='".$_SESSION['ref']."'");
                }
            }
            
            mysql_query("SET NAMES utf8");

            mysql_query($qinsert);

                $_SESSION['insertuserflag'] = "0";
                
                 unset($_SESSION['ref']);
            }
             else{
                mysql_query("UPDATE user SET personname='".$steamprofile['personaname']."' , avatar='".$steamprofile['avatar']."',avatarmedium='".$steamprofile['avatarmedium']."',avatarfull='".$steamprofile['avatarfull']."'  WHERE usteamid=".$steamprofile['steamid']);
                 
                unset($_SESSION['ref']);
             }
        }
    
?>
    
