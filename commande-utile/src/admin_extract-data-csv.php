<?php

  require_once dirname( __DIR__, 2 ) . '/src/sessions_handler.php'; // $db is loaded here!
  
  $competition_id = $_GET['id'];
  
  if( in_array( $competition_id, array_keys( $_SESSION['manageable_competitions'] ) ) OR $_SESSION['is_admin'] )
  {    
    require_once dirname( __DIR__, 2 ) . '/src/mysql_connect.php';
    require_once dirname( __DIR__, 2 ) . '/src/_functions-generic.php';
    require_once dirname( __DIR__, 2 ) . '/src/_functions-encrypt.php';
    require_once dirname( __FILE__ ) . '/_functions-competition-data.php';

    $competition_data = get_competition_data( $competition_id, $conn );
    $columns = array(); // Create file header 
    $temp_columns = array();
    $query_results = $conn->query( "SELECT * FROM {$db['cu']}_{$competition_id} ORDER BY user_name ASC;" );
  
    $conn->close();

    if ( $query_results->num_rows )
    { 
      $catalog = from_pretty_json( $competition_data['competition_catalog'] );

      while ( $field = $query_results->fetch_field() )
      {
        array_push( $columns, trim( $field->name ) ); // Add new column names to header 
      }

      foreach ( $catalog as $block_key => $block )
      {
        foreach ( $block['items'] as $item_key => $item )
        {
          $item_ref = "{$block['name']}_{$item['name']}";
          array_push( $temp_columns, $item_ref ); // Add item name to temporary header 
          
          if ( $item['options'] )
          {
            foreach ( $item['options'] as $option_key => $option )
            {
              foreach ( $option['selections'] as $selection_key => $selection )
              {
                array_push( $temp_columns, "{$item_ref}_{$option['name']}_{$selection['name']}" ); // Add options name to temporary header  
              }
            }
          }
        }
        array_push( $temp_columns, "{$block['name']}_given" ); // Add given status to temporary header for current block 
      }
      
      $columns = array_merge( array_slice( $columns , 0, 4 ), $temp_columns, array_slice( $columns , 5 ) ); // Remove 'order_data' column and insert temporary header in final header 
      $delimiter = ';'; 
      $filename = "{$competition_id}_Commandes_Extract--" . date( 'Y-m-d' ) . '.csv'; 
      $f = fopen( 'php://memory', 'w' ); 
      fputcsv( $f, $columns, $delimiter ); // Write header to buffer 
    
      while ( $row = $query_results->fetch_assoc() ) // For each order placed 
      {
        $temp_order = array();

        $order = from_pretty_json( $row['order_data'] ); // Decode order Json 
        
        foreach ( $catalog as $block_key =>$block )
        {
          foreach ( $block['items'] as $item_key => $item )
          {           
            $item_qty = $order[ $block_key ]['items'][ $item_key ]['qty'] > 0 ? $order[ $block_key ]['items'][ $item_key ]['qty'] : ' '; // Add current item ordered amount in temporary block 

            array_push( $temp_order, $item_qty );
            
            if ( $item['options'] ) 
            {
              $selection_options = array();

              foreach ( $order[ $block_key ]['items'][ $item_key ]['options'] as $possible_option )
              {
                foreach ( $possible_option as $selected_key => $selected )
                {
                  if ( isset( $selection_options[ $selected_key ][ $selected ] ) )
                  {
                    $selection_options[ $selected_key ][ $selected ] = $selection_options[ $selected_key ][ $selected ] + 1;
                  }
                  else
                  {
                    $selection_options[ $selected_key ][ $selected ] = 1;
                  }
                }
              }

              foreach ( $item['options'] as $option_key => $option)
              {
                foreach ( $option['selections'] as $selection_key => $selection )
                {
                  $selected_qty = isset( $selection_options[ $option_key ][ $selection_key ] ) ? $selection_options[ $option_key ][ $selection_key ] : '';

                  array_push( $temp_order, $selected_qty );
                }
              }
            }
          }
          array_push( $temp_order, $order[ $block_key ]['given'] );
        }
        $row = array_merge( array_slice( $row, 0, 3 ), array( decrypt_data( $row['user_email'] ) ), $temp_order, array_slice( $row, 5 ) ); // Remove Order_Data data and insert temporary block in final data 
        fputcsv( $f, $row, $delimiter ); // Write each order to buffer 
      } 
      
      fseek( $f, 0 ); 
      header( 'Content-Type: text/csv' ); 
      header( 'Content-Disposition: attachment; filename="' . $filename . '";' ); 
      ob_end_clean();
      fpassthru( $f ); 
    }  
  }
  else
  {
    header( '' );    
    exit();
  }

?>
