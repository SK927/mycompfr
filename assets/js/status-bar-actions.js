var timeout;

function setStatusBar( text = null, error = null )
{
  clearTimeout( timeout );

  let bar = $( '#status-bar' );
  let container = bar.parent().parent();

  container.hide;
  bar.find( 'small' ).text( '' );

  if ( text )
  {
    bar.removeClass( 'alert-warning' );
    bar.find( 'strong' ).text( text );

    if ( error )
    {
      bar.addClass( 'alert-danger' );
      bar.find( 'small' ).text( ' ( err: ' + error + ' )' );
    } 
    else 
    {
      bar.addClass( 'alert-success' );
    }
  }
  else
  {
    bar.removeClass( 'alert-danger alert-success' );
    bar.addClass( 'alert-warning' );
    bar.find( 'strong' ).text( '\u231b' );
  }  

  container.show();
  timeout = setTimeout( function(){ container.fadeOut( 500 ); }, 3000 );
} 

//-------------------------------------

$( document ).on( 'click', '.close', function( e ){
  $( '#status-bar' ).parent().parent().hide();
} );