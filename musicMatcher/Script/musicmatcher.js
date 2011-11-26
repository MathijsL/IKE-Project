//album list variables
var current = 1;
var max = 0;
var min = 1;
var width = 400;
var headerwidth = 200;

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
		url: 'Data/Webservice.php', //current webservice
		data: "keyword=" + keyword + "&function=getRelatedArtist",
		success: function(data) {
			FillResults(data);
		}
	});
}

//Function used to get autocomplete array
function GetAutoComplete(){
	$.ajax({
		url: 'Data/Webservice.php', //current webservice
		data: "function=autocomplete",
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
		url: 'Data/Webservice.php', //current webservice
		data: "keyword=" + artist + "&function=getInfo&selection=name;beginDate;endDate;type;picture;albums;",
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
	var albumhtml = "";
	var albumtitlehtml = "";
	var html = "";
	var artistinfo = data.split("*");	
	
	html += "<div class='leftcontainer'><b>" + artistinfo[0].replace("name[","") + "</b><br /><br />";
	html += "<img class='artistimage' src='" + artistinfo[5].replace("picture[","") + "'></div>";
	html += "<div class='rightcontainer'><b>Info</b><a class='close' onclick='HideArtist()' href='#'>Close</a><br /><br />";
	html += "Begindate: " + artistinfo[1].replace("beginDate[","") + "<br />";
	html += "Enddate: " + artistinfo[2].replace("endDate[","") + "<br />";
	html += "Type: " + artistinfo[3].replace("type[","") + "<br /></div>";
	html += "<div class='fclear'><div class='albumheader'><b>Albums</b></div>";
	html += "<div class='albumbutton'><a onclick='PrevAlbum()'><b><</b></a></div><div class='albumnamecontainer'><div class='albumname'></div></div><div class='albumbutton'><a onclick='NextAlbum()'><b>></b></a></div>";
	html += "<div class='albumcontainer'><div class='songcontainer'></div></div></div>";

	var albums = artistinfo[4].split(";");
	for(var j =0; j < albums.length; j++) {		
		var parts = albums[j].split("tracks");
		if(parts[1] != undefined){
			var tracks = parts[1].split("|");
			if(tracks.length > 0){
				albumtitlehtml += "<div class='albumtitle' style='width:" + headerwidth + "px;'>" + parts[0].split("name[")[1].split("]")[0] + "</div>";
				albumhtml += "<div class='albumcontent' style='width:" + width + "px;'><ul>";
				for(var l = 0; l < tracks.length; l++) {
					var trackparts = tracks[l].split("_duration[");
					if(trackparts[0].replace("name[", "").replace("[","") != ""){
						albumhtml += "<li><div class='album'><div class='albumleft'>" + trackparts[0].replace("name[", "").replace("[","") + "</div><div class='albumright'><a target='_blank' href='http://www.youtube.com/watch?v='><img class='youtubeimage' src='Script/youtube.png'/></a></div></div></li>";
					}
				}
			}
		}
		albumhtml += "</ul></div>"
	}

	$('.artistinfo').html(html);
	$('.albumname').html(albumtitlehtml);
	$('.songcontainer').html(albumhtml);
	$('.albumcontainer').css("width", width);
	$('.albumnamecontainer').css("width", headerwidth);
	$('.artistinfo').css('margin', -(($('.artistinfo').height()+80)/2) + 'px 0 0 ' + -(($('.artistinfo').width()+40)/2) + 'px');
	$('.lightboxcontent').hide();
	$(".artistinfo").show();
	max = $('.songcontainer > div').length;
}

function NextAlbum(){
	if(current < max){
		$(".songcontainer").animate({left: -(current * (width))}, {duration: 1000, queue:false });
		$(".albumname").animate({left: -(current * headerwidth)}, {duration: 1000, queue:false});
		current++;
	}
}
function PrevAlbum(){
	if(current > min){
		$(".songcontainer").animate({left: -((current-2) * (width))}, {duration: 1000, queue:false});
		$(".albumname").animate({left: -((current-2) * headerwidth)}, {duration: 1000, queue:false});
		current--;
	}
}