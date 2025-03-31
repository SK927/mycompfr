function displayToImport( id )
{
  let template = document.getElementById( 'to-import' );
  let clone = template.content.cloneNode( true );  
  clone.querySelector( '.import-competition' ).value = id;   
  document.getElementById( id + '_admin' ).append( clone );
}

//-------------------------------------

function removeToImport( id )
{
  let block = document.getElementById( id + '_admin' );
  block.querySelector( '.import-competition' ).closest( '.col-sm-auto' ).remove();
}  

//-------------------------------------

function displayImported( id )
{
  let template = document.getElementById( 'imported' );
  let clone = template.content.cloneNode( true );  
  clone.querySelector( '.extract-data' ).href = 'admin-display-registrations?id=' + encodeURIComponent( id );   
  clone.querySelector( '.send-reminder' ).value = id;   
  clone.querySelector( '.update-competitors' ).value = id;   
  document.getElementById( id + '_admin' ).append( clone );
}

//-------------------------------------

function displayCompetition( id, name, startDate, endDate, registration )
{
  let template = document.getElementById( 'displayed' );
  let clone = template.content.cloneNode( true );  
  
  clone.querySelector( '.competition-name' ).innerHTML = name.replace(/\\/g, '');   
  clone.querySelector( '.competition-info' ).innerHTML = "From " + startDate + " to " + endDate; 
  clone.querySelector( '.going' ).value = id;   
  clone.querySelector( '.not-going' ).value = id;  
  clone.querySelector( '.going' ).innerHTML = '&#10003;';
  clone.querySelector( '.not-going' ).innerHTML = '&#10007;';
  
  if ( registration.confirmed === 'YES' )
  {
    clone.querySelector( '.going' ).classList.replace( 'btn-outline-secondary', 'btn-success' );
    clone.querySelector( '.not-going' ).classList.replace( 'btn-danger', 'btn-outline-secondary' );
  }
  else if ( registration.confirmed === 'NO' )
  {
    clone.querySelector( '.going' ).classList.replace( 'btn-success', 'btn-outline-secondary' );
    clone.querySelector( '.not-going' ).classList.replace( 'btn-outline-secondary', 'btn-danger' );
  }
  else
  {
    clone.querySelector( '.competition-not-answered' ).innerHTML += ' <span style="color:#dc3545"><b>You haven\'t answered yet!</b></span>';
  }
  return clone;
} 

//-------------------------------------

function updateCompetitionsList()
{
  $.ajax( {
    type: 'POST',
    url: 'src/admin_ajax-get-updated-competition-list.php',
    data: { valid: true },

    success: function( response )
    { 
      let result = JSON.parse( response );
      let competitionList = result.competition_list;
      let target = document.getElementById( 'imported-competitions' );      
  
      if ( competitionList != null )
      {
        target.innerHTML = '';

        competitionList.forEach( function( elem, i ){
          let competition = displayCompetition( elem.competition_id, elem.competition_name, elem.competition_start_date, elem.competition_end_date, elem.competitor_registration );
          target.append( competition );
        } );
      }
    },
    error: function( xhr, status, error ) 
    {
      setStatusBar( xhr, error );
    } 
  } );
}  

//-------------------------------------

function toggleStatus( id, state )
{
  let bar = $( "#status-bar" );

  setStatusBar();
  
  $.ajax( {
    type: 'POST',
    url: 'src/admin_ajax-update-registration-status.php',
    data: { competition_id: id, new_state: state },
    success: function( response )
    {      
      let result = JSON.parse( response );
      
      updateCompetitionsList();
      setStatusBar( result.text_to_display, result.error );
    },
    error: function( xhr, status, error ) 
    {
      setStatusBar( xhr, error );
    }
  } );
} 

//-------------------------------------

$( document ).on( 'click', '.import-competition', function( e )
{
  e.preventDefault();
  
  let target = $( this );
    
  setStatusBar();
  
  $.ajax( {
    type: 'POST',
    url: 'src/admin_ajax-import-competition.php',
    data: { competition_id: target.val() },
    success: function( response )
    {      
      let result = JSON.parse( response );
      
      updateCompetitionsList();
      removeToImport( target.val() );
      displayImported( target.val() );
      setStatusBar( result.text_to_display, result.error );
    },
    error: function( xhr, status, error ) 
    {
      setStatusBar( xhr, error );
    }
  } );
} );

//-------------------------------------

$( document ).on( 'click', '.going', function( e )
{
  let competitionId = e.target.closest( 'button' ).value;
  toggleStatus( competitionId, 'YES' );   
} ); 

//-------------------------------------

$( document ).on( 'click', '.not-going', function( e )
{
  let competitionId = e.target.closest( 'button' ).value;
  toggleStatus( competitionId, 'NO' );   
} ); 

//-------------------------------------

$( document ).on( 'click', '.send-reminder', function( e )
{
  e.preventDefault();

  if ( confirm( 'Confirm you want to send a reminder?' ) )
  {
    let target = $( this );
    
    setStatusBar();
    
    $.ajax( {
      type: 'POST',
      url: 'src/admin_ajax-send-reminder.php',
      data: { competition_id: target.val() },
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
  }
} ); 

//-------------------------------------

$( document ).on( 'click', '.update-competitors', function( e )
{
  e.preventDefault();
  
  let target = $( this );
    
  setStatusBar();
  
  $.ajax( {
    type: 'POST',
    url: 'src/admin_ajax-update-competitors-list.php',
    data: { competition_id: target.val() },
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