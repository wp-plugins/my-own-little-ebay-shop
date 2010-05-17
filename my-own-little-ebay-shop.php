<?php
/*
Plugin Name: My own little eBay Shop
Plugin URI: http://fuck-dance-lets-art.com/my-own-little-ebay-shop-wordpress-plugin
Description: **THIS IS A ALPHA VERSION, CHANGES WILL HAPPEN!, THE FIRST RELEASE WILL COME VERY SOON**. Very fast plugin that cache your shop's content for a quicker and smoother user experience. Easy to set up, with some clever functionalities,  including retrieving your shop categories, excluding categories, renaming categories (only in wordpress), set a "refresh temp file" time. Displays your ebay shop listing, items infos (pictures, bids, price, etc‚ ...) with links to your listing and shop.
Version: 0.2.1
Author: Thomas Michalak aka TM
Author URI: http://fuck-dance-lets-art.com/
*/

/*  Copyright 2010  Thomas Michalak  (email : http://www.fuck-dance-lets-art.com/contact-me )

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/** TO THE LOVE OF MY LIFE, ANNA **/


/* Get essential Functions */
require_once('my-own-little-ebay-shop-functions.php');

/*************************************************************/
/*   THE OPTION PANNEL THE OPTION PANNEL THE OPTION PANNEL   */
/*************************************************************/
add_option(select_my_own_little_ebay_shop_page, '');
add_option(my_own_little_ebay_shop_username, 'annachocola');
add_option(my_own_little_ebay_shop_cats_excluded, '');
add_option(my_own_little_ebay_shop_categories, '');
add_option(my_own_little_ebay_shop_refresh, '86400');
add_option(my_own_little_ebay_shop_message_switch, 'on');
add_option(my_own_little_ebay_shop_welcome_message, 'Welcome to '.get_bloginfo('name').' ebay shop, browse my lovely items and buy!'); 
add_option(my_own_little_ebay_shop_welcome_category, '');

function my_own_little_ebay_shop_options() {
        	
	//The form
	echo '<div class="wrap">';

        echo '<h2>My Own Little eBay Shop</h2>';
        echo '<h3>Driving quality traffic using real-time eBay listing information. Providing eBay members with unique shopping experience. Display you ebay shop content, items infos (bids, price, etc…) with links to your listing. Using eBay API.</h3>';
	
        echo '<form method="post" action="options.php" id="my_own_little_ebay_shop_form">';
        wp_nonce_field('update-options');
        echo '<table class="my_own_little_ebay_shop_table">';
        
	// Select Page
        echo '<tr valign="top">';
        echo '<th scope="row">Choose a home for your shop</th>';
        echo '<td>';
		echo '<ul class="my_own_little_ebay_shop_list"><li><span>Select a page to display your shop</span> <select name="select_my_own_little_ebay_shop_page">';
	$all_pages = get_posts('numberposts=-1&post_type=page&post_status= ');
	foreach($all_pages as $post){
		$aPost = get_post($post);
		if($aPost->post_title == get_option('select_my_own_little_ebay_shop_page')){
		        $sel = 'selected = "selected"';	
		}else{
		        $sel = '';	
		}
		echo '<option value="'.$aPost->post_title.'"'.$sel.'>'.$aPost->post_title.' ('.$aPost->post_status.')</option>';
	}
	echo '</select></li>';
	echo '</td></tr>';
	
	
	// User Name
    echo '<tr valign="top">';
    echo '<th scope="row">The ebay shop id</th>';
    echo '<td>';
    echo '<p>This is your eaby shop id <small>(ex: http://stores.ebay.co.uk/<u><strong>Anna-Chocola</strong></u>)</p>';
	echo '<ul class="my_own_little_ebay_shop_list">';
	$textarea_content = get_option('my_own_little_ebay_shop_username');
	echo '<li><input class="textarea_style" id="my_own_little_ebay_shop_username" name="my_own_little_ebay_shop_username" value="'.$textarea_content.'"></li>';
	echo '</ul>';
	echo '</td>';
	echo '</tr>';
	
	//Shop Categories
	$my_own_little_ebay_shop_cats_excluded = get_option('my_own_little_ebay_shop_cats_excluded');
	$my_own_little_ebay_shop_categories = get_option('my_own_little_ebay_shop_categories');
	//ebayShopDebug($my_own_little_ebay_shop_categories);
	echo '<tr valign="top">';
    echo '<th scope="row">Manage the ebay shop categories</th>';
	echo '<td id="manageCategories">';
	echo '<a href="" id="retreiveCategories">Retreive my shop categories</a> <small>(this will delete your current categories\' list below)</small>';
	echo '<ul id="my_own_little_ebay_shop_cats" class="my_own_little_ebay_shop_list">';
	/*No Categories saved in the options*/
	if(!empty($my_own_little_ebay_shop_categories)){
		foreach($my_own_little_ebay_shop_categories as $key => $catNiceName){
			if($my_own_little_ebay_shop_categories[$key]['excluded'] == 'on') $checked = 'checked';
			else $checked = '';
			//Excluded default
			echo '<input name="my_own_little_ebay_shop_categories['.$key.'][excluded]" value="off" class="hidden"/>';
			//Rss
			echo '<input name="my_own_little_ebay_shop_categories['.$key.'][rss]" value="'.$my_own_little_ebay_shop_categories[$key]['rss'].'" class="hidden"/>';
			//Name for request and text file
			echo '<input name="my_own_little_ebay_shop_categories['.$key.'][requestName]" value="'.nicePath($key).'" class="hidden"/>';
			//Category Name and Nicename
			echo '<li><input type="checkbox" '.$checked.' name="my_own_little_ebay_shop_categories['.$key.'][excluded]"><input value="'.$my_own_little_ebay_shop_categories[$key]['niceName'].'" name="my_own_little_ebay_shop_categories['.$key.'][niceName]"/></li>';
		}
	}
	echo '</ul>';
	echo '</td></tr>';
		
	//Main Options
	//$tempTimes = array("never" => 0, "1h" => 3600, "12h" => 43200, "every day" => 86400, "every week" => 604800, "every month" => 18748800 );
	$tempTimes = array("4h" => 14400, "12h" => 43200, "every day" => 86400, "every week" => 604800, "every month" => 18748800 );
	echo '<tr valign="top">';
        echo '<th scope="row">Categories\' Cache Files time length</th>';
        echo '<td>';
        echo '<p>Cache file\'s life expectency (how long before cache files get recreated)</p>';
		echo '<ul class="switch_OnOff">';
		foreach($tempTimes as $t=>$tVal){
			if($tVal == get_option('my_own_little_ebay_shop_refresh')){
		        $sel = ' checked = true';
		        $sel_style = ' class = selected';
			}else{
		         $sel = '';
		        $sel_style = '';	
			}
			echo '<li><input type="radio"'.$sel.$sel_style.' value="'.$tVal.'" name="my_own_little_ebay_shop_refresh"><label>'.$t.'</label></li>';
		}
	echo '</td></tr>';
	
	
	// Welcome Message Options
	echo '<tr valign="top">';
	echo '<th scope="row">Welcome Message</th>';
	//Message On/Off
	echo '<td><ul class="switch_OnOff">';
	$message_status = get_option('my_own_little_ebay_shop_message_switch');
	$switch_name = array('On', 'Off');
	foreach($switch_name as $value){
		if($value == $message_status){
		        $sel = ' checked = true';
		        $sel_style = ' class = selected';
	        }else{
		        $sel = '';
		        $sel_style = '';
	        }
		//update_option(allInOneGallery_message_switch, false);
	        echo '<li><input type="radio"'.$sel.$sel_style.' value="'.$value.'" name="my_own_little_ebay_shop_message_switch"><label>'.$value.'</label></li>';
	}
	echo '</ul>';
	//Message content ( if switch is On )
	$textarea_content = get_option('my_own_little_ebay_shop_welcome_message');
	echo '<p> If the Welcome message is ON, display the message below</p>';
	echo '<textarea class="textarea_style" name="my_own_little_ebay_shop_welcome_message">';
	echo $textarea_content;
	echo '</textarea>';
	//Category to show First ( if switch if Off )
	echo '<p style="width:100%"> If the Welcome message is Off, Select a category to be displayed as default <small>(exluded categories won\'t appear in this option)</small></p>';
	echo '<select name="my_own_little_ebay_shop_welcome_category"';
	$welcome_cat = get_option('my_own_little_ebay_shop_welcome_category');
	
	foreach($my_own_little_ebay_shop_categories as $cat){
		if($cat['excluded'] === 'off'){
			if($welcome_cat === $cat['requestName']){
				$sel = 'selected = "selected"';
			}else{
				$sel = '';
			}
			echo '<option value="'.$cat['requestName'].'"'.$sel.'>'.$cat['niceName'].'</option>';
		}
	}
	echo '</select>';
	
	echo '</td></tr>';
	    
	//End of Options 
        echo '</table>';

        echo '<input type="hidden" name="action" value="update" />';
        echo '<input type="hidden" name="page_options" value="select_my_own_little_ebay_shop_page, my_own_little_ebay_shop_username, my_own_little_ebay_shop_categories, my_own_little_ebay_shop_cats_excluded, my_own_little_ebay_shop_refresh, my_own_little_ebay_shop_message_switch, my_own_little_ebay_shop_welcome_message, my_own_little_ebay_shop_welcome_category" />';

        echo '<p class="submit">';
        echo '<input type="submit" name="Submit" value="Save Changes" />';
        echo '</p>';

        echo '</form>';

  		//Extra Stuff
 		 echo '<p id="loading"><img src="'.myOwnLittleEbayShopPluginURLDirect().'/imgs/loader.gif"> Retreiving Categories, please wait…</p>';
	
        echo '</div>';
}

function my_own_little_ebay_shop_menu() {
        add_options_page('My Own Little eBay Shop Options', 'My Little eBay Shop', 8, __FILE__, 'my_own_little_ebay_shop_options');
}
function my_own_little_ebay_style_and_magic(){
	//A bit of style
	echo '<link rel="stylesheet" type="text/css" href="'.myOwnLittleEbayShopPluginURLDirect().'/my-own-little-ebay-shop-option-css.css" />';
	//A Bit of magic
	echo '<script type="text/javascript" src="'.myOwnLittleEbayShopPluginURLDirect().'/my-own-little-ebay-shop-option-js.js" /></script>';
}
add_thickbox();
add_action('admin_head', 'my_own_little_ebay_style_and_magic');

add_action('admin_menu', 'my_own_little_ebay_shop_menu');


/*************************************************************/
/*  THE PLUGIN THE PLUGIN THE PLUGIN THE PLUGIN THE PLUGIN   */
/*************************************************************/
/*check if the plugin should take effect*/

function littleShopTakeEffect(){
    //Look at user's page choice
	$userPageChoice = get_option(select_my_own_little_ebay_shop_page);
	//$userPageChoice = "Private: test";
	//look at the current page name
	$my_page_name = get_the_title();
    //Check if it's a page and compare it with the user's page choice
	if (is_page() && $my_page_name === $userPageChoice){         
                $doIt = TRUE;
        }else{
                $doIt = FALSE;
        }
        return $doIt;
}



function theLittleShop($theShop){

	if(littleShopTakeEffect() === TRUE) {
		
		//Settings
		$sellerID = get_option('my_own_little_ebay_shop_username');
		$welcomeMessage = get_option('my_own_little_ebay_shop_message_switch');
		$query = '';
		$nbrItemsPerPage = '9';
		$items = array();
		$tempValid = true;
		$maxDiffTime = get_option('my_own_little_ebay_shop_refresh');
	
		if(empty($_GET)){
			if( $welcomeMessage === 'On') $showWelcomeMessage = true;
			else {
				$showWelcomeMessage = false;
				$requestedCat = get_option('my_own_little_ebay_shop_welcome_category');
			}
		}else{
			if(isset($_GET["shopFor"]) || ctype_print($_GET["shopFor"])) $requestedCat = $_GET["shopFor"];
		}
	   
	   //Get Categories array with all infos: niceName, excluded, RSS, requestName
	   $categoriesInfosFromOption = get_option('my_own_little_ebay_shop_categories');
	    		
	    //Show Welcome Messsage
	    if($showWelcomeMessage){
		    $results = '<p>';
	    	$results.= get_option('my_own_little_ebay_shop_welcome_message');
	    	$results.= '<p>';
	    }
	    //Show Category content
	    else{
	    //If there is a Temp File
			$tempCatItemsPath = myOwnLittleEbayShopTempFolderPATH().$requestedCat.".txt";
			if(!tempFileValid($tempCatItemsPath, $maxDiffTime)){
				//Find the rss for the category requested
	    		foreach($categoriesInfosFromOption as $cat){
	    			if($cat['requestName'] === $requestedCat) {
	    			$requestedCatRss = $cat['rss'];
	    			checkRSSUpdate($requestedCatRss);
	    			break;
	    			}
	    		}
				//Create a temp file and return array of items
			    $items =  createCatItemsTempFile($sellerID, $query, $tempCatItemsPath, $requestedCatRss);
			}else{
				//Recent: get the Temp file and return a array of items
				$items = getTempFileContent($tempCatItemsPath);
			}
			//ebayShopDebug($items);
			
			//Show Category Items as Thumbs
			$results = '<ul id="thumbs">';
			foreach($items as $item) {
				$myID = $item['ItemID'];
			    $galleryURL = $item['GalleryURL'];
			    $price = $item['ConvertedCurrentPrice'];
				// For each result node, build a link and append it to $results
				$results .= "<li><a href=\"".myOwnLittleEbayShopPluginURL()."/my-own-little-ebay-shop-item.php?cat=$requestedCat&itemID=$myID\"><div class=\"thumb\"><img src=\"$galleryURL\"></div><p>£$price</p></a></li>";
			}	
			$results .= '</ul>';
	    }
	   
		
		
		//Show Menu
		$menu = '';
		$menu .= '<ul id="menu">';
		foreach($categoriesInfosFromOption as $cat){
		 	if($cat['excluded'] === 'off'){
		 		if($cat['requestName'] === $requestedCat) $class= "selected";
		 		else $class = "";
		  		$menu .= "<li class=\"$class\"><a href=\"?shopFor=".$cat['requestName']."\">".$cat['niceName']."</a></li>";
		 	}
		}
		$menu .= '</ul>';
		
		$theShop = '<div id="myLittleShop">'.$menu.$results.'</div>';
		
	}
	
	return $theShop;
}
//Had shop to the Content
add_action('the_content', 'theLittleShop');
	
	

function littleShopEnlarged(){
        if(littleShopTakeEffect() === TRUE) {
	        $enlarged = '<div id="behind"></div>';
	        $enlarged .= '<div id="enlarged">';
	        $enlarged .= '<p id="close"><a href="">Close Me</a></p>';
	        $enlarged .= '<div id="enlargedContent"></div>';
	        $enlarged .= '</div>';
	        echo $enlarged;
        }
}
//Had enlarged to the bottom
add_action('wp_footer', 'littleShopEnlarged');


/*****************************************************************************/
/*  CSS CSS CSS CSS CSS CSS CSS CSS CSS CSS CSS CSS CSS CSS CSS CSS CSS  CSS */
/*****************************************************************************/
function addLittleShopCSS(){
        if(littleShopTakeEffect() === TRUE){
	        echo '<link media="screen" type="text/css" href="'.myOwnLittleEbayShopPluginURL().'/my-own-little-ebay-shop-css.css" rel="stylesheet"/>';
	
	}
}
add_action('wp_head', 'addLittleShopCSS');


/*****************************************************************************/
/*  JS  JS  JS  JS  JS  JS  JS  JS  JS  JS  JS  JS  JS  JS  JS  JS  JS  JS   */
/*****************************************************************************/
function addLittleShopJS(){
        if(littleShopTakeEffect() === TRUE){
        	echo '<script type="text/javascript" src="'.myOwnLittleEbayShopPluginURL().'/jquery-1.4.1.min.js" rel="javascript"/></script>';
	        echo '<script type="text/javascript" src="'.myOwnLittleEbayShopPluginURL().'/my-own-little-ebay-shop-js.js" rel="javascript"/></script>';
	
	}
}
add_action('wp_head', 'addLittleShopJS');

?>