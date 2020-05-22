function divLijevoGumbKlik(){};
function divDesnoGumbKlik(){};
function divIzlazGumbKlik(){};
function onGalleryButtonClick(hello){
  var noviDiv = $('<div>');
  console.log('hi');
  console.log(hello);
  noviDiv.css({
      position: 'absolute',
      top: '10%',
      bottom: '10%',
      left: '10%',
      right: '10%',
      width: '80%',
      height: '80%',
      backgroundColor: 'gray'
    });
  var divIzlazGumb = $('<button/>')
    .text('x')
    .attr('id','izlaz')
    .click(divIzlazGumbKlik)
    .css({
      backgroundColor: 'red',
      fontSize: '200%',
      position: 'absolute',
      top: '0%',
      right: '0%'
    });
  var divLijevoGumb = $('<button/>')
    .text('<<')
    .attr('id','lijevo')
    .click(divLijevoGumbKlik)
    .css({
      fontSize: '200%',
      position: 'absolute',
      bottom: '5%',
      left: '5%'
    });
  var divDesnoGumb = $('<button/>')
    .text('>>')
    .attr('id','desno')
    .click(divDesnoGumbKlik)
    .css({
      fontSize: '200%',
      position: 'absolute',
      bottom: '5%',
      right: '5%'
    });
  var divOpisParagraf = $('<p>')
    .css({
      textAlign: 'center',
      horizontalAlign: 'middle'
    });
  noviDiv.append(divDesnoGumb).append(divIzlazGumb).append(divLijevoGumb).append(divOpisParagraf);
  $('#izlaz').click(function(){
    noviDiv.hide();
  });
  $('body').append(noviDiv);
}



$(document).ready(function()
{
  var divSvi = $('div.gallery');
  $('div.gallery img').hide();
  $('div.gallery p').hide();
  var sveSlike = [];
  for (var i=0; i<divSvi.length; ++i){
    var divSadrzaj = divSvi.eq(i);
    var slike = [];
    var divSlike = divSadrzaj.children('img');
    var divParagrafi = divSadrzaj.children('p');
    for (var j=0; j<divSlike.length; ++j){
      var src = divSlike.eq(j).attr('src');
      var par = $( 'p[data-target="' + src + '"]' ).html();
      slike.push({
        img: src,
        opis: par
      })
    }
    var naslov = divSadrzaj.attr('title');
    sveSlike.push({
      naslov: naslov,
      slike: slike
    });
    var galerijaNaslov = $('<p>').text(naslov);
    var galerijaGumb = $('<button/>')
    .text('Pogledaj galeriju!')
    .on('click', function(){onGalleryButtonClick(slike)});
    divSadrzaj.append(galerijaNaslov).append(galerijaGumb);
  }
});
