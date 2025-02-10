$( document ).on( 'click', '.button-back', function( e )
{
  e.preventDefault();

  sessionStorage.setItem( 'isBack', true );
   
  history.back();
} );

//-------------------------------------

function setNewAmount()
{
  let curr = parseFloat( sessionStorage.getItem( 'amount' ) );
  let regexp = /\(\+(\d*\.?\d+)€\)/;
  let sum = 0;
  let value = 0;

  document.querySelectorAll( '.form-select' ).forEach( function( elem )
  {
    let text = elem.options[ elem.selectedIndex ].text;
    value = text.match( regexp );
    if( value ) sum += parseFloat( value[1] );
  });

  document.getElementById( 'amount' ).innerHTML = (curr + sum).toFixed(2).toString();
}

//-------------------------------------

$( document ).on( 'change', '.form-select', function( e )
{
  setNewAmount();
} );