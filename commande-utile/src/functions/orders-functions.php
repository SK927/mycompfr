<?php

  require_once dirname( __DIR__, 3 ) . '/config/config-db.php';
  require_once dirname( __DIR__, 3 ) . '/src/functions/generic-functions.php';
  require_once dirname( __DIR__, 3 ) . '/src/functions/encrypt-functions.php';
  require_once 'competition-data-functions.php';
  require_once 'email-functions.php';


  /**
   * get_competition_orders_and_amounts(): retrieve all orders made for a given competition
   * @param (string) competition_id: ID of the competition to retrieve data for
   * @param (mysqli) mysqli: database connection object
   * @return (mysqli) the competition orders as a mysqli object
   */

  function get_competition_orders( $competition_id, $mysqli )
  {
    $query_results = $mysqli->query( "SELECT * FROM " . DB_PREFIX_CU . "_{$competition_id} ORDER BY user_name ASC;" );

    $competition_orders = array();

    while( $row = $query_results->fetch_assoc() )
    {
      array_push( $competition_orders, $row );
    }

    return $competition_orders;
  }


  /**
   * get_user_order(): retrieve order made by a given user for a given competition
   * @param (string) competition_id: ID of the competition to retrieve data for
   * @param (int) user_id: ID of the user to retrieve competitions for
   * @param (mysqli) mysqli: database connection object
   * @return (array) the user order as an associative array and the user comment
   */

  function get_user_order( $competition_id, $user_id, $mysqli )
  {   
    $user_order_id = hash_data( $user_id, $competition_id );
    $query_results = $mysqli->query( "SELECT * FROM " . DB_PREFIX_CU . "_{$competition_id} WHERE id = '{$user_order_id}';" );

    try
    {
      $result_row = $query_results->fetch_assoc();
    }
    catch( error $e )
    {
      $error = 'Erreur fatale lors de la récupération de la commande';
    }  

    return array( $error, from_pretty_json( $result_row['order_data'] ), $result_row['user_comment'], $result_row['order_total'], $result_row['has_been_modified'] );
  }


  /**
   * delete_user_order(): delete user order for a given competition
   * @param (string) competition_id: ID of the competition to delete the data from
   * @param (string) user_order_id: ID of the user order to delete 
   * @param (mysqli) mysqli: database connection object
   * @return (string) the error of the mysqli query
   */

  function delete_user_order( $competition_id, $user_order_id, $mysqli )
  {
    $query_results = $mysqli->query( "SELECT * FROM " . DB_PREFIX_CU . "_{$competition_id} WHERE id = '{$user_order_id}';" );
    $user_data = $query_results->fetch_assoc();

    $mysqli->query( "DELETE FROM " . DB_PREFIX_CU . "_{$competition_id} WHERE id = '{$user_order_id}';" );
    
    if ( ! mysqli_error( $mysqli ) )
    {
      $competition_data = get_competition_data( $competition_id, $mysqli );
      $error = send_order_cancellation( $competition_data, $user_order_id, $user_data['user_email'], $user_data['user_name'] );
    }
    else
    {
      $error = mysqli_error( $mysqli );
    }

    return $error;
  }


  /**
   * save_user_order(): save the user order for a given competition in the database 
   * @param (string) competition_id: ID of the competition to save the data for
   * @param (string) user_order_id: ID of the user order to save
   * @param (array) user_data: generic user data such as name, e-mail address...
   * @param (array) order_data: data representing the items selected by the user
   * @param (bool) is_edit: value used to indicate that the order is being edited not created (optional)
   * @param (mysqli) mysqli: database connection object
   * @return (string) the error of the mysqli query
   */

  function save_user_order( $competition_id, $user_order_id, $user_data, $order_data, $is_edit = false, $mysqli )
  { 
    if ( $order_data )
    {
      $competition_data = get_competition_data( $competition_id, $mysqli );
      $catalog = from_pretty_json( $competition_data['competition_catalog'] );

      $user_order = array();
      $user_name_escaped = mysqli_real_escape_string( $mysqli, $user_data['user_name'] );

      $pattern = "/[<>={}]/";
      $user_comment = preg_replace( $pattern, '', $order_data['user_comment']);
      $user_comment_escaped = mysqli_real_escape_string( $mysqli, $user_comment );
      unset( $order_data['user_comment'] );

      foreach ( $order_data as $key => $value ) 
      {
        $id = explode( '-', $key );

        switch ( count( $id ) )
        {
          case 2:
            $user_order[ $id[0] ]['items'][ $id[1] ]['qty'] = $value;
            break;
          case 3:
            $pos = strpos($id[0], '_') + 1;
            $number = substr( $id[0], 0, $pos );
            $blockId = substr( $id[0], $pos, 2 ); 

            $value = explode( '(+', $value );
            $name = $value[0];
            $price = rtrim( $value[1], '€)' );

            $user_order[ $blockId ]['items'][ $id[1] ]['options'][ $number ][ $id[2] ] = $name;
            break;
          default:
            break;
        }
      }

      foreach ( $user_order as $block_key => $block )
      {
        $user_order[ $block_key ]['given'] = 0;

        foreach ( $block['items'] as $item_key => $item )
        {
          $amount += $item['qty'] * $catalog[ $block_key ]['items'][ $item_key ]['price'];

          foreach ( $item['options'] as $option )
          {
            foreach ( $option as $option_key => $selection_key ) 
            {
              $amount += $catalog[ $block_key ]['items'][ $item_key ]['options'][ $option_key ]['selections'][ $selection_key ]['price'];
            }
          }
        }
      }

      $order_json = to_pretty_json( $user_order );

      $sql = "REPLACE INTO ". DB_PREFIX_CU . "_{$competition_id} VALUE ('{$user_order_id}', '{$user_name_escaped}', '{$user_data['user_wca_id']}', '{$user_data['user_email']}', '{$order_json}', {$amount}, '{$user_comment_escaped}', 0, 0);";

      $mysqli->query( $sql );

      if ( ! mysqli_error( $mysqli ) )
      {
        $error = send_order_confirmation( $competition_data, $user_order_id, $user_data['user_email'], $user_data['user_name'], $user_order, $user_comment, $amount, $is_edit );
      }
      else
      {
        $error = mysqli_error( $mysqli );
      }
    }
    else
    {
      $error =  'Commande vide';
    }    
    
    return $error;
  }


  /**
   * search_for_block_items_only(): search for all items selected for a given block
   * @param (string) block_name: name of the block to search items for
   * @param (array) items: associative array of all items selected by user
   * @return (array) items selected by user and pertaining to the given block 
   */

  function search_for_block_items_only( $block_id, $items )
  {
    $pattern = '/.*' . preg_quote( $block_id, '/' ) . '.*/'; 
    return array_intersect_key( $items, array_flip( preg_grep( $pattern, array_keys( $items ) ) ) );
  }


  /**
   * get_items_amounts(): get an array of all the ordered items and their amount for a given competition
   * @param (string) competition_id: ID of the competition to retrieve the data for
   * @param (mysqli) mysqli: database connection object
   * @return (array) the ordered items and their amount
   */

  function get_items_amount( $competition_id, $mysqli )
  {
    $competition_data = get_competition_data( $competition_id, $mysqli );
    $catalog = from_pretty_json( $competition_data['competition_catalog'] );

    $items_amount = array();

    $competition_orders = get_competition_orders( $competition_id, $mysqli );

    foreach ( $competition_orders as $order )
    { 
      foreach ( from_pretty_json( $order['order_data'] ) as $block_key => $block )
      {
        $block_name = $catalog[ $block_key ]['name'];

        foreach ( $block['items'] as $item_key => $item )
        {
          $item_name = $catalog[ $block_key ]['items'][ $item_key ]['name'];
          
          if ( ! isset( $items_amount[ $block_name ][ $item_name ] ) ) $items_amount[ $block_name ][ $item_name ] = 0;
          $items_amount[ $block_name ][ $item_name ] += (int) $item['qty']; 

          foreach ( $item['options'] as $option )
          {
            foreach ( $option as $selection_key => $selection ) 
            {
              $selection_name = $catalog[ $block_key ]['items'][ $item_key ]['options'][ $selection_key ]['selections'][ $selection ]['name'];

              if ( ! isset( $items_amount[ $block_name ][ $selection_name ] ) ) $items_amount[ $block_name ][ $selection_name ] = 0;
              $items_amount[ $block_name ][ $selection_name ] += 1;
            }
          }
        }
      }   
    }

    return $items_amount;
  }

?>