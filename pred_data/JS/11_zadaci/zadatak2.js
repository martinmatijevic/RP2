$( document ).ready( function()
{
	$( "a.fileLink" ).on( "mouseenter", prikaziInfo );
	$( "a.fileLink" ).on( "mouseleave", sakrijInfo );
} );


prikaziInfo = function( event )
{
	// Dohvati element nad kojim je događaj.
	var a = $( this );

	// Dohvati podatke o odgovarajućoj datoteci.
	$.ajax(
	{
		url: "zadatak2.php",
		data: { fileName: a.prop( "href" ) },
		dataType: "json",
		success: function( data )
		{
			console.log( "Dobio od servera: " + JSON.stringify( data ) );

			// Stvori balon (div) i prikaži ga.
			var div = $( "<div></div>" );

			// Uoči: PHP vrati lastModified u sekundama od 1.1.1970.
			// JavaScript koristi milisekunde.
			div
				.prop( "id", "balon" )
				.css(
				{
					"position": "absolute",
					"left": event.clientX + 20,
					"top": event.clientY + 20,
					"border": "solid 1px",
					"background-color": "rgb(245, 245, 255)",
					"padding": "5px"
				} )
				.html(
					data.fileName 
					+ "<br />Last modified: " + (new Date( data.lastModified * 1000 ) )
					+ "<br />File size: " + data.size + " bytes"
				);

			$( "body" ).append( div );
		}
	} );
}


sakrijInfo = function()
{
	// Samo ukloni (jedini) element sa id-om "balon"
	$( "#balon" ).remove();
}

