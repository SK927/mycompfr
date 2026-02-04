<?php

  require_once dirname( __FILE__ ) . '/_functions.php';
  
  $competition_id = get_competition_id( $_POST ); 

  if ( $competition_id )
  {
    $selected = $_POST['event_select'];
    $events = get_events();
    $events = array(
                $events[ $selected[0] ],
                $events[ $selected[1] ]
              );

    array_multisort( array_column( $events, 'solve_count' ), SORT_DESC, $events );

    [ $competition_data, $error ] = read_competition_data_from_public_wcif( $competition_id );

    if ( ! $error )
    {   
      $cumulative_limit = get_shared_cumulative( $competition_data, $events );
      $assignments_id = get_assignments_id_for_events( $competition_data, $events );
      $competition_groups = retrieve_competitors_groups( $competition_data, $events, $assignments_id );

      // Sort final array by events and then by competitor's name
      array_multisort( array_column( $competition_groups, $events[0]['long_name'] ), SORT_ASC, array_column( $competition_groups, $events[1]['long_name'] ), SORT_ASC, array_column( $competition_groups, 'competitor_name'), SORT_ASC, $competition_groups);

      $pdf = create_new_pdf();
      $pdf->SetFillColor( 255, 255, 255 );
  
      $title = "{$competition_data['id']}_{$events[0]['display']} and {$events[1]['display']}--" . date( 'Y-m-d' ) . '.pdf';

      $pdf->SetTitle( $title );

      [ $pdf, $competition_groups, $is_new_page ] = create_dual_scorecards( $pdf, $competition_data, $competition_groups, $cumulative_limit, $events );

      $quarter_page_count = $is_new_page ? 2 : 0; // Calculate remaining space at the bottom of the current page

      [ $pdf, $competition_groups, $current_page ] = create_single_scorecards( $pdf, $competition_data, $competition_groups, $cumulative_limit, $events, $quarter_page_count );
      
      $pdf->Output( $title, 'I' );
    }
  }
  else
  {
    echo 'Competition ID not defined!';
  }
  
?>