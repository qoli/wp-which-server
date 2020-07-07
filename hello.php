<?php
/**
 * @package Which Server
 * @version 1.0.0
 */
/*
Plugin Name: Which Server
Plugin URI: https://www.5mlstudio.com
Description: This plugin is used to determine if a customer is from China or not, and to prompt them as to which site they should jump to.
Author: qoli wong
Version: 1.0.0
Author URI: https://www.5mlstudio.com
*/


// This just echoes the chosen line, we'll position it later.
function jumpCheckIP() {

	$isInChina = false; 
	$ip = $_SERVER['REMOTE_ADDR']; // This will contain the ip of the request 
	// This service tells me where the IP address is from and gives me more data than I need. 
	$userData = json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$ip)); 
	if (is_Null($userData) || empty($userData) || $userData->geoplugin_countryCode == "CN") { 
	    $isInChina = true; // Count no data as in China maybe to be paranoid. 
	}

	jumpShowAlert($isInChina);

}

function jumpShowAlert($isCN) {
	$langWelcomeCN = "欢迎你中国地区的用户";
	$langWelcomeEN = "Welcome";

	$langTextCN = "请选择跳转到“中国站点”，以获得适用于中国地区的更好服务。";
	$langTextEN = "For more information on the international version, please visit the international version website.";

	$langButtonCN = "访问中国站点";
	$langButtonEN = "international website";

	$langHiddenCN = "隐藏";
	$langHiddenEN = "Hide";

	$urlCN = "http://www.chinaelg.cn";
	$urlEN = "http://www.chinaelg.com";

	$langText = $langTextEN;
	$langWelcome = $langWelcomeEN;
	$langButton = $langButtonEN;
	$langHidden = $langHiddenEN;
	$url = $urlEN;

	if ($isCN) {
		$langText = $langTextCN;
		$langWelcome = $langWelcomeCN;
		$langButton = $langButtonCN;
		$langHidden = $langHiddenCN;
		$url = $urlCN;
	}

	$www = $_SERVER['SERVER_NAME'];

	$isShowAlert = true;

	if ($isCN) {
		if ($www == "www.chinaelg.cn") {
			$isShowAlert = false;
		}
	} else {
		if ($www == "www.chinaelg.com") {
			$isShowAlert = false;
		}
	}

	if ($_COOKIE["hideAlert"] != "1") {
		$isShowAlert = false;
	}

	if ($isShowAlert) {
		echo '
		<div id="jumpDiv" class="jumpDiv max-w-sm rounded overflow-hidden shadow-lg">
		    <div class="px-6 py-4">
		        <div class="font-bold text-xl mb-2">'.$langWelcome.'</div>
		        <p class="text-gray-700 text-base">'.$langText.'</p>
		    </div>
		    <div class="px-6 py-4">
		        <p><a class="bg-white hover:bg-gray-100 text-gray-800 font-semibold py-2 px-4 border border-gray-400 rounded shadow" href="'.$url.'">'.$langButton.'</a></p>
		        <p><a class="text-blue-500 hover:text-blue-800" href="javascript:void(0)" onclick="hideAlert()" >'.$langHidden.'</a></p>
		    </div>
		</div>
		';

		echo "
		<script>
	    function setCookie(key, value, expiry) {
	        var expires = new Date();
	        expires.setTime(expires.getTime() + (expiry * 24 * 60 * 60 * 1000));
	        document.cookie = key + '=' + value + ';expires=' + expires.toUTCString();
	    }

		function hideAlert() {
			setCookie('hideAlert','1','5');
			jQuery('#jumpDiv').hide();
		}

		</script>
		";
	}
	

}


// Now we set that function up to execute when the admin_notices action is called.
add_action( 'wp_footer', 'jumpCheckIP' );

// We need some CSS to position the paragraph.
function jump_css() {
	echo '<link href="https://cdn.bootcss.com/tailwindcss/1.2.0/tailwind.min.css" rel="stylesheet">';
	echo "
	<style type='text/css'>
	.jumpDiv {
		z-index: 5000000;
		position: absolute;
		top: 4em;
		left: 50%;
		background-color: #fff;
		transform: translate(-50%, 0);
	}
	</style>
	";
}

add_action( 'get_header', 'jump_css' );