$(document).ready(function(){
	//Set submitbutton onclick
	$("#searchbutton").click(function() {
		//Show loading start fetching results
		ShowLoading();
		GetResults();
		
		//If first search then animate
		if($('.searchbox').css("top").replace("px", "") > 0)
			$('.searchbox,').animate({ top: '0', marginTop: '0',}, 300, function(){ $('.container').css("background-color", "#323232"); });
	});
});

//Function used to get search results
function GetResults(){
	//Get keywords
	var keyword = $("#inputkeywords").val();
	
	//Process search request
	$.ajax({
		url: '/Data/last.FM/Webservice.php', //current webservice
		data: "keyword=" + keyword,
		success: function(data) {
			FillResults(data);
		}
	});
}

//Function used to fill result
function FillResults(data){
	$('.resultbox, .sliderbox').css("height", ($(window).height() - 130));
	$('.slider').css("height", ($(window).height() - 158));
	$('.resultbox').css("overflow-y", "scroll").html(data);
			
	//Set scrollbar
	$('.slider').slider({ orientation: 'vertical', max: '1000', min: '0', value: '1000'});
	$(".slider").bind("slide change stop slidestop", function (event, ui) {
		var maxheight = $(".resultbox").height();
		var height = $(".serviceresult").height();
		if (height > maxheight) {
			$(".resultbox").scrollTop((height-maxheight)-(($(".slider").slider("option", "value") / 1000) * (height - maxheight)));
		}
	});		
	$('.resultbox').scroll(function(){	
		$( ".slider" ).slider( "option", "value", (1000-(($(".resultbox").scrollTop()/($(".serviceresult").height() - $(".resultbox").height()))*1000)));
	});
				
	$('.resultbox').hide();
	HideLoading();
	$('.resultbox').fadeIn(300);
}

//Function that shows the loading box
function ShowLoading(){
	$(".lightbox, .lightboxcontent").css("display", "block");
}

//Function that hides the loading box
function HideLoading(){
	$(".lightbox, .lightboxcontent").css("display", "none");
}