function createSection( id = '', title = '', text = '', section = true )
{
	let template = document.getElementById( 'section' );
	let clone = template.content.cloneNode( true );
	let numberOfSections = document.querySelectorAll('.section').length - 2;


	if ( ! id ) 
	{
	  id = "section" + numberOfSections;
	}

	clone.querySelector( '.section' ).id = id; 
	clone.querySelector( 'input' ).name = id + "_title"; 
	clone.querySelector( 'input' ).value = title; 
	clone.querySelector( 'input' ).readOnly = ! section; 
	clone.querySelector( 'textarea' ).name = id + "_text"; 
	clone.querySelector( 'textarea' ).innerHTML = text; 

	if ( ! section )
	{
		clone.querySelector( '.delete-section' ).remove();
		clone.querySelector( '.move-section-up' ).remove();
		clone.querySelector( '.move-section-down' ).remove();
	}

	if ( document.getElementById( 'outro' ) )
	{
		container = document.getElementById( 'content' );
		lastElement = document.getElementById( 'outro' ).closest( '.section' );
		container.insertBefore( clone, lastElement );
		document.getElementById( id ).focus();
	}
	else
	{
		document.getElementById( 'content' ).appendChild( clone );
	}

}

//-------------------------------------

$( document ).on( 'click', '.add-section', function( e )
{
  e.preventDefault();
  createSection();
});

//-------------------------------------

$( document ).on( 'click', '.delete-section', function( e )
{
  e.preventDefault();
  
  if ( confirm( 'Confirmer la suppression ?' ) )
  {
    $( this ).closest( '.section' ).remove();
  }
});

function swapSections( currentSectionId, direction = 1 )
{
	let currentContainer = document.getElementById( currentSectionId );
  let targetSectionId = 'section' + ( parseInt( currentSectionId.substring( 7 ) ) + direction );
  let targetContainer = document.getElementById( targetSectionId );

  if ( targetContainer != null )
  {
  	let clone = currentContainer.cloneNode( true );
  	clone.id = targetSectionId; 
  	clone.querySelector( 'input' ).name = targetSectionId + "_title";  
		clone.querySelector( 'textarea' ).name = targetSectionId + "_text"; 
		currentContainer.remove();
		targetContainer.id = currentSectionId; 
  	targetContainer.querySelector( 'input' ).name = currentSectionId + "_title";  
		targetContainer.querySelector( 'textarea' ).name = currentSectionId + "_text"; 
		if ( direction == 1 )
		{
			targetContainer.insertAdjacentElement( 'beforebegin', clone );	
		}
		else
		{
			targetContainer.insertAdjacentElement( 'afterend', clone );
		}
  }
}

$( document ).on( 'click', '.move-section-up', function( e )
{
  e.preventDefault();
  swapSections( $( this ).closest( '.section' ).attr( 'id' ), 1 );
});

$( document ).on( 'click', '.move-section-down', function( e )
{
  e.preventDefault();
  swapSections( $( this ).closest( '.section' ).attr( 'id' ), -1 ); 
});

$( document ).on( 'click', '.handle-newsletter', function( e )
{
	let input = $( this ).attr( 'id' ).split( '-' );

	action = input[0];
	from = parseInt( input[2] );
	control = parseInt( input[1] ) - 1;

	let id = window.prompt( 'Entrez l\'ID au format YYMM (doit être supérieur à ' + control  + ')' );

	if ( id )
	{
		if ( control <= id )
		{
			if ( action == 'create' )
			{
				$.ajax( {
			    type: 'POST',
			    url: 'src/admin/ajax-create-newsletter.php',
			    data: {id: id},
			    success: function( response )
			    {  
			      window.location.href = '?id='+id;
			    },
			    error: function( xhr, status, error ) 
			    {
			      window.alert( 'Erreur lors de la création de la newsletter : ' + xhr.status );
			    }
			  });
			}
			if( action == 'duplicate' )
			{
				$.ajax( {
			    type: 'POST',
			    url: 'src/admin/ajax-duplicate-newsletter.php',
			    data: {id: id, from: from},
			    success: function( response )
			    {  
			      window.location.href = '?id='+id;
			    },
			    error: function( xhr, status, error ) 
			    {
			      window.alert( 'Erreur lors de la duplication de la newsletter : ' + xhr.status );
			    }
			  });
			}
		}
		else
		{
			window.alert( 'La valeur d\'ID ne peut pas être inférieure à ' + control );
		}
	}
});

$( document ).on( 'change', '.select-newsletter', function( e )
{
  let id = $( this ).val();
  window.location.href = '?id='+id;
  return false;
});

$( document ).on( 'click', '.delete-newsletter', function( e )
{
  if ( confirm( 'Confirmer la suppression ?' ) )
  {
		let input = $( this ).attr( 'id' ).split( '-' );

		id = parseInt( input[1] );

		$.ajax( {
	    type: 'POST',
	    url: 'src/admin/ajax-delete-newsletter.php',
	    data: {id: id},
	    success: function( response )
	    { 
	      window.location.href = '?';
	    },
	    error: function( xhr, status, error ) 
	    {
	      window.alert( 'Erreur lors de la suppression de la newsletter : ' + xhr.status );
	    }
	  });
	}
});

$( document ).on( 'click', '#publish-newsletter', function( e )
{
	let form = document.querySelector( '.form' );
	let splitForm = form.id.split( '-' );
	let id = splitForm[2];
	let published = document.getElementById( 'published' ).innerText;

  form.action = form.action.replace( 'admin-preview-newsletter.php?id=' + id , 'src/admin/publish-newsletter.php?id=' + id + '&published=' + published );
});

