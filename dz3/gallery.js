var index = 0;

function divIzlazGumbKlik(){
  $('#gallery').remove();
}

function postaviSliku(slika, ukupno){
  var trenutniDiv = $('#gallery');
  $('#slika').remove();
  trenutniDiv.children('p').remove();
  var ratio = slika.nw/slika.nh;
  var divKontejner = $('<div>')
          .attr('id', 'slika')
		  .css({
			margin: 'auto',
			marginTop: '1%',
			width: '90%',
			height: '87%'});
  var divSlika = $('<img>')
		  .attr('src', slika.img)
		  .css({
			maxWidth: '100%',
			maxHeight: '100%'
		  });
  if (ratio>1) divSlika.css('height', '100%')
  else divSlika.css('width', '100%');
  var divOpis1Paragraf = $('<p>')
    .text(slika.opis)
    .css({
      padding: '0',
      margin: '0'
    });
  var divOpis2Paragraf = $('<p>')
    .text("Slika " + (slika.indeks+1) + "/" + ukupno + ".")
    .css({
      padding: '0',
      margin: '0'
    });
  if ((index+1)>=ukupno) trenutniDiv.find('#desno').css({backgroundColor: 'white'})
    else trenutniDiv.find('#desno').css({backgroundColor: 'green'});
  if ((index-1)<0) trenutniDiv.find('#lijevo').css({backgroundColor: 'white'})
    else trenutniDiv.find('#lijevo').css({backgroundColor: 'green'});
  divKontejner.append(divSlika);
  trenutniDiv.append(divKontejner).append(divOpis1Paragraf).append(divOpis2Paragraf);
}

function otvoriGaleriju(slike){
  index = 0;
  if ($('#gallery')!==undefined) $('#gallery').remove();
  var noviDiv = $('<div>');
  noviDiv.attr('id','gallery');
  noviDiv.css({
      textAlign: 'center',
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
    .css({
      backgroundColor: 'red',
      fontSize: '200%',
      position: 'absolute',
      top: '0%',
      right: '0%'
    })
    .click(() => divIzlazGumbKlik());
  var divLijevoGumb = $('<button/>')
    .text('<<')
    .attr('id','lijevo')
    .css({
      fontSize: '200%',
      position: 'absolute',
      bottom: '5%',
      left: '5%'
    })
    .click(
      (function (slike) {
        return function () {
          if (index-1>=0) postaviSliku(slike[--index], slike.length);
        };
      })(slike)
    );
  var divDesnoGumb = $('<button/>')
    .text('>>')
    .attr('id','desno')
    .css({
      fontSize: '200%',
      position: 'absolute',
      bottom: '5%',
      right: '5%'
    })
    .click(
      (function (slike) {
        return function () {
          if (index+1<slike.length) postaviSliku(slike[++index], slike.length);
        };
      })(slike)
    );
  noviDiv.append(divDesnoGumb).append(divLijevoGumb).append(divIzlazGumb);
  $('body').append(noviDiv);
  postaviSliku(slike[index], slike.length);
}

$(document).ready(function()
{
  var divSvi = $('div.gallery');
  $('div.gallery img').hide();
  $('div.gallery p').hide();
  for (var i=0; i<divSvi.length; ++i){
    var divSadrzaj = divSvi.eq(i);
    var slike = [];
    var divSlike = divSadrzaj.children('img');
    var divParagrafi = divSadrzaj.children('p');
    var k = 0;
    for (var j=0; j<divSlike.length; ++j){
      var src = divSlike.eq(j).attr('src');
      var par = divSadrzaj.children('p[data-target="' + src + '"]').html();
	  var nh = divSlike.eq(j).prop('naturalHeight');
	  var nw = divSlike.eq(j).prop('naturalWidth');
      if (src && par)
        slike.push({
          img: src,
          opis: par,
		  nw: nw,
		  nh: nh,
          indeks: k++
        })
    }
    var naslov = divSadrzaj.attr('title');
    var galerijaNaslov = $('<p>').text(naslov);
    var galerijaGumb = $('<button/>')
      .text('Pogledaj galeriju!')
      .click(
        (function (slike) {
          return function () {
            otvoriGaleriju(slike);
          };
        })(slike)
      );
    divSadrzaj.append(galerijaNaslov).append(galerijaGumb);
  }
});
