<?php
  require_once(dirname(__FILE__) . '/No2SMS_Client.class.php');

  /*
    Certaines lignes sont commentées car le programme prenait des paramètres par
    défaut, mais comme celui-ci est publié sur github il est plus sûr d'éviter
    d'avoir les données de connection écrites en dur.
  */
  // $default_user = "";
  // $default_password = "";
  // $default_dest = "";
  // $default_message = "";

  /* on test le nombre d'arguments */
  if ($argc != 5) {
    /* affiche un message d'aide et termine le script */
    print "usage: php index.php user password destination message\n";
    exit(1);

    // print "The program will continue with the following default arguments: \n";
    // print "User: " . $default_user . "\n";
    // print "Password: it is a 'secret'\n";
    // print "Destination: " . $default_dest . "\n";
    // print "Message: " . $default_message . "\n";
    // print "Type 'y' to confirm... \n";
    //
    // $handle = fopen ("php://stdin","r");
    // $line = fgets($handle);
    // if(trim($line) != 'y'){
    //     echo "ABORTING!\n";
    //     exit(1);
    // }
    // fclose($handle);
  }

  // $user = (isset($argv[1]) ? $argv[1] : $default_user);
  // $password = (isset($argv[2]) ? $argv[2] : $default_password);
  // $destination = (isset($argv[3]) ? $argv[3] : $default_dest);
  // $message = (isset($argv[4]) ? $argv[4] : $default_message);

  $user        = $argv[1];
  $password    = $argv[2];
  $destination = $argv[3];
  $message     = $argv[4];

  print $user . " " . $password . " " . $destination . " " . $message . "\n";

  $decoded_password = base64_decode($password);

  print $decoded_password . "\n";

  var_dump(No2SMS_Client::message_infos($message, TRUE));
  var_dump(No2SMS_Client::test_message_conversion($message));

  $client = new No2SMS_Client($user, $decoded_password);

  try {
      /* test de l'authentification */
      if (!$client->auth())
          die('mauvais utilisateur ou mot de passe');

      /* envoi du SMS */
      print "===> ENVOI\n";
      $res = $client->send_message($destination, $message);
      var_dump($res);
      $id = $res[0][2];
      printf("SMS-ID: %s\n", $id);

      /* décommenter pour tester l'annulation */
      //print "===> CANCEL\n";
      //$res = $client->cancel_message($id);
      //var_dump($res);

      print "===> STATUT\n";
      $res = $client->get_status($id);
      var_dump($res);

      /* on affiche le nombre de crédits restant */
      $credits = $client->get_credits();
      printf("===> Il vous reste %d crédits\n", $credits);

  } catch (No2SMS_Exception $e) {
      printf("!!! Problème de connexion: %s", $e->getMessage());
      exit(1);
  }
?>
