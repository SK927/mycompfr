function createCompetitionRow( competitionId, competitionName, startDate, endDate , contactEmail )
{
  let template = document.getElementById( 'competition-row' );
  let clone = template.content.cloneNode( true );
  clone.querySelector( '.competition-name' ).innerHTML = competitionName;  
  clone.querySelector( '.competition-dates' ).innerHTML = 'du ' + startDate + ' au ' + endDate;  
  clone.querySelector( '.competition-contact' ).innerHTML = contactEmail;  
  clone.querySelector( '.competition-handle-url' ).href = 'admin-handle-competition?id=' + encodeURIComponent( competitionId );   
  clone.querySelector( '.btn-danger' ).name = competitionId;  
  document.getElementById( 'competitions' ).append( clone );
}

//-------------------------------------

function createAdministratorRow( login, email )
{
  let template = document.getElementById( 'administrator-row' );
  let clone = template.content.cloneNode( true );
  clone.querySelector( '.administrator-login' ).innerHTML = login;  
  clone.querySelector( '.administrator-email' ).innerHTML = email;  
  clone.querySelector( '.regenerate-password' ).name = login;   
  clone.querySelector( '.delete-administrator' ).name = login;   
  document.getElementById( 'administrators' ).append( clone );
}

//-------------------------------------

function updateCompetitionsList( competitions )
{
  document.getElementById( 'competitions' ).innerHTML = '';

  $.each( competitions, function( id, competition )
  {
    createCompetitionRow( competition.competition_id, competition.competition_name, competition.competition_start_date, competition.competition_end_date, competition.contact_email );
  } );
}

//-------------------------------------

function updateAdministratorsList( administrators )
{
  document.getElementById( 'administrators' ).innerHTML = '';

  $.each( administrators, function( id, administrator )
  {
    createAdministratorRow( administrator.administrator_login, administrator.administrator_email );
  } );
}

//-------------------------------------

$( document ).on( 'click', '#add-competition', function( e )
{
  let button = document.getElementById( 'add-competition' );

  $( '#competition-id' ).val( '' );
  $( '#competition-contact-email' ).val( '' );

  if( button.innerHTML == 'Annuler' ) 
  {
    button.innerHTML = 'Ajouter une compétition';
    button.classList.replace( 'btn-danger', 'btn-light' );
  }
  else 
  {
    button.innerHTML = 'Annuler';
    button.classList.replace( 'btn-light', 'btn-danger' );
  }
} );

//-------------------------------------

$( document ).on( 'click', '#add-administrator', function( e )
{
  let button = document.getElementById( 'add-administrator' );
  
  $( '#administrator-id' ).val( '' );
  $( '#administrator-contact-email' ).val( '' );

  if( button.innerHTML == 'Annuler' ) 
  {
    button.innerHTML = 'Ajouter un administrateur';
    button.classList.replace( 'btn-danger', 'btn-light' );
  }
  else 
  {
    button.innerHTML = 'Annuler';
    button.classList.replace( 'btn-light', 'btn-danger' );
  }
} );

//-------------------------------------

$( document ).on( 'submit', '#form-competition', function( e )
{
  e.preventDefault();

  let inputs = document.getElementsByClassName( 'is-invalid' );
  for ( let input of inputs ) input.classList.remove( 'is-invalid' );

  setStatusBar();

  let target = $( this );

  $.ajax( {
    type: 'POST',
    url: target.attr( 'action' ),
    data: target.serialize(),
    success: function( response )
    { 
      let result = JSON.parse( response );  

      if ( result.error_id ) 
      {
        $( '#competition-id' ).addClass( 'is-invalid' );
      }

      if ( result.error_email )
      {
        $( '#competition-contact-email' ).addClass( 'is-invalid' );
      }

      error = result.error_id || result.error_email || result.error;
      
      setStatusBar( result.text_to_display, error )

      if ( ! error )
      {
        $( '#add-competition' ).click();
        updateCompetitionsList( result.competitions );
      }
    },
    error: function( xhr, status, error ) 
    {
      setStatusBar( xhr, error );
    }
  } );
} );

//-------------------------------------

$( document ).on( 'submit', '#form-administrator', function( e )
{
  e.preventDefault();

  let inputs = document.getElementsByClassName( 'is-invalid' );
  for ( let input of inputs ) input.classList.remove( 'is-invalid' );

  setStatusBar();

  let target = $( this );

  $.ajax( {
    type: 'POST',
    url: target.attr( 'action' ),
    data: target.serialize(),
    success: function( response )
    { 
      let result = JSON.parse( response );  

      if ( result.error_id )
      {
        $( '#administrator-id' ).addClass( 'is-invalid' );
      }

      if ( result.error_email )
      {
        $( '#administrator-contact-email' ).addClass( 'is-invalid' );
      }

      error = result.error_id || result.error_email || result.error;
      
      setStatusBar( result.text_to_display, error )

      if ( !error )
      {
        $( '#add-administrator' ).click();
        updateAdministratorsList( result.administrators );
      }
    },
    error: function( xhr, status, error ) 
    {
      setStatusBar( xhr, error );
    }
  } );
} );

//-------------------------------------

$( document ).on( 'click', '.delete-competition', function( e )
{
  if ( confirm( 'Confirmer ?' ) )
  {
    setStatusBar();

    let target = $( this );

    $.ajax( {
      type: 'POST',
      url: 'src/master/ajax-delete-competition.php',
      data: { competition_id:target.attr( 'name' ) },
      success: function( response )
      { 
        let result = JSON.parse( response );  

        setStatusBar( result.text_to_display, result.error )

        if ( ! result.error )
        {
          target.closest( '.list-group-item' ).remove();
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

$( document ).on( 'click', '.delete-administrator', function( e )
{
  if( confirm( 'Confirmer ?' ) )
  {
    setStatusBar();

    let target = $( this );

    $.ajax( {
      type: 'POST',
      url: 'src/master/ajax-delete-administrator.php',
      data: { administrator_id:target.attr( 'name' ) },
      success: function( response )
      {
        let result = JSON.parse( response );  

        setStatusBar( result.text_to_display, result.error )

        if ( ! result.error )
        {
          target.closest( '.list-group-item' ).remove();
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

$( document ).on( 'click', '.regenerate-password', function( e )
{
  if( confirm( 'Confirmer ?' ) )
  {
    setStatusBar();

    let target = $( this );

    $.ajax( {
      type: 'POST',
      url: 'src/master/ajax-update-administrator-credentials.php',
      data: { administrator_id:target.attr( 'name' ) },
      success: function( response )
      {
        let result = JSON.parse( response );  

        setStatusBar( result.text_to_display, result.error )
      },
      error: function( xhr, status, error ) 
      {
        setStatusBar( xhr, error );
      }
    } );
  }
} );

//-------------------------------------

$( document ).on( 'submit', '#form-credentials', function( e )
{ 
  e.preventDefault();

  document.querySelectorAll( '.is-invalid' ).forEach( function( elem, i ){
    elem.classList.remove( 'is-invalid' );
  } );

  let target = $( this );

  $.ajax( {
    type: 'POST',
    url: target.attr( 'action' ),
    data: target.serialize(),
    success: function( response )
    { 
      let result = JSON.parse( response );  
      let error = result.error_login || result.error_credentials || result.error;

      if ( error )
      {
        if ( result.error_login )
        { 
          $( '#administrator-id' ).addClass( 'is-invalid' );
        }
        else if( result.error_credentials ) 
        {
          $( '#administrator-id' ).addClass( 'is-invalid' );
          $( '#administrator-password' ).addClass( 'is-invalid' );
        }
        setStatusBar( result.text_to_display, error );
      }
      else location.reload();
    },
    error: function( xhr, status, error ) 
    {
      setStatusBar( xhr, error );
    }
  } );
} );
