$( document ).ready( function()
{
	// Kad uđemo mišem nad sekciju, stavimo joj sjenu.
	$( "section" ).on( "mouseover", function()
	{
		// this = HTML element na kojem je nastao događao = section
		// $( this ) = napravi jQuery objekt od this
		$( this ).css( "box-shadow", "5px 5px 5px rgb(50, 50, 50)" );
	} );

	// Kad izađemo van sekcije, maknemo sjenu.
	$( "section" ).on( "mouseout", function()
	{
		$( this ).css( "box-shadow", "" );
	} );

	// Sakrij sve komentare paragrafe klase comment koji se nalaze unutar sekcija
	$( "section p.comment" ).css( "display", "none" );

	// Kad netko klikne na link klase showComments unutar paragrafa...
	$( "section a.showComments").on( "click", function()
	{
		// Popni se do sekcije u kojoj je taj link. Sekcija je "roditelj" od linka.
		// this = HTML element a; $( this ) = napravi jQuery objekt od this-a.
		var section = $( this ).parent();

		console.log( section );

		// Dohvati komentare u toj sekciji. Oni su djeca od sectiona koja odgovaraju selektoru p.comment.
		var comments = section.children( "p.comment" ); 

		// Ako je (prvi) komentar skriven (display: none), otkrij sve komentare (display: block) i obratno.
		if( comments.css( "display") === "none" )
			comments.css( "display", "block" );
		else
			comments.css( "display", "none" );
	} );

} );