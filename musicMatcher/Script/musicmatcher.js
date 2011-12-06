//album list variables
var current = 1;
var max = 0;
var min = 1;
var height = 0;
var width = 440;
var headerwidth = 400;

//limit menu results
$.ui.autocomplete.prototype._renderMenu = function( ul, items ) {
   var self = this;
   $.each( items, function( index, item ) {
      if (index < 10) // here we define how many results to show
         {self._renderItem( ul, item );}
      });
}
//find string length
function StringLength(input)
{
    var ruler = $("#ruler");
    ruler.html(input);
    return ruler.width();
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
		$('.searchbox').animate({ 'top': '0px', 'marginTop': '0px'}, 300, function(){ $('.container').css("background-color", "#323232"); });
		$('.musicbox').animate({ 'bottom': '0px', 'marginBottom': '0px' }, 300);
	}
}

//Function used to get search results
function GetResults(){
	//Get keywords
	var keyword = $("#inputkeywords").val();
	
	$.getJSON('Data/Webservice.php?keyword=' + keyword + '&function=getRelatedArtist',
		function(data){
			$('.resultbox, .sliderbox').css("height", ($(window).height() - 220));
			$('.slider').css("height", ($(window).height() - 250));
			
			var result = "<div class='serviceresult'><ul>";
			$.each(data.artists, function(i, artist) { 
				result += "<li class='a" + i + "'><div class='artistname' onclick='ShowArtist(\"" + artist.name + "\", \"a"+ i + "\")'><b>" + artist.name + "</b></div><div class='artistcontent'></div></li>";
			});
			result + "</ul></div>";
			
			$('.resultbox').css("overflow-y", "scroll").html(result);

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
	);
}

//Function used to get autocomplete array
function GetAutoComplete(){
	$.getJSON('http://ws.audioscrobbler.com/2.0/?method=chart.gettopartists&api_key=b25b959554ed76058ac220b7b2e0a026&limit=50&page=1&format=json',
		function(data){
			var adata = [];
			var c = 0;
			$.each(data.artists.artist, function(i,item){
				adata[c] = item.name;
				c++;
			});
			$("#inputkeywords").autocomplete({
				source: adata,
				position: {my: "center top", at: "bottom" }
			});
		});
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
function ShowArtist(artist, cn){
	$(".resultbox li .artistcontent").css("display", "none");
	$(".resultbox li .artistname").css("display", "");
	$(".serviceresult ul > li").css("height","");
	current = 1;
	max = 0;
	min = 1;
	ShowLoading();

	$.getJSON('Data/Webservice.php?keyword=' + artist + '&function=getInfo&selection=mbid;name;begindate;enddate;type;picture;albums;',
		function(data){
			$("."+cn).css("height","auto");
			$("."+cn+" .artistname").css("display", "none");
			var albumhtml = "";
			var albumtitlehtml = "";
			var html = "";
			var count = 0;
				
			html += "<div class='leftcontainer' onclick='HideArtist(\""+ cn + "\")'><b>" + data.artist.name + "</b><br /><br />";
			html += "<img class='artistimage' src='" + data.artist.picture + "'></div>";
			html += "<div class='rightcontainer'><br /><br />";
			html += "Begindate: " + data.artist.begindate + "<br />";
			html += "Enddate: " + data.artist.enddate + "<br />";
			html += "Type: " + data.artist.type + "<br /></div>";
			html += "<div class='fclear'><div class='albumheader'><b>Albums</b></div><br /><br />";
			html += "<div class='albumbutton'><a onclick='PrevAlbum(\""+ cn + "\")'><b><</b></a></div><div class='albumnamecontainer'><div class='albumname'></div></div><div class='albumbutton'><a onclick='NextAlbum(\""+ cn + "\")'><b>></b></a></div>";
			html += "<div style='height:20px;clear:both;'></div><div class='albumcontainer'><div class='songcontainer'></div></div><div style='height:20px;clear:both;'></div>";

			$.each(data.artist.albums, function(i, album) { 
				if(album.tracks.length > 0){
					albumtitlehtml += "<div class='albumtitle'>" + album.name + "</div>";
					albumhtml += "<div class='albumcontent " + count + "'><img src='" + album.picture + "' class='albumimage'/><ul class='artistinfoul'>";
					count++;
					height = data.artist.albums.length*16;
					
					$.each(album.tracks, function(p, track){
						if(track.name != ""){
							albumhtml += "<li class='artistinfoli'><div class='album'><div class='albumleft'>" + track.name + "</div><div class='albumright'><div class='addbutton'><a onclick=''><b>+</b></a></div></div></div></li>";//<a target='_blank' href='http://www.youtube.com/watch?v='><img class='youtubeimage' src='Script/youtube.png'/></a>
						}
					});
					albumhtml += "</ul></div>"
				}
			});
				
			$("."+cn+" .artistcontent").html(html).fadeIn(300);
			$("."+cn).css("height", "auto");
			$("."+cn+' .albumname').html(albumtitlehtml);
			$("."+cn+' .songcontainer').html(albumhtml);
			$("."+cn+' .albumcontent').css("width", width);
			$("."+cn+' .albumnamecontainer, .albumtitle').css("width", headerwidth);
			$("."+cn+' .albumcontainer').css("height", ($("." + cn + " .0 li").length * 16));
			$("."+cn+' .albumcontainer').css("width", width);
			max = $("."+cn+' .songcontainer > div').length;
			HideLoading();
		}
	);
}

//Function that hides the artist info
function HideArtist(cn){
	$("."+cn).css("height","");
	$("."+cn+" .artistname").css("display", "");
	$("."+cn+" .artistcontent").css("display", "none");
}

function NextAlbum(cn){
	if(current < max){
		$(".songcontainer .addbutton").hide();
		$("." + cn + " .songcontainer").animate({left: -(current * (width))}, {duration: 1000, queue:false });
		$("." + cn + " .albumname").animate({left: -(current * headerwidth)}, {duration: 1000, queue:false});
		$("." + cn + ' .albumcontainer').animate({ 'height' : ($("." + cn + " ."+current+" li").length * 16)}, {duration: 1000, queue:false});
		setTimeout('$(".songcontainer .addbutton").fadeIn(300);', 1000);
		current++;
	}
}
function PrevAlbum(cn){
	if(current > min){
		$(".songcontainer .addbutton").hide();
		$("." + cn + " .songcontainer").animate({left: -((current-2) * (width))}, {duration: 1000, queue:false});
		$("." + cn + " .albumname").animate({left: -((current-2) * headerwidth)}, {duration: 1000, queue:false});
		$("." + cn + ' .albumcontainer').animate({ 'height' : ($("." + cn + " ."+(current-2)+" li").length * 16)}, {duration: 1000, queue:false});
		setTimeout('$(".songcontainer .addbutton").fadeIn(300);', 1000);
		current--;
	}
}