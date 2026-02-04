let numberPattern = /\d+/g; // Get only the numeric values out of the string

class Time{

  constructor()
  {
    this.h = 0;
    this.min = 0;
    this.s = 0;
    this.cs = 0;
  }

  reverseString( input )
  {
    return input.split( '' ).reverse().join( '' );
  } 

  padWithZero( digits )
  {
    if ( digits < 10 )
      return "0" + String( digits );
    else
      return String( digits );
  }
 
  checkTime()
  {
    if ( 59 < this.s)
    {
      this.min = this.min + Math.floor( this.s / 60);
      this.s = this.s % 60; 
    }

    if ( 59 < this.min)
    {
      this.h = this.h + Math.floor( this.min / 60);
      this.min = this.min % 60; 
    }
  }

  setTimeFromString( str = "0" )
  {
    let reversed = this.reverseString( str );

    if ( str.length )
    {
      this.cs = this.cs + parseInt( this.reverseString( reversed.slice( 0, 2 ) ) );

      if ( 2 < str.length )
      {
        this.s = this.s + parseInt( this.reverseString( reversed.slice( 2, 4 ) ) );

        if ( 4 < str.length )
        {
          this.min = this.min + parseInt( this.reverseString( reversed.slice( 4, 6 ) ) );

          if ( 6 < str.length )
          {
            this.h = this.h + parseInt( this.reverseString( reversed.slice( 6, 8 ) ) );
          }
        }
      }      
    }
  }

  getFormattedTime()
  {
    let formattedTime = '';
  
    if( this.h ) 
    {
      formattedTime += this.padWithZero( this.h ) + ':';
    }

    if( formattedTime || this.min )
    {
      formattedTime += this.padWithZero( this.min ) + ':';
    }

    formattedTime += this.padWithZero( this.s ) + '.' + this.padWithZero( this.cs );

    return formattedTime;
  }

  getTimeConvertedToCentiseconds()
  {
    return this.cs + ( this.s * 100 ) + ( this.min * 6000 ) + ( this.h * 360000 );
  }
}

//-------------------------------------

$( document ).ready(function()
{  
  document.querySelectorAll( 'input' ).forEach( function( elem )
  {
    elem.addEventListener( 'input', function()
    {      
      let currentTime = new Time();
      let input = $( this ).val().toString();

      if ( input )
      {
        try
        {
          currentTime.setTimeFromString( input.match( numberPattern ).join( '' ) ); 
        }
        catch
        {
          currentTime.setTimeFromString();
        }

        $( this ).val( currentTime.getFormattedTime() );
      }
    } );
  } );
} );

//-------------------------------------

$( document ).on( 'keydown', 'input', function(e)
{
  if ( e.key === "Enter")
  {
    var next = $( '[tabIndex=' + (this.tabIndex + 1) + ']' );
    
    if ( ! next.length ) 
    {
      next = $( '[tabIndex=1]' );
    }

    next.focus();
  }
});

//-------------------------------------

$( document ).on( 'focusout', '.result-attempt, .cumulative', function()
{
  let remainingDiv = document.getElementById( 'remaining' );
  let remainingCentiseconds = 0;

  document.querySelectorAll( '.result-attempt, .cumulative' ).forEach( function( elem, i )
  {
    try
    {
      let currentTime = new Time();
      currentTime.setTimeFromString( elem.value.toString().match( numberPattern ).join( '' ) );
      currentTime.checkTime();
      elem.value = currentTime.getFormattedTime();

      if ( elem.name == 'cumulative' )
      {
        remainingCentiseconds += currentTime.getTimeConvertedToCentiseconds();
      }
      else
      {
        remainingCentiseconds += (-currentTime.getTimeConvertedToCentiseconds());
      }
    }
    catch{}
  });

  let remainingTime = new Time();
  remainingMilliseconds = new Date( Math.abs( remainingCentiseconds ) * 10 );
  remainingTime.h = remainingMilliseconds.getUTCHours();
  remainingTime.min = remainingMilliseconds.getUTCMinutes();
  remainingTime.s = remainingMilliseconds.getSeconds();
  remainingTime.cs = remainingMilliseconds.getMilliseconds() / 10;

  let sign = '';

  if ( remainingCentiseconds < 0 )
  { 
    sign = '-';
    remainingDiv.style.color = "#c30000";
    remainingDiv.style.fontWeight = "bold";
  }
  else
  {
    remainingDiv.style.color = "#000";
    remainingDiv.style.fontWeight = "normal";
  }

  remainingDiv.value = sign + remainingTime.getFormattedTime();
});

//-------------------------------------

$( document ).on( 'click', '.btn', function()
{
  document.querySelectorAll( '.result-attempt' ).forEach( function( elem )
  {
    elem.value = '';
  } );

  document.getElementById( 'remaining' ).value = '';
} );