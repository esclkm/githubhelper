<?php

  require_once('../githuboauth.php');

  session_start();

  $github = new GithubOAuth();

  // We'll know we need to call 'request_acces_token' when we see the 'code' parameter in the URL.
  // This means the user has approved the application and Github has redirected them back to this
  // page with an access code appended to the URL.  We can then trade this code for an access token.
  if($_GET['code']) {
    $github->set_access_code($_GET['code']);
  } else {
    $op = $_GET['op'];
  }

  switch($op) {
    case 'request_access_code':
      if($_GET['client_id'] && $_GET['client_secret']) {
        $scope = array('user', 'repo');
        $client_id = $_GET['client_id'];
        $client_secret = $_GET['client_secret'];
        $github->request_access_code($scope, $client_id, $client_secret);
      } else {
        $errors[] = 'Client ID required.';
      }
      break;
    case 'request_access_token':
      $github->request_access_token();
      break;
    case 'reset':
      $github->reset();
      header( "Location: " . $_SERVER['SCRIPT_NAME'] );
      break;
    case 'api_request':
      if(isset($_GET['http_verb']) && !empty($_GET['http_verb'])) {
        if ($_GET['http_verb'] != 'GET') die('GET is currently the only supported HTTP verb.');
        $http_verb = $_GET['http_verb'];
      } else {
        $errors[] = 'HTTP Verb not supplied.';
      }
      if(isset($_GET['endpoint']) && !empty($_GET['endpoint'])) {
        $endpoint = $_GET['endpoint'];
      } else {
        $errors[] = 'Endpoint was not supplied';
      }
      $api_response = json_decode( $github->api($http_verb, $endpoint), true );
      break;
    default:
      break;
  }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <title>phphub Demo</title>
    <link rel='stylesheet' href='css/demo.css' />
    <script src='//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js'></script>
    <script src='js/demo.js'></script>
  </head>
  <body>

    <div class="header">
      <a href="http://github.com/csphere/phphub"><span>phphub</span></a>
      <a href="/"><i class="header-bitpivot-logo"></i></a>
    </div>

    <div class='container'>
      <div class='step'>

        <?php if(!is_null($github->get_access_token())): ?>
          <h2><?= 'Make an API Request!' ?></h2>
        <?php elseif(!is_null($github->get_access_code())): ?>
          <h2><?= 'Step 2: Redeem Access Code for Access Token' ?></h2>
        <?php else: ?>
          <h2><?= 'Step 1: Request Access Code' ?></h2>
        <?php endif; ?>

        <?php if(is_null($github->get_access_code())): ?>
          <input id='tbx-client-id' type='text' placeholder='Client ID' /><br>
        <?php else: ?>
          <p><label>Client ID <span class='monospace'><?= $github->get_client_id() ?></span></label>
        <?php endif; ?>

        <?php if(is_null($github->get_access_code())): ?>
          <input id='tbx-client-secret' type='text' placeholder='Client Secret' /><br>
        <?php else: ?>
          <p><label>Client Secret <span class='monospace'><?= $github->get_client_secret() ?></span></label>
        <?php endif; ?>

        <?php if(!is_null($github->get_access_code())): ?>
          <p><label>Access Code 
            <span class='access-code monospace'><?= $github->get_access_code() ?>
              <span class='access-code-redeemed <?= $github->get_access_code_redeemed() == 1 ? 'redeemed' : 'not-redeemed' ?>'>
                (<?= $github->get_access_code_redeemed() == 1 ? 'Redeemed' : 'Not Redeemed' ?>)
              </span>
            </span>
          </label>
        <?php endif; ?>

        <?php if(!is_null($github->get_access_token())): ?>
          <p><label>Access Token 
            <span class='monospace'><?= $github->get_access_token() ?></span>
          </label>
          <p><label>Endpoint URL 
            <input id='tbx-endpoint' type='text' /><br>
            <span class='endpoint-hint'>Hint: Only supports GET requests.</span>
          </label>
        <?php endif; ?>
      </div>
    </div>
    <?php if(!is_null($github->get_access_token())): ?>
      <label>
        <div id='btn-api-request' class='btn'>Execute API Request</div>
        <div id='btn-reset' class='btn'>Reset Token</div>
      </label>
    <?php elseif(!is_null($github->get_access_code())): ?>
      <div id='btn-request-access-token' class='btn'>Request Access Token</div>
    <?php else: ?>
      <div id='btn-request-access-code' class='btn'>Request Access Code</div>
    <?php endif; ?>
    <?php if($op == 'api_request'): ?>
      <div class='api-response'>
        <h1>Response:</h1>
        <pre><?php
          if (empty($errors)) {
            print_r($api_response);
          } else {
            echo '<h3>Errors:</h3>';
            for($i = 0; $i < count($errors); $i++) {
              $err = $errors[$i];
              echo "<p> - $err";
            }
          }      
        ?></pre>
      </div>
    <?php endif; ?>

  </body>
</html>
