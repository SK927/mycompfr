function displayToImport( competitionId )
{
  let template = document.getElementById( 'to-import' );
  let clone = template.content.cloneNode( true );  
  clone.querySelector( '.import-competition' ).value = competitionId;   
  document.getElementById( competitionId + '_admin' ).append( clone );
}

//-------------------------------------

function removeToImport( competitionId )
{
  let block = document.getElementById( competitionId + '_admin' );
  block.querySelector( '.import-competition' ).closest( '.col-sm-auto' ).remove();
}

//-------------------------------------

function displayHandle( competitionId )
{
  let template = document.getElementById( 'handle-registrations' );
  let clone = template.content.cloneNode( true );  
  clone.querySelector( '.handle-competition' ).href = 'admin-handle-competition?id=' + encodeURI( competitionId );
  document.getElementById( competitionId + '_admin' ).append( clone );
}

//-------------------------------------

function displayCompetition( competitionId, competitionName, competitionDate, competitionEvents, competitionRegistrations, competitorRegistration )
{
  let template = document.getElementById( 'displayed' );
  let clone = template.content.cloneNode( true );  
  let eventsDiv = clone.querySelector( '.competition-events' );
  eventsDiv.name = competitionId;
  eventsDiv.id = competitionId + '_register';

  clone.querySelector( '.card-title' ).innerHTML = competitionName + ' <small class=\'text-muted\'>FROM ' + competitionDate +'</small>';   
  clone.querySelector( '.register-to' ).name = competitionId; 
  clone.querySelector( '.register-to' ).id = competitionId; 
  eventsList = JSON.parse( competitionEvents );
  
  for( const [key, value] of Object.entries( eventsList ) )
  {
    let eventsInfo = document.getElementById( 'event-info' ).content.cloneNode( true );
    eventsInfo.querySelector( '.event-checkbox' ).name = key;
    eventsInfo.querySelector( '.event-checkbox' ).id = competitionId + '_' + key;
    
    if( competitorRegistration != null && competitorRegistration['events'][key] ) 
    {
      eventsInfo.querySelector( '.event-checkbox' ).checked = true;
    }
    
    eventsInfo.querySelector( '.event-label' ).innerHTML = value.alias;    
    $( eventsInfo.querySelector( '.event-label' ) ).attr( 'for', competitionId + '_' + key );    
    eventsDiv.prepend( eventsInfo );
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
      
      if( target )
      {
        target.innerHTML = '';

        if( competitionList )
        {
          competitionList.forEach( function( elem, i ){
            let competition = displayCompetition( elem.competition_id, elem.competition_name, elem.competition_start_date, elem.competition_events, elem.competition_registrations, elem.competitor_registration );
            target.append( competition );
          } );
        }
      }
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
    data: { valid: true, competition_id: target.val() },
    success: function( response )
    {
      let result = JSON.parse( response );

      setStatusBar( result.text_to_display, result.error );
      
      if( !result.error )
      {
        updateCompetitionsList();
        removeToImport( target.val() );
        displayHandle( target.val() );
      }
    },
    error: function( xhr, status, error ) 
    {
      setStatusBar( xhr, error );
    }
  } );
} );

//-------------------------------------

$( document ).on( 'click', '.confirm-registration', function( e )
{
  e.preventDefault();
  
  let target = $( this ).closest( 'form' );
  let competitionId = encodeURI( target.attr( 'id' ) );

  setStatusBar();
  
  $.ajax( {
    type: 'POST',
    url: target.attr( 'action' ),
    data: { competition_id: competitionId, events: target.serialize() },
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
