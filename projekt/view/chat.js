var timestamp = 0;
$( window ).on("load", function() {
    $( "#btn" ).on( "click", sendMessage );

  //waitMessage();
} );

//-------------------------------------------------

// Provjera za sprječavanje duplikata
function checkIfExists( id )
{
    var exists = 0;
    $( '.message-id' ).each( function() {
        $id_of_seen_message = ( ( $( this ).attr( "id" ) ).split('_') )[1];
        if( $id_of_seen_message == id )
            exists = 1;
    } );
    return exists;
}

function waitMessage()
{
    console.log( "waitMessage" );
    $("#messages-container").scrollTop($("#messages-container")[0].scrollHeight);
    $.ajax(
    {
        url: "index.php?rt=texteditor/waitMessage",
        type: "GET",
        data:
        {
            // Timestamp = vrijeme kad smo zadnji put dobili poruke sa servera.
            timestamp: timestamp,
            cache: new Date().getTime()
        },
        dataType: "json",
        success: function( data )
        {
            console.log( "waitMessage :: success :: data = " + JSON.stringify( data ) );

            // Da li je u poruci definirano svojstvo error?
            if( typeof( data.error ) !== "undefined" )
            {
                // Ipak je došlo do greške!
                console.log( "waitMessage :: success :: server javio grešku " + data.error );
            }
            else if ( !checkIfExists( data.id_msg ) )
            {
                // Ako nema greške i ako poruka još nije ispisana, pročitaj poruku i dodaj ju u div.
                message = $( "<div></div>" );
                message
                    .attr( "id", 'id_' + data.id_msg )
                    .attr( "class", "message-id" )
                    .html( '<small>' + data.time_msg + '</small><br><b>' + data.user + '</b>: ' + decodeURI( data.msg ) );
                $( "#messages-container" ).append( message );
                timestamp = data.timestamp;

                // Ova poruka je gotova, čekaj iduću.
                waitMessage();
            }
            else
            {
                timestamp = data.timestamp;
                waitMessage();
            }
        },
        error: function( xhr, status )
        {
            console.log( "waitMessage :: error :: status = " + status );
            // Nešto je pošlo po krivu...
            // Ako se dogodio timeout, tj. server nije ništa poslao u zadnjih XY sekundi,
            // pozovi ponovno waitMessage.
            if( status === "timeout" )
                waitMessage();
        },
		timeout: 10000
    } );
}


function sendMessage()
{
    // Za slanje poruke koristimo GET, poslat ćemo ime i poruku.
    // Recimo, koristimo $.ajax (možemo i $.get).
    $.ajax(
    {
        url: "index.php?rt=texteditor/sendMessage",
        type: "GET",
        data:
        {
            msg: encodeURI( $( "#txt" ).val() )
        },
        dataType: "json",
        success: function( data )
        {
            console.log( "sendMessage :: success :: data = " + JSON.stringify( data ) );
        },
        error: function( xhr, status )
        {
            if( status !== null )
                console.log( "sendMessage :: greška pri slanju poruke (" + status + ")" );
        },
		timeout: 10000
    } );

    // Obriši sadržaj text-boxa.
    $( "#txt" ).val( "" );
}
