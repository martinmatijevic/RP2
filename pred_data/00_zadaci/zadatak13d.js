/*
	Iako bismo i sa canvasom mogli napisati podjednako jednostavno rješenje kao u zadatak13b.html,
	ovdje je za ilustraciju napravljena objektno-orijentirana implementacija kojom se ostvaruju 
	sljedeće bitne prednosti:
	- Uopće se ne koriste globalne varijable.
	- Sva logika (i samo logika) igre se nalazi u klasi XO.
	- Sve što se tiče korisničkog sučelja (i samo to) se nalazi u klasi XOUI.
	- Za imalo kompliciraniju aplikaciju, bez ovakve raspodjele bi nastao dosta veliki kaos.
	- Finalni korak bi bio implementirati neku od varijanti MVC-a.
*/

$( document ).ready( function()
{
	var xo = new XO( $( "#xo" ) );
} );


// -------------------------------------------------------------------------------------------------
// XO je klasa koja sadrži samo logiku igre.
// Sva interakcija sa korisničkim sučeljem (canvas, naslov i slično) je unutar klase XOUI.
// -------------------------------------------------------------------------------------------------
XO = function( container )
{
	console.log( "XO :: konstruktor" );

	// HTML element (zapravo, jQuery objekt) u kojem se nalazi igra.
	this.container = container;

	// Incijaliziraj ploču u kojoj je stanje igre.
	this.ploca = [];
	for( var r = 0; r < 3; ++r )
	{
		this.ploca[r] = [];
		for( var c = 0; c < 3; ++c )
			this.ploca[r][c] = "?";
	}

	// Tko je na redu?
	this.naRedu = "x";

	// Inicijaliziraj korisničko sučelje (ui) za igru.
	this.ui = new XOUI( this );
}


XO.prototype.odigrajPotez = function( r, c )
{
	console.log( "XO :: odigrajPotez (r=" + r + ", c=" + c + ")" );

	// Igrač je odigrao potez na polje (r, c);
	if( this.ploca[r][c] === "?" )
	{
		if( this.naRedu === "x" )
		{
			this.ploca[r][c] = "x";
			this.naRedu = "o";

			// Nacrtaj potez na ploči.
			this.ui.crtaj_znak( "x", r, c );
		}
		else
		{
			this.ploca[r][c] = "o";
			this.naRedu = "x";

			// Nacrtaj potez na ploči.
			this.ui.crtaj_znak( "o", r, c );
		}
	}

	// Provjeri je li netko pobijedio
	var pobjednik = this.odrediPobjednika();
	
	if( pobjednik !== "?" )
	{
		this.ui.crtaj_pobjedu( pobjednik );
		this.ui.game_over();
	}
	else
		this.ui.crtaj_naslov(); // Promijenio se onaj koji je na redu.
}


XO.prototype.odrediPobjednika = function()
{
	console.log( "XO :: odrediPobjednika" );

	// Provjeri je li netko pobijedio. Ako je, zaustavi igru.
	pobijedio = "?";

	var ploca = this.ploca; 
	for( var r = 0; r < 3; ++r )
		if( ploca[r][0] !== "?" && ploca[r][0] === ploca[r][1] && ploca[r][0] === ploca[r][2] )
			pobijedio = ploca[r][0];

	for( var c = 0; c < 3; ++c )
		if( ploca[0][c] !== "?" && ploca[0][c] === ploca[1][c] && ploca[0][c] === ploca[2][c] )
			pobijedio = ploca[0][c];

	if( ploca[0][0] !== "?" && ploca[0][0] === ploca[1][1] && ploca[0][0] === ploca[2][2] )
		pobijedio = ploca[0][0];

	if( ploca[0][2] !== "?" && ploca[0][2] === ploca[1][1] && ploca[0][2] === ploca[2][0] )
		pobijedio = ploca[0][2];

	// Da li je sve puno?
	if( pobijedio === "?" )
	{
		var puno = true;
		for( var r = 0; r < 3; ++r )
			for( var c = 0; c < 3; ++c )
				if( ploca[r][c] === "?" )
					puno = false;

		if( puno )
			pobijedio = "nerješeno";
	}

	return pobijedio;
}


// -------------------------------------------------------------------------------------------------
// XOUI je klasa koja opisuje funkcioniranje korisničkog sučelja (canvas, naslov i slično) za igru.
// -------------------------------------------------------------------------------------------------
XOUI = function( game )
{
	console.log( "XOUI :: konstruktor" );
	// Funkcija koja inicijalizira grafičko sučelje za igru.
	
	// Spremi instancu igre kako člana.
	this.game = game;

	// Napravi naslov.
	this.naslov = $( "<h2></h2>" ).css( "font-family", "Verdana" );

	// Napravi canvas za igru.
	this.poljeZaIgru = $( "<canvas></canvas>" );

	// canvas = kvadrat stranice 50% ekrana.
	var y_size = window.innerHeight * 0.5, x_size = y_size;
	this.poljeZaIgru
		.prop( "width", x_size )
		.prop( "height", y_size );

	// Dodaj naslov i polje za igru u container igre (div).
	this.game.container
		.append( this.naslov )
		.append( this.poljeZaIgru );

	// Nacrtaj polje za igru i naslov.
	this.crtaj_poljeZaIgru();
	this.crtaj_naslov();

	// Dodaj event handler za klik na canvas.
	// Pazi: treba self jer se this unutar function(e) odnosi na element koji je generirao događaj, tj. na canvas.
	// Želimo imati pristup do XOUI, pa zato radimo closure: self=objekt tipa XOUI čak i unutar function(e).
	var self = this;
	this.poljeZaIgru.on( "click", function(e) { self.klikNaCanvas(e); } );
}


XOUI.prototype.crtaj_poljeZaIgru = function()
{
	console.log( "XOUI :: crtaj_poljeZaIgru" );

	// Nacrtaj polje za igru.
	var x_size = this.poljeZaIgru.width(), y_size = this.poljeZaIgru.height();

	var ctx = this.poljeZaIgru.get(0).getContext( "2d" );
	ctx.lineWidth = "5";

	// Rešetka (okomite i vodoravne crte) za igru.
	ctx.beginPath();
	ctx.moveTo( x_size/3, 0 ); ctx.lineTo( x_size/3, y_size );
	ctx.moveTo( 2*x_size/3, 0 ); ctx.lineTo( 2*x_size/3, y_size );
	ctx.moveTo( 0, y_size/3 ); ctx.lineTo( x_size, y_size/3 );
	ctx.moveTo( 0, 2*y_size/3 ); ctx.lineTo( x_size, 2*y_size/3 );
	ctx.stroke();
}


XOUI.prototype.crtaj_naslov = function()
{
	console.log( "XOUI :: crtaj_naslov" );
	this.naslov.html( "Na redu je " + this.game.naRedu + "." );
}


XOUI.prototype.klikNaCanvas = function(e)
{
	// Odredi koordinate klika.
	var rect = this.poljeZaIgru.get(0).getBoundingClientRect();
	var x = e.clientX - rect.left, y = e.clientY - rect.top;

	var x_size = this.poljeZaIgru.width(), y_size = this.poljeZaIgru.height();

	// Odredi na koje je polje u canvasu kliknuto te preslikaj to u redak i stupac tablice.
	var r = null, c = null;

	if( 0 <= x && x < x_size/3 )
		c = 0;
	else if( x_size/3 <= x && x < 2*x_size/3 )
		c = 1;
	else
		c = 2;

	if( 0 <= y && y < y_size/3 )
		r = 0;
	else if( y_size/3 <= y && y < 2*y_size/3 )
		r = 1;
	else
		r = 2;

	console.log( "XOUI :: klikNaCanvas (x=" + x + ", y=" + y + ") -> (r=" + r + ", c=" + c + ")" );

	// Odigraj taj potez na polje (r, c).
	// Za to je zadužena logika igre iz klase XO.
	this.game.odigrajPotez( r, c );
}


XOUI.prototype.crtaj_znak = function( znak, r, c )
{
	console.log( "XOUI :: crtaj_znak" );

	// Crta znak x ili o na odgovarajuće mjesto u canvasu.
	var ctx = this.poljeZaIgru.get(0).getContext( "2d" );
	var x_size = this.poljeZaIgru.width(), y_size = this.poljeZaIgru.height();

	// Postavi font.
	var font_size = 0.6 * (x_size / 3); // 60% veličine polja.
	ctx.font = "" + font_size + "px Verdana";

	// x je plavi, o je crveni
	ctx.fillStyle = ( znak === "x" ? "blue" : "red" );

	// Nacrtaj da slovo bude centrirano.
	ctx.textBaseline = "middle";
	ctx.textAlign = "center";

	// Koordinate gdje treba nacrtati znak.
	var x = c*(x_size/3) + (x_size/6), y = r*(y_size/3) + (y_size/6);
	ctx.fillText( znak, x, y );
}


XOUI.prototype.crtaj_pobjedu = function( pobjednik )
{
	console.log( "XOUI :: crtaj_pobjedu" );

	// Jednostavni popup prozor kad je igra gotova.
	if( pobjednik === "nerješeno" )
		alert( "Igra je završila nerješeno." );
	else
		alert( "Pobijedio je " + pobjednik + "!" );
}


XOUI.prototype.game_over = function()
{
	console.log( "XOUI :: game_over" );

	// Zabrani daljnje klikanje po canvasu.
	this.poljeZaIgru.off( "click" );
}
