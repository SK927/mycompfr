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

// ================================================================ //

$( document ).on( 'click', '#add-note', function( e, note = '' )
{
  e.preventDefault();
  let parent = $( this ).parent();
  showNote( parent, note );
  this.remove();
} );

// ================================================================ //

function showNote( parent, note = '' )
{
  let textarea = document.createElement( 'textarea' );
  textarea.classList.add( 'form-control' );
  textarea.placeholder = 'Je mets ici les informations relatives aux commandes. Ces informations seront affichées sur la page de commande et dans les e-mails de confirmation.';
  textarea.name = 'admin_note';
  textarea.innerHTML = note;

  parent.append( textarea );
} 

// ================================================================ //

function searchOrders()
{
  let checked = [];
  let term = $( '#search' ).val().toLowerCase();

  document.querySelectorAll( '.search-checkbox' ).forEach( chkbox => checked.push( chkbox.checked ) ); // Store each checkbox status in array

  let paid = checked.pop();

  document.querySelectorAll( '.placed-order' ).forEach( function( order, i ) 
  { 
    let id = order.id;
    let state = true;

    checked.forEach( function( elem, index )
    {
      if ( checked[ index ] )
      {
        let btn =  order.querySelector( '.b' + index + '-given' );

        if ( btn )
        {
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

// ================================================================ //


$( document ).on( 'keyup', '#search', function( e ){
  searchOrders();
} ); 

// ================================================================ //


$( document ).on( 'click', '.search-checkbox', function( e ){
  searchOrders();
} ); 

// ================================================================ //

$( document ).on( 'click', '.get-emails-list', function( e )
{
  setStatusBar();

  const queryString = window.location.search;
  const urlParams = new URLSearchParams( queryString );
  const id = urlParams.get( 'id' );

  $.ajax( {
    type: 'POST',
    url: 'src/ajax_copy-emails.php',
    data: { id: encodeURI( id ) },
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

// ================================================================ //


$( document ).on( 'click', '.update-list', function( e )
{
  setStatusBar();
  const queryString = window.location.search;
  const urlParams = new URLSearchParams( queryString );
  const id = urlParams.get( 'id' );

  $.ajax( {
    type: 'POST',
    url: 'src/ajax_update-competitors-from-wcif.php',
    data: { id: encodeURI( id ) },
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

// ================================================================ //

$( document ).on( 'click', '#add-comment', function( e, comment = '' )
{
  e.preventDefault();
  let parent = $( this ).closest( 'table' );
  let parentId = parent.attr( 'id' );
  $( '#' + parentId + ' tr:last' ).remove();
  showComment( parent, comment );
} );

// ================================================================ //

function showComment( parent, comment = '' )
{
  let id = parent.closest( '.placed-order' ).attr( 'id' );
  
  let tr = document.createElement( 'tr' );
  tr.classList.add( 'table-danger' )

  let td1 = document.createElement( 'td' );
  td1.classList.add( 'border-0' );
  td1.colSpan = 2;
  let textarea = document.createElement( 'textarea' );
  textarea.id = id + '-admin-comment';
  textarea.classList.add( 'form-control', 'p-0', 'border-0', 'text-end', 'bg-transparent' );
  textarea.placeholder = 'Je mets mon commentaire ici, si besoin.';
  textarea.name = 'admin_comment';
  textarea.innerHTML = comment;

  let btn = document.createElement( 'button' );
  btn.classList.add( 'save-comment', 'btn', 'btn-sm', 'btn-outline-danger', 'float-end', 'mt-2' );
  btn.innerHTML = '&#10003;';
  td1.append( textarea );
  td1.append( btn );
  tr.append( td1 );

  parent.append( tr );
} 

// ================================================================ //

$( document ).on( 'click', '.save-comment', function( e )
{
  setStatusBar();

  const queryString = window.location.search;
  const urlParams = new URLSearchParams( queryString );
  const id = urlParams.get( 'id' );

  let button = $( this );
  let orderId = button.closest( '.placed-order' ).attr( 'id' );
  let comment = document.getElementById( orderId + '-admin-comment' ).value;

  $.ajax( {
    type: 'POST',
    url: 'src/ajax_save-admin-comment.php',
    data: { id: encodeURI( id ), order_id: orderId, comment: comment },
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

// ================================================================ //

$( document ).on( 'click', '.given', function( e )
{
  setStatusBar();

  const queryString = window.location.search;
  const urlParams = new URLSearchParams( queryString );
  const id = urlParams.get( 'id' );

  let button = $( this );
  let containerId = button.closest( '.col-12' ).attr( 'id' );
  let blockInfo = containerId.split( '_' );
  
  $.ajax( {
    type: 'POST',
    url: 'src/ajax_toggle-given.php',
    data: { id: encodeURI( id ), order_id: blockInfo[ 0 ], block_id: blockInfo[ 1 ] },
    success: function( response )
    { 
      let result = JSON.parse( response );
      setStatusBar( result.text_to_display, result.error );

      if ( ! result.error ) 
      {
        button.toggleClass( 'btn-outline-success btn-outline-danger' ); 
        searchOrders();
      }
    },
    error: function( xhr, status, error ) 
    {
      setStatusBar( xhr, error );
    }
  } );
} ); 


// ================================================================ //

$( document ).on( 'click', '.order-is-paid', function( e )
{
  setStatusBar();

  const queryString = window.location.search;
  const urlParams = new URLSearchParams( queryString );
  const id = urlParams.get( 'id' );

  let button = $( this );
  let orderId = button.closest( '.placed-order' ).attr( 'id' );

  $.ajax( {
    type: 'POST',
    url: 'src/ajax_toggle-paid.php',
    data: { id: encodeURI( id ), order_id: orderId },
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

// ================================================================ //


$( document ).on( 'click', '.delete-order', function( e )
{
  if( confirm( 'Confirmer la suppression ?' ) )
  { 
    setStatusBar();

    const queryString = window.location.search;
    const urlParams = new URLSearchParams( queryString );
    const id = urlParams.get( 'id' );

    let button = $( this );
    let order = button.closest( '.placed-order' );
    let orderId = order.attr( 'id' );
    
    $.ajax( {
      type: 'POST',
      url: 'src/ajax_delete-order.php',
      data: { id: encodeURI( id ), order_id: orderId },
      success: function( response )
      { 
        let result = JSON.parse( response );

        setStatusBar( result.text_to_display, result.error );

        if ( ! result.error ) 
        {
          order.remove();
          setTimeout(function(){
            window.location.reload();
          }, 1000);
        }     
      },
      error: function( xhr, status, error ) 
      {
        setStatusBar( xhr, error );
      }
    } );
  }
} ); 