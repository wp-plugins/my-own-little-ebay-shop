var j$ = jQuery.noConflict();


j$(document).ready(function(){
    
    j$("#retreiveCategories").click(function(e){
        e.preventDefault();
        //Get SellerID
        var mySellerID = j$("#my_own_little_ebay_shop_username").val();
        //Send Ajax request
        j$.ajax({
   			type: "POST",
   			url: "../wp-content/plugins/my-own-little-ebay-shop/my-own-little-ebay-shop-functions.php",
   			data: "getCatsRSS="+mySellerID,
   			success: function(returnedData){
   				//Clear loading
   				j$("#my_own_little_ebay_shop_cats").empty();
   				//Append the list of Categories
     			j$("#my_own_little_ebay_shop_cats").append(returnedData);
   			}
 		});
 		//Loading
		j$("#my_own_little_ebay_shop_cats").append("<li>Retreiving Categories, please wait…</li>");
		
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
