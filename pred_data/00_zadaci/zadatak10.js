var labirint = 
	[
		"*.**.**......*..*....", 
	    "...*.*..*..*.**...*..", 
	    ".*.*....*.***...*.**.", 
	    "M*...*..*.....*...*X."
	];

// Koordinate na kojima se nalazi Mirko
var mirko_r = null, mirko_c = null, brojKoraka = 0;

$( document ).ready( function()
{
	// Pripremi tablicu s labirintom.
	var tbl = tablicaLabirint();

	// Nađi Mirka u labirintu, spremi njegove koordinate.
	for( var r = 0; r < labirint.length; ++r )
		for( var c = 0; c < labirint.length; ++c )
			if( labirint[r][c] === "M" )
				mirko_r = r, mirko_c = c;

	// Dodaj tablicu u div #labirint.
	$( "#labirint" ).html( tbl );

	// Dodaj funkcija koja će reagirati na strelice.
	// Ide u document.body jer možemo biti pozicionirani bilo gdje u dokumentu kad se krećemo s Mirkom.
	$( document.body ).on( "keydown", obradiTipku );
} );


tablicaLabirint = function()
{
	// Napravi novi table element za labirint
	var tbl = $( "<table></table>" );

	// Nacrtaj u njemu retke i stupce labirinta
	for( var r = 0; r < labirint.length; ++r )
	{
		// Novi redak u tablici
		var tr = $( "<tr></tr>" );

		for( var c = 0; c < labirint[r].length; ++c )
		{
			// Nova ćelija u tablici
			var td = $( "<td></td>" );

			// Pogledaj koje je vrste to polje u labirintu i postavi odgovarajuću klasu
			if( labirint[r][c] === "." )
				td.addClass( "prazno" );
			else if( labirint[r][c] === "*" )
				td.addClass( "zid" );
			else if( labirint[r][c] === "M" )
				td.addClass( "mirko" );
			else if( labirint[r][c] === "X" )
				td.addClass( "blago" );

			// Dodaj ćeliju u redak
			tr.append( td );
		}

		// Dodaj redak u tablicu
		tbl.append( tr );
	}

	return tbl;
}


obradiTipku = function( event )
{
	// Nove Mirkove koordinate.
	var novi_r = mirko_r, novi_c = mirko_c;

	// Pogledaj koja je tipka stisnuta, analiziraj samo strelice.
	if( event.key === "ArrowUp" )
		--novi_r;
	else if( event.key === "ArrowDown" )
		++novi_r;
	else if( event.key === "ArrowRight" )
		++novi_c;
	else if( event.key === "ArrowLeft" )
		--novi_c;
	else
		return;

	// Provjeri jel smije na nove koordinate.
	if( 0 <= novi_r && novi_r < labirint.length && 
		0 <= novi_c && novi_c < labirint[0].length && 
		labirint[novi_r][novi_c] !== "*" )
	{
		++brojKoraka;

		// Je li došao do blaga?
		var gotovo = false;
		if( labirint[novi_r][novi_c] == "X" )
			gotovo = true;

		// Updateaj izgled labirinta.
		// Uoči, ne smijemo samo labirint[mirko_r][mirko_c] = "." jer se stringovi u JS NE SMIJU MIJENJATI.
		// Treba sagraditi cijeli novi string za retke mirko_r i novi_r.
		labirint[mirko_r] = labirint[mirko_r].substring( 0, mirko_c ) + "." + labirint[mirko_r].substring( mirko_c+1 );
		labirint[novi_r] = labirint[novi_r].substring( 0, novi_c ) + "M" + labirint[novi_r].substring( novi_c+1 );

		mirko_r = novi_r;
		mirko_c = novi_c;

		// Refreshaj sliku labirinta
		var tbl = tablicaLabirint();
		$( "#labirint" ).html( tbl );

		if( gotovo )
		{
			alert( "Bravo! Pronašao si blago u samo " + brojKoraka + " koraka!" );
				
			// Svejedno mu dajemo mogućnost da nastavi pobjedničku šetnju po labirintu i nakon što nađe blago.
			// Ako ne, treba otkomentirati ovu liniju:
			// $( document.body ).off( "keydown" );
		}	
	}
}
