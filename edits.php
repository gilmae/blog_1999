<?
   include("gf.inc");

   $nodeID = isset($variables["nid"])?$variables["nid"]:"-1";
   include_once("msf.inc");
   if (!$oConn = Connect())
   {
      die("Sorry, someone fucked up. Probably me.");
   }
   $oRS = SelectNode($oConn, $nodeID);
   if ($oRec = mysql_fetch_array($oRS))
   {
      $pageTitle="Edits of " . $oRec["nodeTitle"];
      include("header.php");
      AltDisplayNode($oRec, 0, -1, 0, 0, 0, 0, 1);
      $oRS = SelectNodeEdits($oConn, $nodeID);
      echo("            Edits:\n");
      while ($oRecc = mysql_fetch_array($oRS))
      {
         $oEditedRecc = Array("nodeID" => -1, "nodeTitle" => $oRecc["NodeTitle"], "nodeBody" => $oRecc["NodeBody"], "childNodes" => 0, "Pings" => 0, "userName" => $oRecc["userName"], "datetime" => $oRecc["EditDate"], "Edited" => "0", "url" => "");
         AltDisplayNode($oEditedRecc, 1, -1, 0, 1, 1, 5, 0);
         echo("               <hr />\n");
      }
      include("footer.php");
   }
