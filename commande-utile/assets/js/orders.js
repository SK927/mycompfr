// Item 'catalog' in sessionStorage is defined by user-place-order.php.

$( document ).ready( function(){ 
  var position = document.getElementById( 'information-competition' ).getBoundingClientRect();
  document.getElementById( 'information-competition' ).style.top = position.top + 'px';

  var sticky = document.getElementById( 'order-form' ).querySelectorAll( '.sticky-top' );
  position = sticky[0].getBoundingClientRect();
  sticky.forEach((elem) => {
    elem.style.top = position.top + 'px';
  } );

  document.querySelectorAll( '.item-quantity' ).forEach( elem => {
    updateItemCost( elem );
  } );
  updateConfirmButton();
  updateTotalCost();
});

// ================================================================ //

function updateItemCost( elem )
{
  if( elem.value != 0 )
  {
    elem.style.color = '#006A3A';
    elem.style.borderColor = '#006A3A';
    elem.style.fontWeight = 'bold';    }
  else
  {
    elem.style.color = 'black';
    elem.style.borderColor = '#DEE2E6';
    elem.style.fontWeight = 'normal';
  }

  let price = document.getElementById( elem.name + '-item-cost' ).innerHTML;
  let pattern = /\d+\.?\d*/; 
  let cost = parseFloat( elem.value * price.match( pattern ) );
  let options = document.getElementById( elem.name + '-options');

  pattern = /\+(\d+\.?\d*)€/;
  options.querySelectorAll('select').forEach((select) =>{
    if( selectCost = select.options[select.selectedIndex].text.match( pattern ) )
    {
      cost += parseFloat(selectCost[1]);
    }
  });

  document.getElementById( elem.name + '-item-total-cost' ).innerHTML = '(' + cost.toFixed(2) + ' €)' ;


}

// ================================================================ //

function updateTotalCost()
{
  let sum = 0;
  let pattern = /\d+\.?\d*/; 

  document.querySelectorAll( '.item-total-cost ' ).forEach( elem => {
    sum += parseFloat( elem.innerHTML.match( pattern ) );
  } );

  document.getElementById( 'order-total' ).innerHTML =  sum.toFixed(2);
}

// ================================================================ //

function addUserSelections( userSelections, b_key, i_key )
{
  console.log(userSelections);
  userSelections = JSON.parse( userSelections );

  userSelections.forEach( (selectionArray) => {
    let row = document.createElement( 'div' );
    row.classList.add('row', 'justify-content-end');

    for( const [ o_key, s_key ] of Object.entries( selectionArray ) )
    {
      row.append( addSelect( b_key, i_key, o_key, s_key ) );
    }

    document.getElementById( b_key + '-' + i_key + '-options' ).append( row );
  }); 
}

// ================================================================ //

function addSelect( b_key, i_key, o_key, s_key = '' )
{
  let catalog = JSON.parse( sessionStorage.getItem( 'catalog' ) );
  let i = document.getElementById( b_key + '-' + i_key + '-options' ).querySelectorAll('.row').length;

  let i_alias = b_key + '-' + i_key;
  let col = document.createElement( 'div' );
  col.classList.add('col-12', 'col-md-6', 'col-lg-4', 'col-xl-6', 'col-xxl-4', 'p-0' );
  let form = document.createElement( 'div' );
  form.classList.add( 'form-floating' );
  let select = document.createElement( 'select' );
  select.classList.add( 'form-select', 'mt-1', 'text-center' );
  select.name = i + '_' + i_alias + '-' + o_key;
  select.id = select.name;

  for( const [selection_key, selection] of Object.entries( catalog[ b_key ]['items'][ i_key ]['options'][ o_key ]['selections'] ) )
  {
    let value = selection['name'];
    if( selection['price'] != 0 ) value += ' (+' + selection['price'] + '€)';
    
    option = document.createElement( 'option' );
    option.value = selection_key;
          
    if( selection_key == s_key ) option.setAttribute( 'selected', true );

    option.innerHTML = value;
    select.append( option );
  }

  let label = document.createElement( 'label' );
  label.for = i + '_' + i_alias + '-' + o_key;
  label.innerHTML = catalog[ b_key ]['items'][ i_key ]['options'][ o_key ]['name'];

  form.append( select );
  form.append( label );
  col.append( form );

  return col;
}

// ================================================================ //

function removeLastSelect( optionsCount, b_key, i_key )
{
  let row = document.getElementById( b_key + '-' + i_key + '-options' );
  row.removeChild( row.lastElementChild );
}

// ================================================================ //

$( document ).on( 'change', '.item-quantity', function( e )
{
  let catalog = JSON.parse( sessionStorage.getItem( 'catalog' ) );
  [ b_key, i_key ] = this.name.split('-');
  
  if( catalog[ b_key ]['items'][ i_key ]['options'] )
  {
    let optionsCount = Object.keys(catalog[ b_key ]['items'][ i_key ]['options']).length;
    let optionsRow = this.closest('.catalog-item').querySelector('.options');
    let diff = ((this.value * optionsCount) - optionsRow.querySelectorAll('.col-12').length) / optionsCount;
    let abs_diff = Math.abs( diff );

    for( i = 0 ; i < abs_diff ; i++ )
    {
      if( diff < 0 )
      {
        removeLastSelect( optionsCount, b_key, i_key );
      }
      if( diff > 0)
      {
        let row = document.createElement( 'div' );
        row.classList.add('row', 'justify-content-end');
        
        for( o = 0 ; o < optionsCount ; o++ )
        {
          row.append( addSelect( b_key, i_key, 'o' + o ) );
        }

        document.getElementById( b_key + '-' + i_key + '-options' ).append( row );
      } 
    }
  }

  updateConfirmButton();
  updateItemCost( this );
  updateTotalCost();
} );

// ================================================================ //

$( document ).on( 'keypress', '.item-quantity', function( e )
{
  if ( e.which == '13' )
  {
    event.preventDefault();
    event.stopPropagation();
  }
});

// ================================================================ //

$( document ).on( 'change', 'select', function( e )
{
  updateItemCost( this.closest( '.catalog-item' ).querySelector('.item-quantity') );
  updateTotalCost();
});

// ================================================================ //

function updateConfirmButton()
{
  let disabled = true;

  document.querySelectorAll( '.item-quantity' ).forEach( elem =>{
    disabled &= (elem.value == 0);
  });

  document.getElementById( 'confirm-button' ).disabled = disabled;
}

$( document ).on( 'click', '#delete-button', function( e )
{
  if ( ! confirm( 'Confirmer la suppression?' ) )
  {
    e.preventDefault();
  }
  else
  {
    let form = document.getElementById( 'order-form' ); 
    form.action = form.action + '&delete=true';
    form.submit();
  }
} );

// ================================================================ //

$( document ).on( 'click', '#add-comment', function( e, comment = null )
{
  e.preventDefault();
  let parent = $( this ).parent();
  showComment( parent, comment );
  this.remove();
} );

// ================================================================ //

function showComment( parent, comment = null )
{

  let textarea = document.createElement( 'textarea' );
  textarea.classList.add( 'form-control', 'mt-3', 'mb-2' );
  textarea.placeholder = 'Je mets mon commentaire ici, si besoin.';
  textarea.name = 'user_comment';

  if ( comment )
  {
    textarea.innerHTML = comment;
  }

  parent.append( textarea );
} 