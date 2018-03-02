<?
   include("gf.inc");
   if ($variables["nid"] == 7381) {
      header("HTTP/1.1 404 Gone");
      die();
   }
   //header("X-Pingback: http://avocadia.net/avocadia.XML_RPC.php");
   include_once("msf.inc");
   $sideLinks = array();
   //$links= "<link rel=\"pingback\" href=\"http://$domain/avocadia.XML_RPC.php\" />\n";
   $nav=array();
   $nid = isset($variables["nid"])?$variables["nid"]:-1;
   $nesting = 5;
   $maxLevel = 5;
   $minLevel = 5;
   $term = isset($variables["term"])?$variables["term"]:"";
/*   if ($nid == 7381)
   {
      header("HTTP/1.1 410 Gone");
      die();
   }*/
   if (!$oConn = Connect())
   {
      die("Sorry, someone fucked up. Probably me.");
   }
   $oNode = SelectNode($oConn, $nid);
   if (!$oNodeRec = mysqli_fetch_array($oNode))
      die("Could not find node $nid");
   else
   {
      $category = $oNodeRec["nodeType"];
      $nodes[$oNodeRec["nodeID"]] = $oNodeRec;
      $nodeIndex[$oNodeRec["parentNode"]][0] = $oNodeRec["nodeID"];
      $oRS = SelectThreading($oConn, $nid);
      while ($oRec = mysqli_fetch_array($oRS))
      {
         $nodes[$oRec["nodeID"]] = $oRec;
         $i = isset($nodeIndex[$oRec["parentNode"]])?count($nodeIndex[$oRec["parentNode"]]):0;
         $nodeIndex[$oRec["parentNode"]][$i] = $oRec["nodeID"];
      }
      $pageTitle = $nodes[$nid]["nodeTitle"];
      NodeLinks($oConn, $nodes[$nid]);
      include("header.php");
      if (isset($nav))
      {
         echo("            <div id=\"familialNodes\">\n");
         for ($ii=0;$ii<sizeof($nav);$ii++)
            printf("               <a href=\"%s\" title=\"Go to the %s node.\">%s</a>&nbsp;&nbsp;&nbsp;\n", $nav[$ii][1], $nav[$ii][0], $nav[$ii][0]);
         echo("            </div><br />\n");
      }
      ShowNodes(0, $nid);
      $oPingsRS = SelectPingbacks($oConn, $variables["nid"]);
      if ($oPingsRS && mysqli_num_rows($oPingsRS) > 0)
      {
         echo("            <a name=\"pings\"></a><h3>Pingbacks</h3>\n");
         while ($oPing = mysqli_fetch_array($oPingsRS))
            printf("            <p><a href=\"%s\" title=\"%s\">%s</a></p>\n", $oPing["Source"], $oPing["Title"], $oPing["Title"]);
      }
   }


   function ShowNodes($xLvl, $node)
   {
       global $nodeIndex;
       global $nodes;
       global $maxLevel;
       global $collapsible;
       global $nid;
       global $term;

       if (isset($nodes[$node]))
       {
          $oNode = $nodes[$node];
          AltDisplayNode($oNode, $xLvl, true, 1, $term, 6);
          if ($oNode["childNodes"] > 0)
          {
             echo("         <div class=\"nodeChildren\">\n");
             if (isset($nodeIndex[$node])/* && $xLvl<$maxLevel*/)
                for ($i = 0; $i < count($nodeIndex[$node]); $i++)
                   ShowNodes($xLvl + 1, $nodeIndex[$node][$i]);
             echo("         </div>\n");
          }
       }
   }

   function NodeLinks($xConn, $xNode)
   {
      global $nav;
      global $links;
      global $oConn;

      $oBranchRS = SelectNode($xConn, $xNode["parentNode"]);
      if ($oBranch = mysqli_fetch_array($oBranchRS))
         if ($oBranch["FirstChild"] != $xNode["nodeID"])
         {
            array_push($nav, array("first", "viewnode.php?op=nid=" . $oBranch["FirstChild"]));
            $links = $links . "<link rel=\"first\" type=\"text/html\" href=\"viewnode.php?op=nid=" . $oBranch["nodeID"] . "\" />\n";
         }
      if ($xNode["prevSibling"] != -1)
      {
         array_push($nav, array("previous", "viewnode.php?op=nid=" . $xNode["prevSibling"]));
         $links = $links . "      <link rel=\"previous\" type=\"text/html\" href=\"viewnode.php?op=nid=" . $xNode["prevSibling"] . "\" />\n";
      }
      if ($xNode["parentNode"] != -1)
      {
         array_push($nav, array("parent", "viewnode.php?op=nid=" . $xNode["parentNode"]));
         $links = $links . "      <link rel=\"up\" type=\"text/html\" href=\"viewnode.php?op=nid=" . $xNode["parentNode"] . "\" />\n";
      }
      else
      {
         array_push($nav, array("parent", "index.php"));
         $links = $links . "      <link rel=\"up\" type=\"text/html\" href=\"index.php\" />\n";
      }
      if ($xNode["nextSibling"] != -1)
      {
         array_push($nav, array("next", "viewnode.php?op=nid=" . $xNode["nextSibling"]));
         $links = $links . "      <link rel=\"next\" type=\"text/html\" href=\"viewnode.php?op=nid=" . $xNode["nextSibling"] . "\" />\n";
      }
      if ($oBranch)
         if ($oBranch["LastChild"] != $xNode["nodeID"])
         {
            array_push($nav, array("last", "viewnode.php?op=nid=" . $oBranch["LastChild"]));
            $links = $links . "<link rel=\"last\" type=\"text/html\" href=\"viewnode.php?op=nid=" . $oBranch["LastChild"] . "\" />\n";
         }
   }
   Close($oConn);
   include("footer.php");
?>
