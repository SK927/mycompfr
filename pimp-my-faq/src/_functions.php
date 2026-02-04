<?php

  /**
   * get_competition_id(): retrieve competition ID from $_POST object
   * @param (multi) data: POST data to be analyzed
   * @param (string) value: numbered value of the competition to retrieve
   * @return (string) the compared list of competitors
   */

  function get_competition_id( $data )
  {
    return ( ! in_array( $_POST['competition_select'], array( '', 'Other' ) ) ) ? trim( $_POST['competition_select'] ) : trim( $_POST['competition_id'] );
  }


  /**
   * add_paragraphs(): add new paragraphe to current string
   * @param (string) text: the text to add paragraphs to
   * @param (multi) paragraphs: the paragraphs to add to the text
   * @return (string) the modified string
   */
  
  function add_paragraphs( $text, $paragraphs )
  {
    if( ! is_array( $paragraphs ) )
    {
      $paragraphs = array( $paragraphs );
    }

    foreach( $paragraphs as $paragraph )
    {
      $text .= "{$paragraph}\n";
    }

    return $text;
  }


?>