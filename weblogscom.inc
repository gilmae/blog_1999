<?
  //==============================================================================
  // A quick and dirty PHP function to update Weblogs.com
  // ----------------------------------------------------
  // Last Updated: 24 October 2001
  // Author: Matt Bean
  // Email : bean@yaysoft.com
  //
  // Description:
  // This file contains a single function: pingWeblogs() which has two optional
  // arguments - the name of the weblog, and the URL for the weblog, respectively.
  // Should these arguments be omitted, the values contained in the variables
  // $default_name and $default_url will be used as the values for the weblog name
  // and the weblog URL, respectively. The function returns the text message that
  // weblogs.com sends back. You will note that I do not use any fancy libraries,
  // just simple port communications and string truncation, but it works (at least
  // until weblogs.com feels like changing formats again).
  //
  // Use:
  // You may copy this code and place it in a PHP script of your choice, the only
  // values you should need to change are the $default_name and $default_url
  // variables.
  // Here is a quick example:
  //    $result = pingWeblogs("Blog Name", "http://blogurl.com/");
  //    echo "<b>Weblogs.com says</b>: $result";
  //
  // DISCLAIMER:
  // There are NO WARRANTIES, expressed or implied, regarding the use of this
  // code. Matt Bean cannot be held responsible for anything you do with this
  // code and any problems it may cause you or anyone else, either directly or
  // indirectly. Having said that, it's just a stupid little function, and it
  // really shouldn't be able to cause any harm, but I have to cover my bases.
  //
  // Peace, love, and good Karma!
  //==============================================================================    

  $default_name = "My Weblog";
  $default_url  = "http://www.weblogurl.com/";

  function pingWeblogs($name="", $url="") {
    global $default_name, $default_url;

    if (!$name) $name = $default_name;
    if (!$url)  $url  = $default_url;    

    $fp = fsockopen("ping.blo.gs", 80, &$errnum, &$errstr);

$xml = "<methodCall>\n\t<methodName>weblogUpdates.ping</methodName>\n\t<params>\n\t\t<param>\n\t\t\t<value>$name</value>\n\t\t</param>\n\t\t<param>\n\t\t\t<value>$url</value>\n\t\t</param>\n\t</params>\n</methodCall>";


    if(!$fp) { 
      echo "$errstr ($errnum)<br>\n";
      $output = "ERROR!";
    } else  {
 $len = strlen($xml);     
# fputs($fp,"POST /RPC2 HTTP/1.1\r\n");
     fputs($fp,"POST /. HTTP/1.1\r\n");
	fputs($fp, "Host: ping.blo.gs\r\n");
	fputs($fp,"User-Agent: Aquarion's modification of Bean's Weblogs.com Updater (PHP Stylin') to make it use blo.gs \r\n");
      fputs($fp,"Content-Type: text/xml\r\n");
      fputs($fp,"Content-length: $len\r\n\r\n");
      fputs($fp,$xml);        
      $output="";
      while(!feof($fp)) $output.=fgets($fp,4096);
      fclose($fp);        
    }

    $output = preg_replace("/.*<\/boolean>/si","",$output);
    $output = preg_replace("/.*<value>/si","",$output);
    $output = preg_replace("/<\/value>.*/si","",$output);        
    return $output;
  }
?>
