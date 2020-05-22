$(document).ready(function()
{
  var svi_div = $('div.gallery');
  $('img').hide();
  $('p').hide();
  for (var i=0; i<svi_div.length; ++i){
    var div_sadrzaj = svi_div.eq(i);
    var popis = $('<p>');
    popis.append(div_sadrzaj.attr('title')+'.</p><button type="button" id="galerija_gumb">Pogledaj galeriju!</button>');
    div_sadrzaj.append(popis);
    console.log(div_sadrzaj);
    for (var j=0; j<div_sadrzaj.length; ++j){
      var slike;
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
      novidiv.append('<button type="button" id="izlaz" value="X">X</button>');
      novidiv.append('<button type="button" id="lijevo"><<</button>');
      var nj = j+1;
      novidiv.append('<p id=tekst_ispod_slike>Slika ' + nj + '/' + div_sadrzaj.length + '.<br>33');


      //if ('p'.attr('data-target')==="onofrio.jpg") $('img').show();
      novidiv.append('<button type="button" id="desno">>></button>');
    };
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
