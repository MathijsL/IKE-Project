//limit menu results
$.ui.autocomplete.prototype._renderMenu = function( ul, items ) {
   var self = this;
   $.each( items, function( index, item ) {
      if (index < 10) // here we define how many results to show
         {self._renderItem( ul, item );}
      });
}

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
	//Hide autocomplete bars
	$('.ui-autocomplete').hide();

	//Show loading start fetching results
	ShowLoading();
	GetResults();
	
	//If first search then animate
	if($('.searchbox').position().top > 0){
		$('.searchbox').css('top', $('.searchbox').position().top);
		$('.searchbox,').animate({ 'top': '0px', 'marginTop': '0px'}, 300, function(){ $('.container').css("background-color", "#323232"); });
	}
}

//Function used to get search results
function GetResults(){
	//Get keywords
	var keyword = $("#inputkeywords").val();
	
	//Process search request
	$.ajax({
		url: 'Data/last.FM/Webservice.php', //current webservice
		data: "keyword=" + keyword + "&req=getRelatedArtist",
		success: function(data) {
			FillResults(data);
		}
	});
}

//Function used to get autocomplete array
function GetAutoComplete(){
	$.ajax({
		url: 'Data/last.FM/Webservice.php', //current webservice
		data: "keyword=" + "&req=getTopArtist",
		success: function(data) {
			FillAutoComplete(data);
		}
	});
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
		url: 'Data/last.FM/Webservice.php', //current webservice
		data: "keyword=" + artist + "&req=getArtistInfo",
		success: function(data) {
			FillArtistData(data);
		}
	});
	ShowLoading();
}

//Function that hides the artist info
function HideArtist(){
	$(".lightbox, .artistinfo").css("display", "none");
}

//Function that fills the artist info
function FillArtistData(data){
	var dataparts = data.split("$");
	var artistdata = dataparts[0].split("|");
	var html = "<div style='float:left; margin-bottom: 20px;'>";
	html += "<b>" + artistdata[0].split("name:")[1] + "</b>";
	html += "<br />"; 
	html += "<br />";
	html += "<img style='margin-top: 0px; width:126px;' src='" + artistdata[1].split("image:")[1] + "'>";
	html += "</div>";
	html += "<div style='float:left; margin-left:20px;'>";
	html += "<b>Info</b><a style='text-align:right; position:absolute; color:white; right:25px;' onclick='HideArtist()' href='#'>Close</a>";
	html += "<br />";
	html += "<br />";
	html += "Age: 20<br />";
	html += "Members: 4<br />";
	html += "Life-expectancy: 1<br />";
	html += "Active since: 2013 <br />";
	html += "</div>";
	html += "<div style='clear:both;'>";
	html += "<div style='float:left; width:146px;'><b>Albums</b></div>";
	html += "<div class='albumbutton'><a onclick='PrevAlbum()'><b><</b></a></div><div class='albumnamecontainer' style='overflow:hidden;white-space:nowrap; float:left;'><div class='albumname' style='width:10000px; position:relative;'></div></div><div class='albumbutton'><a onclick='NextAlbum()'><b>></b></a></div>";
	html += "<div class='albumcontainer' style='clear:both; overflow:hidden; height:auto;'>";
	html += "<div class='songcontainer' style='width:10000px; position:relative;margin-top:20px;'></div></div></div>";

	$('.artistinfo').html(html)
	CreateAlbum(dataparts[1]);
	$('.artistinfo').css('margin', -(($('.artistinfo').height()+80)/2) + 'px 0 0 ' + -(($('.artistinfo').width()+40)/2) + 'px');
	
	$('.lightboxcontent').hide();
	$(".artistinfo").show();
}

var current = 1;
var max = 0;
var min = 1;
var width = 400;
var headerwidth = 200;

function CreateAlbum(data){
	var albums = data.split("|");
	var albumhtml = "";
	var albumtitlehtml = "";
	$.each(albums, function(i, val) {
		if(val != ""){
			var parts = val.split("/");
			if(parts[1] != ""){
				var name = parts[0].split("name:")[1];
				albumtitlehtml += "<div style='float:left; width:" + headerwidth + "px; margin:0px; padding:0px; text-align:center'>" + name + "</div>";
				albumhtml += "<div style='float:left; margin:0px; padding:0px; width:" + width + "px;'><ul>";
				var songs = parts[1].split("\\");
				$.each(songs, function(i2, val2) {
					if(val2 != ""){
						var songparts = val2.split("#");
						albumhtml += "<li><div style='clear:both; width:100%; margin-top: 1px;'><div style='float:left;'>" + songparts[0] + "</div><div style='float:right; text-align:right;'><a target='_blank' href='http://www.youtube.com/watch?v=" + songparts[1] + "'><img style='border-radius: 5px; height:19px;' src='/musicMatcher/Script/youtube.png'/></a></div></div></li>";
					}
				});
				albumhtml += "</ul></div>"
			}
		}
    });
	
	$('.albumname').html(albumtitlehtml);
	$('.songcontainer').html(albumhtml);
	
	$('.albumcontainer').css("width", width);
	$('.albumnamecontainer').css("width", headerwidth);
	
	max = $('.songcontainer > div').length;
}

function NextAlbum(){
	if(current < max){
		$(".songcontainer").animate({  
			left: -(current * (width))
			}, {duration: 1000, queue:false }
		);
		$(".albumname").animate({  
			left: -(current * headerwidth)
			}, {duration: 1000, queue:false}
		);
		current++;
	}
}
function PrevAlbum(){
	if(current > min){
		$(".songcontainer").animate({  
			left: -((current-2) * (width))
			}, {duration: 1000, queue:false}
		);
		$(".albumname").animate({  
			left: -((current-2) * headerwidth)
			}, {duration: 1000, queue:false}
		);
		current--;
	}
}