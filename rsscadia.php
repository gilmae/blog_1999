<?
   include("gf.inc");
   if ($fileRSS = fopen("journal.xml", "w"))
   {
      fputs($fileRSS, "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>\n");
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
         if (mysql_num_rows($oRS) > 0)
         {
            fputs($fileRSS, "    <items>\n");
            fputs($fileRSS, "      <rdf:Seq>\n");
            while ($oRec = mysql_fetch_array($oRS))
               fputs($fileRSS, "        <rdf:li rdf:resource=\"http://avocadia.net/viewnode.php?op=nid=" . $oRec["nodeID"] . "\" />\n");
            fputs($fileRSS, "      </rdf:Seq>\n");
            fputs($fileRSS, "    </items>\n");
            fputs($fileRSS, "  </channel>\n");
            mysql_data_seek($oRS, 0);
            while ($oRec = mysql_fetch_array($oRS))
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
?>
