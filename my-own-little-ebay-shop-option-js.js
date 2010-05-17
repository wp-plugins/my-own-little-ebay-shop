/*Plugin: my-own-little-ebay-shop*/
/*Content: Javascript for the Admin Options */
/*Author: Thomas Michalak aka TM*/
/*Author URI: http://www.fuck-dance-lets-art.com*/

var j$ = jQuery.noConflict();
var requestOnce = false;

j$(document).ready(function(){
    
    j$("#retreiveCategories").click(function(e){
        e.preventDefault();
        if(!requestOnce){
        	//Get SellerID
        	var mySellerID = j$("#my_own_little_ebay_shop_username").val();
        	//Send Ajax request
        	j$.ajax({
   				type: "POST",
   				url: "../wp-content/plugins/my-own-little-ebay-shop/my-own-little-ebay-shop-functions.php",
   				data: "getCatsRSS="+mySellerID,
   				success: function(returnedData){
   					//Clear loading
   					j$("#loading").fadeTo(500, 0, function(){
   						j$("#loading").css({'left':'-1000000px'});
   					});
   					j$("#manageCategories").fadeTo(1000, 1);
   					//Append the list of Categories
   					j$("#my_own_little_ebay_shop_cats").empty();
     				j$("#my_own_little_ebay_shop_cats").append(returnedData);
     				//TODO: Refresh DropDown Menu for welcome message
     				
     				requestOnce = false;
   				}
 			});
 			//Loading
 			var loadingPos = j$("#manageCategories").position();
 			//j$("#loading").css({'top':loadingPos.top+(j$("#manageCategories").outerHeight()-j$(this).outerHeight())*0.5, 'left':loadingPos.left+(j$("#manageCategories").outerWidth()-j$(this).outerWidth())*0.5});
 			j$("#loading").css({'top':loadingPos.top+(j$("#manageCategories").outerHeight()*0.5)-j$(this).outerHeight(), 'left':loadingPos.left+(j$("#manageCategories").outerWidth()*0.5)-(j$(this).outerWidth())});
 			j$("#manageCategories").fadeTo(1000, 0.2, function(){
 				j$("#loading").fadeTo(500, 1);
 			});
 			requestOnce = true;
        }
    });
    
    
});

/*
function myAjax(id){
    
    var data = {
	action: 'my_special_action',
	catID: id
    };

    // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
    jQuery.post('admin-ajax.php', data, function(response) {
	alert('Got this from the server: ' + response);
    });
    
}
*/
