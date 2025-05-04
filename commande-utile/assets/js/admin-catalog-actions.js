function getRandomInt() {
  return '--' + Math.floor( Math.random() * 1000000 );
} 

//-------------------------------------

function createBlock( name = '' )
{
  let blockList = document.getElementById( 'block-list' );
  let template = document.getElementById( 'block' );
  let clone = template.content.cloneNode( true );
  let id = 'b' + blockList.querySelectorAll( '.block' ).length;
  clone.querySelector( '.block' ).id = id;  
  clone.querySelector( '.block-name' ).id = id + '-name';  
  clone.querySelector( '.block-name' ).name = id + '-name';  
  clone.querySelector( '.block-name' ).value = name;  
  clone.querySelector( '.add-item' ).name = id + '-f(add_item)';
  clone.querySelector( '.delete-block' ).name = id + '-f(delete_block)';  
  clone.querySelector( '.clone-block' ).id = id + '-f(clone-block)'; 
  clone.querySelector( '.item-list' ).id = id + '-item-list'; 
  blockList.appendChild( clone );

  return id;
}

//-------------------------------------

function createItem( blockId, name = '', price = 0, description = '', img = '.' )
{
  let itemList = document.getElementById( blockId + '-item-list' );
  let template = document.getElementById( 'item' );
  let clone = template.content.cloneNode( true );
  let id = blockId + '-i' + itemList.querySelectorAll( '.item' ).length;
  clone.querySelector( '.delete-item' ).name = id + '-f(delete_item)';  
  clone.querySelector( '.item' ).id = id;  
  clone.querySelector( '.item-name' ).id = id + '-name';  
  clone.querySelector( '.item-name' ).name = id + '-name';  
  clone.querySelector( '.item-name' ).value = name;   
  clone.querySelector( '.item-price' ).name = id + '-price';  
  clone.querySelector( '.item-price' ).value = parseFloat( price ).toFixed( 2 ); 
  clone.querySelector( '.item-description' ).name = id + '-descr';  
  clone.querySelector( '.item-description' ).value = description;  
  clone.querySelector( '.item-image' ).id = id + '-image'; 
  clone.querySelector( '.item-image' ).name = id + '-image'; 
  clone.querySelector( '.item-image' ).value = img; 
  clone.querySelector( '.options' ).id = id + '-options';
  itemList.appendChild( clone );

  return id;
}

//-------------------------------------

function createOption( itemId, name = '', auto = false )
{
  let optionsList = document.getElementById( itemId + '-options' );
  let template = document.getElementById( 'option' );
  let clone = template.content.cloneNode( true );
  let id = itemId + '-o' + optionsList.querySelectorAll( '.option' ).length;
  clone.querySelector( '.option' ).id = id;  
  clone.querySelector( '.item-option-name' ).id = id + '-name';  
  clone.querySelector( '.item-option-name' ).name = id + '-name';  
  clone.querySelector( '.item-option-name' ).value = name;  
  clone.querySelector( '.select-list' ).id = id + '-select-list'; 
  optionsList.appendChild( clone );
  
  if ( ! auto )
  {
    for ( let i = 0; i < 2; i++)
    {
      createSelection( id ); 
    }
  }

  return id;
}

//-------------------------------------

function createSelection( optionId, name = '', amount = '0.00' )
{
  let selectList = document.getElementById( optionId + '-select-list' );
  let template = document.getElementById( 'select' );
  let clone = template.content.cloneNode( true );
  let id = optionId + '-s' + selectList.querySelectorAll( '.select' ).length;
  clone.querySelector( '.item-options-select-name' ).id = id + '-name';  
  clone.querySelector( '.item-options-select-name' ).name = id + '-name';  
  clone.querySelector( '.item-options-select-name' ).value = name;  
  clone.querySelector( '.item-options-select-price' ).name = id + '-price'; 
  clone.querySelector( '.item-options-select-price' ).value = amount; 
  selectList.appendChild( clone );

  return id;
}

//-------------------------------------

function updateBlocks( blockList )
{
  blockList.querySelectorAll( '.block').forEach( function( elem, i )
  {
    elem.id = 'b' + i;  
    elem.querySelector( '.block-name' ).id = elem.id + '-name';  
    elem.querySelector( '.block-name' ).name = elem.id + '-name';  
    elem.querySelector( '.add-item' ).name = elem.id + '-f(add_item)';
    elem.querySelector( '.delete-block' ).name = elem.id + 'clone-block)'; 
    elem.querySelector( '.item-list' ).id = elem.id + '-item-list'; 
    let itemList = elem.querySelector( '.item-list' );
    itemList.id = elem.id + '-item-list'; 
    updateItems( itemList );
  });
  return blockList.querySelectorAll( '.block').length - 1;
}

//-------------------------------------

function updateItems( itemList )
{
  let id = itemList.id.replace( '-item-list', '' );
  
  itemList.querySelectorAll( '.item').forEach( function( elem, i )
  {
    elem.id = id + '-i' + i;  
    elem.querySelector( '.delete-item' ).name = elem.id + '-f(delete_item)';  
    elem.querySelector( '.item-name' ).id = elem.id + '-name';  
    elem.querySelector( '.item-name' ).name = elem.id + '-name';  
    elem.querySelector( '.item-price' ).name = elem.id + '-price';  
    elem.querySelector( '.item-description' ).name = elem.id + '-descr';  
    elem.querySelector( '.item-image' ).id = elem.id + '-image'; 
    elem.querySelector( '.item-image' ).name = elem.id + '-image'; 
    elem.querySelector( '.options' ).id = id + '-options';
    let optionList = elem.querySelector( '.options' );
    optionList.id = elem.id + '-options'; 
    updateOptions( optionList );
  });
}

//-------------------------------------

function updateOptions( optionList )
{
  let id = optionList.id.replace( '-options', '' );
  
  optionList.querySelectorAll( '.option').forEach( function( elem, i )
  {
    elem.id = id + '-o' + i;  
    elem.querySelector( '.item-option-name' ).id = elem.id + '-name';  
    elem.querySelector( '.item-option-name' ).name = elem.id + '-name'; 
    let selectList = elem.querySelector( '.select-list' );
    selectList.id = elem.id + '-select-list'; 
    updateSelections( selectList );
  });
}

//-------------------------------------

function updateSelections( selectList )
{
  let id = selectList.id.replace( '-select-list', '' );

  selectList.querySelectorAll( '.select').forEach( function( elem, i )
  {
    elem.querySelector( '.item-options-select-name' ).id = id + '-s' + i + '-name';
    elem.querySelector( '.item-options-select-name' ).name = id + '-s' + i + '-name';
    elem.querySelector( '.item-options-select-price' ).name = id + '-s' + i + '-price';
  });
}

//-------------------------------------

$( document ).on( 'click', '.clone-block', function( e )
{
  e.preventDefault();
  let clonedBlock = $( this ).closest( '.block' );
  let clonedBlockId = clonedBlock.attr( 'id' );
  let clonedImg = document.getElementById( clonedBlockId ).querySelectorAll( '.item-image');
  let clone = document.getElementById( clonedBlockId ).cloneNode( true );
  let blockList = document.getElementById( 'block-list' );

  clone.querySelectorAll( '.item-image ' ).forEach( function(elem, i)
  {
    elem.value = clonedImg[i].value;
  });

  blockList.appendChild( clone );
  let lastBlockNumber = updateBlocks( blockList );
  document.getElementById( 'b' + lastBlockNumber + '-name' ).value = '';
  document.getElementById( 'b' + lastBlockNumber + '-name' ).focus();
});

//-------------------------------------

$( document ).on( 'click', '.add-block', function()
{
  let id = createBlock();
  document.getElementById( id + '-name' ).focus();
});

//-------------------------------------

$( document ).on( 'click', '.add-item', function( e )
{
  e.preventDefault();
  let blockId = this.closest( '.block' ).id;
  let id = createItem( blockId );
  document.getElementById( id + '-name' ).focus();
});

//-------------------------------------

$( document ).on( 'click', '.add-option', function( e )
{
  e.preventDefault();
  let itemId = this.closest( '.item' ).id;
  let id = createOption( itemId );
  document.getElementById( id + '-name' ).focus();
});

//-------------------------------------

$( document ).on( 'click', '.add-select', function( e )
{
  e.preventDefault();
  let optionId = this.closest( '.option' ).id;
  let id = createSelection( optionId );
  document.getElementById( id + '-name' ).focus();
});

//-------------------------------------

$( document ).on( 'click', '.delete-block', function( e )
{
  e.preventDefault();

  if ( confirm( 'Confirmer la suppression ?' ) )
  {
    this.closest( '.block' ).remove();
    updateBlocks( document.getElementById( 'block-list' ) );
  }
});  

//-------------------------------------

$( document ).on('click', '.delete-item', function(e)
{
  e.preventDefault();
  
  if ( confirm( 'Confirmer la suppression ?' ) )
  {
    let itemList = this.closest( '.item-list' );
    this.closest( '.item' ).remove();
    updateItems( itemList );
  }
});

//-------------------------------------

$( document ).on( 'click', '.delete-option', function( e )
{
  e.preventDefault()

  if ( confirm( 'Confirmer la suppression ?' ) );
  {
    let optionList = this.closest( '.options' );
    this.closest( '.option' ).remove();
    updateOptions( optionList );
  }
});

//-------------------------------------

$( document ).on( 'click', '.delete-select', function( e )
{
  e.preventDefault();

  if ( confirm( 'Confirmer la suppression ?' ) )
  {
    let selectList = this.closest( '.select-list' );
    this.closest( '.select' ).remove();
    updateSelections( selectList );
  }
});

//-------------------------------------

$( document ).on( 'input', '.item-price, .item-options-select-price', function(e)
{
  e.preventDefault();

  let input = $( this ).val().toString(); // Get value after replacing . by empty string
  let number = 0;

  if ( input )
  {
    let numberPattern = /\d+/g; // Get only the numeric values out of the string

    try
    {
      number = input.match( numberPattern ).join( '' );  
    }
    catch
    {

    }
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
        updateList();  
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
      
      if ( ! result.error )
      {
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

function updateList( )
{
  const queryString = window.location.search;
  const urlParams = new URLSearchParams( queryString );
  const id = urlParams.get( 'id' );

  $.ajax( {
    type: 'POST',
    url: 'src/admin_ajax-get-catalog.php',
    data: { competition_id: encodeURI( id ) },
    success: function( response )
    { 
      let result = JSON.parse( response );

      if ( result )
      {
        document.getElementById( 'block-list').innerHTML = '';
        
        $.each( result.array, function( block_key, block )
        {
          let blockId = createBlock( block['name'] );

          $.each( block['items'], function( item_key, item )
          {
            let itemId = createItem( blockId, item['name'], item['price'], item['description'], item['image'] );

            $.each( item['options'], function( option_key, option )
            {
              let optionId = createOption( itemId, option['name'], true );

              $.each( option['selections'], function( selection_key, selection )
              {
                createSelection( optionId, selection['name'], selection['price'] );
              } );
            } );
          } );
        } );  
  }
    },
    error: function( xhr, status, error ) 
    {
      console.log( error );
    }
  } );

}

//-------------------------------------

$( document ).ready( function() {
  const elem = document.querySelectorAll( '.block-name, .item-name, .item-description, .item-option-name, .item-option-name, .item-options-select-name' );

  elem.forEach ( field => {
    field.addEventListener( 'input', function handleInput( e ) {
      e.target.value = e.target.value.replace( /[<>"={}]/g, '' ); // Prevent user from entering certain characters in the specified input fields
    });
  });
});
