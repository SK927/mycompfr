function searchOrders()
{
  let checked = [];
  let term = $( '#search' ).val().toLowerCase();

  document.querySelectorAll( '.search-checkbox' ).forEach( chkbox => checked.push( chkbox.checked ) ); // Store each checkbox status in array

  let paid = checked.pop();

  document.querySelectorAll( '.placed-order' ).forEach( function( order, i ) 
  { 
    let state = true;

    checked.forEach( function( elem, index )
    {
      if ( checked[ index ] )
      {
        let block =  order.querySelector( '#b' + index );

        if ( block )
        {
          let btn = block.querySelector( '.given' ); 
          state &= btn.classList.contains( 'btn-outline-danger' ); // If block is not given or if block checkbox is checked
        }
        else
        {
          state &= false;
        }

      }
    } );

    if ( paid )
    {
      state &= order.querySelector( '.order-is-paid' ).classList.contains( 'btn-outline-danger' ); // If order is not paid or non payed checkbox is checked
    }
    
    state &= ( $( '.user-name:eq( ' + i + ' )' ).text().toLowerCase().indexOf( term ) >= 0 ); // If user name matches the search field term
    
    if ( ! state )
    {
      order.style.display = 'none';
    }
    else
    {
      order.style.display = 'inline-block';
    }
  } );
} 

//-------------------------------------

function showComment( parent, type = "user", comment = null )
{
  let template = document.getElementById( type );
  let clone = template.content.cloneNode( true );  

  if ( comment )
  {
    clone.querySelector( '.form-control' ).innerHTML = comment;
  }

  parent.append( clone );
}

//-------------------------------------

$( document ).on( 'click', '.search-checkbox', function( e ){
  searchOrders();
} ); 

//-------------------------------------

$( document ).on( 'keyup', '#search', function( e ){
  searchOrders();
} ); 

//-------------------------------------

function createAmount( block, amount, name ){
  let template = document.getElementById( 'amount-product' );
  let clone = template.content.cloneNode( true );
  clone.querySelector( '.amount' ).innerHTML = amount;  
  clone.querySelector( '.item-name' ).innerHTML = name;  
  document.getElementById( block ).appendChild( clone );
} 

//-------------------------------------

function calculateOrdersTotal()
{
  try
  {
    let total = 0;

    // Parse every order total amounts of the page as float and add to the total 
    document.querySelectorAll( '.order-amount' ).forEach( function( elem, i ){ 
      total += parseFloat( elem.innerHTML.match( /\d+.\d+/ ) ); 
    } );
    
    document.getElementById( 'total-amount' ).innerHTML = '( ' + total.toFixed( 2 ).toString() + '&nbsp;€ )'; ;
  }
  catch{}
} 

//-------------------------------------

$( document ).on( 'click', '.delete-order', function( e )
{
  if( confirm( 'Confirmer la suppression ?' ) )
  { 
    setStatusBar();

    let order = $( this ).closest( '.placed-order' );
    let containerId = order.attr( 'id' );
    let orderInfo = containerId.split( '_' );
    
    $.ajax( {
      type: 'POST',
      url: 'src/admin_ajax-delete-order.php',
      data: { competition_id: encodeURI( orderInfo[ 0 ] ), order_id: orderInfo[ 1 ] },
      success: function( response )
      { 
        let result = JSON.parse( response );

        if ( ! result.error ) 
        {
          order.remove();

          calculateOrdersTotal();
          
          $.ajax( {
            type: 'POST',
            url: 'src/admin_ajax-update-items-amount.php',
            data: { competition_id: encodeURI( orderInfo[0] ) },
            success: function( response )
            { 
              let array = JSON.parse( response );

              document.querySelectorAll( '.item-qty > h5' ).forEach( function( elem, i )
              { 
                let [ blockName, itemName ] = elem.id.split( '_' );

                if ( array != null && typeof array[ blockName ] != 'undefined' && typeof array[ blockName ][ itemName ] != 'undefined' )
                {
                  document.getElementById( blockName + '_' + itemName ).innerHTML = array[ blockName ][ itemName ];
                }
                else
                {
                  elem.closest( '.col-6' ).remove();
                }
              } );
            },
            error: function( xhr, status, error ) 
            {
              setStatusBar( xhr, error );
            }
          } );
          setStatusBar( result.text_to_display, result.error );
        }     
      },
      error: function( xhr, status, error ) 
      {
        setStatusBar( xhr, error );
      }
    } );
  }
} ); 

//-------------------------------------

$( document ).on( 'click', '.order-is-paid', function( e )
{
  setStatusBar();

  let button = $( this );
  let containerId = button.closest( '.placed-order' ).attr( 'id' );
  let orderInfo = containerId.split( '_' );
  
  $.ajax( {
    type: 'POST',
    url: 'src/admin_ajax-toggle-paid.php',
    data: { competition_id: encodeURI( orderInfo[ 0 ] ), order_id: orderInfo[ 1 ] },
    success: function( response )
    { 
      let result = JSON.parse( response );
      setStatusBar( result.text_to_display, result.error );

      if ( ! result.error ) 
      {
        button.toggleClass( 'btn-outline-success btn-outline-danger' ); 
      }
    },
    error: function( xhr, status, error ) 
    {
      setStatusBar( xhr, error );
    }
  } );
} ); 

//-------------------------------------

$( document ).on( 'click', '.given', function( e )
{
  setStatusBar();

  let button = $( this );
  let containerId = button.closest( '.placed-order' ).attr( 'id' );
  let orderInfo = containerId.split( '_' );
  let block = button.closest( '.block' );
  let blockId = block.attr( 'id' );
  
  $.ajax( {
    type: 'POST',
    url: 'src/admin_ajax-toggle-given.php',
    data: { competition_id: encodeURI( orderInfo[ 0 ] ), order_id: orderInfo[ 1 ], block_id: blockId },
    success: function( response )
    { 
      let result = JSON.parse( response );
      setStatusBar( result.text_to_display, result.error );

      if ( ! result.error ) 
      {
        button.toggleClass( 'btn-outline-success btn-outline-danger' ); 

        if ( button.attr( 'class' ).split( /\s+/ ).includes( 'btn-outline-success' ) )
        {
          block.addClass( 'strike' );
        }
        else
        {
          block.removeClass( 'strike' );
        }

        searchOrders();
      }
    },
    error: function( xhr, status, error ) 
    {
      setStatusBar( xhr, error );
    }
  } );
} ); 

//-------------------------------------

$( document ).on( 'click', '.update-list', function( e )
{
  setStatusBar();
  let competition_id = $( '.competition-name' ).attr( 'id' );

  $.ajax( {
    type: 'POST',
    url: 'src/admin_ajax-update-competitors-from-wcif.php',
    data: { competition_id: encodeURI( competition_id ) },
    success: function( response )
    {
      let result = JSON.parse( response );
      setStatusBar( result.text_to_display, result.error );
    },
    error: function( xhr, status, error ) 
    {
      setStatusBar( xhr, error );
    }
  } );
} ); 

//-------------------------------------

$( document ).on( 'submit', '#competition-info', function( e )
{
  e.preventDefault();
  
  let inputs = document.getElementsByClassName( 'is-invalid' );
  
  for ( let input of inputs )
  {
    input.classList.remove( 'is-invalid' );
  }

  setStatusBar();

  let target = $( this );

  $.ajax( {
    type: 'POST',
    url: target.attr( 'action' ),
    data: target.serialize(),
    success: function( response )
    { 
      let result = JSON.parse( response );    

      if ( result.error_email )
      {
        $( '#competition-contact-email' ).addClass( 'is-invalid' );
      }

      if ( result.error_date )
      {
        $( '#competition-orders-closing-date' ).addClass( 'is-invalid' );
      }

      error = result.error_email || result.error_date || result.error_mysqli;
      setStatusBar( result.text_to_display, error )
    },
    error: function( xhr, status, error ) 
    {
      setStatusBar( xhr, error );
    }
  } );
} ); 

//-------------------------------------

$( document ).on( 'click', '.add-comment', function( e )
{
  e.preventDefault();
  let parent = $( this ).parent();
  showComment( parent );
  this.remove();
} ); 

//-------------------------------------

$( document ).on( 'click', '.add-admin-comment', function( e )
{
  e.preventDefault();
  let parent = $( this ).parent();
  showComment( parent, 'admin' );
  this.remove();
} );

//-------------------------------------

$( document ).on( 'keyup keypress', '.admin-comment', function( e )
{
  this.classList.add( 'changed-comment' );
} ); 

//-------------------------------------

$( document ).on( 'click', '.submit-admin-comment', function( e )
{
  setStatusBar();

  let button = $( this );
  let containerId = button.closest( '.placed-order' ).attr( 'id' );
  let orderInfo = containerId.split( '_' );
  let commentArea = document.getElementById( containerId ).querySelector('#comment-admin-area' + orderInfo[1] + ' > .admin-comment');
  let comment = commentArea.value;
  
  $.ajax( {
    type: 'POST',
    url: 'src/admin_ajax-admin-comment-on-order.php',
    data: { competition_id: encodeURI( orderInfo[ 0 ] ), order_id: orderInfo[ 1 ], comment: comment },
    success: function( response )
    { 
      let result = JSON.parse( response );
      commentArea.classList.remove( 'changed-comment' );
      setStatusBar( result.text_to_display, result.error );
    },
    error: function( xhr, status, error ) 
    {
      setStatusBar( xhr, error );
    }
  } );
} ); 

//-------------------------------------    

$( document ).on( 'click', '.get-emails-list', function( e )
{
  setStatusBar();
  let competition_id = $( '.competition-name' ).attr( 'id' );

  $.ajax( {
    type: 'POST',
    url: 'src/admin_ajax-copy-emails.php',
    data: { competition_id: encodeURI( competition_id ) },
    success: function( response )
    {
      let result = JSON.parse( response );
      navigator.clipboard.writeText( result.data );
      setStatusBar( result.text_to_display, result.error );
    },
    error: function( xhr, status, error ) 
    {
      setStatusBar( xhr, error );
    }
  } );
} );
