function getRandomInt() {
  return '--' + Math.floor( Math.random() * 1000000000000 );
} 

//-------------------------------------

function createBlock( id, name = '' )
{
  let template = document.getElementById( 'block' );
  let clone = template.content.cloneNode( true );
  clone.querySelector( '.block' ).id = 'block' + id;  
  clone.querySelector( '.block-name' ).id = 'block-name' + id;  
  clone.querySelector( '.block-name' ).name = 'block_name' + id;  
  clone.querySelector( '.block-name' ).value = name;  
  clone.querySelector( '.add-item' ).name = 'add_item' + id;
  clone.querySelector( '.delete-block' ).name = 'delete_block' + id;  
  clone.querySelector( '.clone-block' ).id = 'clone-block' + id; 
  clone.querySelector( '.item-list' ).id = 'item-list' + id; 
  document.getElementById('block-list' ).appendChild( clone );
}

//-------------------------------------

function createItem( block, id, name = '', price = 0, description = '', img = '.' )
{
  let template = document.getElementById( 'item' );
  let clone = template.content.cloneNode( true );
  clone.querySelector( '.row' ).id = 'row' + id;  
  clone.querySelector( '.item' ).id = 'item' + id;  
  clone.querySelector( '.delete-item' ).name = 'delete_item' + id;  
  clone.querySelector( '.item-id' ).name = 'item_id' + id;  
  clone.querySelector( '.item-id' ).value = id;   
  clone.querySelector( '.item-name' ).name = 'item_name' + id;  
  clone.querySelector( '.item-name' ).value = name;   
  clone.querySelector( '.item-price' ).name = 'item_price' + id;  
  clone.querySelector( '.item-price' ).value = parseFloat( price ).toFixed( 2 ); 
  clone.querySelector( '.item-description' ).name = 'item_descr' + id;  
  clone.querySelector( '.item-description' ).value = description;  
  clone.querySelector( '.item-image' ).id = 'item-image' + id; 
  clone.querySelector( '.item-image' ).name = 'item_image' + id; 
  clone.querySelector( '.item-image' ).value = img; 
  document.getElementById( block ).appendChild( clone );
}

//-------------------------------------

function createOption( item, id, name = '', options = '' )
{
  let template = document.getElementById( 'option' );
  let clone = template.content.cloneNode( true );
  clone.querySelector( '.item-option-name' ).name = 'option_name'+ id;  
  clone.querySelector( '.item-option-name' ).value = name;  
  clone.querySelector( '.item-option-value' ).name = 'option_value'+ id; 
  clone.querySelector( '.item-option-value' ).value = options; 
  document.getElementById( item ).appendChild( clone );
} 

//-------------------------------------    

function updateList( array )
{
  document.getElementById( 'block-list').innerHTML = '';
  
  $.each( array, function( block_key, block_value )
  {
    createBlock( block_key, block_key );

    $.each( block_value, function( item_key, item_value )
    {
      createItem( 'item-list' + block_key, item_key, item_value['item_name'], item_value['item_price'], item_value['item_descr'], item_value['item_image'] );
      
      if ( item_value['options'] != null )
      {
        $.each( item_value['options'], function( option_key, option_value )
        {
          createOption( 'row' + item_key, getRandomInt(), option_key, option_value );
        });
      }
    });
  });
}

//-------------------------------------

$( document ).on( 'click', '.add-block', function()
{
  let id = getRandomInt();
  createBlock( id );
  document.getElementById( 'block-name' + id ).focus();
});

//-------------------------------------

$( document ).on( 'click', '.add-item', function( e )
{
  e.preventDefault();
  let blockId = $( this ).closest( '.block' ).find( '.item-list' ).attr( 'id' );
  let itemId = getRandomInt();
  createItem( blockId, itemId );
  document.getElementsByName( 'item_name' + itemId )[ 0 ].focus();
});

//-------------------------------------

$( document ).on( 'click', '.add-option', function( e )
{
  let itemId = $( this ).closest( '.row' ).attr( 'id' );
  let optionId = getRandomInt();
  createOption( itemId, optionId );
  document.getElementsByName( 'option_name' + optionId )[ 0 ].focus();
});

//-------------------------------------

$( document ).on( 'click', '.delete-block', function( e )
{
  if ( confirm( 'Confirmer la suppression?' ) ) e.preventDefault();
  {
    $( this ).closest( '.block' ).remove();
  }
});  

//-------------------------------------

$( document ).on('click', '.delete-item', function(e)
{
  if ( ! confirm( 'Confirmer la suppression?' ) ) e.preventDefault();
  {
    $( this ).closest( '.item' ).remove();
  }
});

//-------------------------------------

$( document ).on( 'click', '.delete-option', function( e )
{
  if ( confirm( 'Confirmer la suppression?' ) ) e.preventDefault();
  {
    $( this ).closest( '.option' ).remove();
  }
});

//-------------------------------------

$( document ).on( 'click', '.clone-block', function( e )
{
  e.preventDefault();

  let clonedBlockId = $( this ).closest( '.block' ).attr( 'id' );
  let clone = document.getElementById( clonedBlockId ).cloneNode( true );
  let ref = [];
  let opt = [];
  let newBlockId = getRandomInt();

  clone.id = 'block' + newBlockId;
  clone.querySelectorAll( '.block-name' ).forEach( function( elem, i ){ elem.id = 'block-name' + newBlockId; elem.name = 'block_name' + newBlockId; elem.value += newBlockId; } );
  clone.querySelectorAll( '.item' ).forEach( function( elem, i ){ ref.push( getRandomInt() ); elem.id = 'item' + ref[ i ]; } );
  clone.querySelectorAll( '.item-name' ).forEach( function( elem, i ){ elem.name = 'item_name' + ref[ i ]; }) ;
  clone.querySelectorAll( '.item-price' ).forEach( function( elem, i ){ elem.name = 'item_price' + ref[ i ]; }) ;
  clone.querySelectorAll( '.item-id' ).forEach( function( elem, i ){ elem.name = 'item_id' + ref[ i ]; elem.value = '--' + ref[ i ]; } );
  clone.querySelectorAll( '.item-description' ).forEach( function( elem, i ){ elem.name = 'item_descr' + ref[ i ]; } );
  clone.querySelectorAll( '.item-option-name' ).forEach( function( elem, i ){ opt.push( getRandomInt() ); elem.name = 'option_name' + opt[ i ]; } );
  clone.querySelectorAll( '.item-option-value' ).forEach( function( elem, i ){ elem.name = 'option_value' + opt[ i ]; } );
  clone.querySelectorAll( '.item-image' ).forEach( function( elem, i )
  {
    elem.value = document.getElementById( elem.id ).options[document.getElementById( elem.id ).selectedIndex].value;
    elem.name = 'item_image' + ref[i];
    elem.id = 'item-image' + ref[i];
  });
  document.getElementById( 'block-list' ).appendChild( clone );
  document.getElementById( 'block-name' + newBlockId ).focus();
});

//-------------------------------------

$( document ).on( 'keyup', '.item-price', function()
{
  let input = $( this ).val().toString().replace( /,|\./i, '' ); // Get value after replacing . by empty string
  let number = 0;

  if ( input )
  {
    let numberPattern = /\d+/g; // Get only the numeric values out of the string
    number = input.match( numberPattern ).join( '' ); 
  }
  
  $( this ).val( String( (number/ 100).toFixed( 2 ) ) ); // Format new numeric value
}); 

//-------------------------------------

$( document ).on( 'keydown', 'form', function( e)
{ 
  return e.key != 'Enter'; // Prevent user from submitting form when pressing the Enter key
});

//-------------------------------------
        
$( document ).on( 'submit', '#form-csv', function( e )
{
  e.preventDefault();
  let target = $( this );
  let data = new FormData();
  data.append( 'file', $( '#file-input' )[ 0 ].files[ 0 ]);
          
  setStatusBar();

  $.ajax( {
    type: 'POST',
    url: target.attr( 'action' ),
    data: data,
    processData: false,
    contentType: false,
    cache: false,
    success: function( response )
    {  
      let result = JSON.parse( response );
     
      if ( ! result.error )
      {
        document.getElementById( 'file-input' ).value = '';
        updateList( JSON.parse( result.array ) );  
      }
      setStatusBar( result.text_to_display, result.error );
    },
    error: function( xhr, status, error ) 
    {
      setStatusBar( xhr, error );
    }
  });
});

//-------------------------------------

$( document ).on( 'submit', '#form-manual', function( e )
{
  e.preventDefault();
  let target = $( this );
  
  setStatusBar();

  $.ajax({
    type: 'POST',
    url: target.attr( 'action' ),
    data: target.serialize(),
    success: function( response )
    { 
      let result = JSON.parse( response );
      setStatusBar( result.text_to_display, result.error );
    },
    error: function( xhr, status, error ) 
    {
      setStatusBar( xhr, error );
    }
  });
});

//-------------------------------------

$( document ).ready( function() {
  const elem = document.querySelectorAll( '.block-name, .item-name, .item-description, .item-option-name, .item-option-value' );

  elem.forEach ( field => {
    field.addEventListener( 'input', function handleInput( e ) {
      e.target.value = e.target.value.replace( /[<>"={}]/g, '' ); // Prevent user from entering certain characters in the specified input fields
    });
  });
});