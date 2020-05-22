$(document).ready(function()
{
  var divSvi = $('div.gallery');
  $('img').hide();
  $('p').hide();
  for (var i=0; i<divSvi.length; ++i){
    var divSadrzaj = divSvi.eq(i);
    var djeca = divSadrzaj.children();
    var j = 0;
    var slike = djeca.siblings('img');
    var paragrafi = djeca.siblings('p');
    var opisIgumb = $('<p>');
    opisIgumb.append(divSadrzaj.attr('title')+'.</p><button type="button" id="galerija_gumb">Pogledaj galeriju!</button>');
    divSadrzaj.append(opisIgumb);
    var novidiv = $('<div>');
    novidiv.css({
        position: 'absolute',
        top: '20%',
        bottom: '20%',
        left: '20%',
        right: '20%',
        width: '60%',
        height: '60%',
        backgroundColor: 'gray'
      });
    novidiv.append('<button type="button" id="izlaz" value="X">x</button>');
    novidiv.append('<button type="button" id="lijevo"><<</button>');
    novidiv.append('<button type="button" id="desno">>></button>');
    while (j<djeca.length/2){
      var slika = slike.eq(j);
      $(slika).css({
        position: 'absolute',
        top: '5%',
        left: '5%',
        maxWidth: '90%',
        maxHeight: '80%'
      });
      $(slika).show();
      novidiv.append(slika);
      novidiv.append('<p id=tekst_ispod_slike>Slika ' + (j+1) + '/' + slike.length + '.');
      j++;
    }
  };
  $('body').append(novidiv);
  novidiv.hide();
  $('#galerija_gumb').click(function(){
    novidiv.show();
    $('#izlaz').css({
      backgroundColor: 'red',
      fontSize: '200%',
      position: 'absolute',
      top: '0%',
      right: '0%'
    });
    $('#lijevo').css({
      fontSize: '200%',
      position: 'absolute',
      bottom: '5%',
      left: '5%'
    });
    $('#desno').css({
      fontSize: '200%',
      position: 'absolute',
      bottom: '5%',
      right: '5%'
    });
    $('#tekst_ispod_slike').css({
      textAlign: 'center',
      horizontalAlign: 'middle'
    });
  })
  $('#izlaz').click(function(){
    novidiv.hide();
  })
});
