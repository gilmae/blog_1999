<?
   include_once("gf.inc");
   $pageTitle = "Search Nodes";
   include("header.php");
   $term = isset($HTTP_POST_VARS["term"])?$HTTP_POST_VARS["term"]:"";
   $whereClause = "";
   if (!$oConn = Connect())
   {
      die("Sorry, someone fucked up. Probably me.");
   }
   $badTerms = Array("/\band\b/i", "/\bthe\b/i", "/\bor\b/i", "/\bwhere\b/i", "/\bthere\b/i", "/\byou\b/i", "/\bnot\b/i","/\band\b/i","/\bhome\b/i","/\bor\b/i","/\bif\b/i","/\bit\b/i","/\bits\b/i","/\ball\b/i","/\bto\b/i","/\bfor\b/i","/\bthe\b/i","/\bwhen\b/i","/\bwho\b/i","/\bthen\b/i","/\bthan\b/i","/\bof\b/i","/\bhas\b/i","/\bthere\b/i","/\btheir\b/i","/\btheyre \b/i","/\bwere\b/i","/\bwe\b/i","/\bas\b/i","/\bi\b/i","/\bam\b/i","/\ba\b/i","/\bc\b/i","/\bs\b/i","/\bwill\b/i","/\bwas\b/i","/\bin\b/i","/\ban\b/i","/\bis\b/i","/\bbe\b/i","/\bus\b/i","/\buse\b/i","/\bvia\b/i","/\bno\b/i","/\bon\b/i","/\bkb\b/i","/\bour\b/i","/\bhow\b/i","/\bdo\b/i","/\bno\b/i","/\bget\b/i","/\byou\b/i","/\bsee\b/i","/\bwhat\b/i","/\bwww\b/i","/\bhello\b/i","/\bare\b/i","/\byoure\b/i","/\bfrom\b/i","/\bask\b/i","/\bthem\b/i","/\bup\b/i","/\bwith\b/i","/\bat\b/i","/\bwhats\b/i","/\bgo\b/i","/\bthey\b/i","/\beg\b/i","/\bie\b/i","/\bone\b/i","/\bcan\b/i","/\bonly\b/i","/\bmore\b/i","/\bper\b/i","/\bhi\b/i","/\bby\b/i","/\bthis\b/i","/\bmy\b/i","/\byour\b/i","/\bnot\b/i","/\bjust\b/i","/\bwhere\b/i","/\bnor\b/i","/\btry\b/i","/\btoo\b/i","/\bme \b/i","/\bblah\b/i","/\babc\b/i","/\bxyz\b/i");
   $term = preg_replace($badTerms, "", $term);
   $term = implode(" ", explode("  ", $term)); // kill the double spaces left behind when terms were removed.
   $terms = explode(" ", $term);
   for ($i=0;$i<count($terms);$i++)
      if (strlen($terms[$i]) > 1)
         $whereClause = $whereClause . (($whereClause != "")?" AND ":"") . "(nodeTitle RLIKE '[[:<:]]" . trim($terms[$i]) . "[[:>:]]' OR nodeBody RLIKE '[[:<:]]" . trim($terms[$i]) . "[[:>:]]' OR nodePrecise RLIKE '[[:<:]]" . trim($terms[$i]) . "[[:>:]]')";
   if ($whereClause <> "")
   {
      printf("            <p>Searching for %s.", $term);
      $oRS = SelectSomeOrderedNodes($oConn, $whereClause, "nodeID DESC");
      printf(" Returned %s results...</p>\n", mysql_num_rows($oRS));
      echo("            <ul>\n");
      while ($oRec = mysql_fetch_array($oRS))
         printf("               <li><a href=\"viewnode.php?op=nid=%s;term=%s\">%s</a>, %s, %s</li>\n", $oRec["nodeID"], $term, $oRec["nodeTitle"], Counter2Date($oRec["datetime"]), $oRec["typeName"]);
      echo("            </ul>\n");
   }
   include("footer.php");
?>
