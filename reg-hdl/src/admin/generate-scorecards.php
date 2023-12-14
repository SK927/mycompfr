<?php

  require_once dirname( __DIR__, 3 ) . '/src/sessions/session-handler.php';
  require_once dirname( __DIR__, 3 ) . '/src/functions/encrypt-functions.php';

  $competition_id = decrypt_data( $_POST['competition_id'] );

  if ( $_SESSION['logged_in'] AND ! empty( $_POST ) AND in_array( $competition_id, $_SESSION['manageable_competitions'] ) )
  {
    require_once '../config.php';

    require_once dirname( __DIR__, 3 ) . '/src/mysql/mysql-connect.php';
    require_once '../custom-functions.php'; 
    require_once '../pdf/tfpdf.php';
    
    [ $competition_name ] = get_competition_data_from_db( $competition_id, $conn );
    
    $user_id = decrypt_data( $_POST['user_id'] );
    $user_registrant_id = $_POST['user_registrant_id'];
    
    unset( $_POST['competition_id'] );
    unset( $_POST['user_id'] );
    unset( $_POST['user_registrant_id'] );
    unset( $_POST['printed'] );

    $query_results = $conn->query( "SELECT competition_registrations FROM " . DB_PREFIX . "_Main WHERE competition_id = '{$competition_id}';" );

    $result_row = from_pretty_json( $query_results->fetch_assoc()['competition_registrations'] );
    $user_data = $result_row[ $user_id ]['user_data'];

    // Instanciation of inherited class
    $pdf = new tFPDF();
    $pdf->title = "{$competition_id}_{$user_data['registration_data[name]']}_Scorecards--" . date( 'Y-m-d' ) . '.pdf';
    $pdf->SetAutoPageBreak( false, 0 );

    $pdf->AddFont( 'DejaVu', '', 'arialuni.ttf', true );
    
    $count = 0;
    $offset = 0;
    $offset_x = array( 0, 105, 0, 105 );
    $offset_y = array( 0, 0, 148.5, 148.5 );
    
    $events = array(
                'e333' => '3x3x3', 
                'e222' => '2x2x2', 
                'e444' => '4x4x4', 
                'e555' => '5x5x5', 
                'e666' => '6x6x6', 
                'e777' => '7x7x7', 
                'e333oh' => '3x3x3 One-Handed', 
                'e333bf' => '3x3x3 Blindfolded', 
                'e333fm' => 'Fewest moves', 
                'eclock' => 'Clock', 
                'eminx' => 'Megaminx', 
                'epyram' => 'Pyraminx', 
                'eskewb' => 'Skewb', 
                'esq1' => 'Square-1', 
                'e444bf' => '4x4x4 Blindfolded', 
                'e555bf' => '5x5x5 Blindfolded', 
                'e333mbf' => 'Multi-blind',
              );
    
    foreach ( $_POST as $key => $value )
    {
      $key = str_replace( $user_id, '', $key );
      $event = $events[ $key ];
      
      $current_scorecard_number = $count % 4; /* Make the PDF so that 4 scorecards are displayed on one page */

      if ( $current_scorecard_number == 0 )
      {
        $pdf->AddPage( 'P', 'A4' );
        $pdf->SetDrawColor(  127, 127, 127 );
        $pdf->SetDash(  2, 2 );
        $pdf->Line( 0, 148.5, 210, 148.5  );
        $pdf->Line( 105, 0, 105, 297 );
        $pdf->SetDash();
      }
      
      $pdf->SetFont( 'DejaVu','',16 );
      $pdf->SetXY( $offset_x[ $current_scorecard_number ], 3.70 + $offset_y[ $current_scorecard_number ] + $offset );
      $pdf->Cell( 105, 10.12, $competition_name, false, 0, 'C', false );
      
      $pdf->SetFont( 'DejaVu','',8 );
      $pdf->SetXY( 5 + $offset_x[ $current_scorecard_number ], 16.22 + $offset_y[ $current_scorecard_number ] + $offset );
      $pdf->Cell( 67.86, 3.70, 'Event', false, 0, 'L', false );
      $pdf->Cell( 13.57, 3.70, 'Round', false, 0, 'C', false );
      $pdf->Cell( 13.57, 3.70, 'Group', false, 0, 'C', false );
      
      $pdf->SetXY( 5 + $offset_x[ $current_scorecard_number ], 30.77 + $offset_y[ $current_scorecard_number ] + $offset );
      $pdf->Cell( 13.57, 3.70, 'ID', false, 0, 'L', false );
      $pdf->Cell( 81.43, 3.70, 'Name', false, 0, 'L', false );
      
      $pdf->SetFont( 'DejaVu','',14 );
      $pdf->SetXY( 5 + $offset_x[ $current_scorecard_number ], 21.22 + $offset_y[ $current_scorecard_number ] + $offset );
      $pdf->Cell( 67.86, 7.16, $event, true, 0, 'L', false );
      $pdf->Cell( 13.57, 7.16, 1, true, 0, 'C', false );
      $pdf->Cell( 13.57, 7.16, $value, true, 0, 'C', false );

      $pdf->SetXY( 5 + $offset_x[ $current_scorecard_number ], 35.77 + $offset_y[ $current_scorecard_number ] + $offset );
      $pdf->Cell( 13.57, 7.16, $user_registrant_id, true, 0, 'C', false );
      $pdf->Cell( 81.43, 7.16, substr( $user_data['registration_data[name]'], 0, 30 ), true, 0, 'L', false );
   
      $pdf->SetFont( 'DejaVu','',8 );
      $pdf->SetXY( 12.90 + $offset_x[ $current_scorecard_number ], 53.55 + $offset_y[ $current_scorecard_number ] + $offset );
      $pdf->Cell( 11.49, 4.69, 'Scr', false, 0, 'C', false );
      $pdf->Cell( 52.63, 4.69, 'Result', false, 0, 'C', false );
      $pdf->Cell( 11.49, 4.69, 'Judge', false, 0, 'C', false );
      $pdf->Cell( 11.49, 4.69, 'Comp', false, 0, 'C', false );

      $pattern = '/e666|e777|e333bf|e444bf|e555bf|e333mbf/';

      if( preg_match( $pattern, $key ) ) /* Discriminate events */
      {
        $attempt_number = array( 1, 2, 3, '-');
        $spacing = array( 0, 12.34, 24.68, 41.71 );
      }
      else
      {
        $attempt_number = array( 1, 2, 3, 4, 5, '-' );
        $spacing = array( 0, 12.34, 24.68, 37.02, 49.36, 66.39 );
      }

      $max_attempt = count( $attempt_number ) - 1;

      for ( $attempt = 0 ; $attempt <= $max_attempt ; $attempt++ )
      {
        $pdf->SetXY( 5 + $offset_x[ $current_scorecard_number ], 58.24 + $spacing[ $attempt ] + $offset_y[ $current_scorecard_number ] + $offset );
        $pdf->SetFont( 'DejaVu','',24 );
        $pdf->Cell( 7.90, 10.86, $attempt_number[ $attempt ], false, 0, 'L', false );
        $pdf->SetFont( 'DejaVu','',12 );
        $pdf->Cell( 11.49, 10.86, '', true, 0, 'C', false );
        $pdf->Cell( 52.63, 10.86, '', true, 0, 'C', false );
        $pdf->Cell( 11.49, 10.86, '', true, 0, 'C', false );
        $pdf->Cell( 11.49, 10.86, '', true, 0, 'C', false );
      }
      
      $pdf->SetFont( 'DejaVu','',10 );
      $pdf->SetXY( 6.20 + $offset_x[ $current_scorecard_number ], 52.24 + $spacing[ $max_attempt ] + $offset_y[ $current_scorecard_number ] + $offset );
      $pdf->Cell( 29.12, 6.42, 'Extra attempt', false, 0, 'L', false );
          
      $count++;
    }

    $conn->close();
  }

  $pdf->Output( 'D', utf8_decode( $competition_name ) . "_{$user_data['registration_data[name]']}_Scorecards--" . date( 'Y-m-d' ) . '.pdf' );
  
?>