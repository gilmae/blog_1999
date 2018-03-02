<?
   include("gf.inc");
   include("weblogscom.inc");
   $variables["submit"] = isset($_POST["submit"])?$_POST["submit"]:"";
   $pageTitle = "Add Node";
   if ($variables["submit"] == "")
      $variables["public"] = 1;
   if ($variables["submit"] == "post")
   {
      $variables["nodeTitle"] = isset($_POST["nodeTitle"])?$_POST["nodeTitle"]:"";
      $variables["nodeBody"] = isset($_POST["nodeBody"])?$_POST["nodeBody"]:"";
      $variables["nodePrecise"] = isset($_POST["nodePrecise"])?$_POST["nodePrecise"]:"";
      $variables["nodeType"]= isset($_POST["nodeType"])?$_POST["nodeType"]:"";
      $variables["nodeCategory"] = isset($_POST["nodeCategory"])?$_POST["nodeCategory"]:Array();
      $variables["public"] = isset($_POST["public"])?1:0;
      $variables["uid"] = isset($_POST["uid"])?$_POST["uid"]:"";
      $variables["pwd"] = isset($_POST["pwd"])?$_POST["pwd"]:"";
      $variables["pid"] = isset($_POST["pid"])?$_POST["pid"]:-1;
      if (!$oConn = Connect())
         die("Sorry, someone fucked up. Probably me.");
      $variables["counter"] = VerifyAdmin($variables["uid"], $variables["pwd"]);
      if ($variables["counter"] > 0)
      {
         if ($variables["pid"] != -1)
         {
            $oRS = SelectNode($oConn, $variables["pid"]);
            if (mysqli_num_rows($oRS) == 0)
               die("Could not attach node because parent node " . $variables["pid"] . " does not exist.");
         }
         $variables["nid"] = AddNode($oConn, $variables["nodeTitle"], $variables["nodeBody"], $variables["nodePrecise"], $variables["nodeType"], $variables["pid"], $variables["counter"], $variables["public"]);
         if ($variables["nid"] < 1)
            die("             <h4>mySQL Error</h4>Error Number: " . mysqli_errno() . "</p><p>Error: " . mysqli_error() . "</p>");
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
         if (count($variables["nodeCategory"]) > 0);
            for ($ii=0;$ii<count($variables["nodeCategory"]); $ii++)
               InsertNodeCategory($oConn, $variables["nid"], $variables["nodeCategory"][$ii]);
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
         $oTypeRS = SelectNodeType($oConn, $variables["nodeType"]);
         RSScadia();
         //if ($oTypeRec = mysqli_fetch_array($oTypeRS))
         //{
            //if ($bLive && ($oTypeRec["PingbackEnabled"] == 1))
            //{
               //$aLinks = FindLinks(stripslashes($variables["nodeBody"]));
               //TraverseLinksAndDiscoverPing($aLinks, $variables["nid"]);
            //}
            //if ($bLive && ($oTypeRec["BlogsPingName"] != ""))
              // PingBloGs($oTypeRec["BlogsPingName"], $oTypeRec["BlogsPingURI"]);
         //}
         header("Location: viewnode.php?op=nid=" . $variables["nid"]);
         Close($oConn);
      }
      else
      {
         include("header.php");
         switch ($variables["counter"])
         {
            case -99:
               printf("<h4>mySQL Error</h4><p>Error Number: %s</p><p>Error Description: %s</p>\n", mysqli_errno(), mysqli_error());
               break;
            case -3:
               printf("<h4>User is not an Administrator.</h4>");
               break;
            case -2:
               printf("<h4>Password is not recognised.</h4>");
               break;
            case -1:
               printf("<h4>User is not recognised.</h4>");
               break;
         }
         include("footer.php");
      }
   }
   else if ($variables["submit"] == "preview")
   {
      include("header.php");
      $variables["nodeTitle"] = isset($_POST["nodeTitle"])?$_POST["nodeTitle"]:"";
      $variables["nodeBody"] = isset($_POST["nodeBody"])?$_POST["nodeBody"]:"";
      $variables["nodePrecise"] = isset($_POST["nodePrecise"])?$_POST["nodePrecise"]:"";
      $variables["nodeType"]= isset($_POST["nodeType"])?$_POST["nodeType"]:"";
      $variables["nodeCategory"] = isset($_POST["nodeCategory"])?$_POST["nodeCategory"]:Array();
      $variables["public"] = isset($_POST["public"])?1:0;
      $variables["uid"] = isset($_POST["uid"])?$_POST["uid"]:"";
      $variables["pid"] = isset($_POST["pid"])?$_POST["pid"]:-1;
      $variables["nodeBody"] = stripslashes($variables["nodeBody"]);
      $variables["nodePrecise"] = stripslashes($variables["nodePrecise"]);
      $variables["nodeTitle"] = stripslashes($variables["nodeTitle"]);
      $oRecc = Array("nodeID" => -1, "nodeTitle" => "Preview of " . $variables["nodeTitle"], "nodeBody" => $variables["nodeBody"], "childNodes" => 0, "Pings" => 0, "userName" => $variables["uid"], "datetime" => time(), "Edited" => "0", "url" => "");
      AltDisplayNode($oRecc, 1, true, 0);
      echo("               <hr />\n");
   }
   else
   {
      include("header.php");
      $variables["pid"] = isset($variables["pid"])?$variables["pid"]:-1;
      $variables["uid"] = isset($_COOKIE["name"])?$_COOKIE["name"]:"";
      $variables["nodeTitle"] = "";
      $variables["nodeBody"] = "";
      $variables["nodePrecise"] = "";
      $variables["nodeType"]= "";
      $variables["nodeCategory"] = Array();
   }
   if ($variables["submit"] != "post")
   {
      echo("            <form action=\"addnode.php\" method=\"post\">\n");
      echo("               <div>\n");
      printf("               <input type=\"hidden\" value=\"%s\" name=\"pid\" />\n", $variables["pid"]);
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
      echo("                     <select id=\"nodeType\" name=\"nodeType\" onchange=\"PopulateCategories()\">\n");
      if (!$oConn = Connect())
         die("Sorry, someone fucked up. Probably me.");
      $oRS = GetNodeTypes($oConn);
      while ($oRec = mysqli_fetch_array($oRS))
         printf("                        <option value=\"%s\" %s>%s</option>\n", $oRec["typeCode"], $oRec["typeCode"]==$variables["nodeType"]?"selected=\"selected\"":"", $oRec["typeName"]);
      echo("                     </select>\n");
      echo("                  </div>\n");
      echo("                  <div class=\"rowHeader\">\n");
      echo("                     <label for=\"nodeCategory\">Category</label>\n");
      echo("                     <select id=\"nodeCategory\" name=\"nodeCategory[]\" style=\"width:200px\" multiple=\"multiple\">\n");
      echo("                     </select>\n");
      echo("                  </div>\n");
      echo("                  <div class=\"rowHeader\">\n");
      echo("                     <label for=\"public\">Public?</label>\n");
      printf("                     <input id=\"public\" type=\"checkbox\" size=\"50\" name=\"public\" %s/>\n", $variables["public"]==1?"checked=\"checked\"":"");
      echo("                  </div>\n");
      echo("                  <div class=\"rowHeader\">\n");
      echo("                     <label for=\"nodeBody\">Body:</label>\n");
      echo("                  </div>\n");
      echo("                  <div class=\"rowHeader\">\n");
      echo("                  <div class=\"rowHeader\">\n");
      echo("                     <input type=\"submit\" name=\"submit\" value=\"preview\" />\n");
      echo("                     <input accesskey=\"9\" type=\"submit\" name=\"submit\" value=\"post\" />\n");
      echo("                  </div>\n");
      printf("                     <textarea id=\"nodeBody\" rows=\"30\" cols=\"70\" name=\"nodeBody\">%s</textarea>\n", $variables["nodeBody"]);
      echo("                  </div>\n");
      echo("                  <div class=\"rowHeader\">\n");
      echo("                     <input type=\"submit\" name=\"submit\" value=\"preview\" />\n");
      echo("                     <input accesskey=\"9\" type=\"submit\" name=\"submit\" value=\"post\" />\n");
      echo("                  </div>\n");
      echo("                  <div class=\"rowHeader\">\n");
      echo("                     <label for=\"nodePrecise\">Precise:</label>\n");
      echo("                  </div>\n");
      echo("                  <div class=\"rowHeader\">\n");
      printf("                     <textarea id=\"nodePrecise\" rows=\"15\" cols=\"70\" name=\"nodePrecise\">%s</textarea>\n", $variables["nodePrecise"]);
      echo("                  </div>\n");
      echo("               </div>\n");
      echo("            </form>\n");
      echo("            <script type=\"text/javascript\">\n");
      echo("            <!--\n");
      echo("               var arrCategories;\n");
      echo("               arrCategories = Array(Array(\"\", \"\", \"-1\")");
      $oCategories = SelectCategories($oConn);
      while ($oCategory = mysqli_fetch_array($oCategories))
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
      include("footer.php");
   }

   function FindLinks($xStr)
   {
      $links = Array();
      preg_match_all("/(<a)([^>]*>)([\w\s.\/~_]*)(<\/a>)/", $xStr, $results, PREG_SET_ORDER);
      for ($i=0;$i < count($results); $i++)
         if (preg_match('/href\="http:\/\/(.*)"/iU', $results[$i][0], $aHrefMatches))
            array_push($links, Array($aHrefMatches[1], ''));
      return $links;
   }
   function Ping($xPingBackServer, $xNodeID, $xLink)
   {
      global $domain;
      $client = new IXR_Client("http://$xPingBackServer");
      $client->query("ping.pingback", "http://$domain/viewnode.php?op=nid=" . $xNodeID, "http://" . $xLink);
      if ($client->isError())
         print_r($client->getErrorMessage());
      else
         print_r($client->getResponse());
   }

   function PingBloGs($xName, $xURI)
   {
      global $domain;
      $client = new IXR_Client("http://ping.blo.gs");
      $client->query("weblogUpdates.ping", $xName, $xURI);
   }

   function TraverseLinksAndDiscoverPing($xLinks, $xNodeID)
   {
      $maxChunksToSearch = 500;
      for ($ii=0;$ii<count($xLinks);$ii++)
      {

         if ($oFile = fopen("http://" . $xLinks[$ii][0], "r"))
         {
            $bFound = false;
            $bNoLink = false;
            $iChunkCount = 0;
            while (!($bFound || $bNoLink || ($iChunkCount++ > $maxChunksToSearch) || feof($oFile)))
            {
               $sChunk = fgets($oFile, 4096);
               $pregex = "\<link rel\=\"pingback\" href\=\"http:\/\/(.*)\"(.*)(\/?)>";
               if (preg_match("/$pregex/i", $sChunk, $aMatches))
               {
                  $bFound = true;
                  Ping($aMatches[1], $xNodeID, $xLinks[$ii][0]);
               }
               else if (preg_match("/<\/head>/i", $sChunk))
                  $bNoLink = true;
            }
         }
      }
   }
?>
