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
  clone.querySelector( '.copy-emails' ).value = id;   
  clone.querySelector( '.update-competitors' ).value = id;   
  document.getElementById( id + '_admin' ).append( clone );
}

//-------------------------------------

function displayCompetition( id, name, startDate, endDate, competitorId, registration )
{
  let template = document.getElementById( 'displayed' );
  let clone = template.content.cloneNode( true );  
  
  clone.querySelector( '.competition-name' ).innerHTML = name.replace(/\\/g, '');   
  clone.querySelector( '.competition-info' ).innerHTML = "Du " + startDate + " au " + endDate; 
  clone.querySelector( '.going' ).value = id + '_' + competitorId;   
  clone.querySelector( '.maybe' ).value = id + '_' + competitorId;   
  clone.querySelector( '.not-going' ).value = id + '_' + competitorId;  

  if( registration === 'OK' )
  {
    clone.querySelector( '.going' ).classList.replace( 'btn-outline-secondary', 'btn-success' );
    clone.querySelector( '.not-going' ).classList.replace( 'btn-danger', 'btn-outline-secondary' );
    clone.querySelector( '.maybe' ).classList.replace( 'btn-warning', 'btn-outline-secondary' );
  }
  else if( registration === 'NO' )
  {
    clone.querySelector( '.going' ).classList.replace( 'btn-success', 'btn-outline-secondary' );
    clone.querySelector( '.not-going' ).classList.replace( 'btn-outline-secondary', 'btn-danger' );
    clone.querySelector( '.maybe' ).classList.replace( 'btn-warning', 'btn-outline-secondary' );
  }
  else if( registration === 'ND' )
  {
    clone.querySelector( '.going' ).classList.replace( 'btn-success', 'btn-outline-secondary' );
    clone.querySelector( '.not-going' ).classList.replace( 'btn-danger', 'btn-outline-secondary' );
    clone.querySelector( '.maybe' ).classList.replace( 'btn-outline-secondary', 'btn-warning' );
  }
  else
  {
    clone.querySelector( '.competition-not-answered' ).innerHTML += ' <span style="color:#dc3545"><b>Vous n\'avez pas encore répondu !</b></span>';
  }
  return clone;
} 

//-------------------------------------

function updateCompetitionsList()
{
  $.ajax( {
    type: 'POST',
    url: 'src/ajax_get-updated-competition-list.php',
    data: { valid: true },
    success: function( response )
    { 
      let result = JSON.parse( response );
      let competitionList = result.competition_list;
      let target = document.getElementById( 'imported-competitions' );      
  
      if( competitionList != null )
      {
        target.innerHTML = '';

        competitionList.forEach( function( elem, i ){
          let competition = displayCompetition( elem.competition_id, elem.competition_name, elem.competition_start_date, elem.competition_end_date, elem.competitor_id, elem.competitor_registration );
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

function toggleStatus( info, state )
{
  let bar = $( "#status-bar" );

  setStatusBar();

  info = info.split( '_' );

  $.ajax( {
    type: 'POST',
    url: 'src/ajax_update-registration-status.php',
    data: { competition_id: info[0], user_id: info[1], new_state: state },
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
    url: 'src/ajax_import-competition.php',
    data: { competition_id: target.val() },
    success: function( response )
    {      
      let result = JSON.parse( response );
      
      setStatusBar( result.text_to_display, result.error );

      if( ! result.error )
      {      
        updateCompetitionsList();
        removeToImport( target.val() );
        displayImported( target.val() );
      }
    },
    error: function( xhr, status, error ) 
    {
      setStatusBar( xhr, error );
    }
  } );
} );

//-------------------------------------

$( document ).on( 'click', '.maybe', function( e )
{
  let competitionId = e.target.closest( 'button' ).value;
  toggleStatus( competitionId, 'ND' );   
} );

//-------------------------------------

$( document ).on( 'click', '.going', function( e )
{
  let competitionId = e.target.closest( 'button' ).value;
  toggleStatus( competitionId, 'OK' );   
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

  if( confirm( 'Souhaitez-vous envoyer un rappel aux personnes n\'ayant pas encore confirmé ?' ) )
  {
    let target = $( this );
    
    setStatusBar();
    
    $.ajax( {
      type: 'POST',
      url: 'src/ajax_send-reminder.php',
      data: { competition_id: target.val() },
      success: function( response )
      {     
        console.log(response);
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
    url: 'src/ajax_update-competitors-list.php',
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

  updateCompetitionsList();
} );

//-------------------------------------    

$( document ).on( 'click', '.copy-emails', function( e )
{
  setStatusBar();

  let target = $( this );

  $.ajax( {
    type: 'POST',
    url: 'src/ajax_copy-emails.php',
    data: { competition_id: target.val() },
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