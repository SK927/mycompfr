$( document ).on( 'click', '.no-default', function( e )
{
  e.preventDefault();
} );

$( document ).on( 'click', '.firsttimer', function( e )
{
  var isFalseFirstTimer = this.classList.contains( 'false' ) ? true : false;
  var td = this.closest('td');
  this.classList.toggle('firsttimer');
  this.classList.toggle('pending');
  this.innerHTML = 'Requête en cours...';


  setTimeout(() => {
    this.classList.toggle('pending');
    this.classList.toggle('checked');

    if( ! isFalseFirstTimer )
    {
      this.innerHTML = 'Nouveau compétiteur';
      td.closest( 'tr' ).classList.remove('table-warning');
      td.closest( 'tr' ).classList.add('table-success');
    }
    else
    {
      this.innerHTML = 'Détecté comme 2025XXXX01';
      td.innerHTML += "<p class=\"mt-2 mb-0\"><i>C'est très rare, mais il arrive que certaines personnes soient détectés comme ayant déjà effectué des compétitions. Dans ce cas, le plus simple est de demander à votre Délégué.e ce qu'il convient de faire. Dans le cadre de cet entraînement, on considèrera qu'il s'agit d'un faux positif. Cette personne effectue donc bien sa première compétition.</i></p>"
    }
  }, 500);
} ); 

$( function () {
  $('[data-toggle="tooltip"]').tooltip();
} )