<?php

  /**
   * get_afs_color(): get row color from competitor status to mimic AFS website
   * @param (array) competitor: competitor info
   * @return (string) class for target row
   */

  function get_afs_color( $competitor )
  {
    if ( $competitor['member'] ) 
    {
      $color = 'table-success';
    }
    else if ( $competitor['firsttimer'] )
    {
      $color = 'table-warning';
    }
    else
    {
      $color = 'table-danger';      
    }

    return $color;
  }


  /**
   * get_afs_text(): get text to display in status column to mimic AFS website
   * @param (array) competitor: competitor info
   * @return (string) the text to display
   */

  function get_afs_text( $competitor )
  {
    if ( $competitor['member'] ) 
    {
      $text = 'Pris en charge par l\'AFS';
    }
    else if ( $competitor['firsttimer'] )
    {
      $class = "";

      if ( $competitor['false_firsttimer'] )
      {
        $class = ' false';
      }

      $text = "<a class=\"firsttimer{$class} no-default\" href=\"\">Vérifier s'il est nouveau compétiteur<a/>";
    }
    else
    {
      $text = 'Doit payer';      
    }

    return $text;
  }


  /**
   * get_wca_paid(): get text to display in paid column to mimic WCA website
   * @param (array) competitor: competitor info
   * @return (string) the text to display
   */

  function get_wca_paid( $competitor )
  {
    if ( $competitor['paid_at'] != "not paid" ) 
    {
      $text = date( 'd/m/Y', strtotime( $competitor['paid_at'] ) );
    }
    else
    {
      $text = 'Non payée';      
    }

    return $text;
  }


  /**
   * get_registered_at_tooltip(): format the string date to be displayed as tooltip
   * @param (string) date: the date the competitor registered at
   * @return (string) the formatted string
   */

  function get_registered_at_tooltip( $date )
  {
    return date( 'Y-m-d g:i:s A', strtotime( $date ) );
  }

  /**
   * get_paid_at_tooltip(): format the string to be displayed as tooltip
   * @param (array) competitor: competitor info
   * @return (string) the formatted text
   */

  function get_paid_at_tooltip( $competitor )
  {
    if ( $competitor['paid_at'] != "not paid" ) 
    {
      $text = date( 'Y-n-j H:i:s.v \G\M\T P', strtotime( $competitor['paid_at'] ) );
    }
    else
    {
      $text = 'Paiement demandé le : ' . date( 'Y-n-j H:i:s.v \G\M\T P', strtotime( $competitor['registered_at'] ) );      
    }

    return $text;
  }


  /**
   * get_response_correctness(): check user's response correctness
   * @param (string) response: the user given response
   * @param (string) solution: the case solution
   * @return (string) the formatted text
   */


  function get_response_correctness( $response, $solution )
  {
    $response_array = str_split( $response );
    $solution_array = str_split( $solution );

    foreach( $solution_array as $i => $letter )
    {
      if( $response_array[ $i ] != $letter )
      {
        $response_array[ $i ] = "<b class=\"error\">{$response_array[ $i ]}</b>";
      }
    }

    return implode( "", $response_array );
  }

?>