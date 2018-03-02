<?
   include_once("gf.inc");
   $pageTitle = "Show Links";
   if (!$oConn = Connect())
   {
      die("Sorry, someone fucked up. Probably me.");
   }
   $oLinksRS = SelectLinks($oConn);
   include("header.php");
   if (mysql_num_rows($oLinksRS) > 0)
   {
      echo("            <table class=\"adminTable\" summary=\"Table of links to external sites and links for their administration\" style=\"width:100%;padding:0px;margin:0px;\">\n");
      echo("               <tr><td colspan=\"3\"><a href=\"addlink.php\" title=\"Add a link\">Add Link</a></td></tr>\n");
      while ($oLink = mysql_fetch_array($oLinksRS))
      {
         echo("               <tr>\n");
         printf("                  <td style=\"width:80%%;text-align:left;\"><a href=\"%s\" title=\"%s\">%s</a></td>\n", $oLink["Link"], $oLink["LinkDescription"], $oLink["LinkName"]);
         printf("                  <td style=\"width:10%%;text-align:center;\"><a href=\"editlink.php?op=lid=%s\" title=\"Edit %s\">Edit</a></td>\n", $oLink["LinkID"], $oLink["LinkName"]);
         printf("                  <td style=\"width:10%%;text-align:center;\"><a href=\"dellink.php?op=lid=%s\" title=\"Delete %s\">Delete</a></td>\n", $oLink["LinkID"], $oLink["LinkName"]);
         echo("               </tr>\n");
      }
      echo("            </table>\n");
   }
   include("footer.php");
?>