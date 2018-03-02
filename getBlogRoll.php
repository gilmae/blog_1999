<?
   header("Content-type: text/xml");
   $myBlogRoll = "";
   $myBlogRollLifetime = 3600;
   $myRemoteFile = "http://blo.gs/387/favorites.rss";
   //$myRemoteFile = "test.xml";
   $myLocalFile = "library/rss/favourites.rss";
   
   if (time() - filemtime("favourites.html") > $myBlogRollLifetime)
   {
      $myBlogRoll = GetRemoteRoll();
      WriteLocalRoll($myBlogRoll);
   }   
   echo($myBlogRoll);
       
   function GetRemoteRoll()
   {
      if ($fileBlogs = fopen("http://blo.gs/387/favorites.rss", "r"))
      {
         while (!feof($fileBlogs))
            $sBlogs .= fgets($fileBlogs, 4096);
         fclose($fileBlogs);
         return $sBlogs;
      }
      else
         return "";
   }

   function WriteLocalRoll($aBlogRoll)
   {
      include("class.RSS.php");
      $myHTML="<div id=\"blogRoll\">\n";
      $myHTML = $myHTML . "<h5>from <a href=\"http://blo.gs\" title=\"Blo.gs, a blogroll service\">blo.gs</a><br />updated <span id=\"BlogRollUpdateTime\" class=\"UTC\">" . date("r", time()) . "<!--" . time() . "--></span></h5>\n";
      $myHTML = $myHTML . "<ul id=\"BlogRollList\">";
      if ($aBlogRoll != "")
      {
         $myRSS = new RSS($aBlogRoll);
         $myItems = $myRSS->getAllItems();
         for ($ii=0;$ii<count($myItems);$ii++)
            $myHTML = $myHTML . "<li><a href=\"" . $myItems[$ii]["LINK"] . "\" title=\"" . $myItems[$ii]["TITLE"] . " last update - " . $myItems[$ii]["PUBDATE"] . "\">" . $myItems[$ii]["TITLE"] . "</a><br />updated <span class=\"UTC\">" . $myItems[$ii]["PUBDATE"] . "<!--" . strtotime($myItems[$ii]["PUBDATE"]) . "--></span></li>\n";
      }
      $myHTML = $myHTML . "</ul>"; 
      if ($fileLocalBlogs = fopen("favourites.html", "w"))
      {   
         fputs($fileLocalBlogs, $myHTML);
         fclose($fileLocalBlogs);
      }
   }      
?>