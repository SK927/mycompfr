<?php

  require_once dirname( __DIR__, 2 ) . '/src/_functions-generic.php';
  require_once dirname( __DIR__, 2 ) . '/src/_functions-wcif.php';
  require_once dirname( __DIR__, 2 ) . '/src/tcpdf/tcpdf.php';


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
    * get_shared_cumulative(): retrieve cumulative time for selected events
    * @param (array) competition_data: competition data extracted from WCIF
    * @param (array) events_alias: selected events aliases (e.g. 444bf)
    * @return (string) the cumulative time limit
    */ 
  
  function get_shared_cumulative( $competition_data, $events_alias )
  {
    foreach ( $competition_data['events'] as $event ) // Get $events_alias[0] and $events_alias[1] shared time limit
    {
      if ( $event['id'] == $events_alias[0] )
      {
        $cumulative_limit = gmdate( 'H:i:s', $event['rounds'][0]['timeLimit']['centiseconds'] / 100 );
        break;
      }
    }
    return $cumulative_limit;
  }


  /**
   * get_assignments_id_for_events(): retrieve selected events groups assignment ids 
   * @param (array) competition_data: competition data extracted from WCIF
   * @param (array) events_id: selected events ids (e.g. 4x4x4 Blindfolded)
   * @return (array) the assignments ID as an array for each group of selected events
   */

  function get_assignments_id_for_events( $competition_data, $events_id )
  {
    $assignments_id = array(
                        $events_id[0] => array(), 
                        $events_id[1] => array(),
                      );

    foreach ( $competition_data['schedule']['venues'] as $venue ) 
    {
      foreach ( $venue['rooms'] as $room )
      { 
        $pattern = "/{$events_id[0]}|{$events_id[1]}/"; 
        $activities = array_filter( $room['activities'], function( $a ) use( $pattern ){ return preg_grep( $pattern, $a ); } ); // Keep only information for $events_id[0] and $events_id[1] 

        foreach ( $activities as $round )
        {
          foreach ( $round['childActivities'] as $group )
          {
            $pattern ="/({$events_id[0]}|{$events_id[1]}).* Group (\d+)/";
            preg_match_all( $pattern, $group['name'], $match ); // Get the corresponding group

            $assignments_id[ $match[1][0] ][ $match[2][0] ] = $group['id']; // Save activity id for each groups of $events_id[0] and $events_id[1]
          }
        }
      }
    }  
    return $assignments_id;
  }


  /** 
   * retrieve_competitors_groups(): return competitors group assignments, expressed as event assignment id
   * @param (array) competition_data: competition data extracted from WCIF
   * @param (array) events_id: selected events ids (e.g. 4x4x4 Blindfolded)
   * @param (array) assignments_id: events assignment ids, corresponding to all the groups created for said events
   * @return  (array) competitors' assigned groups as an associative array
   */

  function retrieve_competitors_groups( $competition_data, $events_id, $assignments_id )
  {
    $competition_groups = array();
    
    foreach ( $assignments_id as $event => $group ) // Search assignments as competitors for $events_id[0] and $events_id[1]
    {
      foreach ( $competition_data['persons'] as $person )
      {
        $temp = array(
                  $events_id[0] => $competition_groups[ $person['wcaUserId'] ][ $events_id[0] ], 
                  $events_id[1] => $competition_groups[ $person['wcaUserId'] ][ $events_id[1] ],
                ); // Store currently saved assignments to temporary variable
        
        foreach ( $person['assignments'] as $assignment )
        {
          // If assignment id corresponds to saved activities id and person is assigned as competitor, save assignment
          if ( $key = array_search( $assignment['activityId'], $group ) AND $assignment['assignmentCode'] == "competitor" ) 
          {
            $temp[ $event ] = $key;
          }
          
        }        

        if( $temp[ $events_id[0] ] OR $temp[ $events_id[1] ] )
        {
          $competition_groups[ $person['wcaUserId'] ] = array_merge( ['competitor_name' => utf8_decode( $person['name'] ), 'registrant_id' => $person['registrantId']], $temp ); // If assignment is found, save to person data
        }
      }        
    }
    return $competition_groups;
  }


  /**
   * create_new_pdf(): create new PDF reference
   * @return (pdf) the PDF reference created
   */

  function create_new_pdf()
  {
    $pdf = new TCPDF( PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false );
    $pdf->setCreator( PDF_CREATOR );
    $pdf->setAuthor( PDF_AUTHOR );
    $pdf->setPrintHeader( false );
    $pdf->setPrintFooter( false );
    $pdf->SetDefaultMonospacedFont( PDF_FONT_MONOSPACED );
    $pdf->SetMargins( 0, 0, 0, true );
    $pdf->setAutoPageBreak( TRUE, 0 );
    $pdf->setFontSubsetting( true );

    return $pdf;
  }


  /**
   * create_dual_scorecards(): generate dual scorecard for all concerned competitors
   * @param (pdf) pdf: reference to the PDF being created
   * @param (array) competition_data: competition data extracted from WCIF
   * @param (array) competition_groups: group assignments for all competitors participating in selected events
   * @param (string) time_limit: cumulative time limit for selected events
   * @param (array) events_display: selected events strings to be displayed on scorecards (e.g. 4x4 BLD)
   * @param (array) events_id: selected events ids (e.g. 4x4x4 Blindfolded)
   * @param (int) scorecard_count: number of dual scorecards already generated, if you need to insert other stuff in between (optional)
   * @return (multi) the PDF reference and necessary data for the continuation of processing
   */

  function create_dual_scorecards( $pdf, $competition_data, $competition_groups, $time_limit, $events_display, $events_id, $scorecard_count = 1 )
  {
    $offset = 5;
    $attempt_number = array( 1, 2, 3, '-' );
    $spacing = array( 0, 12.34, 24.68, 41.71 ); // All elements should be evenly spaced, except for the extra attempt
    
    foreach ( $competition_groups as $person => $data )
    {
      if ( $data[ $events_id[0] ] AND $data[ $events_id[1] ] ) // Create dual scorecards first
      {
        $is_new_page = $scorecard_count % 2; // Only display 2 dual scorecards per page
        
        if ( $is_new_page )
        {
          $pdf->AddPage();
          $pdf->SetLineStyle( array( 'width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => '10,4', 'color' => array( 127, 127, 127 ) ) );
          $pdf->Line( 0, 148.5, 210, 148.5 );
          $pdf->SetLineStyle( array( 'width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array( 0, 0, 0 ) ) );
        }

        $pdf->SetDrawColor( 0, 0, 0 );
        
        // Print competition name
        $pdf->setFont( 'dejavusanscondensed', '', 16, '', true );
        $pdf->SetXY( 31.59, 3.70 + 148.5 * (($scorecard_count - 1) % 2) + $offset );
        $pdf->Cell( 146.83, 10.12, utf8_encode( $competition_data['name'] ), false, 0, 'C', false );
        
        // Print competitor info header
        $pdf->SetFont( 'dejavusanscondensed', '', 8, '', true );
        $pdf->SetXY( 11.60, 16.22 + 148.5 * (($scorecard_count - 1) % 2) + $offset );
        $pdf->Cell( 14.07, 3.70, 'ID', false, 0, 'L', false );
        $pdf->Cell( 145.84, 3.70, 'Name', false, 0, 'L', false );
        $pdf->Cell( 13.57, 3.70, 'Round', false, 0, 'C', false );
        $pdf->Cell( 13.57, 3.70, 'Group', false, 0, 'C', false );

        // Print scorecards results table header
        $pdf->SetXY( 3.70, 53.55 + 148.5 * (($scorecard_count - 1) % 2) + $offset );
        $pdf->Cell( 7.90, 4.69, '', false, 0, 'C', false );
        $pdf->Cell( 11.49, 4.69, 'Solve #', false, 0, 'C', false );
        $pdf->Cell( 11.49, 4.69, 'Scr', false, 0, 'C', false );
        $pdf->Cell( 45.59, 4.69, 'Result', false, 0, 'C', false );
        $pdf->Cell( 11.49, 4.69, 'Judge', false, 0, 'C', false );
        $pdf->Cell( 11.49, 4.69, 'Comp', false, 0, 'C', false );
        $pdf->Cell( 3.70, 4.69, '', false, 0, 'C', false );
        $pdf->Cell( 11.49, 4.69, 'Solve #', false, 0, 'C', false );
        $pdf->Cell( 11.49, 4.69, 'Scr', false, 0, 'C', false );
        $pdf->Cell( 45.59, 4.69, 'Result', false, 0, 'C', false );
        $pdf->Cell( 11.49, 4.69, 'Judge', false, 0, 'C', false );
        $pdf->Cell( 11.49, 4.69, 'Comp', false, 0, 'C', false );
        
        // Print competitor info
        $pdf->SetFont( 'dejavusanscondensed', '', 14, '', true );
        $pdf->SetXY( 11.60,21.22 + 148.5 * (($scorecard_count - 1) % 2) + $offset );
        $pdf->Cell( 14.07, 7.16, $data['registrant_id'], true, 0, 'C', false );    
        $pdf->Cell( 145.84, 7.16, utf8_encode( $data['competitor_name'] ), true, 0, 'L', false );
        $pdf->Cell( 13.57, 7.16, 1, true, 0, 'C', false );
        $pdf->Cell( 13.57, 7.16, max( $data[ $events_id[0] ], $data[ $events_id[1] ] ), true, 0, 'C', false );

        // Print events name
        $pdf->SetFont( 'dejavusanscondensed', 'B', 36, '', true );
        $pdf->SetXY( 11.60, 39.73 + 148.5 * (($scorecard_count - 1) % 2) + $offset );
        $pdf->Cell( 91.55, 9.13, $events_display[0], false, 0, 'C', false );
        $pdf->Cell( 3.70, 9.13, '', false, 0, 'C', false );
        $pdf->Cell( 91.55, 9.13, $events_display[1], false, 0, 'C', false );
      
        // Print scorecards results table
        $pdf->SetFont( 'dejavusanscondensed','B',24, '', true );

        for( $i = 0 ; $i <= (count( $attempt_number ) - 1) ; $i++ )
        {
          $pdf->SetXY( 3.70, 58.24 + $spacing[ $i ] + 148.5 * (($scorecard_count - 1) % 2) + $offset );
          $pdf->Cell( 7.90, 10.86, $attempt_number[ $i ], false, 0, 'L', false );
          $pdf->Cell( 11.49, 10.86, '', true, 0, 'C', true );
          $pdf->Cell( 11.49, 10.86, '', true, 0, 'C', false );
          $pdf->Cell( 45.59, 10.86, '', true, 0, 'C', false );
          $pdf->Cell( 11.49, 10.86, '', true, 0, 'C', false );
          $pdf->Cell( 11.49, 10.86, '', true, 0, 'C', false );
          $pdf->Cell( 3.70, 10.86, '', false, 0, 'C', false );
          $pdf->Cell( 11.49, 10.86, '', true, 0, 'C', true );
          $pdf->Cell( 11.49, 10.86, '', true, 0, 'C', false );
          $pdf->Cell( 45.59, 10.86, '', true, 0, 'C', false );
          $pdf->Cell( 11.49, 10.86, '', true, 0, 'C', false );
          $pdf->Cell( 11.49, 10.86, '', true, 0, 'C', false );
        }
        
        $pdf->SetFont( 'dejavusanscondensed', '', 10, '', true );
        $pdf->SetXY( 4.90, 93.75 + 148.5 * (($scorecard_count - 1) % 2) + $offset );
        $pdf->Cell( 29.12, 6.42, 'Extra attempt', false, 0, 'L', false );
        
        // Print cumulative limit */
        $pdf->SetXY( 93.87, 117.46 + 148.5 * (($scorecard_count - 1) % 2) + $offset );
        $pdf->Cell( 104.53, 6.42, "Time limit:{$time_limit} in total for {$events_display[0]} and {$events_display[1]}", false, 0, 'R', false );
         
        $scorecard_count++;

        unset( $competition_groups[ $person ] ); // Remove person from assignment array when it has been processed
      }
    }
    return array( $pdf, $competition_groups, $is_new_page );
  }

  /**
   * create_single_scorecards(): generate dual scorecard for all concerned competitors
   * @param (pdf) pdf: reference to the PDF being created
   * @param (array) competition_data: competition data extracted from WCIF
   * @param (array) competition_groups: group assignments for all competitors participating in selected events
   * @param (string) time_limit: cumulative time limit for selected events
   * @param (array) events_display: selected events strings to be displayed on scorecards (e.g. 4x4 BLD)
   * @param (array) events_id: selected events ids (e.g. 4x4x4 Blindfolded)
   * @param (int) quarter_page_count: number of dual scorecards already generated, if you need to insert other stuff in between (optional)
   * @return (multi) the PDF reference and necessary data for the continuation of processing
   */

  function create_single_scorecards( $pdf, $competition_data, $competition_groups, $time_limit, $events_display, $events_id, $quarter_page_count = 0 )
  {
    $offset = 5;
    $attempt_number = array( 1, 2, 3, '-' );
    $spacing = array( 0, 12.34, 24.68, 41.71 ); // All elements should be evenly spaced, except for the extra attempt

    if( $quarter_page_count ) // If space remains, add dashed line to create two additionnal scorecards at the bottom of current page
    {
      $pdf->SetLineStyle( array( 'width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => '10,4', 'color' => array( 127, 127, 127 ) ) );
      $pdf->Line( 105, 148.5, 105, 297 );
      $pdf->SetLineStyle( array( 'width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array( 0, 0, 0 ) ) );
    }
      
    $offset_x = array( 0, 105, 0, 105 );
    $offset_y = array( 0, 0, 148.5, 148.5 );
    
    foreach ( $competition_groups as $person => $data ) // Create scorecards for people assigned to only one event ( $events_id[0] or $events_id[1] )
    {
      $current_page = $quarter_page_count % 4;
      
      if ( ! $current_page ) // Add new page if current page if filled
      {
        $pdf->AddPage();
        $pdf->SetLineStyle( array( 'width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => '10,4', 'color' => array( 127, 127, 127 ) ) );
        $pdf->Line( 0, 148.5, 210, 148.5 );
        $pdf->Line( 105, 0, 105, 297 );
        $pdf->SetLineStyle( array( 'width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array( 0, 0, 0 ) ) );
      }
      
      // Print competition name
      $pdf->SetFont( 'dejavusanscondensed', '', 16 );
      $pdf->SetXY( $offset_x[ $current_page ], 3.70 + $offset_y[ $current_page ] + $offset );
      $pdf->Cell( 105, 10.12, utf8_encode( $competition_data['name'] ), false, 0, 'C', false );
      
      // Print competitor info header
      $pdf->SetFont( 'dejavusanscondensed', '', 8 );
      $pdf->SetXY( 5 + $offset_x[ $current_page ], 16.22 + $offset_y[ $current_page ] + $offset );
      $pdf->Cell( 13.57, 3.70, 'ID', false, 0, 'L', false );
      $pdf->Cell( 54.29, 3.70, 'Name', false, 0, 'L', false );
      $pdf->Cell( 13.57, 3.70, 'Round', false, 0, 'C', false );
      $pdf->Cell( 13.57, 3.70, 'Group', false, 0, 'C', false );

      // Print scorecards results table header
      $pdf->SetXY( 12.90 + $offset_x[ $current_page ], 53.55 + $offset_y[ $current_page ] + $offset );
      $pdf->Cell( 11.49, 4.69, 'Scr', false, 0, 'C', false );
      $pdf->Cell( 52.63, 4.69, 'Result', false, 0, 'C', false );
      $pdf->Cell( 11.49, 4.69, 'Judge', false, 0, 'C', false );
      $pdf->Cell( 11.49, 4.69, 'Comp', false, 0, 'C', false );
        
      // Print competitor info header
      $pdf->SetFont( 'dejavusanscondensed', '', 14 );
      $pdf->SetXY( 5 + $offset_x[ $current_page ],21.22 + $offset_y[ $current_page ] + $offset );
      $pdf->Cell( 11.49, 7.16, $data['registrant_id'], true, 0, 'C', false );
      $pdf->Cell( 60.53, 7.16, utf8_encode( $data['competitor_name'] ), true, 0, 'L', false );
      $pdf->Cell( 11.49, 7.16, 1, true, 0, 'C', false );
      $pdf->Cell( 11.49, 7.16, max( $data[ $events_id[0] ], $data[ $events_id[1] ] ), true, 0, 'C', false );

      // Print events name */
      $pdf->SetFont( 'dejavusanscondensed','B',36 );
      $pdf->SetXY( $offset_x[ $current_page ], 39.73 + $offset_y[ $current_page ] + $offset );
      $pdf->Cell( 105, 9.13, ( $data[ $events_id[0] ] ? $events_display[0] : $events_display[1] ), false, 0, 'C', false );

      // Print scorecards results table
      $pdf->SetFont( 'dejavusanscondensed', 'B', 24 );

      for( $i = 0 ; $i <= (count( $attempt_number ) - 1) ; $i++ )
      {
        $pdf->SetXY( 5 + $offset_x[ $current_page ], 58.24 + $spacing[ $i ] + $offset_y[ $current_page ] + $offset );
        $pdf->Cell( 7.90, 10.86, $attempt_number[ $i ], false, 0, 'L', false );
        $pdf->Cell( 11.49, 10.86, '', true, 0, 'C', false );
        $pdf->Cell( 52.63, 10.86, '', true, 0, 'C', false );
        $pdf->Cell( 11.49, 10.86, '', true, 0, 'C', false );
        $pdf->Cell( 11.49, 10.86, '', true, 0, 'C', false );
      }
      
      $pdf->SetFont( 'dejavusanscondensed', '', 10 );
      $pdf->SetXY( 6.20 + $offset_x[ $current_page ], 93.75 + $offset_y[ $current_page ] + $offset );
      $pdf->Cell( 29.12, 6.42, 'Extra attempt', false, 0, 'L', false );
      
      // Print cumulative limit
      $pdf->SetXY( $offset_x[ $current_page ], 117.46 + $offset_y[ $current_page ] + $offset );
      $pdf->Cell( 100, 6.42, "Time limit: $time_limit in total for " . $events_display[0] . ' and ' . $events_display[1], false, 0, 'R', false );
      
      $quarter_page_count++;
      
      unset( $competition_groups[ $person ] ); // Remove person from assignment array when it has been processed
    }
    return array( $pdf, $competition_groups, $current_page );
  }

?>