$(document).ready(function(){
	//Set submitbutton onclick
	$("#searchbutton").click(function() {
		ShowLoading();
		GetResults();
	});
});

//Function used to get search results
function GetResults(){
	//Get keywords
	var keyword = $("#inputkeywords").val();
	
	//Process search request
	$.ajax({
		url: 'Webservice.php',
		data: "keyword=" + keyword,
		success: function(data) {
			FillResults(data);
		}
	});
}

//Function used to fill result
function FillResults(data){
	if($('.searchbox').css("top").replace("px", "") > 0){
		//Move searchbar to top
		$('.searchbox,').animate({
				top: '0',
				marginTop: '0',
		    }, 300, function() {
				//Set result area
				$('.slider').slider({ orientation: 'vertical' });
				$('.searchbox').css("position", "fixed");
				$('.container').css("background-color", "#323232");
				$('.resultbox, .slider').css("height", ($(window).height() - 130));
				$('.resultbox').css("overflow-y", "scroll").html(data);
				//TODO remove timeout
				setTimeout("$('.resultbox').hide();HideLoading();$('.resultbox').fadeIn(300);", 1000);
		});
	}
	else{
		//Set result data
		$('.resultbox').hide();
		$('.resultbox').html(data);
		//TODO remove timeout
		setTimeout("HideLoading();$('.resultbox').fadeIn(300);", 1000);
	}	
}

//Function that shows the loading box
function ShowLoading(){
	$(".lightbox, .lightboxcontent").css("display", "block");
}

//Function that hides the loading box
function HideLoading(){
	$(".lightbox, .lightboxcontent").css("display", "none");
}