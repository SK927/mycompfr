const interval = window.setInterval( checkDisplay, 10000 );

$( 'document' ).ready( function(){
  let live = document.getElementById( 'live' );
  let top = live.getBoundingClientRect().top;
  live.style.minHeight = (window.innerHeight - top - 20) + "px";
  live.style.maxHeight = (window.innerHeight - top - 10) + "px";

});

function checkDisplay() 
{
  let current = document.getElementById( 'td-current' );
  let next = document.getElementById( 'td-next' );
  let live = document.getElementById( 'live' );
  let id = findGetParameter( 'id' );

  const now = new Date();
  const hours = now.getHours().toString().padStart(2, '0');
  const minutes = now.getMinutes().toString().padStart(2, '0');
  document.getElementById('td-time').textContent = `${hours}:${minutes}`;

  $.ajax( {
    type: 'POST',
    url: 'src/viewer_ajax-get-current.php',
    data:{ id: encodeURI( id ) },
    success: function( response )
    { 
      let result = JSON.parse( response );

      if ( current.innerHTML != result.text_current )
      {
        current.innerHTML = result.text_current;
      }

      if ( next.innerHTML != result.text_next )
      {
        next.innerHTML = result.text_next;
      }     

      if ( live.getAttribute('src') != result.src_live )
      {
        live.setAttribute('src', result.src_live );
      }     
    },
    error: async function( xhr, status, error ) 
    {

    } 
  } );
}

function findGetParameter(parameterName) 
{
  var result = null;
  var tmp = [];
  
  location.search.substr( 1 ).split( '&' ).forEach( function( item ) 
  {
    tmp = item.split( '=' );
    if ( tmp[0] === parameterName ) result = decodeURIComponent( tmp[1] );
  });
  
  return result;
}

