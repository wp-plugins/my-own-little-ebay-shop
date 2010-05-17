/*Plugin: my-own-little-ebay-shop*/
/*Content: Javascript for the plugin */
/*Author: Thomas Michalak aka TM*/
/*Author URI: http://www.fuck-dance-lets-art.com*/


var j$ = jQuery.noConflict();


j$fadeSpeed = 200;
j$imgNumber = 1;

j$(document).ready(function(){
   
   
   //OPEN PopUP
   j$("#thumbs a").click( function(event){
   	//Prevent default click action
   	event.preventDefault();
   	//Show background
    j$("#behind").css({"display":"block", "width" : j$(document).width(), "height" : j$(document).height(), "left":j$("html").position().left, "opacity":0.5});	
    //Get the request
   	j$myrequest = j$(this).attr("href");
   	//Load content
    j$("#enlarged").load(j$myrequest, function(){
    	//CALLBACK
    	//get the position
    	j$x = (j$(document).width()/2)-j$("#enlarged").width()/2;
    	j$y = j$(window).scrollTop()+40;
    	//Move box
    	j$(this).css({"left" : j$x, "top" : j$y});
    	//Fade box In
    	j$(this).fadeIn(j$fadeSpeed);
   
   
   		//CLOSE PopUP
   		j$("#close, #behind").click( function(event){
   		 	//Prevent default action
   		 	event.preventDefault();
   	 		//Fade box out
   	 		j$("#enlarged").fadeOut(j$fadeSpeed, function(){
   	 			//Move box
   	 			j$("#enlarged").css({"left" : "-10000000px"});
   	 			//Hide background
   	 			j$("#behind").css('display', 'none');
   	    	});
   		});
   
   
   		
   		//SLIDE IMAGES
   		j$slideW = parseInt(j$("#imgs ul li").css("width"));
   		j$nbrItems = j$("#imgs ul li").length-1;
   		j$listLength = -(j$slideW*j$nbrItems);
   		
   		console.log( j$nbrItems );
   		console.log( j$listLength );
   		
   		//Previous
   		j$("a.previous").click( function(event){
   	 		//Prevent default action
   	 			event.preventDefault();
   	 			if(j$("#imgs ul").position().left<0){
   	 				j$("#imgs ul").animate({
   	 					left: "+="+j$slideW+"px"
   	 				},500, "swing", function(){
   	 					    j$imgNumber-=1;
   	 						j$("#imgsCount span").text(j$imgNumber); 
   	 					}); 
   	 				
   	 			}
   		});
        //NEXT
   		j$("a.next").click( function(event){
   	 		//Prevent default action
   	 		event.preventDefault();
 			if(j$("#imgs ul").position().left>j$listLength){
   	 				j$("#imgs ul").animate({
   	 				left: "-="+j$slideW+"px"
   	 				},500, "swing", function(){
   	 						j$imgNumber+=1;
   	 						j$("#imgsCount span").text(j$imgNumber);
   	 					}); 
			 }
   		});
    });
   	});


});