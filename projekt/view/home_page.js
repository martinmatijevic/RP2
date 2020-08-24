var timestamp = 0;
var getUrl = window.location;
var projektUrl = getUrl .protocol + "//" + getUrl.host + "/"
                    + getUrl.pathname.split('/')[1] + '/projekt';

$( document ).ready( waitForNewDocuments );
$( '#create_new' ).on( "click", createNew );

//-------------------------------------------------

function createNew()
{
    var title = prompt("Unesi ime novog dokumenta", "Novi dokument");

    if ( title === null )
        return;

    $.ajax(
        {
            url: "index.php?rt=texteditor/createNewDocument",
            type: "POST",
            data:
            {
                title: title
            },
            success: function( data )
            {
                console.log( "createNew :: success :: data = " + JSON.stringify( data ) );

                if( typeof( data.error ) !== "undefined" )
                {
                    // Ipak je došlo do greške!
                    console.log( "createNew :: success :: server javio grešku " + data.error );
                }
                // Ako nema greške, ne radi ništa
                //exit();
            },
            error: function( xhr, status )
            {
                console.log( "createNew :: error :: status = " + status );
                // Nešto je pošlo po krivu...
            }
        }
    );
}

// Provjera za sprječavanje duplikata
function checkIfExists( id )
{
    var exists = 0;
    $( '.doc-id' ).each(function() {
        if( $( this ).html() == id )
            exists = 1;
    } );
    return exists;
}

function waitForNewDocuments()
{
    $.ajax(
        {
            url: 'index.php?rt=homepage/ajaxSendNewUsersDocuments',
            type: "GET",
            dataType: "json",
            data:
            {
                // Timestamp = vrijeme kad smo zadnji put dobili poruke sa servera.
                timestamp: timestamp,
                cache: new Date().getTime()
            },
            dataType: "json",
            success: function( data )
            {
                console.log( "waitForNewDocuments :: success :: data = " + JSON.stringify( data ) );
                // Da li je u poruci definirano svojstvo error?
                if( typeof( data.error ) !== "undefined" )
                {
                    // Ipak je došlo do greške!
                    console.log( "waitForNewDocuments :: success :: server javio grešku " + data.error );
                }
                else if ( !checkIfExists( data.id ) )
                {
                    // Ako nema greške i dokument nije na popisu, dodaj ga na popis
                    var div = $( '<div></div>' );
                    div.prop( 'class', 'link' )

                    var dt = $( '<dt></dt>' );
                    dt
                        .html( data.id )
                        .prop( 'class', 'doc-id' );

                    var dd = $( '<dd></dd>' );
                    var a = $( '<a></a>' );
                    a
                        .html( data.title )
                        .prop( 'href', projektUrl + '/index.php?rt=texteditor&document_id=' + data.id + '&title=' + data.title);
                    dd.append( a );
                    div
                        .append( dt )
                        .append( dd );

                    $( 'dl' ).append( div );
                    timestamp = data.timestamp;

                    // Ova poruka je gotova, čekaj iduću.
                    waitForNewDocuments();
                }
                else
                {
                    timestamp = data.timestamp;
                    waitForNewDocuments();
                }
            },
            error: function( xhr, status )
            {
                console.log( "waitForNewDocuments :: error :: status = " + status );
                // Nešto je pošlo po krivu...
                // Ako se dogodio timeout, tj. server nije ništa poslao u zadnjih XY sekundi,
                // pozovi ponovno waitForNewDocuments.
                if( status === "timeout" )
                    waitForNewDocuments();
            }
        } );
}
