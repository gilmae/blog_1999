<?
   $sideLinks = array();
   include("gf.inc");

   include_once("msf.inc");
   $variables["fiction"] = 1;
   if (!$oConn = Connect())
   {
      die("Sorry, someone fucked up. Probably me.");
   }
   $limit = 8;
   $oRS = SelectNodeTypeCategories($oConn, 'f', -1);
   $pageTitle = "Fiction";
   include("header.php");
   while ($oRec = mysql_fetch_array($oRS))
   {
      printf("            <div class=\"node\" style=\"width:98%%;margin-left:1%%\">\n");
      printf("               <div class=\"nodeHeader\" >\n");
      printf("                  <div class=\"nodeHeaderLeft\"><h4>&nbsp;%s</h4></div>\n", $oRec["Category"]);
      printf("                  <div class=\"nodeHeaderRight\">&nbsp;</div>&nbsp;\n");
      echo("               </div>\n");
      printf("               <div class=\"nodeBody\">%s&nbsp;</div>\n", $oRec["CategoryDesc"]);
      $whereStr = "nc.CategoryID = " . $oRec["CategoryID"];
      $orderStr = "n.datetime DESC LIMIT 0,1";
      $oNodesRS = SelectSomeNodeCategoryNodes($oConn, $whereStr, $orderStr);
      echo("               <div class=\"nodeFooterLeft\">");
      if ($oNode = mysql_fetch_array($oNodesRS))
         printf("                  <a href=\"viewnode.php?op=nid=%s;tid=%s\">Latest Episode</a>\n", $oNode["nodeID"], $oNode["threadID"]);
      else
         printf("                  No episodes posted.\n");
      echo("               </div>\n");
      echo("            </div>\n");
      echo("            <br />");
   }



   /*if ($oRecc = mysql_fetch_array($oRS))
      if ($oRecc["public"])
         DisplayNode($oRecc, 0, -1, 0, 0, 0, 0, 1);
   while ($oRecc = mysql_fetch_array($oRS))
      if ($oRecc["public"])
         if (CounterIsToday($oRecc["datetime"]))
            DisplayNode($oRecc, 0, -1, 0, 0, 0, 0, 1);
         else
            DisplayNode($oRecc, 0, -1, 1, 0, 0, 0, 1);*/
   Close($oConn);
   include("footer.php");
?>

