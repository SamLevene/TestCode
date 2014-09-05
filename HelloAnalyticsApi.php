<?php
require_once 'Google/Client.php';
require_once 'Google/Service/Analytics.php';

session_start();

$date = new DateTime();
$end_date = $date->format('Y-m-d');

$date->sub(new DateInterval('P1M'));
$start_date = $date->format('Y-m-d');

$client = new Google_Client();
$client->setApplicationName('Hello Analytics API Sample');

// Visit https://console.developers.google.com/ to generate your
// client id, client secret, and to register your redirect uri.
$client->setClientId('210320462893-8rpjfhj7te22ak8j0forpsdnlv1gicta.apps.googleusercontent.com');
$client->setClientSecret('UMmLJsL43vWAkUhdcsX3SNrH');
$client->setRedirectUri('http://localhost/HelloAnalyticsApi.php');
$client->setDeveloperKey('AIzaSyCVCMUjePAXEfi2EpRre0PoAVjkXVeZW8o');
$client->setScopes(array('https://www.googleapis.com/auth/analytics.readonly'));

// Magic. Returns objects from the Analytics Service instead of associative arrays.
//$client->setUseObjects(true);

if (isset($_GET['code'])) {
  $client->authenticate($_GET['code']);
  $_SESSION['token'] = $client->getAccessToken();
  $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
  header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
}

if (isset($_SESSION['token'])) {
  $client->setAccessToken($_SESSION['token']);
}

if (!$client->getAccessToken()) {
  $authUrl = $client->createAuthUrl();
  print "<a class='login' href='$authUrl'>Connect Me!</a>";

} else {
  // Create analytics service object. See next step below.
  $analytics = new Google_Service_Analytics($client);
  runMainDemo($analytics);
}
function runMainDemo($analytics) {
  try {
    $profileId = getFirstProfileId($analytics);

    if (isset($profileId)) {
      $results = getResults($analytics, $profileId);
      printResults($results);
    }

  } catch (apiServiceException $e) {
    // Error from the API.
    print 'There was an API error : ' . $e->getCode() . ' : ' . $e->getMessage();

  } catch (Exception $e) {
    print 'There was a general error : ' . $e->getMessage();
  }
}

function getFirstprofileId($analytics) {
  $accounts = $analytics->management_accounts->listManagementAccounts();

  if (count($accounts->getItems()) > 0) {
    $items = $accounts->getItems();
    $firstAccountId = $items[0]->getId();

    $webproperties = $analytics->management_webproperties
        ->listManagementWebproperties($firstAccountId);

    if (count($webproperties->getItems()) > 0) {
      $items = $webproperties->getItems();
      $firstWebpropertyId = $items[0]->getId();

      $profiles = $analytics->management_profiles
          ->listManagementProfiles($firstAccountId, $firstWebpropertyId);

      if (count($profiles->getItems()) > 0) {
        $items = $profiles->getItems();
        return $items[0]->getId();
      } else {
        throw new Exception('No views (profiles) found for this user.');
      }
    } else {
      throw new Exception('No web properties found for this user.');
    }
  } else {
    throw new Exception('No accounts found for this user.');
  }
}

function getResults($analytics, $profileId) {
   return $analytics->data_ga->get('ga:' . $profileId, $start_date, $end_date, 'ga:users');
}

function printResults($results) {
  if (count($results->getRows()) > 0) {
    $profileName = $results->getProfileInfo()->getProfileName();
    $rows = $results->getRows();
    $sessions = $rows[0][0];

    print "<p>First view (profile) found: $profileName</p>";
    print "<p>Total Users: $sessions</p>";

  } else {
    print '<p>No results found.</p>';
  }
}
?>
