function createtables() {
    $.ajax({
        dataType: "json",
        url: "/setup/setup.exec.php",
        data: { method: 'CREATE-TABLES' },
        success: function( json ) {
	    $("#setup2").html("");
            $("#setup2").append("<ul><li>" + json.MESSAGE + "</li></ul>");
	    $("#button button").hide();
	    $("#setup2").append("<a href='/index.php'>Hier weiter zur Startseite ... !</a>");
        }
    });
}

$(function() {
    $('#searchcontrol input[type="text"]').val("");
    $('#searchcontrol input[type="text"]').on('input',function(e){
	var value = $('#searchcontrol input[type="text"]').val();
	$.ajax({
    	    dataType: "json",
    	    url: "/setup/setup.exec.php",
    	    data: {
		method: 'SEARCH-APP',
		text: value
	    },
    	    success: function( json ) {
		$("#main").html("");
		var tindex = 1;
		$.each(json.APPS, function() {
		    $("#main").append("<div id='app' tabindex='" + tindex + "' class='app" + this.AID + "' onkeypress='{if (event.keyCode==13) { window.open(\"" + this.AURL + "\", \"_blank\"); } }' onclick='window.open(\"" + this.AURL + "\", \"_blank\");' style='background-image: url(\"" + this.AIPT + "\");'><span>" + this.ADSC + "</span></div><div id='deldiv' class='hide del" + this.AID + "' onclick=\"editapp('" + this.AID + "');\" title=''></div>");
		    var url = this.AURL;
		    if (json.APPS.length == 1) {
			$("body").off().on("keydown", function(e) {
			    var key = e.which||e.keyCode;
			    if (key == 13) {
				e.preventDefault();
				window.open(url, "_blank");
				// wished error
				return false;
			    }
			});
		    }
		    tindex++;
		});
    	    }
	});
    });
});