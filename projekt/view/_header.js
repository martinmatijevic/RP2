$( document ).ready( checkLogin );

function checkLogin() {
    $.ajax(
    {
        url: 'index.php?rt=users/ajaxCheckLogin',
        type: "GET",
        dataType: "json",
        success: function( data )
        {
            console.log( "checkLogin :: success :: data = " + JSON.stringify( data ) );
            // Pro‹itaj poruku i dodaj gumb za logout u header
            if( data === "logged")
            {
                var button = $( '<button class="btn btn-danger" style="float: right"></button>' );
                button
                    .prop( 'type', 'submit')
                    .html('Odjava!');

                var form = $( '<form></form>' );
                form
                    .prop( 'action', 'index.php?rt=texteditor/unlockUsersLines&logout=true' )
                    //.prop( 'action', 'index.php?rt=texteditor/unlockUsersLines' )
                    .prop( 'method', 'post')
                    .prop( 'id', 'logout-form')
                    .append( button );

                $( "#logout-div" ).append( form );
                timestamp = data.timestamp;
            }
            else
                $( "#logout-div" ).html( "" );
        },
        error: function( xhr, status )
        {
            console.log( "checkLogin :: error :: status = " + status );
        }
    } );
}
