<?
   include_once("gf.inc");
   $pageTitle = "Delete Link";
   if (!$oConn = Connect())
   {
      die("Sorry, someone fucked up. Probably me.");
   }
   $variables["submit"] = isset($_POST["submit"])?$_POST["submit"]:"";
   $variables["uid"] = isset($_POST["uid"])?$_POST["uid"]:"";
   $variables["pwd"] = isset($_POST["pwd"])?$_POST["pwd"]:"";
   if ($variables["submit"] == "delete")
   {
      $variables["lid"] = isset($_POST["lid"])?$_POST["lid"]:"";
      if ($variables["lid"] == "")
      {
         include("header.php");
         Close($oConn);
         die("<h4>Could not find link .</h4>");
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
         die("<h4>User is not recognised.</h4>");
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
      if (DeleteLink($oConn, $variables["lid"]))
      {
         Close($oConn);
         header("Location: showlinks.php");
      }
      else
      {
         include("header.php");
         echo("           <h4>mySQL Error</h4>");
         printf("             <p>Error Number: %s</p><p>Error: %s</p>", mysqli_errno(), mysqli_error());
         include("footer.php");
      }
   }
   else
   {
      $oLinkRS = SelectLink($oConn, $variables["lid"]);
      if ($oLink = mysqli_fetch_array($oLinkRS))
      {
         include("header.php");
         printf("              <p><a href=\"%s\" title=\"%s\">%s</a> (%s) - %s</p>\n", $oLink["Link"], $oLink["LinkDescription"], $oLink["LinkName"], $oLink["Link"], $oLink["LinkDescription"]);
         echo("              <form action=\"dellink.php\" method=\"post\">\n");
         printf("                 <input type=\"hidden\" name=\"lid\" value=\"%s\" />\n", $variables["lid"]);
         echo("                 <div class=\"rowHeader\">\n");
         echo("                    <label for=\"uid\">uid: </label>\n");
         printf("                    <input id=\"uid\" type=\"text\" size=\"10\" name=\"uid\" />\n");
         echo("                 </div>\n");
         echo("                 <div class=\"rowHeader\">\n");
         echo("                    <label for=\"pwd\">pwd: </label>\n");
         echo("                    <input id=\"pwd\" type=\"password\" size=\"10\" name=\"pwd\" />\n");
         echo("                  </div>\n");
         echo("                  <div class=\"rowHeader\">\n");
         echo("                     <input accesskey=\"9\" type=\"submit\" name=\"submit\" value=\"delete\"/>\n");
         echo("                  </div>\n");
         echo("              </form>\n");
         include("footer.php");
      }
      else
      {
         include("header.php");
         Close($oConn);
         die("<h4>Could not find link " . $variables["lid"] . ".</h4>");
         include("footer.php");
      }
   }
?>
