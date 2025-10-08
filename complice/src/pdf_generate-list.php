<?php

  require_once dirname( __DIR__, 2 ) . '/src/sessions_handler.php';

  $competition_id = $_GET['id'];

  if ( in_array( $competition_id, array_keys( $_SESSION['manageable_competitions'] ) ) )
  { 
    require_once dirname( __FILE__ ) . '/_functions.php';

    [ $returning_competitors, $new_competitors, $error ] = retrieve_competitors( $competition_id, $_SESSION['user_token'] );

    if ( ! $error )
    {
      $pdf = create_new_pdf();
      $pdf->SetTitle( $title );
      [ $pdf, $space_left_at_bottom ] = print_returning_competitors( $pdf, $returning_competitors );
      $pdf = print_new_competitors( $pdf, $new_competitors, $space_left_at_bottom );
      $title = "{$competition_id}_Compétiteurs_" . date( 'Y-m-d' ) . ".pdf";
      $pdf->Output( $title, 'I' );
    }
  }
  else
  {
    echo 'Vous ne pouvez pas accéder aux informations privées de cette compétition!';
  }
  
?>