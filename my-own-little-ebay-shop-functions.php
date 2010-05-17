<?php

/*Plugin: my-own-little-ebay-shop*/
/*Content: Plugin main functions */
/*Author: Thomas Michalak aka TM*/
/*Author URI: http://www.fuck-dance-lets-art.com*/

//error_reporting(E_ALL);  // turn on all errors, warnings and notices for easier debugging

/* my base path to the files */
define('MY_OWN_LITTLE_EBAY_SHOP_BASE', dirname(__FILE__));


/********************************************************************************/
/********************************************************************************/
/******************************** POST POST *************************************/
/********************************************************************************/
/********************************************************************************/
if(!empty($_POST)){
	//Retreive Categories
	if(isset($_POST["getCatsRSS"]) || ctype_print($_POST["getCatsRSS"])) {
	    //Crawl the ebay shop and found the links and names
		$catRSS = getCatsRSS($_POST["getCatsRSS"]);
		//Serialized
    	//$serializedCatsList = serialize($catRSS);
    	//write the txt file
    	//ebayShopDebug($catRSS);
    	//file_put_contents($tempShopCatsPath, $serializedCatsList, LOCK_EX);
        //Return the list of Categories
		$returnCatsRSSList='';
		foreach($catRSS as $cat => $val){
			//Excluded default
			$returnCatsRSSList.= '<input name="my_own_little_ebay_shop_categories['.$cat.'][excluded]" value="off" class="hidden"/>';
			//Rss
			$returnCatsRSSList.= '<input name="my_own_little_ebay_shop_categories['.$cat.'][rss]" value="'.$val.'" class="hidden"/>';
			//Name for request and text file
			$returnCatsRSSList.= '<input name="my_own_little_ebay_shop_categories['.$cat.'][requestName]" value="'.nicePath($cat).'" class="hidden"/>';
			//Category Name and Nicename
			$returnCatsRSSList.='<li><input type="checkbox" name="my_own_little_ebay_shop_categories['.$cat.'][excluded]"><input value="'.$cat.'" name="my_own_little_ebay_shop_categories['.$cat.'][niceName]"/></li>';
			//Create Categories Temp Files
			$tempCatItemsPath = myOwnLittleEbayShopTempFolderPATH().nicePath($cat).'.txt';
			createCatItemsTempFile($_POST["getCatsRSS"], $query, $tempCatItemsPath, $val);
		}
		//Return Category list
		echo $returnCatsRSSList;
	}
}


/********************************************************************************/
/********************************************************************************/
/******************************** FUNCTIONS *************************************/
/********************************************************************************/
/********************************************************************************/


/********************************************************************************/
/*                              GetMultipleItems                                */
/********************************************************************************/
/********************************************************************************/
//Create a Temp File and return a unserialized Array
function createCatItemsTempFile($seller, $query, $myPath, $cat){
	//Get Items ID
	$itemsID = getItemsID($cat);
	//ebayShopDebug($itemsID);
	//Create empty Array for storing reponses
	$allItems = array();
	//Find out how many times you have to send a request
	$loopFor = count($itemsID)/20;
	//Loop your request
	for($i=0; $i<$loopFor; $i++){
		//Create a string of Items' ID
		$sendItemsID = implode(',' , array_slice($itemsID, $i*20, 20));
        //Store response in the Array
		$allItems += collectItems($seller, $query, $sendItemsID);
	}
	//ebayShopDebug($allItems);
    //Serialized
    $serializedAllItems = serialize($allItems);
    //write the txt file
    file_put_contents($myPath, $serializedAllItems, LOCK_EX);
    //Return Array
    return $allItems;
}




/********************************************************************************/
//Collect Items
function collectItems($seller, $myQuery, $itemsIDArray){
	$endpoint = 'http://open.api.ebay.com/shopping';  // URL to call
	$responseEncoding = 'XML';   // Format of the response
	$appID = 'ThomasMi-8d7f-4440-a4c7-72a5967fc3ec'; //appID
	$includeSelector = 'TextDescription, Details';
	$SafeQuery = urlencode($myQuery);
	//Ebay Affilated (ebay Partner)
	$trackingID = '5335905449';
	$trackingpartnercode = '9';
	$affiliateuserid = 'annachocola';
	
	// Ebay API Call
	$apicall = "$endpoint?callname=GetMultipleItems&version=525&siteid=3&appid=$appID&QueryKeywords=$SafeQuery&sellerID=$seller&ItemID=$itemsIDArray&IncludeSelector=$includeSelector&trackingID=$trackingID&trackingpartnercode=$trackingpartnercode&affiliateuserid=$affiliateuserid&responseencoding=$responseEncoding";
	
	// Load the call and capture the document returned by the API
	$resp = simplexml_load_file($apicall);
	//ebayShopDebug($resp);
	// Check to see if the response was loaded, else print an error
	if ($resp) {
		//Recursively transform object into array.
		$itemsObjectToArray = array();
		foreach($resp->Item as $key => $item){
			$itemsObjectToArray[] = objectToArray($item);
		}
	  	//Set Items Array key to Item's ID and replace gallery path with big thumbs path
		$itemArray = array();
		foreach($itemsObjectToArray as $key => $item){
			$itemArray[$item['ItemID']] = $item;
			$urlImg = $itemArray[$item['ItemID']]['GalleryURL'];
			$itemArray[$item['ItemID']]['GalleryURL'] = str_replace('6464_', '_', $urlImg);
		}
	}else{
		$itemArray = false;
	}
	return $itemArray;

}


/********************************************************************************/
//Recursive funtion to convert objects in array
function objectToArray($obj){
	//Cast Object into Array ( Assume that you will always start with a Object )
	$newArray = (array)$obj;
	//Loop through the Array
	foreach($newArray as $key => $val){
		//If its a Object or a Array, apply function (recursive) to the object
		if(is_object($val) || is_array($val)){
			$newArray[$key] = objectToArray($val);
		}
		//If its just a Attribute
		else{
			$newArray[$key] = $val;
		}
	}
	//return array
	return $newArray;
}



/********************************************************************************/
//Get Temp file and return Array
function getTempFileContent($myFile){
	$tempAllItems = file_get_contents($myFile);
	return unserialize($tempAllItems);
}

//Look if temp file exist and if it's not to old
function tempFileValid($path, $maxDiffTime){
	if(file_exists($path)) {
		//check the file age
		$diffTime = (time()-filemtime($path))/60; //mn
		//If it's less then a number of minutes
		if($diffTime<$maxDiffTime) $tempValid = true;
		else $tempValid = false;
	}
	else $tempValid = false;
	return $tempValid;
}

/********************************************************************************/
//Check Any Changes in the eBay Shop
function checkRSSUpdate($catRSS){
	// Load the call and capture the document returned by the rss
    $resp = simplexml_load_file($catRSS);
    //ebayShopDebug((string)$resp->comment);
    ebayShopDebug($resp);
}



/********************************************************************************/
//Get Item
function getItem($ID, $path){
	$allItems = getTempFileContent($path);
	return $allItems[$ID];
}


/********************************************************************************/
//Get Items ID from RSS
function getItemsID($catRSS){
	// Load the call and capture the document returned by the rss
    $resp = simplexml_load_file($catRSS);
    //ebayShopDebug($resp);
    if($resp){
    	//Extract IDs and put it in array
    	$itemsID= array();
    	foreach($resp->channel->item as $item) {
			preg_match('/\/(\d+)\?cmd/', (string)$item->link, $matches);
			$itemsID[] = $matches[1];
		}
    }else{
    	$itemsID = false;
    }
    //ebayShopDebug($itemsID);
	return $itemsID;
}


/********************************************************************************/
//Get Categories Names and Link 
function getCatsNamesAndLinks($sellerID){
	$myUrl = "http://stores.shop.ebay.co.uk/".$sellerID."__W0QQ_armrsZ1";
	$doc = new DOMDocument();
	@$doc->loadHTMLFile($myUrl);
	$nodes = $doc->getElementsByTagName('a'); // Find Sections 
    
    $cats = array();
    $cats['All'] = $myUrl;
    foreach( $nodes as $n ){
    	$myLink = $n->getAttribute('href'); 
    	$compareTo = "/".$sellerID."_";
   		if(strpos($myLink, $compareTo)===0){
   			if(preg_match('#/'.$sellerID.'_(?!_W0QQ)#', $myLink)){
   				$cats[$n->nodeValue] = "http://stores.shop.ebay.co.uk".$myLink;	
   			}
   		}
	}
	//ebayShopDebug($cats);
	return $cats;

}


/********************************************************************************/
//Get Categories RSS 
function getCatsRSS($sellerID){
	$myCats = getCatsNamesAndLinks($sellerID);
	
	
	foreach($myCats as $cat=>$url){
		$doc = new DOMDocument();
		@$doc->loadHTMLFile($url);
		$nodes = $doc->getElementsByTagName('a'); // Find links

		foreach( $nodes as $n ){
    		$myLink = $n->getAttribute('href'); 
    		$compareTo = "rssstore";
   			if(strpos($myLink, $compareTo)!=false){
	   			$myCats[$cat] = $myLink;
	   			break;
   			}
		}
	
	}
	
	//ebayShopDebug($myCats);
	return $myCats;
}



/********************************************************************************/
//UTILITIES
function nicePath($string, $replacement = '-') {
    $map = array(
        '/à|á|å|â/' => 'a',
        '/è|é|ê|ẽ|ë/' => 'e',
        '/ì|í|î/' => 'i',
        '/ò|ó|ô|ø/' => 'o',
        '/ù|ú|ů|û/' => 'u',
        '/ç/' => 'c',
        '/ñ/' => 'n',
        '/ä|æ/' => 'ae',
        '/ö/' => 'oe',
        '/ü/' => 'ue',
        '/Ä/' => 'Ae',
        '/Ü/' => 'Ue',
        '/Ö/' => 'Oe',
        '/ß/' => 'ss',
        '/[^\w\s]/' => ' ',
        '/\\s+/' => $replacement,
        "/$replacement+/" => $replacement
    );
    return preg_replace(array_keys($map), array_values($map), $string);
}

function websiteURL(){
	$v = get_bloginfo('url');
	return $v;
}

function myOwnLittleEbayShopPluginPATH(){
		$v = MY_OWN_LITTLE_EBAY_SHOP_BASE;
		return $v;
}
function myOwnLittleEbayShopPluginURL(){
		$v = websiteURL().'/wp-content/plugins/my-own-little-ebay-shop';
		return $v;
}
function myOwnLittleEbayShopPluginURLDirect(){
		$v = '/wp-content/plugins/my-own-little-ebay-shop';
		return $v;
}
function myOwnLittleEbayShopTempFolderPATH(){
	    $v = MY_OWN_LITTLE_EBAY_SHOP_BASE.'/temp/';
	    return $v;
}

function tempShopCatsPath(){
	$v = myOwnLittleEbayShopTempFolderPATH()."myCats.txt";
	return $v;
}

/********************************************************************************/
//Debug
function ebayShopDebug($val){
	printf("<pre>%s</pre>", print_r($val, true));
}


?>