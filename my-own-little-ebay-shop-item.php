<?php

/*Plugin: my-own-little-ebay-shop*/
/*Content: Items functions*/
/*Author: Thomas Michalak aka TM*/
/*Author URI: http://www.fuck-dance-lets-art.com*/


require_once('my-own-little-ebay-shop-functions.php');

if(empty($_GET)){
	$cat = 'All';
	$itemID = '0';
}else{
	if(isset($_GET["cat"]) || ctype_print($_GET["cat"])) $cat = $_GET["cat"];
	if(isset($_GET["itemID"]) || ctype_digit($_GET["itemID"])) $itemID = $_GET["itemID"];
}

$myItem = getItem($itemID, myOwnLittleEbayShopTempFolderPATH().$cat.'.txt'); //Recent: get the Temp file and return a array of items

//ebayShopDebug($myItem);

//Item
date_default_timezone_set('Europe/London');
$EndTime = date("F j, Y, g:i a", strtotime($myItem['EndTime'])); 
$pictureURL = $myItem['PictureURL'];
$price = $myItem['ConvertedCurrentPrice'];
$title = $myItem['Title'];
$descript = $myItem['Description'];
$Location = $myItem['Location']." | ".$myItem['Country'];; 
$quantity = $myItem['Quantity'];
$itemOnEbay = $myItem['ViewItemURLForNaturalSearch'];
//Seller
$storeName = $myItem['Storefront']['StoreName'];
$storeURL = $myItem['Storefront']['StoreURL'];
$feedbackRatingStar = $myItem['Seller']['FeedbackRatingStar'];
$feedbackScore = $myItem['Seller']['FeedbackRatingStar'];
$positiveFeedbackPercent = $myItem['Seller']['PositiveFeedbackPercent'];

$results = '<ul>';
$idLi = '1';
//Single Image
if(!is_array($pictureURL)) {
	$results .= '<li id="'.$idLi.'" style="background-image:URL(\''.$pictureURL.'\')"></li>'; 
	$nbrImages = 1;
}
//Multiple Images
else {
	foreach($pictureURL as $pictURL){
		$results .= '<li id="'.$idLi.'" style="background-image:URL(\''.$pictURL.'\')"></li>';
		$idLi++;
	}
	$nbrImages = count($pictureURL);
}
$results .= '</ul>';

//OUTPUT
echo '<div id="close"></div>';
echo '<div id="topEnlarged">';
echo '<h3><a href="'.$itemOnEbay.'" target="_blank">'.$title.'</a></h3>';
echo '<p>price: &#163;'.$price.' | quantity: '.$quantity.' | <a class="bt round3 shadowSL" href="'.$itemOnEbay.'" target="_blank">Buy It Now on eBay</a> | <a href="'.$storeURL.'" target="_blank">visit '.$storeName.' ebay shop</a></p>';
echo '</div>';
echo '<div id="enlargedContent">';
echo '<p id="imgsCount"><span>1</span> of '.$nbrImages.' images</p>';
echo '<div id="slideImgs">';
echo '<a href="" class="nav previous"></a>';
echo '<div id="imgs">';
echo $results;
echo '</div>';
echo '<a href="" class="nav next"></a>';
echo '</div>';
echo '<p>'.$descript.'</p>';
echo '</div>';
echo '<div id="bottomEnlarged">';
echo '<p id="ebayLogo" style="width:89px"><a href="'.$itemOnEbay.'" target="_blank">Powered by<img src="'.myOwnLittleEbayShopPluginURLDirect().'/imgs/logo-ebay.gif" width=89 height=37></a></p><p id="feedback"><a href="'.$storeURL.'" target="_blank">'.$storeName.'</a> has a positive feedback of '.$positiveFeedbackPercent .'%</p>';
echo '</div>';

?>
