<?PHP

$user = "root";
$pass = "";

$link = new mysqli('localhost', $user, $pass, CURR_DB);

$http503 = false;
if (mysqli_connect_errno())
{
  $dberror = date('r') . " | Error connecting to database | " . mysqli_connect_error();

  exit($dberror);
}

function getData($query)
{
  global $link;
  $result = $link->query($query) or notifyOfFailure($query, $link->error);
  return $result;
}

function notifyOfFailure($q, $err)
{
  $intro = "The following MYSQL query at http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . " (" . $_SERVER['PHP_SELF'] . ") failed on " . date("Y-m-d") . " at " . date("G:i:s T") . "; IP:" . $_SERVER['REMOTE_ADDR'];
  $break = "\r\n\r\n";

  mail("YOUR EMAIL ADRESS", "FAILED MYSQL QUERY", $intro . $break . $q . $break . $err . $break . $webdev, "From:no-reply@leisure-partners.net");
}

?>