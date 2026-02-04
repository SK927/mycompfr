<?php

  require_once dirname( __DIR__, 2 ) . '/src/sessions_handler.php';

  $competitions_list = array();

  if( $_SESSION['logged_in'] and $_POST['valid'] )
  {
    require_once dirname( __FILE__ ) . '/_functions.php';
    require_once dirname( __DIR__, 2 ) . '/src/mysqli.php';

    $user_id = $_SESSION['user_id'];

    mysqli_open( $mysqli );
    
    $sql = "SELECT c.id, c.name, c.start_date, c.end_date, u.user_id, u.response FROM {$db['rg']}_Users AS u
            JOIN {$db['rg']}_Competitions AS c ON c.id = u.competition_id
            WHERE u.user_id = {$user_id}
            ORDER BY c.start_date;";

    if( $results = $mysqli->query( $sql ) )
    {
      $temp_array = array();

      while( $row = $results->fetch_assoc() ) 
      {            
        $temp_array['competition_id'] = $row['id'];
        $temp_array['competition_name'] = addslashes( $row['name'] );
        $temp_array['competitor_id'] = encrypt_data( $user_id );
        $temp_array['competitor_registration'] = $row['response'];
        $temp_array['competition_start_date'] = date( 'Y-m-d', strtotime( $row['start_date'] ) );
        $temp_array['competition_end_date'] = date( 'Y-m-d', strtotime($row['end_date'] ) );
        $competition_list[] = $temp_array;
      }
    }
    else
    {
      $error = $mysqli->error;
    }

    $mysqli->close();
  }
  
  $response = array( 
                    'competition_list' => $competition_list, 
                    'error' => $error, 
                  );

  echo json_encode( $response );

?>