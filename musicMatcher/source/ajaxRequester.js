// CSE 190 M Lab 8 (Mr. Potato Head) starter JS code

var WEB_APP = "http://www.dbpedia.org/sparql";

document.observe("dom:loaded", function() {
    // Exercise 4: reload saved initial state from web server ...
    new Ajax.Request(WEB_APP, {
        method: "get",
		parameters: {default-graph-uri: http%3A%2F%2Fdbpedia.org,query: select+distinct+%3FConcept+where+%7B%5B%5D+a+%3FConcept%7D, format: text%2Fhtml, timeout: 0, debug: on},
        onSuccess: ajaxGotState,
        onFailure: ajaxFailure,
        onException: ajaxFailure
    });
    
});

function ajaxGotState(ajax) {
    $("result").innerHTML = "Coming up: " + ajax.responseText;
}

// standard provided Ajax error-handling function
function ajaxFailure(ajax, exception) {
    alert("Error making Ajax request:" + 
          "\n\nServer status:\n" + ajax.status + " " + ajax.statusText + 
          "\n\nServer response text:\n" + ajax.responseText);
    if (exception) {
        throw exception;
    }
}
