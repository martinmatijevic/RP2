// Minimalan i maximalan broj sekundi na početku na buttonu.
var min_s = 5, max_s = 10;

// Broj buttona, širina i visina ekrana; id intervala od setInterval.
var N = null, x_size = null, y_size = null, id_intevral = null;

// Polje buttona.
var btn = [];


$( document ).ready( function()
{
	// Učitaj broj buttona.
	N = Number( prompt( "Unesite broj buttona!" ) );

	// Napravi N buttona na random lokacijama.
	// Dohvati veličinu prozora.
	x_size = window.innerWidth, y_size = window.innerHeight;

	for( var i = 0; i < N; ++i )
	{
		// Napravi i-ti button.
		btn[i] = $( "<button></button>" );

		// Generaraj random broj koji će pisati na buttonu.
		var broj = min_s + Math.floor( (max_s-min_s) * Math.random() );

		// Napiši broj na button, smjesti ga na random mjesto, podesi što se radi kad kliknemo na njega.
		btn[i]
			.html( broj )
			.css( "position", "absolute" )
			.css( "left", Math.floor( x_size * Math.random() ) )
			.css( "top", Math.floor( y_size * Math.random() ) )
			.on( "click", klik );

		// Dodaj button na stranicu.
		$( "body" ).append( btn[i] );
	}

	// Svake sekunde smanjujemo brojeve na buttonima.
	id_intevral = setInterval( updateBtns, 1000 );
} );


klik = function()
{
	// Button na kojeg je kliknuto
	var btn = $( this );

	// Generiraj novi broj u njemu
	var broj = min_s + Math.floor( (max_s-min_s) * Math.random() );

	// Premjesti ga na drugu lokaciju
	btn
		.html( broj )
		.css( "left", Math.floor( x_size * Math.random() ) )
		.css( "top", Math.floor( y_size * Math.random() ) );
}


updateBtns = function()
{
	// Svake sekunde prođi kroz sve buttone i smanji im sekundu za 1.
	// Ako neki padne na 0, disableaj ga.

	var broj_aktivnih = 0;
	for( var i = 0; i < N; ++i )
	{
		if( btn[i].prop( "disabled" ) === false )
		{	
			var broj = Number( btn[i].html() );
			--broj;
			btn[i].html( broj );

			if( broj === 0 )
				btn[i].prop( "disabled", true );
		}

		if( btn[i].prop( "disabled" ) === false )
			++broj_aktivnih;
	}

	// Ako su svi buttoni disableani, igra je gotova.
	if( broj_aktivnih === 0 )
	{
		alert( "Game over." );
		clearInterval( id_intevral );
	}
}

