<?php

  require_once dirname( __DIR__, 3 ) . '/src/sessions/session-handler.php';
  
  $competition_id = $_GET['id'];
  
  if( $_SESSION['logged_in'] AND ( in_array( $competition_id, array_keys( $_SESSION['manageable_competitions'] ) ) OR $_SESSION['is_admin'] ) )
  {    
    require_once dirname( __DIR__, 3 ) . '/src/mysql/mysql-connect.php';
    require_once dirname( __DIR__, 3 ) . '/src/functions/generic-functions.php';
    require_once dirname( __DIR__, 3 ) . '/src/functions/encrypt-functions.php';
    require_once '../functions/competition-data-functions.php';

    $competition_data = get_competition_data( $competition_id, $conn );

    $columns = array(); /* Create file header */
    $temp_columns = array();

    $query_results = $conn->query( "SELECT * FROM " . DB_PREFIX_CU . "_{$competition_id} ORDER BY user_name ASC;" );
    
    $conn->close();

    if ( $query_results->num_rows )
    { 
      $competition_catalog = from_pretty_json( $competition_data['competition_catalog'] );

      while ( $field = $query_results->fetch_field() )
      { 
        array_push( $columns, trim( $field->name ) ); /* Add new column names to header */
      }

      foreach ( $competition_catalog as $block_name => $block_value )
      {
        foreach ( $block_value as $item_id => $item_value )
        {
          $item_ref = $block_name . '_' . $item_value['item_name'];
          array_push( $temp_columns, $item_ref ); /* Add item name to temporary header */
          
          if ( $item_value['options'] != NULL ) 
          {
            foreach ( $item_value['options'] as $option_name => $option_value ) 
            {
              $option_array = explode( ';', $option_value );
              
              foreach ( $option_array as $option )
              {
                array_push( $temp_columns, $item_ref . '_' . $option ); /* Add options name to temporary header */ 
              }
            }
          }
        }
        array_push( $temp_columns, $block_name . '_given' ); /* Add given status to temporary header for current block */
      }
      
      $columns = array_merge( array_slice( $columns , 0, 4 ), $temp_columns, array_slice( $columns , 5 ) ); /* Remove 'order_data' column and insert temporary header in final header */
      
      $delimiter = ';'; 
      $filename = "{$competition_id}_Commandes_Extract--" . date( 'Y-m-d' ) . '.csv'; 
      
      $f = fopen( 'php://memory', 'w' );
            
      fputcsv( $f, $columns, $delimiter ); /* Write header to buffer */
    
      while ( $row = $query_results->fetch_assoc() ) /* For each order placed */
      {
        $temp_order = array();

        $order = from_pretty_json( $row['order_data'] ); /* Decode order Json */
        
        foreach ( $competition_catalog as $block_name =>$block_value )
        {
          foreach ( $block_value as $item_id => $item_value )
          {           
            $item_qty = $order[ $block_name ][ $item_value['item_name'] ]['qty'] > 0 ? $order[ $block_name ][ $item_value['item_name'] ]['qty'] : ' '; /* Add current item ordered amount in temporary block */

            array_push( $temp_order, $item_qty );
            
            if ( $item_value['options'] ) 
            {
              $selected_options = $order[ $block_name ][ $item_value['item_name'] ]['options']; /* Get name and value of all the selectable options in order */

              foreach( $item_value['options'] as $option_name => $option_value)
              {
                $option_array = explode( ';', $option_value );
                
                foreach ( $option_array as $option ) /* For each options selectable value of the current item */
                {
                  $option_qty = is_array( $selected_options ) ? array_count_values( array_column( $selected_options, $option_name ) )[ $option ] : ' '; /* Extract quantity added for current order */
                  array_push( $temp_order, $option_qty );
                }
              }
            }
          }

          array_push( $temp_order, $order[ $block_name ]['given'] );
        }
        
        $row = array_merge( array_slice( $row, 0, 3 ), array( decrypt_data( $row['user_email'] ) ), $temp_order, array_slice( $row, 5 ) ); /* Remove Order_Data data and insert temporary block in final data */
        fputcsv( $f, $row, $delimiter ); /* Write each order to buffer */
      } 
      
      fseek( $f, 0 ); 
       
      header( 'Content-Type: text/csv' ); 
      header( 'Content-Disposition: attachment; filename="' . $filename . '";' ); 
      ob_end_clean();
      
      fpassthru( $f ); 

    }  
    else
    {
      /* Placeholder */
    }
  }
  else
  {
    header( '' );    
    exit();
  }

?>
