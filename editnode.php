<?
   include_once("gf.inc");
   $pageTitle = "Edit Node";
   $variables["submit"] = isset($HTTP_POST_VARS["submit"])?$HTTP_POST_VARS["submit"]:"";
   if (!isset($variables["nid"]))
      $variables["nid"] = isset($HTTP_POST_VARS["nid"])?$HTTP_POST_VARS["nid"]:-1;
   if (!isset($variables["tid"]))
      $variables["tid"] = isset($HTTP_POST_VARS["tid"])?$HTTP_POST_VARS["tid"]:-1;
   $variables["nodeCategory"] = isset($HTTP_POST_VARS["nodeCategory"])?$HTTP_POST_VARS["nodeCategory"]:Array();
   if (!$oConn = Connect())
   {
      die("Sorry, someone fucked up. Probably me.");
   }
   if ($variables["submit"] == "edit")
   {
      $variables["nodeTitle"] = $HTTP_POST_VARS["nodeTitle"];
      $variables["nodeBody"] = $HTTP_POST_VARS["nodeBody"];
      $variables["nodePrecise"] = $HTTP_POST_VARS["nodePrecise"];
      $variables["nodeType"] = $HTTP_POST_VARS["nodeType"];
      $variables["public"] = isset($HTTP_POST_VARS["public"])?1:0;
      $variables["uid"] = $HTTP_POST_VARS["uid"];

      $oRS = SelectUser($oConn, $variables["uid"]);
      if (!$oRS)
      {
         include("header.php");
         printf("             <h4>mySQL Error</h4>Error Number: %s</p><p>Error: %s</p>", mysql_errno(), mysql_error());
         Close($oConn);
         die();
         include("footer.php");
      }
      if (mysql_num_rows($oRS) == 0)
      {
         include("header.php");
         Close($oConn);
         die("<h4>User is not recognised. Check user name.</h4>");
         include("footer.php");
      }
      $oRec = mysql_fetch_array($oRS);
      $variables["pwd"] = isset($HTTP_POST_VARS["pwd"])?$HTTP_POST_VARS["pwd"]:"";
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
      $uc = $oRec["counter"];
      $oRS = SelectNode($oConn, $variables["nid"]);
      if (EditNode($oConn, $variables["nodeTitle"], $variables["nodeBody"], $variables["nodePrecise"], $variables["nid"], $variables["public"], $variables["nodeType"]))
      {
         RSScadia();  
         DeleteNodeCategories($oConn, $variables["nid"]);
         if (count($variables["nodeCategory"]) > 0);
            for ($ii=0;$ii<count($variables["nodeCategory"]); $ii++)
               InsertNodeCategory($oConn, $variables["nid"], $variables["nodeCategory"][$ii]);
         if ($oRec = mysql_fetch_array($oRS))
            InsertNodeEdit($oConn, $variables["nid"], AddSlashes($oRec["nodeTitle"]), AddSlashes($oRec["nodeBody"]), AddSlashes($oRec["nodePrecise"]), Now2Counter(), $uc);
         Close($oConn);
         header("Location: viewnode.php?op=nid=" . $variables["nid"] . ";tid=" . $variables["tid"]);
      }
      else
      {
         include("header.php");
         echo("           <h4>mySQL Error</h4>");
         printf("             <p>Error Number: %s</p><p>Error: %s</p>", mysql_errno(), mysql_error());
         include("footer.php");
      }
   }
   else
   {
      include("header.php");
      $oRS = SelectNode($oConn, $variables["nid"]);
      if (!$oRec = mysql_fetch_array($oRS))
            die("SQL Error, could not find node " . $variables["nid"] . ".");
      if ($variables["submit"] == "")
      {
         $variables["nodeTitle"] = stripslashes($oRec["nodeTitle"]);
         $variables["nodeBody"] = stripslashes($oRec["nodeBody"]);
         $variables["nodePrecise"] = stripslashes($oRec["nodePrecise"]);
         $variables["parentNode"] = stripslashes($oRec["parentNode"]);
         $variables["nodeType"] = stripslashes($oRec["nodeType"]);
         $variables["public"] = stripslashes($oRec["public"]);
         $variables["uid"] = "";
      }
      else
      {
         $variables["nodeTitle"] = stripslashes($HTTP_POST_VARS["nodeTitle"]);
         $variables["nodeBody"] = stripslashes($HTTP_POST_VARS["nodeBody"]);
         $variables["nodePrecise"] = stripslashes($HTTP_POST_VARS["nodePrecise"]);
         $variables["nodeType"] = stripslashes($HTTP_POST_VARS["nodeType"]);
         $variables["public"] = isset($HTTP_POST_VARS["public"])?1:0;
         $variables["uid"] = stripslashes($HTTP_POST_VARS["uid"]);
      }
      if ($variables["submit"] == "preview")
      {
      $oRecc = Array("nodeID" => -1, "nodeTitle" => "Preview of " . $variables["nodeTitle"], "nodeBody" => $variables["nodeBody"], "childNodes" => 0, "Pings" => 0, "userName" => $variables["uid"], "datetime" => time(), "Edited" => "1", "url" => "");
      AltDisplayNode($oRecc, 1, true, 0);
      echo("               <hr />\n");
      }
      AltDisplayNode($oRec, 0, -1, 0, 1, 1, 1, 0);
      echo("            <hr />\n");
      echo("            <form action=\"editnode.php\" method=\"post\">\n");
      printf("               <input type=\"hidden\" value=\"%s\" name=\"nid\">\n", $variables["nid"]);
      printf("               <input type=\"hidden\" value=\"%s\" name=\"tid\">\n", $variables["tid"]);
      echo("               <div>\n");
      echo("                  <div class=\"rowHeader\">\n");
      echo("                     <label for=\"uid\">uid: </label>\n");
      printf("                     <input id=\"uid\" type=\"text\" size=\"10\" name=\"uid\" value=\"%s\"/>\n", $variables["uid"]);
      echo("                  </div>\n");
      echo("                  <div class=\"rowHeader\">\n");
      echo("                     <label for=\"pwd\">pwd: </label>\n");
      echo("                     <input id=\"pwd\" type=\"password\" size=\"10\" name=\"pwd\" />\n");
      echo("                  </div>\n");
      echo("                  <div class=\"rowHeader\">\n");
      echo("                     <label for=\"nodeTitle\">Title</label>\n");
      printf("                     <input id=\"nodeTitle\" type=\"text\" size=\"50\" name=\"nodeTitle\" value=\"%s\"/>\n", $variables["nodeTitle"]);
      echo("                  </div>\n");
      echo("                  <div class=\"rowHeader\">\n");
      echo("                     <label for=\"nodeType\">Type</label>\n");
      echo("                     <select id=\"nodeType\" name=\"nodeType\">\n");
      if (!$oConn = Connect())
         die("Sorry, someone fucked up. Probably me.");
      $oRS = GetNodeTypes($oConn);
      while ($oRec = mysql_fetch_array($oRS))
         printf("                        <option value=\"%s\" %s>%s</option>\n", $oRec["typeCode"], $oRec["typeCode"]==$variables["nodeType"]?"selected=\"selected\"":"", $oRec["typeName"]);
      echo("                     </select>\n");
      echo("                  </div>\n");
      echo("                  <div class=\"rowHeader\">\n");
      echo("                     <label for=\"nodeCategory\">Category</label>\n");
      echo("                     <select id=\"nodeCategory\" name=\"nodeCategory[]\" multiple=\"multiple\">\n");
      $oRS = SelectNodeTypeCategories($oConn, $variables["nodeType"],$variables["nid"]);
      while ($oRec = mysql_fetch_array($oRS))
         printf("                        <option value=\"%s\"%s>%s</option>\n", $oRec["CategoryID"], ($oRec["NodeCategoryID"] != -1)?" selected=\"selected\"":"", $oRec["Category"]);
      echo("                     </select>\n");
      echo("                  </div>\n");
      echo("                  <div class=\"rowHeader\">\n");
      echo("                     <label for=\"public\">Public?</label>\n");
      printf("                     <input id=\"public\" type=\"checkbox\" size=\"50\" name=\"public\" %s/>\n", $variables["public"]==1?"checked=\"checked\"":"");
      echo("                  </div>\n");
      echo("                  <div class=\"rowHeader\">\n");
      echo("                     <label for=\"nodePrecise\">Precise:</label>\n");
      echo("                  </div>\n");
      echo("                  <div class=\"rowHeader\">\n");
      printf("                     <textarea id=\"nodePrecise\" rows=\"10\" cols=\"70\" name=\"nodePrecise\">%s</textarea>\n", $variables["nodePrecise"]);
      echo("                  </div>\n");
      echo("                  <div class=\"rowHeader\">\n");
      echo("                     <label for=\"nodeBody\">Body:</span></span>\n");
      echo("                  </div>\n");
      echo("                  <div class=\"rowHeader\">\n");
      printf("                     <textarea id=\"nodeBody\" rows=\"30\" cols=\"70\" name=\"nodeBody\">%s</textarea>\n", $variables["nodeBody"]);
      echo("                  </div>\n");
      echo("                  <div class=\"rowHeader\">\n");
      echo("                     <input type=\"submit\" name=\"submit\" value=\"preview\" />\n");
      echo("                     <input accesskey=\"9\" type=\"submit\" name=\"submit\" value=\"edit\" />\n");
      echo("                  </div>\n");
      echo("               </div>\n");
      echo("            </form>\n");
      echo("            <script type=\"text/javascript\">\n");
      echo("            <!--\n");
      echo("               var arrCategories;\n");
      echo("               arrCategories = Array(Array(\"\", \"\", \"-1\")");
      $oCategories = SelectCategories($oConn);
      while ($oCategory = mysql_fetch_array($oCategories))
         printf(", Array(\"%s\", \"%s\", \"%s\")", $oCategory["CategoryID"], $oCategory["Category"], $oCategory["nodeType"]);
      echo(");\n");
?>
               PopulateCategories();
               function PopulateCategories()
               {
                  // arrCategories is php-generated array created
                  // for editnode and addnode scripts

                  var elType, elCategory, elOpt;
                  var ii;
                  var sOptions;
                  elType = document.getElementById("nodeType");
                  elCategory = document.getElementById("nodeCategory");
                  sOptions = ""
                  for (ii=elCategory.childNodes.length-1;ii>=0;ii--)
                     elCategory.removeChild(elCategory.childNodes[ii]);
                  for(ii=0;ii<arrCategories.length;ii++)
                     if (arrCategories[ii][2] == elType.value)
                     {
                        elOpt = document.createElement("option");
                        elOpt.appendChild(document.createTextNode(arrCategories[ii][1]));
                        elOpt.value=arrCategories[ii][0];
                        elCategory.appendChild(elOpt);
                     }
               }
<?
      echo("            -->\n");
      echo("            </script>\n");
   }
   Close($oConn);
   include("footer.php");
?>
