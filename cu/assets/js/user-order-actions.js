function showAdd( id )
{
  let template = document.getElementById( 'add-item' );
  let clone = template.content.cloneNode( true );  
  document.getElementById( id ).append( clone );
  document.getElementById( id ).classList.remove( 'added-item' );
} 

//-------------------------------------

function showQuantity( id, qty = 1 )
{
  let template = document.getElementById( 'increment-item-quantity' );
  let clone = template.content.cloneNode( true );  
  clone.querySelector( '.item-quantity' ).name = id;
  clone.querySelector( '.item-quantity' ).id = id + '_qty';
  clone.querySelector( '.item-quantity' ).value = qty;
  document.getElementById( id ).append( clone );
  document.getElementById( id ).classList.add( 'added-item' );
} 

//-------------------------------------

function showComment( parent, comment = null )
{
  let template = document.getElementById( 'comment-textarea' );
  let clone = template.content.cloneNode( true );  

  if ( comment )
  {
    clone.querySelector( '.form-control' ).innerHTML = comment;
  }

  parent.append( clone );
} 

//-------------------------------------

function updateItemsQuantity()
{
  document.querySelectorAll( '.catalog-item' ).forEach( elem => {
    if ( sessionStorage.getItem( elem.id ) )
    {
      $( elem ).find( '.add-item' ).remove();
      showQuantity( elem.id, sessionStorage.getItem( elem.id ) );
    } 
  } );

  updateOrderSum();

  sessionStorage.clear();
} 

//-------------------------------------

function updateOrderSum()
{
  let optionsToSelect = false;
  let regexp = /\d+\.?\d*/;
  let sum = 0.00;
  let itemsQuantity = document.querySelectorAll( '.item-quantity' );

  itemsQuantity.forEach( elem => {
    let item = elem.closest( '.catalog-item' );
    let price = item.querySelector( '.item-price' ).innerHTML;

    if ( item.querySelector( '.item-includes' ) )
    {
      optionsToSelect = optionsToSelect || true;
    }

    sum += parseFloat( elem.value * price.match( regexp )[ 0 ] ); // Add product of current item by quantity ordered to order sum 
  } );

  document.getElementById( 'amount' ).innerHTML = sum.toFixed( 2 ).toString();
  
  updateConfirmButton( optionsToSelect );
} 

//-------------------------------------

function updateConfirmButton( hasOptionsToSelect )
{
  let button = document.getElementById( 'confirm-button' );
  let form = document.getElementById( 'order-form' );
  
  if ( hasOptionsToSelect ) 
  {
    button.innerHTML = 'Sélectionner mes options';
    form.action = form.action.replace( 'user-confirm-order', 'user-set-options' );
  }
  else
  {
    button.innerHTML = 'Confirmer la commande';
    form.action = form.action.replace( 'user-set-options', 'user-confirm-order' );
  }
}

//-------------------------------------

$( document ).on( 'click', '.add-item', function( e )
{
  e.preventDefault();

  let id = this.closest( '.catalog-item' ).id;
  
  this.remove();

  showQuantity( id );
  
  updateOrderSum();
} ); 

//-------------------------------------

$( document ).on( 'click', '.decrement', function( e )
{
  e.preventDefault();
  
  let id = this.closest( '.catalog-item' ).id;
  let footer = $( this ).parent();
  let quantity = parseInt( document.getElementById( id + '_qty' ).value );

  if ( quantity == 1 )
  {
    footer.remove();
    showAdd( id );
  }
  else
  {
    document.getElementById( id + '_qty' ).value = quantity - 1;
  }

  updateOrderSum();
} ); 

//-------------------------------------

$( document ).on( 'click', '.increment', function( e )
{
  e.preventDefault();
  
  let item = $( this ).closest( '.catalog-item' );
  let itemId = item.attr( 'id' );
  let quantity = parseInt( document.getElementById( itemId + '_qty' ).value );

  document.getElementById( itemId + '_qty' ).value = quantity + 1;

  updateOrderSum();
} ); 

//-------------------------------------

$( document ).on( 'click', '.add-comment', function( e )
{
  e.preventDefault();
  
  let parent = $( this ).parent();
  showComment( parent );
} );

//-------------------------------------

$( document ).on( 'click', '#delete-button', function( e )
{
  if ( ! confirm( 'Confirmer la suppression?' ) )
  {
    e.preventDefault();
  }
  else
  {
    let form = document.getElementById( 'order-form' );
    
    form.action = form.action.replace( 'user-set-options', 'user-confirm-order' ) + '&delete=true';
    form.submit();
  }
} );