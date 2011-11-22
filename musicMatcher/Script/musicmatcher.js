$(document).ready(function(){
	//Set autocomplete data
	GetAutoComplete();
	
	//Focus on textbox
	$("#inputkeywords").focus();

	//Set enterbutton onclick
	$('.container').keypress(function(e){
		if(e.which == 13 && $("#inputkeywords").is(":focus")){
			e.preventDefault();
			StartSearch();
		}
    });
	//Set submitbutton onclick
	$("#searchbutton").click(function() {
		StartSearch();
	});
});

function StartSearch(){
	//Show loading start fetching results
	ShowLoading();
	GetResults();
	
	//If first search then animate
	if($('.searchbox').css("top").replace("px", "") > 0)
		$('.searchbox,').animate({ top: '0', marginTop: '0',}, 300, function(){ $('.container').css("background-color", "#323232"); });
}

//Function used to get search results
function GetResults(){
	//Get keywords
	var keyword = $("#inputkeywords").val();
	
	//Process search request
	$.ajax({
		url: '../Data/last.FM/Webservice.php', //current webservice
		data: "keyword=" + keyword + "&req=getRelatedArtist",
		success: function(data) {
			FillResults(data);
		}
	});
}

//Function used to get autocomplete array
function GetAutoComplete(){
	$.ajax({
		url: '../Data/last.FM/Webservice.php', //current webservice
		data: "keyword=" + "&req=getTopArtist",
		success: function(data) {
			FillAutoComplete(data);
		}
	});
}

//Function that fills the artist info
function FillArtistData(data){
	$('.artistinfo').html(data);
}

//Function used to fill autocomplete array
function FillAutoComplete(data){
	$("#inputkeywords").autocomplete({
		source: data.split("|"),
		position: {my: "center top", at: "bottom" }
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
		else{
			$( ".slider" ).slider( "option", "value", 1000);
		}
	});		
	$('.resultbox').scroll(function(){	
		$( ".slider" ).slider( "option", "value", (1000-(($(".resultbox").scrollTop()/($(".serviceresult").height() - $(".resultbox").height()))*1000)));
	});
				
	$('.resultbox').hide();
	HideLoading();
	$('.slider, .sliderbox').show();
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

//Function that shows the artist info
function ShowArtist(artist){
	$.ajax({
		url: '../Data/last.FM/Webservice.php', //current webservice
		data: "keyword=" + artist + "&req=getArtistInfo",
		success: function(data) {
			FillArtistData(data);
		}
	});
	$(".lightbox, .artistinfo").css("display", "block");
}

//Function that hides the artist info
function HideArtist(){
	$(".lightbox, .artistinfo").css("display", "none");
}