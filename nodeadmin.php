<?
   $pageTitle = "Node Admin";
   include_once("gf.inc");
   include("header.php");
   //$pid = isset($_GET["pid"])?$_GET["pid"]:-1;
   if (!isset($variables["pid"]))
      $variables["pid"] = -1;
   if (!isset($variables["page"]))
      $variables["page"] = 1;
   $start = ($variables["page"]-1)*10;
   if (!$oConn = Connect())
   {
      die("Sudden shock / of spring storm's thunder. / Alert! A bug.\n SQL Error");
   }
   $oRS = SelectSomeOrderedNodes($oConn, "parentNode = " . $variables["pid"], "datetime DESC LIMIT $start,10");
   echo("                 <table class=\"adminTable\">\n");
   printf("                    <tr><td colspan=\"4\">[+] <a class = \"nav\" href=\"addnode.php?op=pid=%s\">Add a Node</a></td></tr>\n", $variables["pid"]);
   while ($oRec = mysqli_fetch_array($oRS))
   {
         echo("                    <tr>\n");
         printf("                       <td style=\"width:70%%;text-align:left;\">%s - <a href=\"viewnode.php?op=nid=%s;tid=%s\">%s</a> ( %s )</td>\n", Counter2Date($oRec["datetime"]), $oRec["nodeID"], $oRec["threadID"], $oRec["nodeTitle"], $oRec["nodeType"]);
         printf("                       <td style=\"width:10%%;text-align:center;\"><a class = \"nav\" href=\"editnode.php?op=nid=%s;tid=%s\">edit</a></td>\n", $oRec["nodeID"], $oRec["threadID"]);
         printf("                       <td style=\"width:10%%;text-align:center;\"><a class = \"nav\" href=\"delnode.php?op=nid=%s\">delete</a></td>\n", $oRec["nodeID"]);
         printf("                       <td style=\"width:10%%;text-align:center;\"><a class = \"nav\" href=\"addnode.php?op=pid=%s\">add child</a></td>\n", $oRec["nodeID"]);
         echo("                    </tr>\n");
         if ($oRec["childNodes"] > 0)
         {
            echo("                    <tr>\n");
            printf("                    <td colspan=\"3\" style=\"width:90%%;text-align:left;\">&nbsp;</td>\n");
            printf("                    <td style=\"text-align:right;\" colspan=\"3\"><a class = \"nav\" href=\"nodeadmin.php?op=pid=%s\">children</a></td>\n", $oRec["nodeID"]);
            echo("                    </tr>\n");
         }
   }
   echo("                    <tr>\n");
   echo("                         <td style=\"width:70%\">&nbsp;</td>\n");
   printf("                       <td style=\"width:10%%;text-align:center;\">%s</td>\n", $variables["page"]>1?"<a href=\"nodeadmin.php?op=pid=" . $variables["pid"] . ";page=" . ($variables["page"] - 1) . "\" title=\"Previous 10 nodes\">prev</a>":"");
   printf("                       <td style=\"width:10%%;text-align:center;\">%s</td>\n", mysqli_num_rows($oRS) == 10?"<a href=\"nodeadmin.php?op=pid=" . $variables["pid"] . ";page=" . ($variables["page"] + 1) . "\" title=\"Next 10 nodes\">next</a>":"");
   echo("                         <td style=\"width:10%\">&nbsp;</td>\n");
   echo("                    </tr>\n");
   echo("                 </table>\n");
   Close($oConn);
   include("footer.php");
?>