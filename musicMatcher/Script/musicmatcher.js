//album list variables
var current = 1;
var max = 0;
var min = 1;
var height = 0;

(function ($) {
    var imgList = [];
    $.extend({
        preload: function (imgArr, option) {
            var setting = $.extend({
                init: function (loaded, total) { },
                loaded: function (img, loaded, total) { },
                loaded_all: function (loaded, total) { }
            }, option);
            var total = imgArr.length;
            var loaded = 0;

            setting.init(0, total);
            for (var i in imgArr) {
                imgList.push($("<img />")
					.attr("src", imgArr[i])
					.load(function () {
					    loaded++;
					    setting.loaded(this, loaded, total);
					    if (loaded == total) {
					        setting.loaded_all(loaded, total);
					    }
					})
				);
            }

        }
    });
})(jQuery);


//limit menu results
$.ui.autocomplete.prototype._renderMenu = function( ul, items ) {
   var self = this;
   $.each( items, function( index, item ) {if (index < 10){self._renderItem( ul, item );}});
}

//on window resize resize window
$(window).resize(function() {
  	$('.resultbox, .sliderbox').css("height", ($(window).height() - 220));
	$('.slider').css("height", ($(window).height() - 250));
});

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
	$('.searchbox, .musicbox').removeClass('bordersbig').addClass('borderssmall');

	$('.ui-autocomplete').css("visibility","hidden");
	
	//Show loading start fetching results
	ChangeLoading(true);
	GetResults();
	
	//If first search then animate
	if($('.searchbox').position().top > 0){
		$('.searchbox').css('top', $('.searchbox').position().top).animate({ 'top': '0px', 'marginTop': '0px'}, 300, function(){ $('.container').css("background-color", "#474747").css('border-left','1px solid #303030').css('border-right', '1px solid #303030'); });
		$('.musicbox').animate({ 'bottom': '0px', 'marginBottom': '0px' }, 300);
	}
}

//Function used for hovering
function StarHover(rowid, rating){
	for(var i = 5; i > 0; i--)
		$("."+rowid+" .rating ."+i).css("background", (i > rating) ? "url('Script/stargray.png')" : "url('Script/starglow.png')");
}
//Function that return the html of the rating system
function SetRating(artistname, rowid, rating){
	return "<div onmouseout='StarHover(\""+ rowid + "\", \"" + rating + "\")' onmouseover='StarHover(\""+ rowid + "\", \"5\")' onclick='SetNewRating(\"" + artistname + "\", \""+ rowid + "\", \"5\")' class='" + ((rating > 4) ? "starglow" : "stargray") + " 5' /><div onmouseout='StarHover(\""+ rowid + "\", \"" + rating + "\")' onmouseover='StarHover(\""+ rowid + "\", \"4\")' onclick='SetNewRating(\"" + artistname + "\", \""+ rowid + "\", \"4\")' class='" + ((rating > 3) ? "starglow" : "stargray") + " 4' /><div onmouseout='StarHover(\""+ rowid + "\", \"" + rating + "\")' onmouseover='StarHover(\""+ rowid + "\", \"3\")' onclick='SetNewRating(\"" + artistname + "\", \""+ rowid + "\", \"3\")' class='" + ((rating > 2) ? "starglow" : "stargray") + " 3' /><div onmouseout='StarHover(\""+ rowid + "\", \"" + rating + "\")' onmouseover='StarHover(\""+ rowid + "\", \"2\")' onclick='SetNewRating(\"" + artistname + "\", \""+ rowid + "\", \"2\")' class='" + ((rating > 1) ? "starglow" : "stargray") + " 2' /><div onmouseout='StarHover(\""+ rowid + "\", \"" + rating + "\")' onmouseover='StarHover(\""+ rowid + "\", \"1\")' onclick='SetNewRating(\"" + artistname + "\", \""+ rowid + "\", \"1\")' class='" + ((rating > 0) ? "starglow" : "stargray") + " 1' />";
}
//Function that sets a new rating
function SetNewRating(artistname, rowid, rating){
	//TODO SAVE RATING
	$("."+rowid+" .rating").html(SetRating(artistname, rowid, rating));
}

//Function used to get search results
function GetResults(){
	//Get keywords
	var keyword = $("#inputkeywords").val();
	
	$.getJSON('Data/Webservice.php?keyword=' + keyword + '&function=getRelatedArtist',
		function(data){
			$('.ui-autocomplete').hide();
			$('.ui-autocomplete').css("visibility","visible");
			$('.resultbox, .sliderbox').css("height", ($(window).height() - 220));
			$('.slider').css("height", ($(window).height() - 250));
	
			var result = "<div class='serviceresult'><ul>";
			$.each(data.artists, function(i, artist) { 
				var rating = 3;
				result += "<li style='position:relative;' class='a" + i + "'><div class='backgroundbox'></div><div class='loadingbox'></div><div class='artistname' style='width:400px; z-index:300'><a onmousedown='ShowArtist(\"" + artist.name + "\", \"a"+ i + "\")'><b>" + artist.name + "</b></a></div><div class='loading'><img style='height:25px; z-index:999;' src='Script/loading.gif'/></div><div class='rating'>" + SetRating(artist.name, 'a'+i, rating) + "</div><br /><br /><div class='artistcontent'></div></li>";
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
			ChangeLoading(false);
			$('.slider, .sliderbox').show();
			$('.resultbox').fadeIn(300);
			$.each($(".resultbox li .artistcontent"), function(i, item){
					$(".backgroundbox").fadeOut(500);
					if($(item).css("display") != "none")
						$(item).slideToggle("slow", function(){$(item).parent().css("height","");}).fadeOut("slow");
				});
			$("#inputkeywords").focusout();
		}
	);
}

//Function used to get autocomplete array
function GetAutoComplete(){
	$.getJSON('http://ws.audioscrobbler.com/2.0/?method=chart.gettopartists&api_key=b25b959554ed76058ac220b7b2e0a026&limit=50&page=1&format=json',
		function(d){
			var adata = [];
			$.each(d.artists.artist, function(i,item){adata[i] = item.name;});
			$("#inputkeywords").autocomplete({source: adata, position: {my: "center top", at: "bottom" }});
		}
	);
}

//Function that shows the loading box
function ChangeLoading(v){
	$(".lightbox, .lightboxcontent").css("display", (v) ? "block" : "none");
}

//Changes the music
function changeMusic(title, artist){
	$.post("Data/WebserviceGS.php", {artist: artist, title: title}, function(d) {
		$("#music").html(d);
	});
}

//Function that shows the artist info
function ShowArtist(artist, cn){
	if($("."+cn+" .artistcontent").css("display") != "none"){
		$("."+cn+" .artistcontent").slideToggle("slow", function () {$("."+cn).css("height","");});
		$("."+cn+" .backgroundbox, ."+cn+" .leftcontainer, ."+cn+" .rightcontainer, ."+cn+" .albumcontainer").fadeOut(500);
	}
	else{
		$("."+cn+" .loading, ."+cn+" .loadingbox").fadeIn(200);
		current = 1;
		max = 0;
		min = 1;

		$.getJSON('Data/Webservice.php?keyword=' + artist + '&function=getInfo&selection=mbid;name;begindate;enddate;type;picture;albums;',
			function(d){
				var albumhtml = "";
				var albumtitlehtml = "";
				var count = 0;
				var images = [];
				images[0] = d.artist.picture;
				var imgc = 1;
					
				//process json
				html = "<div class='leftcontainer'><img class='artistimage' src='" + d.artist.picture + "'></div><div class='rightcontainer'>";
				html += "Begindate: " + d.artist.begindate + "<br />Enddate: " + d.artist.enddate + "<br />Type: " + d.artist.type + "<br /></div><div class='fclear'><div class='albumheader'><b>Albums</b></div><br /><br />";
				html += "<div class='albumbutton'><a onclick='PrevAlbum(\""+ cn + "\")'><b><</b></a></div><div class='albumnamecontainer'><div class='albumname'></div></div><div class='albumbutton'><a onclick='NextAlbum(\""+ cn + "\")'><b>></b></a></div>";
				html += "<div class='spacer'></div><div class='albumcontainer'><div class='songcontainer'></div></div><div class='spacer'></div>";

				$.each(d.artist.albums, function(i, album) { 
					if(album.tracks.length > 0){
						albumtitlehtml += "<div class='albumtitle'>" + album.name + "</div>";
						albumhtml += "<div class='albumcontent " + count + "'><img src='" + album.picture + "' class='albumimage'/><ul class='artistinfoul'>";
						images[imgc] = album.picture;
						imgc++;
						height = d.artist.albums.length*16;
						count++;
						
						$.each(album.tracks, function(p, track){
							if(track.name != "")
								albumhtml += "<li class='artistinfoli'><div class='album'><div class='albumleft'>" + track.name + "</div><div class='albumright'><div class='addbutton'><a onclick='changeMusic(\"" + (track.name.replace(/'/gi, "")).replace(/"/gi, "") + "\", \"" + (d.artist.name.replace(/'/gi, "")).replace(/"/gi, "") + "\")'><b>Play</b></a></div></div></div></li>";
						});
						albumhtml += "</ul></div>"
					}
				});
				
				//hide the openend artists
				$.each($(".resultbox li .artistcontent"), function(i, item){
					$(".backgroundbox").fadeOut(500);
					if($(item).css("display") != "none")
						$(item).slideToggle("slow", function(){$(item).parent().css("height","");}).fadeOut("slow");
				});
				
				//preload artist & album images
				$(function () {
					$.preload(images, {
						loaded_all: function (loaded, total) {
							$("."+cn).css("height", "auto");
							$("."+cn+" .artistcontent").html(html);
							$("."+cn+' .albumname').html(albumtitlehtml);
							$("."+cn+' .songcontainer').html(albumhtml);
							$("."+cn+" .artistcontent").slideToggle("slow");
							$("."+cn+' .albumcontainer').css("height", ($("." + cn + " .0 li").length * 16));
							$("."+cn+" .loading, ."+cn+" .loadingbox").fadeOut(200);
							$("."+cn+" .backgroundbox").fadeIn(500);
							max = $("."+cn+' .songcontainer > div').length;
						}
					});
				});
			}
		);
	}
}

//Select next album
function NextAlbum(cn){
	if(current < max){
		$(".songcontainer .addbutton").hide();
		$("." + cn + " .songcontainer").animate({left: -(current * (720))}, {duration: 1000, queue:false });
		$("." + cn + " .albumname").animate({left: -(current * 670)}, {duration: 1000, queue:false});
		$("." + cn + ' .albumcontainer').animate({ 'height' : ($("." + cn + " ."+current+" li").length * 16)}, {duration: 1000, queue:false});
		setTimeout('$(".songcontainer .addbutton").fadeIn(300);', 1000);
		current++;
	}
}

//Select previous album
function PrevAlbum(cn){
	if(current > min){
		$(".songcontainer .addbutton").hide();
		$("." + cn + " .songcontainer").animate({left: -((current-2) * (720))}, {duration: 1000, queue:false});
		$("." + cn + " .albumname").animate({left: -((current-2) * 670)}, {duration: 1000, queue:false});
		$("." + cn + ' .albumcontainer').animate({ 'height' : ($("." + cn + " ."+(current-2)+" li").length * 16)}, {duration: 1000, queue:false});
		setTimeout('$(".songcontainer .addbutton").fadeIn(300);', 1000);
		current--;
	}
}