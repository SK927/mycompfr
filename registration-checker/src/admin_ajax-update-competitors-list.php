<?php

  require_once dirname( __DIR__, 2 ) . '/src/sessions_handler.php'; // $db is loaded here!

  $competition_id = $_POST['competition_id'];

  if ( in_array( $competition_id, array_keys( $_SESSION['manageable_competitions'] ) ) )
  {
    require_once dirname( __DIR__, 2 ) . '/src/mysql_connect.php';
    require_once dirname( __FILE__ ) . '/_functions.php';

    [ $new_competitors_list, $error ] = get_competitors_from_private_wcif( $competition_id, $_SESSION['user_token'] );

    if ( ! $error )
    {
      $query_results = $conn->query( "SELECT competition_registrations FROM {$db['rg']}_Main WHERE competition_id = '{$competition_id}'" ); /* Get current competitors list */

      if ( $query_results )
      {   
        $result_row = $query_results->fetch_assoc();
        $current_competitors_list = from_pretty_json( $result_row['competition_registrations'] );
        $new_competitors_list = format_wcif_persons_data( $new_competitors_list );

        /* Update competitors list while keeping existing competitors data */
        foreach ( $current_competitors_list as $user_id => $competitor )
        {
          if ( isset( $new_competitors_list[ $user_id ] ) )
          {
            $new_competitors_list[ $user_id ]['confirmed'] = $competitor['confirmed'];
          }
        }

        $error = update_competition_registrations( $competition_id, $new_competitors_list, $conn );
        $text_to_display = $error ? 'Unable to update registrations list...' : 'Registrations list updated successfully!';
      }
      else
      {
        $text_to_display = 'Unable to update registrations list...';
        $error = mysqli_error( $conn );
      }
    }
    else
    {
      $text_to_display = 'Unable to update registrations list...';
    }
    $conn->close();
  }
  else
  {
    $text_to_display = 'Access denied!';
    $error = 'Not authenticated';
  }

  $response = array( 
                    'text_to_display' => $text_to_display, 
                    'ajax_error' => $error, 
                  );

  echo json_encode( $response );

?>