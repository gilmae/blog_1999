<?
   include_once("gf.inc");
   $pageTitle = "Delete Node";
   if (!$oConn = Connect())
   {
      die("Sorry, someone fucked up. Probably me.");
   }
   $variables["submit"] = isset($_POST["submit"])?$_POST["submit"]:"";
   $variables["uid"] = isset($_POST["uid"])?$_POST["uid"]:"";
   if ($variables["submit"] == "delete")
   {
      $variables["nid"] = $_POST["nid"];
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
      $variables["pwd"] = isset($_POST["pwd"])?$_POST["pwd"]:"";
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
      $oNode = mysqli_fetch_array(SelectNode($oConn, $variables["nid"]));
      $nodes[$oNode["nodeID"]] = $oNode;
      $nodeIndex[$oNode["parentNode"]][0] = $oNode["nodeID"];
      $oNodeRS = SelectThreading($oConn, $variables["nid"]);
      while ($oRec = mysqli_fetch_array($oRS))
      {
         $nodes[$oRec["nodeID"]] = $oRec;
         $i = isset($nodeIndex[$oRec["parentNode"]])?count($nodeIndex[$oRec["parentNode"]]):0;
         $nodeIndex[$oRec["parentNode"]][$i] = $oRec["nodeID"];
      }
      TraverseAndDeleteNodes($variables["nid"], $nodes, $nodeIndex);
      AdjustNodeChildNodes($oConn, $oNode["parentNode"], ($oNode["childNodes"]+1) * -1);
      DeleteNodeCategories($oConn, $variables["nid"]);
      if ($oNode)
      {
         $oBranch = mysqli_fetch_array(SelectThreadRoot($oConn, $oNode["parentNode"]));
         if ($oBranch)
         {
            if ($oBranch["FirstChild"] == $variables["nid"])
               SetThreadFirstChild($oConn, $oBranch["nodeID"], $oNode["nextSibling"]);
            if ($oBranch["LastChild"] == $variables["nid"])
               SetThreadLastChild($oConn, $oBranch["nodeID"], $oNode["prevSibling"]);
         }
         if ($oNode["nextSibling"] != -1)
            SetNextSiblingNodePrevSibling($oConn, $oNode["nextSibling"], $oNode["prevSibling"]);
         else
            SetThreadLastChild($oConn, $oRec["parentNode"], $oRec["prevSibling"]);
         if ($oNode["prevSibling"] != -1)
            SetPrevSiblingNodeNextSibling($oConn, $oNode["prevSibling"], $oNode["nextSibling"]);
         else
            SetThreadFirstChild($oConn, $oNode["parentNode"], $oNode["nextSibling"]);
      }
      Close($oConn);
      header("Location: nodeadmin.php");
   }
   else
   {
      $variables["nid"] = isset($variables["nid"])?$variables["nid"]:-1;
      include("header.php");
      $oRS = SelectNode($oConn, $variables["nid"]);
      if ($oRec = mysqli_fetch_array($oRS));
      {
         echo("            <form action=\"delnode.php\" method=\"post\">\n");
         printf("               <input type=\"hidden\" value=\"%s\" name=\"nid\">\n", $variables["nid"]);
         echo("               <div>\n");
         echo("                  <div class=\"rowHeader\">\n");
         echo("                     <label for=\"uid\">uid: </label>\n");
         echo("                     <input id=\"uid\" type=\"text\" size=\"10\" name=\"uid\" />\n");
         echo("                  </div>\n");
         echo("                  <div class=\"rowHeader\">\n");
         echo("                     <label for=\"pwd\">pwd: </label>\n");
         echo("                     <input id=\"pwd\" type=\"password\" size=\"10\" name=\"pwd\" />\n");
         echo("                  </div>\n");
         echo("                  <br />");
         AltDisplayNode($oRecc, 1, true, 0);
         echo("                  <hr />\n");
         echo("                  <div class=\"rowHeader\">\n");
         echo("                     <input accesskey=\"9\" type=\"submit\" name=\"submit\" value=\"delete\" />\n");
         echo("                  </div>\n");
         echo("               </div>\n");
         echo("            </form>\n");
      }
   }
   Close($oConn);
   include("footer.php");

   function TraverseAndDeleteNodes($xNode, $xNodes, $xNodeIndex)
   {
       global $oConn;
       if (isset($xNodes[$xNode]))
       {
          $oNode = $xNodes[$xNode];
          DeleteNode($oConn, $oNode["nodeID"]);
          if ($oNode["childNodes"] > 0)
          {
             if (isset($xNodeIndex[$xNode]))
                for ($i = 0; $i < count($xNodeIndex[$xNode]); $i++)
                   TraverseAndDeleteNodes($xNodeIndex[$xNode][$i], $xNodes, $xNodeIndex);
          }
       }
   }

?>