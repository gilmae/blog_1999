<?
   include("gf.inc");
   $variables["uid"] = isset($_POST["uid"])?$_POST["uid"]:"";
   $variables["pwd"] = isset($_POST["pwd"])?$_POST["pwd"]:"";
   $variables["linkName"] = isset($_POST["linkName"])?$_POST["linkName"]:"";
   $variables["link"] = isset($_POST["link"])?$_POST["link"]:"";
   $variables["linkRSS"] = isset($_POST["linkRSS"])?$_POST["linkRSS"]:"";
   $variables["linkDescription"] = isset($_POST["linkDescription"])?$_POST["linkDescription"]:"";
   if (!isset($variables["submit"]))
      $variables["submit"] = isset($_POST["submit"])?$_POST["submit"]:"";
   $pageTitle = "Add Link";
   if ($variables["submit"] == "post")
   {
      if (!$oConn = Connect())
      {
         die("Sorry, someone fucked up. Probably me.");
      }
      if ($variables["uid"] == "")
      {
         include("header.php");
         Close($oConn);
         die("<h4>User is not recognised as an administrator. Check password.</h4>");
         include("footer.php");
      }
      $oRS = SelectUser($oConn, $variables["uid"]);
      if (!$oRS)
      {
         include("header.php");
         printf("             <h4>mySQL Error</h4>Error Number: %s</p><p>Error: %s</p>", mysqli_errno(), mysqli_error());
         Close($oConn);
         die();
         include("footer.php");
      }
      if (mysqli_num_rows($oRS) == 0)
      {
         include("header.php");
         Close($oConn);
         die("<h4>User is not recognised. Check user name.</h4>");
         include("footer.php");
      }
      $oRec = mysqli_fetch_array($oRS);
      if ($variables["pwd"] != $oRec["password"])
      {
         include("header.php");
         Close($oConn);
         die("<h4>Password is incorrect.</h4>");
         include("footer.php");
      }
      if (!$oRec["admin"])
      {
         include("header.php");
         Close($oConn);
         die("<h4>User is not an admin.</h4>");
         include("footer.php");
      }
      AddLink($oConn, $variables["linkName"], $variables["link"], $variables["linkRSS"], $variables["linkDescription"]);
      Header("Location: index.php");
   }
   /*elseif ($variables["submit"] == "sidebar")
   {
      if (!$oConn = Connect())
      {
         die("Sorry, someone fucked up. Probably me.");
      }
      AddLink($oConn, $variables["linkName"], $variables["link"], "", $variables["linkName"]);
      header("Location: " . $variables["link"]);
   }*/
   else
   {
      include("header.php");
      echo("              <form action=\"addlink.php\" method=\"post\">\n");
      echo("                 <div class=\"rowHeader\">\n");
      echo("                    <label for=\"uid\">uid: </label>\n");
      printf("                    <input id=\"uid\" type=\"text\" size=\"10\" name=\"uid\" />\n");
      echo("                 </div>\n");
      echo("                 <div class=\"rowHeader\">\n");
      echo("                    <label for=\"pwd\">pwd: </label>\n");
      echo("                    <input id=\"pwd\" type=\"password\" size=\"10\" name=\"pwd\" />\n");
      echo("                  </div>\n");
      echo("                  <div class=\"rowHeader\">\n");
      echo("                     <label for=\"linkName\">Link Name: </label>\n");
      printf("                    <input id=\"linkName\" type=\"text\" size=\"50\" name=\"linkName\" />\n");
      echo("                  </div>\n");
      echo("                  <div class=\"rowHeader\">\n");
      echo("                     <label for=\"link\">Link: </label>\n");
      echo("                     <input id=\"link\" type=\"text\" size=\"75\" name=\"link\" />\n");
      echo("                  </div>\n");
      echo("                  <div class=\"rowHeader\">\n");
      echo("                     <label for=\"linkDescription\">Link Description: </label>\n");
      echo("                     <input id=\"linkDescription\" type=\"text\" size=\"75\" name=\"linkDescription\" />\n");
      echo("                  </div>\n");
      echo("                  <div class=\"rowHeader\">\n");
      echo("                     <label for=\"linkRSS\">Link RSS: </label>\n");
      echo("                     <input id=\"linkRSS\" type=\"text\" size=\"75\" name=\"linkRSS\" />\n");
      echo("                  </div>\n");
      echo("                  <div class=\"rowHeader\">\n");
      echo("                     <input type=\"submit\" name=\"submit\" value=\"post\"/>\n");
      echo("                  </div>\n");
      echo("              </form>\n");
      include("footer.php");
   }
?>

