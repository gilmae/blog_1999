<?
   $sideLinks = array();
   include("gf.inc");
   $category = isset($variables["category"])?$variables["category"]:"";
   switch ($category)
   {
      case "n":
      case "nanowrimo":
         $category="n";
         $pageTitle = "NaNoWriMo";
         break;
      case "f": // fallthru
      case "fiction":
         $category="f";
         $pageTitle = "Fiction";
         break;
      case "r": //fall thru
      case "random":
         $category="r";
         $pageTitle = "Attacks of Random";
         break;
      case "t": // fallthru
      case "tyranny":
         $category="t";
         $pageTitle = "Tyranny of the Minority";
         break;
      case "journal":
      default:
         $category="j";
         $pageTitle = "Journal";
   }
   $pageBanner = $pageTitle;
   $variables["index"] = 1;
   if (!$oConn = Connect())
   {
      die("Sorry, someone fucked up. Probably me.");
   }
   $limit = 8;
   $whereStr = "parentNode=-1";
   $whereStr = $whereStr . ($category==""?"":" AND nodeType = '$category'");
   $oRS = SelectSomeOrderedNodes($oConn, $whereStr, "datetime DESC LIMIT 0,$limit");
   include("header.php");
   $sDay = "";
   while ($oRecc = mysqli_fetch_array($oRS))
   {
      if ($sDay != Counter2Day($oRecc["datetime"]))
      {
         $sDay = Counter2Day($oRecc["datetime"]);
         printf("         <h3>%s</h3>\n", $sDay);
      }
      AltDisplayNode($oRecc, 0, false, 1, "", 0);
   }
   Close($oConn);
   include("footer.php");
?>

