<?php

  require_once dirname( __DIR__, 2 ) . '/src/sessions_handler.php';

  $competition_id = $_POST['competition_id'];

  if( isset( $_SESSION['manageable_competitions'][ $competition_id ] ) )
  {
    require_once dirname( __FILE__ ) . '/_functions.php';
    require_once dirname( __DIR__, 2 ) . '/src/mysqli.php';

    mysqli_open( $mysqli );

    $sql = "SELECT * FROM {$db['rg']}_Users WHERE competition_id = '{$competition_id}'";
    
    if( $results = $mysqli->query( $sql ) )
    { 
      $sql = "DELETE FROM {$db['rg']}_Users WHERE competition_id = '{$competition_id}'";

      if( $mysqli->query( $sql ) )
      { 
        $error = import_competition_into_db( $competition_id, $_SESSION[ 'user_email' ], $_SESSION[ 'user_token' ], $mysqli );

        if( ! $error )
        {
          while( $row = $results->fetch_assoc() )
          {
            $sql = "UPDATE {$db['rg']}_Users SET response = '{$row['response']}' WHERE competition_id = '{$competition_id}' AND user_id = '{$row['user_id']}'";   

            if( ! $mysqli->query( $sql ) )
            {
              break;
            }
          }
        }
      }
    }

    $text_to_display = $error ? 'Échec de la mise à jour de la liste des compétiteurs...' : 'Liste des compétiteurs mise à jour avec succès !';
   
    $mysqli->close();
  }
  else
  {
    $text_to_display = 'Accès interdit !';
    $error = 'Not authenticated';
  }

  $response = array( 
                    'text_to_display' => $text_to_display, 
                    'error' => $error, 
                  );

  echo json_encode( $response );

?>