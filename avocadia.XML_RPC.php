<?
   include("gf.inc");

   function PingBack($args)
   {
      $sourceFile = "";
      $localURL = $args[1];
      $sourceURL = $args[0];
      if (preg_match("/op=nid=(\d+)/i", $localURL, $matches))
         $nodeID = $matches[1];
      else
         return new IXR_Error(33, $localURL . " cannot be parsed. Valid target URI's are the form http://www.avocadia.net/viewnode.php?op=nid=xx where xx is the identifier of the pingable resource.");
      if (!$oConn = Connect())
         return new IXR_Error(50, "Some sort of mySQL error going on ...or rather, sonar has jammed, Captain!");
      $oRS = SelectNode($oConn, $nodeID);
      if (!$oRS)
         return new IXR_Error(50, "Some sort of mySQL error going on ...or rather, sonar has jammed, Captain!");
      if (mysql_num_rows($oRS) == 0)
         return new IXR_Error(32, $localURL . " does not exist. Check your link. Valid target URI's are the form http://www.avocadia.net/viewnode.php?op=nid=xx where xx is the identifier of the pingable resource. Remember, you must not ping whales, it messes with their minds...'Wow, this krill is, like, crawling into my blubber. Wow, whale.'");
      if (!$sourceFD = fopen($sourceURL, "r"))
         return new IXR_Error(16, $sourceURL . " does not exist. So what's up with that, hmmm?.");
      else
      {
         while (!feof($sourceFD))
            $sourceFile = $sourceFile . fgets($sourceFD, 65536);
         if (!stristr($sourceFile, $localURL))
            return new IXR_Error(17, $sourceURL . " does not appear to contain a link to " . $localURL . ".");
         else
         {
            $regEx = "<title>(.*)<\/title>";
            if (preg_match("/$regEx/i", $sourceFile, $matches))
               $title = $matches[1];
            else
               $title = $sourceURL;
         }
      }
      $oRS = SelectPingbackByNodeAndSource($oConn, $nodeID, $sourceURL);
      if (!$oRS)
         return new IXR_Error(50, "Some sort of mySQL error going on ...or rather, sonar has jammed, Captain!");
      if (mysql_num_rows($oRS) > 0)
         return new IXR_Error(48, $sourceURL . " has already pinged " . $localURL . " and should by now have formulated a firing pattern. What are you bloody messing around for?");

      // all data verified
      if (InsertPing($oConn, $nodeID, $sourceURL, $title) > 0)
      {
         IncrementPings($oConn, $nodeID);
         return "Captain! We've been pinged!";
      }
      else
         return new IXR_Error(50, "Some sort of mySQL error going on ...or rather, sonar has jammed, Captain!");
   }

   $xmlServer = new IXR_Server(array('ping.pingback' => 'PingBack'));
?>

