<?php

  error_reporting( E_ERROR );
  ini_set( "display_errors", 1 );

  require_once dirname( __DIR__, 2 ) . '/src/sessions_handler.php';
  
  $competition_id = $_GET['id'];
  $is_manageable = isset( $_SESSION['manageable_competitions'][ $competition_id ] );

  if( $is_manageable or $_SESSION['is_admin'] )
  {
    require_once dirname( __FILE__ ) . '/_functions.php';

    mysqli_open( $mysqli );
    $competition = get_competition_data( $competition_id, $mysqli );
    $catalog = get_catalog( $competition_id, $mysqli ); 
    [ $all_orders, $total ] = get_all_orders( $competition_id, $mysqli );

    if( $all_orders )
    {
      $columns = array();
      $temp_columns = array();

      $sql = "SELECT *
              FROM INForMATION_SCHEMA.COLUMNS
              WHERE TABLE_NAME = N'{$db['cu']}_Orders_Info'
              orDER BY orDINAL_POSITION";

      $results = $mysqli->query( $sql );

      while( $row = $results->fetch_assoc() )
      {    
        array_push( $columns, trim( $row['COLUMN_NAME'] ) ); // Add new column names to header 
      }

      foreach( $catalog as $block_key => $block )
      {
        foreach( $block['items'] as $item_key => $item )
        {
          $item_ref = "{$block['name']}_{$item['name']}";
          array_push( $temp_columns, $item_ref ); // Add item name to temporary header 
          
          if( $item['options'] )
          {
            foreach( $item['options'] as $option_key => $option )
            {
              foreach( $option['selections'] as $selection_key => $selection )
              {
                array_push( $temp_columns, "{$item_ref}_{$option['name']}_{$selection['name']}" ); // Add options name to temporary header  
              }
            }
          }
        }
        array_push( $temp_columns, "{$block['name']}_given" ); // Add given status to temporary header for current block 
      }
      
      $columns = array_merge( array_slice( $columns , 0, 5 ), $temp_columns, array_slice( $columns , 6 ) ); // Remove 'order_data' column and insert temporary header in final header 
      $delimiter = ';'; 
      $filename = "{$competition_id}_Commandes_Extract--" . date( 'Y-m-d' ) . '.csv'; 
      $f = fopen( 'php://memory', 'w' ); 
      fputcsv( $f, $columns, $delimiter ); // Write header to buffer 
    
      foreach( $all_orders as $order )
      {
        $order['user_email'] = decrypt_data( $order['user_email'] );
        
        $row = array();
        
        foreach( $catalog as $block_key => $block )
        {
          foreach( $block['items'] as $item_key => $item )
          {
            array_push( $row, $order['content'][ $block_key ]['items'][ $item_key ]['qty'] ); // Add item name to temporary header 
            
            if( $item['options'] )
            {
              foreach( $item['options'] as $option_key => $option )
              {
                foreach( $option['selections'] as $selection_key => $selection )
                {
                  array_push( $row, $order['content'][ $block_key ]['items'][ $item_key ]['options'][ $option_key ][ $selection_key]['qty'] ); // Add options name to temporary header  
                }
              }
            }
          }
          array_push( $row, "{$order['content'][ $block_key ]['given']}" ); // Add given status to temporary header for current block 
        }
        unset( $order['content'] );
        $row = array_merge( array_slice( $order, 0, 5 ), $row, array_slice( $order, 6 ) ); // Remove Order_Data data and insert temporary block in final data 
        fputcsv( $f, $row, $delimiter ); // Write each order to buffer 
      }
      
      fseek( $f, 0 ); 
      header( 'Content-Type: text/csv' ); 
      header( 'Content-Disposition: attachment; filename="' . $filename . '";' ); 
      ob_end_clean();
      fpassthru( $f ); 
    }
    else
    {
      echo "Aucune commande passée pour le moment... Reviens plus tard :)";
    }  
    
    $mysqli->close();
  }
  else
  {
    echo "ERREUR : Vous n'êtes pas connecté ou vous n'êtes pas organisateur de cette compétition !";
  }

?>
