<?
   include("gf.inc");
   /*if ($variables["nid"] == 7381) {
      header("HTTP/1.1 410 Gone");
      die();
   }
   else
   {
      header("Location: http://blog.avocadia.net/viewnode/" . $variables["nid"]);
      die();
   }*/
   
?>
<html>
   <head>
      <META NAME="ROBOTS" CONTENT="NOARCHIVE">
   </head>
   <body>
      <p><? if ($variables["nid"] != 7381) echo("Find me at <a href=\"http://blog.avocadia.net/viewnode/" . $variables["nid"] . "\">blog.avocadia.net/viewnode/" . $variables["nid"] . "</a>.") ?></p>
   </body>
</html>
