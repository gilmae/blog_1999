<?
   include("gf.inc");
   if (!$oConn = Connect())
   {
      die("Sorry, someone fucked up. Probably me.");
   }
   $pageTitle = "Add Comment";
   $variables["submit"] = isset($HTTP_POST_VARS["submit"])?$HTTP_POST_VARS["submit"]:"";
   if ($variables["submit"] == "post")
   {
      $variables["name"] = isset($HTTP_POST_VARS["uid"])?$HTTP_POST_VARS["uid"]:$name;
      $variables["email"] = isset($HTTP_POST_VARS["email"])?$HTTP_POST_VARS["email"]:$email;
      $variables["url"] = isset($HTTP_POST_VARS["url"])?$HTTP_POST_VARS["url"]:$url;
      $variables["url"] = preg_replace("/^(http:\/\/|ftp:\/\/|mailto:)/", "", $variables["url"]);
      $variables["nodeTitle"] = isset($HTTP_POST_VARS["nodeTitle"])?$HTTP_POST_VARS["nodeTitle"]:"";
      $variables["pid"] = isset($HTTP_POST_VARS["pid"])?$HTTP_POST_VARS["pid"]:-1;
      $variables["nodeBody"] = isset($HTTP_POST_VARS["nodeBody"])?$HTTP_POST_VARS["nodeBody"]:"";
      $oRS = SelectNode($oConn, $variables["pid"]);
      if ($oRec = mysqli_fetch_array($oRS)) {
         if ($oRec["datetime"] < strtotime("-3 months")) {
	    header("Status: 410 No Further Comments");
	    die();
	 }
      }	 
      if (isset($variables["counter"]))
         UpdateUser($oConn, $variables["name"], $variables["name"], $variables["email"], $variables["url"], 0, $variables["uc"]);
      else
      {
          $oUserRS = SelectUser($oConn, $variables["name"]);
          if (mysqli_num_rows($oUserRS) == 0)
             $variables["counter"] = InsertUser($oConn, $variables["name"], $variables["name"], $variables["email"], $variables["url"], 0);
          else
          {
             $oUser = mysqli_fetch_array($oUserRS);
             $variables["counter"] = $oUser["counter"];
             UpdateUser($oConn, $variables["name"], $variables["name"], $variables["email"], $variables["url"], 0, $variables["counter"]);
          }
      }
      if ($HTTP_POST_VARS["remember"] = "on")
      {
         setcookie("counter", $variables["counter"], time() + 7776000);
         setcookie("name", $variables["name"], time() + 7776000);
         setcookie("url", $variables["url"], time() + 7776000);
         setcookie("email", $variables["email"], time() + 7776000);
      }
      $variables["nid"] = AddNode($oConn, MakeStringSafe($variables["nodeTitle"]), MakeStringSafe($variables["nodeBody"]), MakeStringSafe($variables["nodeBody"]), 'c', $variables["pid"], $variables["counter"], 1);
      $BlockID = $variables["pid"];
      $oIsBlockThreaded = FindParentInThreading($oConn, $variables["pid"]);
      if (mysqli_num_rows($oIsBlockThreaded) < 1)
      {
         $parentID = $variables["pid"];
         AddToThreading($oConn, $BlockID, $parentID);
         $sql = "SELECT parentNode FROM nodes where nodeID = $parentID";
         while ($parentID != -1)
         {
            $sql = "SELECT parentNode FROM nodes where nodeID = $parentID";
            $oParentRS = mysqli_query($sql, $oConn);
            if ($oParentRec = mysqli_fetch_array($oParentRS))
               $parentID = $oParentRec["parentNode"];
            else
               $parentID = -1;
            AddToThreading($oConn, $BlockID, $parentID);
         }
      }
      if ($variables["pid"] != -1)
      {
         $oBranchRS = SelectThreadRoot($oConn, $variables["pid"]);
         if ($oBranch = mysqli_fetch_array($oBranchRS))
         {
            if ($oBranch["FirstChild"] == -1)
               SetThreadFirstChild($oConn, $variables["pid"], $variables["nid"]);
            $prevSibling = $oBranch["LastChild"];
            SetThreadLastChild($oConn, $variables["pid"], $variables["nid"]);
            SetNodeSiblings($oConn, $variables["nid"], $prevSibling, -1);
            if ($prevSibling != -1)
               SetPrevSiblingNodeNextSibling($oConn, $prevSibling, $variables["nid"]);
         }
      }
      include("header.php");
      $oNodeRS = SelectNode($oConn, $variables["nid"]);
      if ($oNode = mysqli_fetch_array($oNodeRS))
         AltDisplayNode($oNode, 0);
      include("footer.php");
      die();
   }
   else if ($variables["submit"] == "preview")
   {
         $variables["name"] = isset($HTTP_POST_VARS["uid"])?$HTTP_POST_VARS["uid"]:$name;
         $variables["email"] = isset($HTTP_POST_VARS["email"])?$HTTP_POST_VARS["email"]:$email;
         $variables["url"] = isset($HTTP_POST_VARS["url"])?$HTTP_POST_VARS["url"]:$url;
         $variables["url"] = preg_replace("/^(http:\/\/|ftp:\/\/|mailto:)/", "", $variables["url"]);
         $variables["nodeTitle"] = isset($HTTP_POST_VARS["nodeTitle"])?$HTTP_POST_VARS["nodeTitle"]:"";
         $variables["pid"] = isset($HTTP_POST_VARS["pid"])?$HTTP_POST_VARS["pid"]:-1;
         $variables["nodeBody"] = isset($HTTP_POST_VARS["nodeBody"])?$HTTP_POST_VARS["nodeBody"]:"";
   }
   else
   {
      $variables["name"] = isset($HTTP_COOKIE_VARS["name"])?$HTTP_COOKIE_VARS["name"]:"anonyomus";
      $variables["email"] = isset($HTTP_COOKIE_VARS["email"])?$HTTP_COOKIE_VARS["email"]:"";
      $variables["url"] = isset($HTTP_COOKIE_VARS["url"])?$HTTP_COOKIE_VARS["url"]:"";
      $variables["url"] = preg_replace("/^(http:\/\/|ftp:\/\/|mailto:)/", "", $variables["url"]);
      $variables["pid"] = isset($variables["nid"])?$variables["nid"]:-1;
   }
   include("header.php");
   $oNodeRS = SelectNode($oConn, $variables["pid"]);

   if (!$oNode = mysqli_fetch_array($oNodeRS))
      die("Could not attach comment because node " . $variables["pid"] . " does not exist.");
   AltDisplayNode($oNode, 0, $variables["pid"], 0, 1, 1, 1, 0);

   if ($variables["submit"] == "preview")
   {
      $oRecc = Array("nodeID" => -1, "nodeTitle" => "Preview of " . $variables["nodeTitle"], "nodeBody" => $variables["nodeBody"], "childNodes" => 0, "Pings" => 0, "userName" => $variables["name"], "datetime" => time(), "Edited" => "1", "url" => "");
      AltDisplayNode($oRecc, 1, true, 0);
      echo("               <hr />\n");
   }
   else
   {
      $variables["nodeTitle"] = $oNode["nodeTitle"];
      if (!preg_match("/Re:/i", $variables["nodeTitle"]))
         $variables["nodeTitle"] = "Re: " . $variables["nodeTitle"];
      $variables["nodeBody"] = "";
   }
   echo("              <form action=\"addcomment.php\" method=\"post\">\n");
   printf("                 <input type=\"hidden\" value=\"%s\" name=\"pid\">\n", $variables["pid"]);
   echo("                 <input type=\"hidden\" value=\"c\" name=\"nodeType\">\n");
   echo("                 <div>\n");
   echo("                    <div class=\"rowHeader\">\n");
   echo("                       <label for=\"uid\">Name:</label>\n");
   printf("                       <input id=\"uid\" type=\"text\" size=\"25\" name=\"uid\" value=\"%s\" />\n", $variables["name"]);
   echo("                    </div>\n");
   echo("                    <div class=\"rowHeader\">\n");
   echo("                       <label for=\"email\">email:</label>\n");
   printf("                       <input id=\"email\" type=\"text\" size=\"40\" name=\"email\" value=\"%s\" />\n", $variables["email"]);
   echo("                    </div>\n");
   echo("                    <div class=\"rowHeader\">\n");
   echo("                       <label for=\"url\">url:</label>\n");
   printf("                       <input id=\"url\" type=\"text\" size=\"40\" name=\"url\" value=\"%s\" />\n", $variables["url"]);
   echo("                    </div>\n");
   echo("                    <div class=\"rowHeader\">\n");
   echo("                       <label for=\"remember\">Remember me</label><input id=\"remember\" type=\"checkbox\" name=\"remember\" checked=\"checked\" /><span class=\"small\">&nbsp;&nbsp;(a cookie will be placed on your computer and will expire in 90 days.)</span>\n");
   echo("                    </div>\n");
   echo("                    <br />\n");
   echo("                    <div class=\"rowHeader\">\n");
   echo("                       <label for=\"title\">Title:</label>\n");
   printf("                       <input id=\"title\" type=\"text\" size=\"40\" name=\"nodeTitle\" value=\"%s\" />\n", $variables["nodeTitle"]);
   echo("                    </div>\n");
   echo("                    <div class=\"rowHeader\">\n");
   echo("                       <label for=\"nodeBody\">Comment:</label>\n");
   echo("                    </div>\n");
   echo("                    <div class=\"rowHeader\">\n");
   printf("                       <textarea id=\"nodeBody\"rows=\"7\" cols=\"40\" name=\"nodeBody\">%s</textarea><br /><span class=\"small\">(html will be laughed at and exterminated)</span>\n", stripslashes($variables["nodeBody"]));
   echo("                    </div>\n");
   echo("                    <div class=\"rowHeader\">\n");
   echo("                       <input type=\"submit\" name=\"submit\" value=\"preview\"/>\n");
   echo("                       <input accesskey=\"9\" type=\"submit\" name=\"submit\" value=\"post\"/>\n");
   echo("                    </div>\n");
   echo("                 </div>\n");
   echo("              </form>\n");
   Close($oConn);
   include("footer.php");
?>
