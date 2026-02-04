<?php

  require_once dirname( __DIR__, 2 ) . '/src/_functions-wcif.php';
  require_once dirname( __DIR__, 2 ) . '/src/tcpdf/tcpdf.php';
  require_once dirname( __DIR__, 2 ) . '/src/yaml_spyc-reader.php';


  /**
   * get_events(): returns the WCA events info
   * @return (array) events info
   */

  function get_events()
  {
    return array_shift( spyc_load_file( dirname( __DIR__, 1 ) . "/assets/events.yaml" ) );
  }


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
  
  function get_shared_cumulative( $competition_data, $events )
  {
    foreach( $competition_data['events'] as $event ) // Get $events_alias[0] and $events_alias[1] shared time limit
    {
      if( $event['id'] == $events[0]['alias'] )
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

  function get_assignments_id_for_events( $competition_data, $events )
  {
    $assignments_id = array(
                        $events[0]['long_name'] => array(), 
                        $events[1]['long_name'] => array(),
                      );

    foreach( $competition_data['schedule']['venues'] as $venue ) 
    {
      foreach( $venue['rooms'] as $room )
      { 
        $pattern = "/{$events[0]['long_name']}, Round 1|{$events[1]['long_name']}, Round 1/"; 
        $activities = array_filter( $room['activities'], function( $a ) use( $pattern ){ return preg_grep( $pattern, $a ); } ); // Keep only information for 

        foreach( $activities as $round )
        {
          foreach( $round['childActivities'] as $group )
          {
            $pattern ="/({$events[0]['long_name']}|{$events[1]['long_name']}).* Group (\d+)/";
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

  function retrieve_competitors_groups( $competition_data, $events, $assignments_id )
  {
    $competition_groups = array();
    foreach( $assignments_id as $event => $group ) // Search assignments as competitors for $events_id[0] and $events_id[1]
    {
      foreach( $competition_data['persons'] as $person )
      {
        $temp = array(
                  $events[0]['long_name'] => $competition_groups[ $person['wcaUserId'] ][ $events[0]['long_name'] ], 
                  $events[1]['long_name'] => $competition_groups[ $person['wcaUserId'] ][ $events[0]['long_name'] ],
                ); // Store currently saved assignments to temporary variable
        
        foreach( $person['assignments'] as $assignment )
        {
          // ifassignment id corresponds to saved activities id and person is assigned as competitor, save assignment
          if( $key = array_search( $assignment['activityId'], $group ) AND $assignment['assignmentCode'] == "competitor" ) 
          {
            $temp[ $event ] = $key;
          }
          
        }        

        if( $temp[ $events[0]['long_name'] ] OR $temp[ $events[1]['long_name'] ] )
        {
          $competition_groups[ $person['wcaUserId'] ] = array_merge( ['competitor_name' => utf8_decode( $person['name'] ), 'registrant_id' => $person['registrantId']], $temp ); // ifassignment is found, save to person data
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
   * @param (int) scorecard_count: number of dual scorecards already generated, ifyou need to insert other stuff in between (optional)
   * @return (multi) the PDF reference and necessary data for the continuation of processing
   */

  function create_dual_scorecards( $pdf, $competition_data, $competition_groups, $cumulative_limit, $events, $scorecard_count = 1 )
  {
    $offset_x = 11.60;
    $offset_y = 0;
    $half_page_height = 148.5;
    $page_width = 210;
    $cell_sm_width = 11.5;
    $cell_lg_width = 45.55;

    foreach( $competition_groups as $person => $data )
    {
      if( $data[ $events[0]['long_name'] ] AND $data[ $events[1]['long_name'] ] ) // Create dual scorecards first
      {
        $is_new_page = $scorecard_count % 2; // Only display 2 dual scorecards per page
        $starting_y = $half_page_height * (($scorecard_count - 1) % 2) + $offset_y;

        if( $is_new_page )
        {
          $pdf->AddPage();
          $pdf->SetLineStyle( array( 'width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => '10,4', 'color' => array( 127, 127, 127 ) ) );
          $pdf->Line( 0, $half_page_height, $page_width, $half_page_height );
          $pdf->SetLineStyle( array( 'width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array( 0, 0, 0 ) ) );
        }

        $pdf->SetDrawColor( 0, 0, 0 );
        
        // Print competition name
        $pdf->setFont( 'dejavusanscondensed', '', 16, '', true );
        $pdf->SetXY( 0, $starting_y + 2 );
        $pdf->Cell( $page_width, 12, $competition_data['name'], false, 1, 'C', false );
        
        // Print competitor info header
        $pdf->SetFont( 'dejavusanscondensed', '', 8, '', true );
        $pdf->SetX( $offset_x );
        $pdf->Cell( 14.1, 3.70, 'ID', false, 0, 'L', false );
        $pdf->Cell( 144.5, 3.70, 'Name', false, 0, 'L', false );
        $pdf->Cell( 14.1, 3.70, 'Round', false, 0, 'C', false );
        $pdf->Cell( 14.1, 3.70, 'Group', false, 1, 'C', false );

        $pdf->SetFont( 'dejavusanscondensed', '', 14, '', true );
        $pdf->SetX( $offset_x );
        $pdf->Cell( 14.1, 7.16, $data['registrant_id'], true, 0, 'C', false );    
        $pdf->Cell( 144.5, 7.16, utf8_encode( $data['competitor_name'] ), true, 0, 'L', false );
        $pdf->Cell( 14.1, 7.16, 1, true, 0, 'C', false );
        $pdf->Cell( 14.1, 7.16, $data[ $events[0]['long_name'] ], true, 1, 'C', false ); 

        $y = $pdf->getY();

        foreach( $events as $cnt => $event )
        {
          $pdf->SetXY( $offset_x + $cnt * ( $page_width - 91.55 - 2 * $offset_x ), $y + 5 );
          $pdf->SetFont( 'dejavusanscondensed', 'B', 36, '', false );
          $pdf->Cell( 91.55, 20, $events[ $cnt ]['display'], false, 2, 'C', false );

          // Print scorecards results table header
          $pdf->SetFont( 'dejavusanscondensed', '', 8, '', false );
          
          if( ! $cnt )
          {
            $pdf->setX( 0 );
            $pdf->Cell( $offset_x, 4.7, '', false, 0, 'C', false );
          }
          
          $pdf->Cell( $cell_sm_width, 4.7, 'Solve #', false, 0, 'C', false );
          $pdf->Cell( $cell_sm_width, 4.7, 'Scr', false, 0, 'C', false );
          $pdf->Cell( $cell_lg_width, 4.7, 'Result', false, 0, 'C', false );
          $pdf->Cell( $cell_sm_width, 4.7, 'Judge', false, 0, 'C', false );
          $pdf->Cell( $cell_sm_width, 4.7, 'Comp', false, 1, 'C', false );

          for( $i = 0 ; $i < $event['solve_count'] ; $i++ )
          {
            $pdf->SetX( $offset_x + $cnt * ( $page_width - 91.55 - 2 * $offset_x ) );
            $pdf->SetFont( 'dejavusanscondensed', 'B', 24, '', true );

            if( ! $cnt )
            {
              $pdf->setX( 0 );
              $pdf->Cell( $offset_x, 10.9, $i + 1, false, 0, 'R', false );
            }
            
            $pdf->Cell( $cell_sm_width, 10.9, '', true, 0, 'C', false );
            $pdf->Cell( $cell_sm_width, 10.9, '', true, 0, 'C', false );
            $pdf->Cell( $cell_lg_width, 10.9, '', true, 0, 'C', false );
            $pdf->Cell( $cell_sm_width, 10.9, '', true, 0, 'C', false );
            $pdf->Cell( $cell_sm_width, 10.9, '', true, 1, 'C', false );
            $pdf->SetFont( 'dejavusanscondensed', 'B', 1, '', true );
            $pdf->Cell( $cell_sm_width, 1, '', false, 1, 'C', false );
          }

          $pdf->SetX( $offset_x + $cnt * ( $page_width - 91.55 - 2 * $offset_x ) );
          $pdf->SetFont( 'dejavusanscondensed', '', 10, '', true );
          $pdf->Cell( 30, 6.4, 'Extra attempt', false, 1, 'L', false );

          $pdf->SetX( $offset_x + $cnt * ( $page_width - 91.55 - 2 * $offset_x ) );
          $pdf->Cell( $cell_sm_width, 10.9, 'Ex', true, 0, 'C', false );
          $pdf->Cell( $cell_sm_width, 10.9, '', true, 0, 'C', false );
          $pdf->Cell( $cell_lg_width, 10.9, '', true, 0, 'C', false );
          $pdf->Cell( $cell_sm_width, 10.9, '', true, 0, 'C', false );
          $pdf->Cell( $cell_sm_width, 10.9, '', true, 1, 'C', false );
        }
    
        // Print cumulative limit */
        $pdf->SetXY( $offset_x, $starting_y + $half_page_height - 10 );
        $pdf->Cell( $page_width - 2 * $offset_x, 6.4, "Time limit: {$cumulative_limit} in total for {$events[0]['display']} and {$events[1]['display']}", false, 0, 'R', false );
         
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
   * @param (int) quarter_page_count: number of dual scorecards already generated, ifyou need to insert other stuff in between (optional)
   * @return (multi) the PDF reference and necessary data for the continuation of processing
   */

  function create_single_scorecards( $pdf, $competition_data, $competition_groups, $cumulative_limit, $events, $quarter_page_count = 0 )
  {
    $offset_x = 0;
    $offset_y = 0;
    $page_height = 297;
    $half_page_height = $page_height / 2 ;
    $page_width = 210;
    $half_page_width = $page_width / 2;
    $cell_sm_width = 11.5;
    $cell_lg_width = 45.55;

    $event = $data[ $events[0]['long_name'] ] ? $events[0] : $events[1];

    $start_x = array( 0, $half_page_width, 0, $half_page_width );
    $start_y = array( 0, 0, $half_page_height, $half_page_height );

    if( $quarter_page_count ) // ifspace remains, add dashed line to create two additionnal scorecards at the bottom of current page
    {
      $pdf->SetLineStyle( array( 'width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => '10,4', 'color' => array( 127, 127, 127 ) ) );
      $pdf->Line( $half_page_width, $half_page_height, $half_page_width, $page_height );
      $pdf->SetLineStyle( array( 'width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array( 0, 0, 0 ) ) );
    }

    foreach( $competition_groups as $person => $data ) // Create scorecards for people assigned to only one event ( $events_id[0] or $events_id[1] )
    {
      $current_page = $quarter_page_count % 4;
      $starting_x = $start_x[ $current_page ] + $offset_x;
      $starting_y = $start_y[ $current_page ] + $offset_y;    
      
      if( ! $current_page ) // Add new page ifcurrent page iffilled
      {
        $pdf->AddPage();
        $pdf->SetLineStyle( array( 'width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => '10,4', 'color' => array( 127, 127, 127 ) ) );
        $pdf->Line( 0, $half_page_height, $page_width, $half_page_height );
        $pdf->Line( $half_page_width, 0, $half_page_width, $page_height );
        $pdf->SetLineStyle( array( 'width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array( 0, 0, 0 ) ) );
      }
      
      // Print competition name
      $pdf->SetFont( 'dejavusanscondensed', '', 16 );
      $pdf->SetXY( $starting_x, 3.70 + $starting_y );
      $pdf->Cell( $half_page_width, 12, $competition_data['name'], false, 1, 'C', false );
      
      // Print competitor info header
      $pdf->SetFont( 'dejavusanscondensed', '', 8 );
      $pdf->SetX( $starting_x + 5 );
      $pdf->Cell( 13.6, 3.70, 'ID', false, 0, 'L', false );
      $pdf->Cell( 54.2, 3.70, 'Name', false, 0, 'L', false );
      $pdf->Cell( 13.6, 3.70, 'Round', false, 0, 'C', false );
      $pdf->Cell( 13.6, 3.70, 'Group', false, 1, 'C', false );

      $pdf->SetFont( 'dejavusanscondensed', '', 14 );
      $pdf->SetX( $starting_x + 5 );
      $pdf->Cell( 13.6, 3.70, $data['registrant_id'], true, 0, 'C', false );
      $pdf->Cell( 54.2, 3.70, utf8_encode( $data['competitor_name'] ), true, 0, 'L', false );
      $pdf->Cell( 13.6, 3.70, 1, true, 0, 'C', false );
      $pdf->Cell( 13.6, 3.70, $data[ $event['long_name'] ], true, 1, 'C', false );

      // Print events name 
      $pdf->SetFont( 'dejavusanscondensed','B', 36 );
      $pdf->SetX( $starting_x );
      $pdf->Cell( $half_page_width, 20, $event['display'], false, 2, 'C', false );

      $pdf->SetFont( 'dejavusanscondensed','', 8 );
      $pdf->SetX( $starting_x + 11.6 );
      $pdf->Cell( $cell_sm_width, 4.7, 'Scr', false, 0, 'C', false );
      $pdf->Cell( $cell_lg_width, 4.7, 'Result', false, 0, 'C', false );
      $pdf->Cell( $cell_sm_width, 4.7, 'Judge', false, 0, 'C', false );
      $pdf->Cell( $cell_sm_width, 4.7, 'Comp', false, 1, 'C', false );

      // Print scorecards results table
      for( $i = 0 ; $i < $event['solve_count'] ; $i++ )
      {
        $pdf->SetX( $starting_x );
        $pdf->SetFont( 'dejavusanscondensed', 'B', 24, '', true );
        $pdf->Cell( 11.6, 10.9, $i + 1, false, 0, 'R', false );
        $pdf->Cell( $cell_sm_width, 10.9, '', true, 0, 'C', false );
        $pdf->Cell( $cell_lg_width + 8.35, 10.9, '', true, 0, 'C', false );
        $pdf->Cell( $cell_sm_width, 10.9, '', true, 0, 'C', false );
        $pdf->Cell( $cell_sm_width, 10.9, '', true, 1, 'C', false );
        $pdf->SetFont( 'dejavusanscondensed', 'B', 1, '', true );
        $pdf->Cell( $cell_sm_width, 1, '', false, 1, 'C', false );
      }

      $pdf->SetX( $starting_x + 5 );
      $pdf->SetFont( 'dejavusanscondensed', '', 10, '', true );
      $pdf->Cell( 30, 6.4, 'Extra attempt', false, 1, 'L', false );

      $pdf->SetFont( 'dejavusanscondensed', 'B', 24, '', true );
      $pdf->SetX( $starting_x );
      $pdf->Cell( 11.6, 10.9, '-', false, 0, 'R', false );
      $pdf->Cell( $cell_sm_width, 10.9, '', true, 0, 'C', false );
      $pdf->Cell( $cell_lg_width + 8.35, 10.9, '', true, 0, 'C', false );
      $pdf->Cell( $cell_sm_width, 10.9, '', true, 0, 'C', false );
      $pdf->Cell( $cell_sm_width, 10.9, '', true, 1, 'C', false );
      
      // Print cumulative limit */
      $pdf->SetFont( 'dejavusanscondensed', '', 10, '', true );
      $pdf->SetXY( $starting_x + 5, $starting_y + $half_page_height - 10 );
      $pdf->Cell( $half_page_width - 10, 6.4, "Time limit: {$cumulative_limit} in total for {$events[0]['display']} and {$events[1]['display']}", false, 0, 'R', false );

      $quarter_page_count++;
      
      unset( $competition_groups[ $person ] ); // Remove person from assignment array when it has been processed
    }
    return array( $pdf, $competition_groups, $current_page );
  }

?>