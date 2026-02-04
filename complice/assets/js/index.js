$( document ).on( 'click', '.btn', function( e )
{
  e.preventDefault();
  
  competitionId = document.getElementById( 'competition-select' ).value;

  if ( this.innerHTML == 'PDF' )
  {
    window.open( 'src/pdf_generate-list.php?id=' + competitionId );
  }
  else if ( this.innerHTML == 'CSV' )
  {
    window.open( 'src/csv_generate-list.php?id=' + competitionId );
  }
} );

