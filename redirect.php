<?
   include("gf.inc");
   $nid = isset($variables["nid"])?$variables["nid"]:-1;
   $type = isset($variables["type"])?$variables["type"]:"www";
   if (!$oConn = Connect())
   {
      die("Sorry, someone fucked up. Probably me.");
   }
   $oRS = SelectNode($oConn, $nid);
   if ($oRec = mysqli_fetch_array($oRS))
      if ($type == "www")
      {
         Close($oConn);
         header("Location: http://" . $oRec["url"]);
      }
      elseif ($type == "email")
      {
         Close($oConn);
         header("Location: mailto:" . $oRec["email"]);
      }
   include("header.php");
   echo("<h4>Sorry, the link you requested does not exist.</h4>\n");
   include("footer.php");
   Close($oConn);
?>
