<?php

  require_once dirname( __DIR__, 3 ) . '/src/sessions/session-handler.php';
  require_once dirname( __DIR__, 3 ) . '/src/mysql/mysql-connect.php';
  require_once dirname( __DIR__, 3 ) . '/src/functions/generic-functions.php';
  require_once dirname( __DIR__, 3 ) . '/src/functions/encrypt-functions.php';
  require_once '../functions/competition-data-functions.php';
  require_once 'tfpdf.php';
  
  $competition_id = $_GET['id'];
  $competition_data = get_competition_data( $competition_id, $conn ); 

  // Instanciation of inherited class
  $pdf = new CustomPDF();
  $pdf->title = utf8_decode( $competition_data['competition_name'] );
  $pdf->AliasNbPages();

  $pdf->SetFont( 'Times', 'B', 14 );

  $pdf->SetFillColor( 0, 136, 88 );
  $pdf->SetDrawColor( 0, 136, 88 );

  foreach ( from_pretty_json( $competition_data['competition_catalog'] ) as $block_name => $block_value )
  {
    $pdf->AddPage( 'P', 'A4' );
    $pdf->SetTextColor( 255 );
    $pdf->SetWidths( array( 190 ) );

    $pdf->SetFont( 'Times', 'B', 14 );
    $pdf->RowList( array( utf8_decode( $block_name ) ), true, 10 );
    
    $pdf->SetFont( 'Times', '', 12 );
    $pdf->SetTextColor( 0 );

    $pdf->SetWidths( array( 40, 120, 30 ) );
          
    $text = "\n";
    foreach( $block_value as $item_key => $item_value )
    { 
      if ( $item_value['options'] != null ) /* If item has options display info about options */
      { 
        $txt = "\n\nInclut : ";
        foreach ( $item_value['options'] as $option_key => $option_value)
        {
          $txt .= "\n\t\t\t>> {$option_key} (" . str_replace( ";", " ; ", $option_value ) . ")"; /* Display options choices */
        }
      }
      else
      {
        $txt = "";
      }
      $pdf->RowList( array( utf8_decode( $item_value['item_name'] ), utf8_decode( $item_value['item_descr'] ) . utf8_decode($txt), number_format( $item_value['item_price'], 2 ) . ' EUR' ), false, 5 );
    }
  }

  $conn->close();

  $pdf->Output( 'D', "{$competition_id}_Liste_Produits--" . date( 'Y-m-d' ) . '.pdf' );

?>