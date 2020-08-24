//Početak dokumenta
var username;
var documentid;
var timestamp;

//tu ide dio koji poziva unlockUsersLines prilikom refresha ili kad se izađe iz te stranice
window.addEventListener("unload", logData, false);
function logData()
{
	navigator.sendBeacon("index.php?rt=texteditor/unlockUsersLines", null);
}

$( window ).on("load", function()
{
	timestamp = ( new Date().getTime() ) / 1000;
	username = $("#username").html();
	documentid = (iddecompose($(".uredivo p").attr('id')))[0];
	$( "#btn_dodaj" ).on( "click", addCollaborator);
	$("#messages-container").scrollTop($("#messages-container")[0].scrollHeight);
	//nađemo onaj dio stranice koja se može uređivati
	$(".uredivo p").on("click", f=function()
	{
		//console.log(this)
		var id = this.id
		//alert(id)

		var odlomak=$(this)
		//console.log(odlomak)

		var dozvola = false;
		//prije nego zatrežimo dozvolu,
		//provjerimo nije li možda class=="locked"
		if(odlomak.attr("class")==="locked")
		{
			alert("Netko već uređuje ovu liniju")
			return;
			//return će nam ubiti funkciju,
			//ako ovo dobro radi, else nam čak ni ne treba
		}
		else
		{
			//tek sad smijemo tražiti dozvolu
			dozvola = askForEditingPermission( id, username )
		}

		//console.log(dozvola)

		if(dozvola === false)
		{
			//ako nismo dobili dozvolu, server nam je poslao false
			alert("Netko već uređuje ovu liniju")
		}
		else
		{
			//dobili smo dozvolu, započinjemo s uređivanjem

			//spremamo predhodno stanje linije
			var predhodno=odlomak.text()
			//console.log(predhodno)

			//stvaramo komandnu ploču i dodajemo je iza linije koje uređujemo
			var gumbi= $('<div id="gumbi"></div>')
			var spremi= $('<div id="spremi">spremi</div>')
			var odustani= $('<div id="odustani">odustani</div>')
			var newline= $('<div id="newline">nova linija</div>')
			var deleteline= $('<div id="deleteline">obriši liniju</div>')
			//gumbi.id="gumbi"
			gumbi.append(spremi)
			gumbi.append(odustani)
			gumbi.append(newline)

			if($(".uredivo p").length > 1)
			{
				gumbi.append(deleteline)
			}

			odlomak.after(gumbi)

			//console.log(gumbi)



			//dodjeljujemo funkcije gumbima
			$("#spremi").on("click", function()
			{
				//alert(odlomak.text())

				//šaljemo promjene serveru
				saveLineChange(id, username, odlomak )
				//možda provjeravamo uspješnost?

				var temp = "";

				if( odlomak.text()[0] === '+' )
				{
					if( odlomak.text()[1] === ' ' )
						temp = ( odlomak.text() ).substr( 2 );
					else
						temp = ( odlomak.text() ).substr( 1 );
				}
				else
					temp = odlomak.text();

				temp = temp.trim();

				odlomak.html( '+ ' + temp );
				//na kraju moramo ubiti gumbe
				gumbi.remove()
				//zabraniti editanje
				odlomak.attr("contenteditable", "false")
				//vratiti funkciju na odlomak (jer smo ju prvo skinuli, dolje u kodu)
				$(".uredivo p").on("click", f)
			})
			$("#odustani").on("click", function()
			{
				//vratimo kako je bilo
				odlomak.text(predhodno)

				//vraćamo dozvolu
				cancelChange(id, username)

				//ubijamo gumbe
				gumbi.remove()
				odlomak.attr("contenteditable", "false")
				$(".uredivo p").on("click", f)
			})

			$("#newline").on("click", function()
			{
				//tražimo od servera da ubaci novi redak
				//server nam vraća id novog redka
				var newid = newLineOnServer(id, username);

				if(newid<0)
				{
					//poruka o grešci

				}
				else
				{
					//dodajemo redak
					//novom redku odmah i dodjeljujemo njegov novi id koji nam je server poslao
					odlomak.after('<p id="'+ newid +'">+</p>');
					//u novom redku piše '(novi)', ali na serveru će biti przan
					//dodat cemo znak + ispred svakog novog reda. Tako cemo izbjeci (novi)
					//sad se u bazu spremaju prazne linije umjesto s contentom (novi)

					//i vracamo sadrzaj gornje linije na predhodno
					//vratimo kako je bilo
					odlomak.text(predhodno)
				}

				//uklanjamo gumbe i branimo editanje
				gumbi.remove();
				odlomak.attr("contenteditable", "false");

				//vraćamo funkciju na dokument
				$(".uredivo p").on("click", f);

			})
			$('#deleteline').on("click", function()
			{
				//najprije pitamo klijenta da li je dobro razmislio o ovoj radnji
				if(confirm("Jeste li sigurni da želite obrisati ovu liniju?"))
				{
					//brišemo liniju

					//tražimo od servera da obriše liniju
					var test = deleteLine(id, username)

					if(test < 0)
					{
						//poruka o grešci?
					}
					else
					{
						//pravimo se kao da je linija već obrisana na serveru
						//brišemo liniju i lokalno
						odlomak.remove()

						// i na kraju uklanjamo gumbe i branimo editanje
						gumbi.remove();
						odlomak.attr("contenteditable", "false");

						//vraćamo funkciju na dokument
						$(".uredivo p").on("click", f);
					}

				}
				else
				{
					//ne brišemo liniju
				}


			})


			//console.log(this)
			//console.log(gumbi)

			//dozvoljavamo uređivanje
			odlomak.attr("contenteditable", "true")
			//skidamo funkciju s dokumenta da se ne pokreće dok ga editamo
			$(".uredivo p").off("click")
		}
	})


	waitChanges();

})


//-----------------------------------------------------------------------





//funkcija za dobivanje id-eva od jednog velikog id-a
function iddecompose(id)
{
	var id=id.split('_');
	/*
	for(let i=0; i<id.length; i++)
	{
		while(isNaN(id[i]) && id[i].length)
		{
			id[i]=id[i].substring(1);
		}

		id[i]=parseInt(id[i],10);
	}
	*/

	return id;
}

//privremena funkcija za traženje dozvole
//vraća true ili false
function askForEditingPermission(id, username )
{
	//iz id-a (koji je string) moramo izvući id od dokumenta i linije

	var id=iddecompose(id);
	var documentid=id[0];
	var lineid=id[1];

	var dozvola=false;

	//sad kad imamo documentid i lineid kontaktiramo radimo ajax upit

	$.ajax(
	{
		url: "index.php?rt=texteditor/permissionForEditingLine",
		data:
		{
			id_document: documentid,
			id_line: lineid,
			username: username
		},
		type: "POST",
		dataType: "json",
		success: function( data )
		{
			//server vraća username korisnika koji smije uređivati liniju
			//usporedi svoj username i vrati true ili false
			//true je dozvola za uređivanje
			//false znači da neki drugi korisnik već ima dozvolu za uređivanje
			if( typeof( data.editing ) !== "undefined" )
			{
				if( data.editing === username )
					dozvola = true;
			}
			else if( typeof( data.error ) !== "undefined" )
			{
				console.log(data.error)
			}
			else
			{
				console.log("nepoznata greška (ajax traženje dozvole)")
			}
		},
		error: function()
		{
			console.log( "askForEditingPermission :: error :: status = " + status );
		},
		timeout: 10000,
		async: false
	});

	return dozvola;
}

//funkcija za spremanje na server
function saveLineChange(id, username, odlomak)
{
	//Prvo maknemo praznine ako ih ima
	var content = odlomak.text().trim();
	//Sada moramo ubiti novonastale pluseve (ako ih ima)
	// točnije, mičemo samo taj prvi po redu
	if( content[0] === '+' )
	{
		content=content.substring(1);
		//Ponovo maknemo praznine
		content=content.trim();
	}


	//opet imamo id koji rebamo dekompozati

	var id=iddecompose(id);
	var documentid=id[0];
	var lineid=id[1];

	//ili .html() ili .text()? .html() će nam dati i potencijalne stilove...?
	//.text() izostavlja <br> na kraju
	//sličnu stvar imamo i dolje u osluškivanju servera
	console.log(content);
	var output = false;

	//sve to šaljemo serveru
	$.ajax(
	{
		url: "index.php?rt=texteditor/saveLineChangeToDocument",
		data:
		{
			id_document: documentid,
			id_line: lineid,
			username: username,
			line_content: content,
		},
		type: "POST",
		dataType: "json",
		success: function( data )
		{

			if( typeof( data.success ) !== "undefined" )
			{
				//success sadrži 1 ako je spremanje uspjelo
				output = data.success;
			}
			else if ( typeof( data.error ) !== "undefined" )
			{
				console.log(data.error);
				return -1;
			}
		},
		error: function()
		{
			console.log( "spremi_na_server :: error :: status = " + status );
		},
		timeout: 10000,
		//async: false
	})


	return output;
}

//funkcija za odustajanje
function cancelChange(id, username)
{
	//dekomponiramo id

	var id=iddecompose(id);
	var documentid=id[0];
	var lineid=id[1];

	var output = false;

	//javljamo serveru da ne mjenja liniju i dozvoli promjene nekom drugom
	$.ajax(
	{
		url: "index.php?rt=texteditor/cancelChange",
		data:
		{
			id_document: documentid,
			id_line: lineid,
			username: username,
		},
		type: "POST",
		dataType: "json",
		success: function( data )
		{

			if( typeof( data.success ) !== "undefined" )
			{
				//success sadrži 1 ako je sve uredu
				output = data.success;
			}
			else if ( typeof( data.error ) !== "undefined" )
			{
				console.log(data.error);
				return -1;
			}
		},
		error: function()
		{
			console.log( "cancelChange :: error :: status = " + status );
		},
		timeout: 10000,
		//async: false
	});

	return output;
}

//funkcija za dodavanje novog redka na server
function newLineOnServer(id, username)
{

	//opet dekomponiramo
	var id=iddecompose(id);
	var documentid=id[0];
	var lineid=id[1];

	var newid = -1;

	//javljamo serveru da napravi novu liniju iza linije s danim id-om
	$.ajax(
	{
		url: "index.php?rt=texteditor/addLineToDocument",
		data:
		{
			id_document: documentid,
			id_previous_line: lineid,
			username: username,
		},
		type: "POST",
		dataType: "json",
		success: function( data )
		{
			//skripta bi nam trebala poslati id od nove linije
			if( data.id !== "undefined" )
			{
				newid = documentid + "_" + data.id;
			}
			else if( data.error !== "undefined")
			{
				//ako ne, dobivamo poruku o grešci
				console.log(data.error);
				return -1;
			}
			else
			{
				console.log("nepoznata greška (ajax nova linija)")
				return -1;
			}
		},
		error: function()
		{
			console.log( "newLineOnServer :: error :: status = " + status );
		},
		timeout: 10000,
		async: false
	});


	return newid;
}

//funkcija za brisanje redka
//vraća samo da li je akcija bila uspješna
function deleteLine(id, username)
{
	//dekomponiramo id

	var id=iddecompose(id);
	var documentid=id[0];
	var lineid=id[1];

	var output;

	//javljamo serveru da briše liniju
	$.ajax(
	{
		url: "index.php?rt=texteditor/deleteLineFromDocument",
		data:
		{
			id_document: documentid,
			id_line: lineid,
			username: username,
		},
		type: "POST",
		dataType: "json",
		success: function( data )
		{

			if( data.success !== "undefined" )
			{
				//success sadrži 1 ako je brisanje uspjelo
				output = data.success;
			}
			else
			{
				console.log("nepoznata greška (ajax odustajanje)")
				output = -1;
			}
		},
		error: function()
		{
			console.log( "deleteLine :: error :: status = " + status );
		},
		timeout: 10000,
		//async: false
	});

	return output;
}


function waitChanges()
{
    console.log( "waitChanges" );

	$.ajax(
	{
		url: "index.php?rt=texteditor/ajaxSendChangesToUsers",
		type: "GET",
		data:
		{
			timestamp: timestamp,
			cache: new Date().getTime(),
			username: username,
			id_document: documentid,

		},
		dataType: "json",
		success: function( data )
		{
			console.log( "waitChanges :: success :: data = " + JSON.stringify( data ) );
			//prvo provjerimo što trebamo napraviti
			if('action' in data)
			{
				if(data.action==="delete")
				{
					//ako brišemo, treba nam samo id_linije
					if('id_line' in data)
					{
						$('#'+documentid+'_'+data.id_line).remove();

					}
					else
					{
						console.log("waitChanges/delete: nemamo id_linije")
					}
				}

				else if(data.action==="new")
				{
					//ako dodajemo novi red, isto trebamo samo id_linije
					if('id_line' in data && 'id_previous_line' in data)
					{
						if(data.id_line)
						{
							newline=$("<p>+</p>");
							newline.attr('id', documentid+'_'+data.id_line);

							$('#'+documentid+'_'+data.id_previous_line).after(newline);

							$('#'+documentid+'_'+data.id_previous_line).removeClass("locked");

							//novo dodana linija mora se također podvrgnuti klikablinosti
							newline.on('click', f);
							//f nam je ona velika funkcija od gore
						}
						else
						{
							//dodajemo na prvi redak
							console.log("ovo se nije trebalo desiti!")
						}

					}
					else
					{
						console.log("waitChanges/new: nemamo id_linije");
					}
				}

				else if(data.action==="editcontent")
				{

					//ako mjenjamo liniju, treba nam id linije i novi sadržaj
					if('id_line' in data && 'content' in data)
					{
						$('#'+documentid+'_'+data.id_line).html("+ ");
						$('#'+documentid+'_'+data.id_line).html('+ ' + data.content);
						//nakon spremanja promjena je sigurno za pretpostaviti da je linija oslobođena
						$('#'+documentid+'_'+data.id_line).removeClass("locked");
						//opet ista priča kao gore
						//ako serveru šaljemo html, onda ovdje imamo isto html
						//inače, ako gore imamo text, onda moramo i tu imati text
					}
					else
					{
						console.log("waitChanges/editcontent: nemamo id_linije i content");
					}
				}

				else if(data.action==="lock")
				{
					//očekujem username i id_line
					if('username' in data && 'id_line' in data)
					{
						if(username === data.username)
						{
							//ne činimo ništa
							//mi uređujemo
						}
						else
						{
							//netko drugi uređuje
							$('#'+documentid+'_'+data.id_line).addClass("locked")
						}


					}
					else
					{
						console.log("waitChanges/lock : nemamo sve podatke")
					}

				}
				else if(data.action==="unlock")
				{
					//očekujemo samo id_line
					if('id_line' in data)
					{
						$('#'+documentid+'_'+data.id_line).removeClass("locked")
					}
				}

				else
				{
					console.log("waitChanges: nepoznata akcija")
				}

				if('timestamp' in data)
				{
					//morali smo dobiti timestamp
					timestamp=data.timestamp
				}
				else
				{
					//žalim slučaj
				}

				waitChanges();
			}
			else
			{
				//ako nemamo action u dati, bunimo se
				console.log("waitChanges : nema action u dati");
			}
		},
		error: function( xhr, status )
		{
			//ovo je sve prepisano iz primjera 3 iz predavanja 11
			console.log( "waitChanges :: error :: status = " + status );
            // Nešto je pošlo po krivu...
            // Ako se dogodio timeout, tj. server nije ništa poslao u zadnjih XY sekundi,
            // pozovi ponovno cekajPoruku.
            if( status === "timeout" )
                waitChanges();
		},
		timeout: 10000
	} );
}

function addCollaborator()
{
    // Za dodavanje collaboratora koristimo GET, poslat ćemo username i id dokumenta.
    // Recimo, koristimo $.ajax (možemo i $.get).
	console.log($( "#txt_dodaj" ).val());
	console.log(documentid);
	$.ajax(
    {
        url: "index.php?rt=texteditor/addCollaborator",
        type: "GET",
        data:
        {
            id_document: documentid,
            username: /*encodeURI*/( $( "#txt_dodaj" ).val() )
        },
        dataType: "json",
        success: function( data )
        {
			if( data.success === 0 )
			{
				$( "#add_collab" )
					.html('<small>' + 'korisnik ' + data.username + ' ne postoji' + '</small>');
			}
			if( data.success === -1 )
			{
				$( "#add_collab" )
					.html('<small>' + 'korisnik ' + data.username + ' je već kolabolator' + '</small>');
			}
			if( data.success === 1 )
			{
				$( "#add_collab" )
					.html('<small>' + 'korisnik ' + data.username + ' je dodan' + '</small>');
			
			console.log( "addCollaborator :: success :: data = " + JSON.stringify( data ) );
			}
        },
        error: function( xhr, status )
        {
            if( status !== null )
                console.log( "addCollaborator :: greška pri dodavanju (" + status + ")" );
        },
		timeout: 10000,
    } );

    // Obriši sadržaj text-boxa.
    $( "#txt_dodaj" ).val( "" );
}
