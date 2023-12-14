<?php

  require_once '../pdf/fpdf.php';
  require_once '../custom-functions.php';
  
  /* Setup strings to generate PDF */
  $is_bld = ( $_POST['event_select'] == 'bld' );
  
  $events_id = array( 
                $is_bld ? '4x4x4 Blindfolded' : '6x6x6', 
                $is_bld ? '5x5x5 Blindfolded' : '7x7x7',
              );
  
  $events_alias = array(
                    $is_bld ? '444bf' : '666',
                    $is_bld ? '555bf' : '777',
                  );
  
  $events_display = array(
                      $is_bld ? '4x4 BLD' : '6x6',
                      $is_bld ? '5x5 BLD' : '7x7',
                    );

  if ( ! empty( $_POST ) )
  {
    $competition_id = get_competition_id_from_url( $_POST['competition_url'] );
  
    [ $competition_data, $error ] = read_competition_data_from_public_wcif( $competition_id );

    if ( ! $error )
    {   
      $time_limit = get_shared_cumulative( $competition_data, $events_alias );
      $assignments_id = get_assignments_id_for_events( $competition_data, $events_id );
      $competition_groups = retrieve_competitors_groups( $competition_data, $events_id, $assignments_id );

      array_multisort( array_column( $competition_groups, $events_id[0] ), SORT_ASC, array_column( $competition_groups, $events_id[1] ), SORT_ASC, array_column( $competition_groups, 'competitor_name'), SORT_ASC, $competition_groups); /* Sort final array by events and then by competitor's name */
        
      $pdf = new FPDF();
      
      [ $pdf, $competition_groups, $is_new_page ] = create_dual_scorecards( $pdf, $competition_data, $competition_groups, $time_limit, $events_display, $events_id );

      $quarter_page_count = $is_new_page ? 2 : 0; /* Check if space remains at the bottom of the current page */

      [ $pdf, $competition_groups, $current_page ] = create_single_scorecards( $pdf, $competition_data, $competition_groups, $time_limit, $events_display, $events_id, $quarter_page_count );
      
      $pdf->Output( 'D', "{$competition_id}_{$events_display[0]} and {$events_display[1]}--" . date( 'Y-m-d' ) . '.pdf' ); /* Generate PDF */
    }
  }
  

?>