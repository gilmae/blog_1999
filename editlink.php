<?
   include_once("gf.inc");
   $pageTitle = "Edit Link";
   if (!$oConn = Connect())
   {
      die("Sorry, someone fucked up. Probably me.");
   }
   $variables["submit"] = isset($_POST["submit"])?$_POST["submit"]:"";
   $variables["uid"] = isset($_POST["uid"])?$_POST["uid"]:"";
   $variables["pwd"] = isset($_POST["pwd"])?$_POST["pwd"]:"";
   if ($variables["submit"] == "edit")
   {
      $variables["lid"] = isset($_POST["lid"])?$_POST["lid"]:"";
      if ($variables["lid"] == "")
      {
         include("header.php");
         Close($oConn);
         die("<h4>Could not find link .</h4>");
         include("footer.php");
      }
      $variables["linkName"] = isset($_POST["linkName"])?$_POST["linkName"]:"";
      $variables["link"] = isset($_POST["link"])?$_POST["link"]:"";
      $variables["linkDescription"] = isset($_POST["linkDescription"])?$_POST["linkDescription"]:"";
      $variables["linkRSS"] = isset($_POST["linkRSS"])?$_POST["linkRSS"]:"";
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
      if (EditLink($oConn, $variables["lid"], $variables["linkName"], $variables["link"], $variables["linkDescription"], $variables["linkRSS"]))
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
         echo("              <form action=\"editlink.php\" method=\"post\">\n");
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
         echo("                     <label for=\"linkName\">Link Name: </label>\n");
         printf("                    <input id=\"linkName\" type=\"text\" size=\"50\" name=\"linkName\" value=\"%s\" />\n", StripSlashes($oLink["LinkName"]));
         echo("                  </div>\n");
         echo("                  <div class=\"rowHeader\">\n");
         echo("                     <label for=\"link\">Link: </label>\n");
         printf("                     <input id=\"link\" type=\"text\" size=\"75\" name=\"link\" value=\"%s\" />\n", StripSlashes($oLink["Link"]));
         echo("                  </div>\n");
         echo("                  <div class=\"rowHeader\">\n");
         echo("                     <label for=\"linkDescription\">Link Description: </label>\n");
         printf("                     <input id=\"linkDescription\" type=\"text\" size=\"75\" name=\"linkDescription\" value=\"%s\" />\n", StripSlashes($oLink["LinkDescription"]));
         echo("                  </div>\n");
         echo("                  <div class=\"rowHeader\">\n");
         echo("                     <label for=\"linkRSS\">Link RSS: </label>\n");
         printf("                     <input id=\"linkRSS\" type=\"text\" size=\"75\" name=\"linkRSS\" value=\"%s\" />\n", StripSlashes($oLink["LinkRSS"]));
         echo("                  </div>\n");
         echo("                  <div class=\"rowHeader\">\n");
         echo("                     <input accesskey=\"9\" type=\"submit\" name=\"submit\" value=\"edit\"/>\n");
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