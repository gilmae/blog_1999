<?
   // general functions

   include("IXR_Library.inc.php");
   include("msf.inc");
   $variables = Array();
   $op = isset($_GET["op"])?$_GET["op"]:"";
   if ($op != "")
   {
      $op = explode(";", $op);
      if (count($op) > 0)
      for ($i=0; $i < count($op); $i++)
      {
         $line = explode("=", $op[$i]);
         $variables[$line[0]] = $line[1];
      }
   }
   if (isset($HTTP_COOKIE_VARS["dateLastHere"]))
      $variables["dateLastHere"] = $HTTP_COOKIE_VARS["dateLastHere"];
   else
   {
      $variables["dateLastHere"] = isset($HTTP_COOKIE_VARS["dateLastLogged"])?$HTTP_COOKIE_VARS["dateLastLogged"]:Now2Counter();
      setcookie("dateLastHere", $variables["dateLastHere"]);
      setcookie("dateLastLogged", Now2Counter(), time() + 7776000);
      $variables["referer"] = isset($HTTP_SERVER_VARS["HTTP_REFERER"])?$HTTP_SERVER_VARS["HTTP_REFERER"]:"";
   }
   
   function AltDisplayNode($oRecc, $xLvl, $xFullDate = true, $xWithFooter=1, $xTerm = "", $maxLevel=5)
   {
      $nesting=5;
      if (!$oConn = Connect())
      {
         die("Sorry, someone fucked up. Probably me.");
      }
      /*$offset = $nesting*$xLvl;
      printf("         <div class=\"node\" id=\"node%s\">\n", $oRecc["nodeID"]);
      printf("            <div class=\"nodeHead\">\n"); 
      printf("               <h4>%s</h4>\n", $oRecc["nodeTitle"]);
      printf("            </div>\n");
      printf("            <div class=\"nodeBody\">\n");
      printf("               <p class=\"meta\">%s\n", $xFullDate?Counter2Date($oRecc["datetime"]):Counter2Time($oRecc["datetime"]));
      if ($oRecc["url"] == "") 
         printf("               | %s\n", $oRecc["userName"]);
      else
         printf("               | <a href=\"redirect.php?op=nid=%s\" title=\"%s\">%s</a>\n", $oRecc["nodeID"], $oRecc["userName"], $oRecc["userName"]);
      printf("               | <a href=\"viewnode.php?op=nid=%s\"title=\"permanent link to this node\">link</a>\n", $oRecc["nodeID"]);
      if ($oRecc["Edited"] == "1")
         printf("               | <a href=\"edits.php?op=nid=%s\"title=\"Editing performed on this node\">Edited</a>\n", $oRecc["nodeID"]);
      echo("               </p>\n");
      printf("               %s\n", MLP($oRecc["nodeBody"], $xTerm));
      $oCats = SelectNodeCategories($oConn, $oRecc["nodeID"]);
      if (mysqli_num_rows($oCats) > 0)
      {
         $catCount = 0;
         echo("               <p>\n");
         while ($oCat = mysqli_fetch_array($oCats))
            printf("                  %s", $oCat["Category"]);
         echo("               </p>\n"); 
      }   
      printf("            </div>\n");
      if ($xWithFooter == 1)
      {
         echo("            <ul>");       
         if ($oRecc["datetime"] >= strtotime("-3 months"))
            printf("               <li><a href=\"addcomment.php?op=nid=%s\" title=\"Add a comment to this post\">Make a comment</a></li>\n", $oRecc["nodeID"], $oRecc["nodeID"]);
         if (($oRecc["childNodes"] > 0) && $xLvl >= $maxLevel)
            printf("               <li><a href=\"viewnode.php?op=nid=%s\" title=\"View thread\">%s followup%s</a></li>\n", $oRecc["nodeID"], $oRecc["childNodes"], $oRecc["childNodes"] > 0?"s":"");
         echo("            </ul>");       
      }   
      printf("         </div>\n");*/
      //printf("            <li>\n");
      printf("               <div class=\"node\">\n");
      printf("                  <h3><a href=\"viewnode.php?op=nid=%s\"title=\"permanent link to this node\">%s</a></h3>\n", $oRecc["nodeID"], $oRecc["nodeTitle"]);
      printf("                  <ul class=\"nodeMeta\">\n");
      printf("                     <li>%s</li>\n", $oRecc["userName"]);
      printf("                     <li>%s</li>\n", Counter2Date($oRecc["datetime"]));
      printf("                     <li>");
      if ($oRecc["datetime"] >= strtotime("-3 months"))
         printf("<a href=\"addcomment.php?op=nid=%s\" title=\"Add a comment to this post\">followup</a>", $oRecc["nodeID"]);
      if ($xLvl >= $maxLevel)
         printf(" <a href=\"viewnode.php?op=nid=%s\" title=\"%s\">(%s)%s</a>", $oRecc["nodeID"], $oRecc["nodeTitle"], $oRecc["childNodes"], $oRecc["datetime"] < strtotime("-3 months")?" followups":"");
	 
	
      printf("</li>\n");
      printf("                  </ul>\n");
      printf("                  <div class=\"body\">\n");
      printf("                     %s\n", MLP($oRecc["nodeBody"], $xTerm));
      printf("                  </div>\n");
      printf("               </div>\n");
      //printf("            </li>\n");
      
   }   

   function Counter2Date($xCounter)
   {
      return Date("l, d/m/Y H:i:s", $xCounter);
   }
   function Counter2Time($xCounter)
   {
      return Date("H:i:s", $xCounter);
   }
  
   function Counter2Day($xCounter)
   {
      return Date("l, d/m/Y", $xCounter);
   }
   
   function CounterIsToday($xCounter)
   {
      return ((time() + 57600 - $xCounter) < 86400)?1:0;   
   }   
   
   function DateDiff($dDate)
   {
      $arrPeriods = Array(Array("week",604800), Array("day",86400), Array("hour",3600), Array("minute",60), Array("second",1));
      $arrDateDiffs = Array();
      $sDateDiff = "";
      for ($ii=0;$ii<count($arrPeriods);$ii++)
      {
         $arrDateDiffs[$ii] = floor($dDate / $arrPeriods[$ii][1]);
         $dDate = $dDate % $arrPeriods[$ii][1];
      }
      for ($ii=0;$ii<count($arrPeriods);$ii++)
         if ($sDateDiff == "" && $arrDateDiffs[$ii] > 0)
            $sDateDiff .= ($arrDateDiffs[$ii] . " " . $arrPeriods[$ii][0] . ($arrDateDiffs[$ii]>1?"s ":" "));
      return $sDateDiff . " ago";
   }
   
   function GetCycle($xNow, $xCycle)
   {
      $deathOfReise = 610502400;
      $crowningOfJoan = 613276200;
      
      if ($xNow <= $deathOfReise)
         return SuffixNumber($xCycle) . " Reise";
      else if ($xNow >= $crowningOfJoan)
         return SuffixNumber($xCycle - 20) . " Joan";
      else
         return "Mourning";
   }

   function GetEmpireDate()
   {
      $secondsInACycle = 30412800;
      $secondsInATurn = 2764800;
      $secondsInAPhase = 345600;
      $secondsInADay = 86400;
      $phases = array(1 => "Crescent Rising",
                   2 => "Half Rising",
                   3 => "Gibbous Rising",
                   4 => "Ascendant",
                   5 => "Gibbous Falling",
                   6 => "Half Falling",
                   7 => "Crescent Falling",
                   8 => "Devoured");

      $turns = array(1 => "1st Hale",
                  2 => "2nd Hale",
                  3 => "3rd Hale",
                  4 => "1st Url",
                  5 => "2nd Url",
                  6 => "3rd Url",
                  7 => "4th Url",
                  8 => "1st Seric",
                  9 => "2nd Seric",
                  10 => "3rd Seric",
                  11 => "4th Seric");

      $now = time();
      $cycle = floor($now / $secondsInACycle) + 1;
      $cycleName = GetCycle($now, $cycle);
      $now = $now % $secondsInACycle;
      $turn = floor($now / $secondsInATurn) + 1;
      $now = $now % $secondsInATurn;
      $phase  = floor($now / $secondsInAPhase) + 1;
      $now = $now % $secondsInAPhase;
      $day = floor($now / $secondsInADay) + 1;
      return SuffixNumber($day) . " " . $phases[$phase] . ", " . $turns[$turn] . ", " . $cycleName;
   }

   function MakeStringSafe($xString)
   {
      $safeStr = preg_replace("/(<\/?)(\w*)([^>]*>)/", "", $xString);
      $safeStr = htmlspecialchars($safeStr, ENT_QUOTES);
      $safeStr = nl2br($safeStr);
      return $safeStr;
   }

   function MLP($xStr, $xTerm)
   {
      global $links;
      $search = array();
      $replaces = array();
      if ($xTerm != "")
      {
         $terms = explode(" ", $xTerm);
         for ($i=0;$i<count($terms);$i++)
         {
            if (strlen($terms[$i]) > 1)
            {
               array_push($search, "/\b" . $terms[$i] . "\b/i");
               array_push($replaces, "<span class=\"searchTerm\">\\0</span>");
            }   
         }    
         for ($i=0;$i<count($search);$i++)
            //if (preg_match($search[$i], $xStr))
               $returnStr = preg_replace($search, $replaces, $xStr);
      }            
      if (!$oConn = Connect())
      {
         die("Sorry, someone fucked up. Probably me.");
      }

      $oRS = SelectSmartLinks($oConn);
      while ($oRec = mysqli_fetch_array($oRS))
      {
          array_push($search, $oRec["SmartLinkKey"]);
          array_push($replaces, $oRec["SmartLink"]);
      }    
      for ($i = 0; $i < count($search); $i++)
      {
         if (preg_match($search[$i], $xStr))
            $xStr = preg_replace($search[$i], $replaces[$i], $xStr);
      }
    
      $returnStr = $xStr;

      return $returnStr;
   }

   function Now2Counter()
   {
      return time() + 57600;
   }

   function SuffixNumber($xNum)
   {
      $units = $xNum % 10;
      $tens = floor($xNum  / 10) % 100;
      if ($units == 1 && $tens != 1)
         return $xNum . "st";
      else if ($units == 2 && $tens != 1)
         return $xNum . "nd";
      else if ($units == 3 && $tens != 1)
         return $xNum . "rd";
      else
         return $xNum . "th";
   }

   function VerifyAdmin($xUID, $xPWD)
   {
      if (!$oConn = Connect())
      {
         return -99;
      }
      if ($xUID == "")
      {
         return -1;
      }
      $oUserRS = SelectUser($oConn, $xUID);
      if (!$oUser = mysqli_fetch_array($oUserRS))
         return -1;
      if ($xPWD != $oUser["password"])
      {
         return -2;
      }
      if (!$oUser["admin"])
      {
         return -3; 
      }
      return $oUser["counter"];
   }   
   
   function ZeroLead($num, $min)
   {
       if ($num < $min)
          return "0" . $num;
       else
          return $num;
   }
   
   function RSScadia()
   {
      if ($fileRSS = fopen("journal.xml", "w"))
      {
         fputs($fileRSS, "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n");
         fputs($fileRSS, "<rdf:RDF\n");
         fputs($fileRSS, "  xmlns:rdf=\"http://www.w3.org/1999/02/22-rdf-syntax-ns#\"\n");
         fputs($fileRSS, "  xmlns:dc=\"http://purl.org/dc/elements/1.1/\"\n");
         fputs($fileRSS, "  xmlns:sy=\"http://purl.org/rss/1.0/modules/syndication/\"\n");
         fputs($fileRSS, "  xmlns:admin=\"http://webns.net/mvcb/\"\n");
         fputs($fileRSS, "  xmlns:content=\"http://purl.org/rss/1.0/modules/content/\"\n");
         fputs($fileRSS, "  xmlns=\"http://purl.org/rss/1.0/\">\n");
         fputs($fileRSS, "  <channel rdf:about=\"http://avocadia.net\">\n");
         fputs($fileRSS, "    <title>Avocadia</title>\n");
         fputs($fileRSS, "    <link>http://avocadia.net/</link>\n");
         fputs($fileRSS, "    <description>Expect the expected</description>\n");
         fputs($fileRSS, "    <dc:language>en-au</dc:language>\n");
         fputs($fileRSS, "    <dc:creator>gilmae</dc:creator>\n");
         fputs($fileRSS, "    <dc:date>" . date("Y-m-d\TH:i:s+10", time()) . "</dc:date>\n");
         fputs($fileRSS, "    <admin:generatorAgent rdf:resource=\"http://avocadia.net\" />\n");
         fputs($fileRSS, "    <admin:errorReportsTo rdf:resource=\"mailto:gilmaevski@mail.ru\"/>\n");
         fputs($fileRSS, "    <sy:updatePeriod>hourly</sy:updatePeriod>\n");
         fputs($fileRSS, "    <sy:updateFrequency>1</sy:updateFrequency>\n");
         fputs($fileRSS, "    <sy:updateBase>2000-01-01T12:00+00:00</sy:updateBase>\n");
         if ($oConn = Connect())
         {
            $whereStr = "1=1";
            $oRS = SelectSomeOrderedNodes($oConn, "parentNode=-1 AND nodeType='j'", "datetime DESC LIMIT 0,8");
            if (mysqli_num_rows($oRS) > 0)
            {
               fputs($fileRSS, "    <items>\n");
               fputs($fileRSS, "      <rdf:Seq>\n");
               while ($oRec = mysqli_fetch_array($oRS))
                  fputs($fileRSS, "        <rdf:li rdf:resource=\"http://avocadia.net/viewnode.php?op=nid=" . $oRec["nodeID"] . "\" />\n");
               fputs($fileRSS, "      </rdf:Seq>\n");
               fputs($fileRSS, "    </items>\n");
               fputs($fileRSS, "  </channel>\n");
               mysqli_data_seek($oRS, 0);
               while ($oRec = mysqli_fetch_array($oRS))
               {
                  fputs($fileRSS, "   <item rdf:about=\"http://avocadia.net/viewnode.php?op=nid=" . $oRec["nodeID"] . "\">\n");
                  fputs($fileRSS, "      <title>" . htmlspecialchars($oRec["nodeTitle"]) . "</title>\n");
                  fputs($fileRSS, "      <description>" . htmlspecialchars($oRec["nodePrecise"]) . "</description>\n");
                  fputs($fileRSS, "      <content:encoded><![CDATA[" . $oRec["nodeBody"] . "]]></content:encoded>\n");
                  fputs($fileRSS, "      <link>http://avocadia.net/viewnode.php?op=nid=" . $oRec["nodeID"] . "</link>\n");
                  fputs($fileRSS, "      <dc:subject>Journal</dc:subject>\n");
                  fputs($fileRSS, "      <dc:creator>gilmae</dc:creator>\n");
                  fputs($fileRSS, "      <dc:date>" . date("Y-m-d\TH:i:s+10", $oRec["datetime"]) . "</dc:date>\n");
                  fputs($fileRSS, "   </item>\n");
               }
                  Close($oConn);
            }
         }
         else
            fputs($fileRSS, "  </channel>\n");
         fputs($fileRSS, "</rdf:RDF>\n");
         fclose($fileRSS);
      }
   }   
   
?>
