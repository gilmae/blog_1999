<?

$theRSS1File = "http://del.icio.us/rss/avocadia";
$theLocalCopy = "del.icio.us/del.icio.us.local";
$theRefreshTime = 3600;
$theNumPrinted = 0;
$theMaxToDisplay = 10;

class RSSParser {

   var $insideitem = false;
   var $tag = "";
   var $title = "";
   var $description = "";
   var $link = "";

   function startElement($parser, $tagName, $attrs) {
       if ($this->insideitem) {
           $this->tag = $tagName;
       } elseif ($tagName == "ITEM") {
           $this->insideitem = true;
       }
   }

   function endElement($parser, $tagName) {
      global $theNumPrinted;
      global $theMaxToDisplay;
       if ($tagName == "ITEM" && $theNumPrinted++ < $theMaxToDisplay) {
           printf("   <li>\n      <p>         <a href='%s'>%s</a>\n",
             trim($this->link),htmlspecialchars(trim($this->title)));
           printf("         <br />%s</p>\n   </li>\n",
             htmlspecialchars(trim($this->description)));
           $this->title = "";
           $this->description = "";
           $this->link = "";
           $this->insideitem = false;
       }
   }

   function characterData($parser, $data) {
       if ($this->insideitem) {
           switch ($this->tag) {
               case "TITLE":
               $this->title .= $data;
               break;
               case "DESCRIPTION":
               $this->description .= $data;
               break;
               case "LINK":
               $this->link .= $data;
               break;
           }
       }
   }
}

//if (RequiresUpdate())
//   GetRSS();

if (file_exists($theLocalCopy))
{
   $xml_parser = xml_parser_create();
   $rss_parser = new RSSParser();
   xml_set_object($xml_parser,$rss_parser);
   xml_set_element_handler($xml_parser, "startElement", "endElement");
   xml_set_character_data_handler($xml_parser, "characterData");
   echo("<ul class=\"links delicious\">\n");
   $fp = fopen($theLocalCopy, "r")
      or die("Error reading RSS data.");
   while ($data = fread($fp, 4096))
      xml_parse($xml_parser, $data, feof($fp))
          or die(sprintf("XML error: %s at line %d",
              xml_error_string(xml_get_error_code($xml_parser)),
              xml_get_current_line_number($xml_parser)));
   echo("</ul>\n");
   fclose($fp);
   xml_parser_free($xml_parser);
}


function GetRSS()
{

   global $theRSS1File;
   global $theLocalCopy;

   if ($fd = fopen($theRSS1File, "r"));
      if ($localfd = fopen($theLocalCopy, "w"));
         while (!feof ($fd))
            fputs($localfd, fgets($fd, 4096));

}

function RequiresUpdate()
{
   global $theLocalCopy;
   global $theRefreshTime;

   if (file_exists($theLocalCopy))
      return (mktime() - filemtime($theLocalCopy)) > $theRefreshTime;
   else
      return 1;
}
?>
