<?php

  require_once dirname( __DIR__, 2 ) . '/src/mysqli.php'; // $db is loaded here!
  require_once dirname( __DIR__, 2 ) . '/src/_functions-generic.php';
  require_once dirname( __DIR__, 2 ) . '/src/_functions-encrypt.php';



  // ============================================================================================================
  //
  //  GENERIC FUNCTIONS
  //
  // ============================================================================================================

  /**
   * sanitize_value_manual(): makes sure the value entered manually do not contain any of the given characters
   * @param (string) value: the string or value to sanitize
   * @return (string) the sanitized value
   */

  function sanitize_value_manual( $value )
  {
    $pattern = "/[<>={}]/";
    return preg_replace( $pattern, '', $value );
  }

  /**
   * sanitize_value_csv(): makes sure the value entered via CSV do not contain any of the given characters
   * @param (string) value: the string or value to sanitize
   * @return (string) the sanitized value
   */

  function sanitize_value_csv( $value )
  {
    // Test it and see if it is UTF-8 or not
    $utf8 = \mb_detect_encoding( $value, ['UTF-8'], true );

    if( $utf8 !== false )
    {
      return $value;
    }
    else
    {
      return utf8_encode( $value );
    }
  }


  // ============================================================================================================
  //
  //  COMPETITIONS RELATED FUNCTIONS
  //
  // ============================================================================================================

  require_once dirname( __DIR__, 2 ) . '/src/_functions-wcif.php';


  /**
   * get_competitors_list_via_wcif(): retrieve competitors list from WCA and updates it in database
   * @param (string) competition_id: ID of the competition to update the data for
   * @return (array) the list of all competitor and the WCIF access error
   */

  function get_competitors_list_via_wcif( $competition_id  )
  {
    [ $competitors, $error ] = get_competitors_from_public_wcif( $competition_id );

    if( ! $error )
    {
      $formatted_list = format_competitors_list( $competitors );
    }

    return [ $formatted_list, $error ];
  }


  /**
   * format_competitors_list(): format the competitors list from WCA to the format used in database
   * @param (array) competitors_list: list of competitors in WCA associative array format
   * @return (string) the formatted list as a string to be stored in database
   */

  function format_competitors_list( $competitors_list )
  {
    $formatted_list = '';

    foreach( $competitors_list as $competitor )
    {
      $formatted_list .= "[{$competitor['wcaUserId']}]";
    }

    return $formatted_list;
  }


  /**
   * insert_competition_into_db(): read competition data from WCA, format it and store in database
   * @param (string) competition_id: ID of the competition to store the data for
   * @param (string) contact_email: contact email provided by organizers/delegates
   * @param (mysqli) mysqli: database connection object
   * @return (string) the error of the mysqli query
   */

  function insert_competition_into_db( $competition_id, $contact_email, $mysqli )
  {  
    global $db;

    [ $competition_data, $error ] = read_competition_data_from_public_wcif( $competition_id );

    if( ! $error )
    {
      $contact_email = encrypt_data( $contact_email );
      $competition_name = addslashes( $competition_data['name'] );
      $competition_start_date = $competition_data['schedule']['startDate'];
      $competition_end_date = date( 'Y-m-d', strtotime( $competition_start_date . ' + ' . (int)($competition_data['schedule']['numberOfDays'] - 1) . ' days' ) );
    
      $competitors_list = format_competitors_list( $competition_data['persons'] );

      $sql = "REPLACE INTO {$db['cu']}_Competitions (id, name, contact, information, competitors_list, start_date, end_date, orders_closing_date) VALUES ('{$competition_id}', '{$competition_name}', '{$contact_email}', '', '{$competitors_list}', '{$competition_start_date}', '{$competition_end_date}', '0000-00-00')";
      
      if( $mysqli->query( $sql ) ) 
      {
        $error = mysqli_error( $mysqli );
      }
    }
  
    return $error;
  }


  /**
    * get_competition_data(): retrieve the competition date from database
    * @param (string) competition_id: the id of the competition to retrieve data for
    * @param (mysqli) mysqli: mysqli object
    * @return (associative array) the competition data
    */

  function get_competition_data( $competition_id, $mysqli )
  {
    global $db;

    $sql = "SELECT * FROM {$db['cu']}_Competitions WHERE id = '{$competition_id}'";
    
    return $mysqli->query( $sql )->fetch_assoc();
  }


  /**
    * get_user_competitions(): retrieve user competitions, whether they are imported or not, and distribute them in the corresponding array
    * @param (int) user: the user WCA user ID
    * @param (mysqli) mysqli: mysqli object
    * @return (array) both array, one for the imported competitions, the other one for the importable ones
    */

  function get_user_competitions( $user, $mysqli )
  {
    global $db;

    if( $user['is_admin'] )
    {
      $clause .= "WHERE 1";
    }
    else if( $user['manageable_competitions'] )
    {
      $manageable_competitions = "('" . implode( "', '", array_keys( $user['manageable_competitions'] ) ) . "')";
      $clause .= "WHERE id IN {$manageable_competitions} or competitors_list LIKE '%[{$user['user_id']}]%'";      
    }
    else
    {
      $clause = "WHERE competitors_list LIKE '%[{$user['user_id']}]%'";
    }
                                                                                                                                                                                                                                   
    $sql = "SELECT c.id as competition_id, c.name as competition_name, orders_closing_date, (cc.alias IS NOT NULL) as has_catalog, (o.id IS NOT NULL) as has_ordered FROM {$db['cu']}_Competitions as c
            LEFT OUTER JOIN {$db['cu']}_Orders_Info as o ON c.id = o.competition_id AND o.user_id = {$user['user_id']}
            LEFT OUTER JOIN {$db['cu']}_Catalogs as cc ON c.id = cc.competition_id AND cc.alias = 'b0'
            WHERE c.id IN (SELECT id FROM {$db['cu']}_Competitions {$clause} )
            ORDER BY c.start_date, competition_name";

    $now = strtotime( date( 'Y-m-d' ) );

    if( $results = $mysqli->query( $sql ) )
    {
      $imported_competitions = array();
      $importable_competitions = $user['manageable_competitions'];
      
      while( $competition = $results->fetch_assoc() )
      {
        $closing_date = strtotime( $competition['orders_closing_date'] );

        if( $competition['has_catalog'] and $competition['orders_closing_date'] != '0000-00-00' )
        {
          if( $now <= $closing_date )
          {
            $competition['orders_status_class'] = 'open';
            $competition['orders_status_text'] = "Jusqu'au {$competition['orders_closing_date']}";
          }
          else
          {
            $competition['orders_status_class'] = 'closed';
            $competition['orders_status_text'] = 'Commandes closes';
          }
        }
        else
        {
          $competition['orders_status_class'] = 'pending';
          $competition['orders_status_text'] = 'Compétition non configurée';
        }

        if( isset( $user['manageable_competitions'][ $competition['competition_id'] ] ) or $user['is_admin'] )
        {
          $competition['can_manage'] = true;
        }

        $imported_competitions[ $competition['competition_id'] ] = $competition;
        unset( $importable_competitions[ $competition['competition_id'] ] );
      }
    }

    foreach( $importable_competitions as $id => $competition )
    {
      if( ! $competition['announced'] or $competition['start'] <= date( 'Y-m-d' ) )
      {
        unset( $importable_competitions[ $id ] );
      }
    }

    return [ $imported_competitions, $importable_competitions ];
  }



  // ============================================================================================================
  //
  //  CATALOG RELATED FUNCTIONS
  //
  // ============================================================================================================

  /**
    * get_catalog(): retrieve the competition catalog
    * @param (string) competition_id: the id of the competition to retrieve catalog for
    * @param (mysqli) mysqli: mysqli object
    * @return (array) the competition catalog
    */

  function get_catalog( $competition_id, $mysqli )
  {
    global $db;

    $catalog = array();

    $sql = "SELECT * FROM {$db['cu']}_Catalogs WHERE competition_id = '{$competition_id}' ORDER BY alias";

    $catalog_items = $mysqli->query( $sql );

    while( $item = $catalog_items->fetch_assoc() )
    {
      $alias = explode( '-', $item['alias'] );

      switch( count( $alias ) ) 
      {
        case 1:
          $catalog[ $alias[0] ]['name'] = $item['name'];
          break;
        case 2:
          $catalog[ $alias[0] ]['items'][ $alias[1] ]['name'] = $item['name'];
          $catalog[ $alias[0] ]['items'][ $alias[1] ]['price'] = $item['price'];
          $catalog[ $alias[0] ]['items'][ $alias[1] ]['description'] = $item['description'];
          $catalog[ $alias[0] ]['items'][ $alias[1] ]['image'] = $item['image'];
          break;
        case 3:
          $catalog[ $alias[0] ]['items'][ $alias[1] ]['options'][ $alias[2] ]['name'] = $item['name'];
          break;
        default:
          $catalog[ $alias[0] ]['items'][ $alias[1] ]['options'][ $alias[2] ]['selections'][ $alias[3] ]['name'] = $item['name'];
          $catalog[ $alias[0] ]['items'][ $alias[1] ]['options'][ $alias[2] ]['selections'][ $alias[3] ]['price'] = $item['price'];
          break;
      }
    }

    return $catalog;
  }



  // ============================================================================================================
  //
  //  ORDERS RELATED FUNCTIONS
  //
  // ============================================================================================================

  /**
    * save_order(): store the order in the database
    * @param (string) competition_id: id of the competition to store the order for
    * @param (array) user_data: data of the user saving an order
    * @param (string) post_data: the data to be stored in the database, from the main form
    * @param (mysqli) mysqli: mysqli object
    * @return (string) error of the function
    */

  function save_order( $competition_id, $user_data, $post_data, $mysqli )
  {
    global $db;

    if( $post_data )
    {
      $order_id = hash_data( $competition_id, $user_data['user_id'] );

      $sql = "DELETE FROM {$db['cu']}_Orders_Items WHERE order_id = '{$order_id}'";
      
      if( $mysqli->query( $sql ) )
      {
        $user_name_escaped = mysqli_real_escape_string( $mysqli, $user_data['user_name'] );
        [ $user_comment_escaped, $ordered_items ] = format_post_data( $post_data, $mysqli );
        $order_total = get_total_cost( $competition_id, $ordered_items, $mysqli );

        if( ! empty( $ordered_items ) )
        {
          $sql = '';

          foreach( $ordered_items as $alias => $qty )
          {
            $sql .= "INSERT INTO {$db['cu']}_Orders_Items (order_id, alias, qty) VALUES ('{$order_id}', '{$alias}', {$qty});";
          }

          $sql .= "REPLACE INTO {$db['cu']}_Orders_Info (competition_id, id, user_id, user_name, user_wca_id, user_email, user_comment, order_total)
                  VALUES ('{$competition_id}', '{$order_id}', '{$user_data['user_id']}', '{$user_name_escaped}', '{$user_data['user_wca_id']}', '{$user_data['user_email']}', '{$user_comment_escaped}', {$order_total})";

          $mysqli->multi_query( $sql );
          while($mysqli->next_result());
        }
        else
        {
          $error = 'Aucun produit sélectionné !';
        }
      }

      $error = $mysqli->error;  
    }
    else
    {
      $error = 'Données vides !';
    }

    return $error;
  }


  /**
    * delete_order(): delete the order for the order id provided
    * @param (string) order_id: id of the order to delete from the database
    * @param (mysqli) mysqli: mysqli object
    * @return (string) error of the function
    */

  function delete_order( $order_id, $mysqli )
  {
    global $db;

    $sql = "SELECT id, user_name, user_email FROM {$db['cu']}_Orders_Info WHERE id = '{$order_id}'";

    if( $result = $mysqli->query( $sql ) )
    {
      $order_info = $result->fetch_assoc();

      $sql = "DELETE FROM {$db['cu']}_Orders_Items WHERE order_id = '{$order_id}';
              DELETE FROM {$db['cu']}_Orders_Info WHERE id = '{$order_id}'";

      $mysqli->multi_query( $sql );
      while($mysqli->next_result());
    }

    return [ $mysqli->error, $order_info ];
  }


  /**
    * get_order(): retrieve the order associated to the order number
    * @param (string) order_id: the order id to retrieve data for
    * @param (mysqli) mysqli: mysqli object
    * @return (array) the order
    */

  function get_order( $order_id, $mysqli )
  {
    global $db;

    $sql = "SELECT * FROM {$db['cu']}_Orders_Info WHERE id = '{$order_id}'";
    
      
    if( $result = $mysqli->query( $sql ) )
    {
      $order = $result->fetch_assoc();

      $sql = "SELECT c.type, i.alias, c.name, i.qty, (i.qty * c.price) as total_cost FROM {$db['cu']}_Orders_Items as i 
              LEFT OUTER JOIN {$db['cu']}_Orders_Info as o ON o.id = i.order_id
              LEFT OUTER JOIN {$db['cu']}_Catalogs as c ON i.alias = c.alias AND o.competition_id = c.competition_id
              WHERE i.order_id = '{$order_id}'
              ORDER BY i.alias";
      $items = $mysqli->query( $sql );
      $ordered_items = array();

      while( $item = $items->fetch_assoc() )
      {
        array_push( $ordered_items, $item );
      }

      if( $ordered_items )
      {    
        $order['content'] = format_items_to_order( $ordered_items );
      }
    }

    return $order;
  }


  /**
    * get_all_orders(): retrieve all order associated to a given competition id
    * @param (string) competition_id: the id of the competition to retrieve orders for
    * @param (mysqli) mysqli: mysqli object
    * @return (array) all the competition orders
    */

  function get_all_orders( $competition_id, $mysqli )
  {
    global $db;

    $sql = "SELECT * FROM {$db['cu']}_Orders_Info WHERE competition_id = '{$competition_id}'";
    $orders_in_db = $mysqli->query( $sql );
    $orders = array();

    $sql = "SELECT o.id as order_id, c.type, i.alias, c.name, i.qty, (i.qty * c.price) as total_cost FROM {$db['cu']}_Orders_Items as i 
            LEFT OUTER JOIN {$db['cu']}_Orders_Info as o ON o.id = i.order_id
            LEFT OUTER JOIN {$db['cu']}_Catalogs as c ON i.alias = c.alias AND o.competition_id = c.competition_id
            WHERE o.competition_id = '{$competition_id}'
            ORDER BY order_id, i.alias";

    $items = $mysqli->query( $sql );
    $items_per_order = array();
    $total = 0;

    while( $order = $orders_in_db->fetch_assoc() )
    {
      $order['content'] = array();
      $orders[ $order['id'] ] = $order;
      $items_per_order[ $order['id'] ] = array();
    }
    
    while( $item = $items->fetch_assoc() )
    {
      array_push( $items_per_order[ $item['order_id'] ], $item );
      $total += (float) $item['total_cost'];
    }

    foreach( $items_per_order as $order_id => $items ) 
    {
      $orders[ $order_id ]['content'] = format_items_to_order( $items );
    }

    return [ $orders, number_format( $total, 2 ) ]; 
  }


  


  /**
    * get_order_total(): get the total price of a given order, directly from the database
    * @param (string) order_id: the order id to retrieve data for
    * @param (mysqli) mysqli: mysqli object
    * @return (float) the total price
    */

  function get_order_total( $order_id, $mysqli )
  {
    global $db;

    $sql = "SELECT SUM(i.qty * c.price) as total_cost FROM {$db['cu']}_Orders_Items as i 
            LEFT OUTER JOIN {$db['cu']}_Orders_Info as o ON o.id = i.order_id
            LEFT OUTER JOIN {$db['cu']}_Catalogs as c ON i.alias = c.alias AND o.competition_id = c.competition_id
            WHERE i.order_id = '{$order_id}'";

    $order_total = $mysqli->query( $sql )->fetch_assoc()['total_cost'];

    return $order_total;
  }


  /**
    * get_total_cost(): get the total price of the order, from its data
    * @param (string) competition_id: the id of the competition to retrieve orders for
    * @param (array) order_data: the content of the order
    * @param (mysqli) mysqli: mysqli object
    * @return (float) the total price
    */

  function get_total_cost( $competition_id, $order_data, $mysqli )
  {
    $catalog = get_catalog( $competition_id, $mysqli );

    $sum = 0;

    foreach( $order_data as $alias => $qty )
    {
      $alias = explode( '-', $alias );

      switch( count( $alias ) )
      {
        case 2:
          $sum += $qty * $catalog[ $alias[0] ]['items'][ $alias[1] ]['price'];
          break;
        case 4:
          $sum += $qty * $catalog[ $alias[0] ]['items'][ $alias[1] ]['options'][ $alias[2] ]['selections'][ $alias[3] ]['price'];
          break;
        default:
          break;
      }
    }
    
    return $sum;
  }


  /**
    * get_items_amount(): retrieve all items ordered for the competitions and the corresponding amounts
    * @param (string) competition_id: the id of the competition to retrieve orders for
    * @param (mysqli) mysqli: mysqli object    
    * @return (array) an array of items and corresponding amounts
    */

  function get_items_amount( $competition_id, $mysqli )
  {
    global $db;

    $amounts = array();

    $sql = "SELECT name, alias, SUM(qty) as qty FROM
            ( 
              SELECT c.name, i.alias, i.qty FROM {$db['cu']}_Orders_Items as i
              JOIN {$db['cu']}_Orders_Info as o ON o.id = i.order_id AND competition_id = '{$competition_id}'
              JOIN {$db['cu']}_Catalogs as c ON c.alias = i.alias AND c.competition_id = '{$competition_id}'
            ) as result
            GROUP BY alias
            ORDER BY alias";

    $results = $mysqli->query( $sql );

    while( $row = $results->fetch_assoc() )
    {
      list($block, $alias) = explode( '-', $row[ 'alias' ], 2 );
      
      if( $alias )
      {
        $amounts[ $block ]['items'][ $alias ]['name'] = $row['name'];
        $amounts[ $block ]['items'][ $alias ]['qty'] = $row['qty'];
      }
      else
      {
        $amounts[ $block ]['name'] = $row['name'];
      }
    }
    
    return $amounts;
  }



  /**
    * format_items_to_order(): format the raw order data to an associative array
    * @param (array) ordered_items: the raw order data
    * @return (array) the associative array of the order
    */

  function format_items_to_order( $ordered_items )
  {
    $order = array();

    foreach( $ordered_items as $item )
    {
      $alias = explode( '-', $item['alias'] );

      switch( count( $alias ) )
      {
        case 1:
          $order[ $alias[0] ]['name'] = $item['name'];
          $order[ $alias[0] ]['given'] = $item['qty'];
          $order[ $alias[0] ]['total_cost'] = 0;
          break; 
        case 2:
          $order[ $alias[0] ]['items'][ $alias[1] ]['name'] = $item['name']; 
          $order[ $alias[0] ]['items'][ $alias[1] ]['qty'] = $item['qty']; 
          $order[ $alias[0] ]['items'][ $alias[1] ]['total_cost'] = $item['total_cost']; 
          $order[ $alias[0] ]['total_cost'] += $item['total_cost']; 
          break;
        case 4:
          $order[ $alias[0] ]['items'][ $alias[1] ]['options'][ $alias[2] ][ $alias[3] ]['name'] = $item['name'];
          $order[ $alias[0] ]['items'][ $alias[1] ]['options'][ $alias[2] ][ $alias[3] ]['qty'] = $item['qty'];
          $order[ $alias[0] ]['items'][ $alias[1] ]['options'][ $alias[2] ][ $alias[3] ]['total_cost'] = $item['total_cost'];
          $order[ $alias[0] ]['total_cost'] += $item['total_cost'];
          break;
        default:
          break;
      }
    }

    return $order;
  }

  /**
    * format_post_data(): format the raw data to an array
    * @param (string) post_data: the data to format
    * @param (mysqli) mysqli: mysqli object  
    * @return (array) the comment and formatted data
    */

  function format_post_data( $post_data, $mysqli )
  {
    if( $post_data['user_comment'] )
    {
      $pattern = "/[<>={}]/";
      $user_comment = preg_replace( $pattern, '', $post_data['user_comment'] );
      $user_comment_escaped = mysqli_real_escape_string( $mysqli, $user_comment );
      unset( $post_data['user_comment'] );
    }
    
    $formatted_items = array();

    foreach( $post_data as $item_key => $item_value )
    {
      $stripped_alias = explode( '_', $item_key ); // Alias is smthg like b0-i1 or 0_b1-i2-o3

      switch( count( $stripped_alias ) )
      {
        case 1:
          if( $item_value )
          {
            $block_key = explode( '-', $item_key )[0];
            $formatted_items[ $block_key ] = 0;
            $formatted_items[ $item_key ] = $item_value;
          }
          break;
        case 2:
          if( ! isset( $formatted_items[ "{$stripped_alias[1]}-{$item_value}" ] ) )
          {
            $formatted_items[ "{$stripped_alias[1]}-{$item_value}" ] = 1;
          }
          else
          {
            $formatted_items[ "{$stripped_alias[1]}-{$item_value}" ]++;
          }
          break;
        default:
          break;
      }
    }

    return [ $user_comment_escaped, $formatted_items ];
  }

  
  /**
    * format_options(): format the item options to fit display on order page
    * @param (array) options: the options to format
    * @return (array) the formatted options
    */

  function format_options( $options )
  {
    $formatted_options = array();

    foreach( $options as $option_id => $selections )
    {
      $cnt = 0;

      foreach( $selections as $selection_id => $selection )
      {
        for( $i = 0 ; $i < $selection['qty'] ; $i++ )
        {
          $formatted_options[ $cnt ][ $option_id ] = $selection_id;
          $cnt++;
        }
      }
    }

    return $formatted_options;
  }


  
  // ============================================================================================================
  //
  //  PDF RELATED FUNCTIONS
  //
  // ============================================================================================================

  require_once dirname( __DIR__, 2 ) . '/src/tcpdf/tcpdf.php';

  class CUPDF extends TCPDF
  {
    public function WriteLine( $html )
    {
      $this->startTransaction(); 
      $start_page = $this->getPage();                       
      $this->writeHTMLCell( 0, 0, '', '', $html, 0, 1, false, true, 'L'  );
      $end_page = $this->getPage();
      
      if  ($end_page != $start_page)
      {
        $this->rollbackTransaction( true ); 
        $this->AddPage();
        $this->writeHTMLCell( 0, 0, '', '', $html, 0, 1, false, true, 'L'  );
      }
      else
      {
        $this->commitTransaction();     
      } 
    }
  }


  /**
   * create_new_pdf(): create new PDF reference
   * @return (pdf) the PDF reference created
   */

  function create_new_pdf( $competition_name )
  {
    $pdf = new CUPDF( PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false );
    $pdf->setCreator( PDF_CREATOR );
    $pdf->setAuthor( PDF_AUTHOR );
    $pdf->SetHeaderData( dirname( __DIR__, 1 ) . "/assets/img/favicon.png", PDF_HEADER_LOGO_WIDTH, ' Commande Utile', "  » {$competition_name}", array( 0,0,0 ), array( 50,10,10 ) );
    $pdf->setHeaderFont( array( PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN ) );
    $pdf->setFooterFont( array( PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_MAIN ) );
    $pdf->SetDefaultMonospacedFont( PDF_FONT_MONOSPACED );
    $pdf->SetMargins( PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT );
    $pdf->SetHeaderMargin( PDF_MARGIN_HEADER );
    $pdf->SetFooterMargin( PDF_MARGIN_FOOTER );
    $pdf->setAutoPageBreak( TRUE, PDF_MARGIN_BOTTOM );
    $pdf->setImageScale( PDF_IMAGE_SCALE_RATIO );
    $pdf->setFontSubsetting( true );
    $pdf->setFont( 'dejavusanscondensed', '', 10, '', true );

    return $pdf;
  }



  // ============================================================================================================
  //
  //  EMAILS RELATED FUNCTIONS
  //
  // ============================================================================================================

  require_once dirname( __DIR__, 2 ) . '/src/yaml_spyc-reader.php';
  require_once dirname( __DIR__, 2 ) . '/src/_class-email.php';


  /**
   * format_email_order() : format the order data to be displayed in an email
   * @param (array) order: data of the order being formatted
   * @return (string) the formatted order
   */

  function format_email_order( $order )
  {
    $order_html = '<table style="border-collapse:collapse;"s>';

    foreach( $order as $block_key => $block )
    {
      $total = number_format( $block['total_cost'], 2 );
      $order_html .= "<tr style=\"background-color:#006A3A;color:#fff\">
                        <td colspan=\"2\" style=\"padding:2px 7px;border:1px solid #006A3A\"><b>{$block['name']}</b></td>
                      </tr>";

      foreach( $block['items'] as $item_key => $item )
      {
        $total = number_format( $item['total_cost'], 2 );
        $order_html .= "<tr>
                          <td style=\"padding:2px 7px;border:1px solid #006A3A\">{$item['qty']} x {$item['name']}</td>
                          <td style=\"padding:2px 7px;border:1px solid #006A3A;text-align:right\">{$total} €</td>
                      </tr>";

        foreach( $item['options'] as $option )
        {
          foreach( $option as $selection ) 
          {
            $total = $selection['total_cost'] ? number_format( $selection['total_cost'], 2 ) : '--';
            $order_html .= "<tr style=\"color:#686a6d\">
                          <td style=\"padding:2px 7px 2px 17px;border:1px solid #006A3A\">&#8627; {$selection['qty']} x {$selection['name']}</td>
                          <td style=\"padding:2px 7px;border:1px solid #006A3A;text-align:right\">{$total} €</td>
                      </tr>";
          }
        }
      }
    }
    $order_html .= '</table>';

    return $order_html;
  }

  /**
   * send_order_confirmation() : send an email to confirm the order has been properly stored in database
   * @param (array) competition: data of the competition, such as competition name
   * @param (array) order: data of the order being stored in database
   * @return (string) error if sending the email failed
   */

  function send_order_confirmation( $competition, $order )
  {       
    $to = decrypt_data( $order['user_email'] );
    $from = decrypt_data( $competition['contact'] );
    $user_order = format_email_order( $order['content'] );
    $order_total = number_format( $order['order_total'], 2, '.', '' );
    $closing_date = date( 'd/m/Y', strtotime( $competition['orders_closing_date'] ) );
    $folder =  explode( '/' , $_SERVER['REQUEST_URI'] )[1];
    $content = spyc_load_file( dirname( __DIR__, 1 ) . "/assets/emails.yaml" )['email_confirm_order'];

    $email = new email();
    $email->create_header( $from );

    if( $is_edit )
    {
      $email->subject = $content['subject']['edit'];
    }
    else
    {
      $email->subject = $content['subject']['confirm'];
    }

    foreach( $content['text'] as $paragraph )
    {
      $email->concatenate_to_message( "<p>{$paragraph}</p>" );
    }

    $email->concatenate_to_message( "<p>----</p>" );
    $email->concatenate_to_message( "<p>{$content['sign']}</p>" );

    if( $competition['information'] != null ) 
    {
      $email->concatenate_to_message( "<p style=\"color:red\">{$content['note']}</p>" );
    }

    $email->concatenate_to_message( '</body></html>' );

    $email->replace_subject_text( "{competition_name}", $competition['name'] );
    $email->replace_subject_text( "{order_nr}", $order['id'] );    
    $email->replace_message_text( "{competition_name}", $competition['name'] );
    $email->replace_message_text( "{order_nr}", $order['id'] );
    $email->replace_message_text( "{username}", $order['user_name'] );
    $email->replace_message_text( "{order}", $user_order );
    $email->replace_message_text( "{user_comment}", $order['user_comment']  );
    $email->replace_message_text( "{order_total}", $order_total );
    $email->replace_message_text( "{closing_date}", $closing_date );
    $email->replace_message_text( "{site}", "https://{$_SERVER['SERVER_NAME']}/{$folder}" );
    $email->replace_message_text( "{admin_note}", $competition['information'] );

    // Send email
    if( mail( $to, $email->subject, $email->message, $email->header ) )
    {
      return null;
    }
    else
    {
      return "Échec de l'envoi de l'e-mail de confirmation";
    }
  }


  /**
   * send_order_cancellation() : send an email to confirm the order has been properly removed from the database
   * @param (array) competition: data of the competition, such as competition name
   * @param (string) order_info: information of the order being deleted from the database
   * @return (string) error if sending the email failed
   */
    
  function send_order_cancellation( $competition, $order_info)
  {  
    $to = decrypt_data( $order_info['user_email'] );
    $from = decrypt_data( $competition['contact'] );
    $content = spyc_load_file( dirname( __DIR__, 1 ) . "/assets/emails.yaml" )['email_delete_order'];

    $email = new email();
    $email->create_header( $from );
    $email->subject = $content['subject'];

    foreach( $content['text'] as $paragraph )
    {
      $email->concatenate_to_message( "<p>{$paragraph}</p>" );
    }

    $email->concatenate_to_message( "<p>----</p>" );
    $email->concatenate_to_message( "<p>{$content['sign']}</p>" );
    $email->concatenate_to_message( '</body></html>' );

    $email->replace_subject_text( "{competition_name}", $competition['name'] );
    $email->replace_subject_text( "{order_nr}", $order_info['id'] );    
    $email->replace_message_text( "{competition_name}", $competition['name'] );
    $email->replace_message_text( "{order_nr}", $order_info['id'] );
    $email->replace_message_text( "{username}", $order_info['user_name'] );

    // Send email
    if( mail( $to, $email->subject, $email->message, $email->header ) )
    {
      return null;
    }
    else
    {
      return "Échec de l'envoi de l'e-mail de confirmation";
    }
  }


  /**
   * send_creation_competition_cu() : send an email to confirm the competition has been properly created in database
   * @param (string) competition_id: ID of the competition being removed from the database
   * @param (string) orga_email: email of the organizers of the competition
   * @param (string) all_administrators_email: email of all the website administrators
   * @return (string) error if sending the email failed
   */
  
  function send_creation_competition_cu( $competition_id, $orga_email, $all_administrators_email )
  {
    $to = $orga_email;
    $from = $all_administrators_email;
    $content = spyc_load_file( dirname( __DIR__, 1 ) . "/assets/emails.yaml" )['email_create_competition'];

    $email = new email();
    $email->create_header( $from );
    $email->subject = $content['subject'];

    foreach( $content['text'] as $paragraph )
    {
      $email->concatenate_to_message( "<p>{$paragraph}</p>" );
    }

    $email->concatenate_to_message( "<p>----</p>" );
    $email->concatenate_to_message( "<p>{$content['sign']}</p>" );
    $email->concatenate_to_message( '</body></html>' );
     
    $email->replace_subject_text( "{competition_id}", $competition_id );
    $email->replace_message_text( "{competition_id}", $competition_id );    

    // Send email
    if( mail( $to, $email->subject, $email->message, $email->header ) )
    {
      return null;
    }
    else
    {
      return "Échec de l'envoi de l'e-mail de confirmation";
    }
  }


  /**
   * send_deletion_competition_cu() : send an email to confirm the competition has been properly removed from the database
   * @param (string) competition_id: ID of the competition being removed from the database
   * @param (string) orga_email: email of the organizers of the competition
   * @param (string) all_administrators_email: email of all the website administrators
   * @return (string) error if sending the email failed
   */

  function send_deletion_competition_cu( $competition_id, $orga_email, $all_administrators_email )
  {
    $to = $orga_email;
    $from = $all_administrators_email;
    $content = spyc_load_file( dirname( __DIR__, 1 ) . "/assets/emails.yaml" )['email_delete_competition'];

    $email = new email();
    $email->create_header( $from );
    $email->subject = $content['subject'];

    foreach( $content['text'] as $paragraph )
    {
      $email->concatenate_to_message( "<p>{$paragraph}</p>" );
    }

    $email->concatenate_to_message( "<p>----</p>" );
    $email->concatenate_to_message( "<p>{$content['sign']}</p>" );
    $email->concatenate_to_message( '</body></html>' );
     
    $email->replace_subject_text( "{competition_id}", $competition_id );
    $email->replace_message_text( "{competition_id}", $competition_id );    

    // Send email
    if( mail( $to, $email->subject, $email->message, $email->header ) )
    {
      return null;
    }
    else
    {
      return "Échec de l'envoi de l'e-mail de confirmation";
    }
  }


 
  
?>

