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
