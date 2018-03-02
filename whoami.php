<?
   include("gf.inc");
   include_once("User.php");
   include_once("Token.php");
   include("header.php");

      
   if (!$theConn = Connect())
   {
      die("Sorry, someone fucked up. Probably me.");
   }

   $theEngine = new UserEngine($theConn);
   if (isset($HTTP_COOKIE_VARS["counter"]))
   {
      $theUser = $theEngine->GetUser($HTTP_COOKIE_VARS["counter"]);
      
      echo("<form method=\"post\">\n");
      echo("   <input type=\"hidden\" name=\"userId\" value=\"" . $theUser->id() . "\" />\n");
      echo("   <div class=\"name question\"><label for=\"name\">Name:</label><input name=\"name\" id=\"name\" type=\"text\" maxlength=\"100\" value=\"" . $theUser->name() . "\" /></div>\n");
      echo("   <div class=\"email question\"><label for=\"email\">Email:</label><input name=\"email\" id=\"email\" type=\"text\" maxlength=\"100\" value=\"" . $theUser->email() . "\" /></div>\n");
      echo("   <div class=\"url question\"><label for=\"url\">Url:</label><input name=\"url\" id=\"url\" type=\"text\" maxlength=\"100\" value=\"" . $theUser->url() . "\" /></div>\n");
      echo("   <input type=\"submit\" value=\"update\" />\n");
      echo("</form>\n");
   }
   else
   {
      if (strtoupper($_SERVER["REQUEST_METHOD"]) == "POST")
      {
	 if ($HTTP_POST_VARS["submit"] == "search")
	    if ($HTTP_POST_VARS["email"] != "")
	       Search($HTTP_POST_VARS["email"]);
	    else
	       echo("<p class=\"error\">You can only claim a identity with a non-blank email address</p>\n");
         elseif ($HTTP_POST_VARS["submit"] == "merge and claim")
	    Claim();
      }

      echo("<p>I've no idea who you are. If you've left a comment here before, you can claim that prior identity. Just enter your email address below.</p>\n");
      echo("   <form method=\"post\">\n");
      echo("   <div class=\"question name\"><label for=\"name\">Name:</label><input type=\"text\" name=\"name\" id=\"name\" maxlength=\"100\" /></div>\n");
      echo("   <div class=\"question email\"><label for=\"email\">Email:</label><input type=\"text\" name=\"email\" id=\"email\" maxlength=\"100\" /></div>\n");
      echo("   <input type=\"submit\" name=\"submit\" value=\"search\" />\n");
      echo("</form>\n");
   }
   
   echo("<script src=\"script/WhoAmI.js\" />\n");
   include("footer.php");

   function Search($anEmail)
   {
      global $theEngine;

      if ($theUsers = $theEngine->getUsersByEmail($anEmail))
         if (count($theUsers) > 0)
         {
            $ii = 0;
            echo("<form method=\"post\" id=\"UserSearchResults\"><table>\n");
            echo("   <tr>\n");
            echo("      <th class=\"YouKnowMe\"><input type=\"checkbox\" name=\"YouKnowMeAll\" id=\"YouKnowMeAll\" onclick=\"SelectAllYouKnowMe();\" /></th>\n");
            echo("      <th class=\"name\">Name</th>\n");
            echo("      <th class=\"email\">Email</th>\n");
            echo("   </tr>\n");  
            foreach($theUsers as $theUser)
            {
               echo("   <tr>\n");
               printf("      <td class=\"YouKnowMe\"><input type=\"checkbox\" name=\"IKnowMe%d\" name=\"IKnowMe%d\" /></td>\n", $theUser->id(), $theUser->id());
	       printf("      <td class=\"name\"><label for=\"IKnowMe%d\">%s</label></td>\n", $theUser->id(), $theUser->name());
	       printf("      <td class=\"email\">%s</td>\n", $theUser->email());
	       echo("   </tr>\n");
	    }
	    echo("</table>\n");
	    echo("<input type=\"submit\" name=\"submit\" value=\"merge and claim\" />\n");
	    echo("</form>\n");
	 }
   }

   function Claim()
   {
      global $HTTP_POST_VARS;
      global $theEngine;

      $theUserIds = array();
      foreach ($HTTP_POST_VARS as $key=>$value)
      {
         $theMatches = array();
         if (preg_match("/IKnowMe(\d+)/", $key, $theMatches) && $value == "on")
	    array_push($theUserIds, $theMatches[1]);
      }

      if (count($theUserIds) > 0)
      {
        // Get token, record identities, send email
	$theToken = new Token();
	$theToken->generateToken();
	echo(join("|", $theUserIds));
        $theToken->setData(join("|", $theUserIds));

        $theUser = $theEngine->GetUser($theUserIds[0]);
	$theTokenEngine = new TokenEngine(Connect());
	$theTokenEngine->Save($theToken);
       
        echo($theToken->id());
	echo("**" . mail($theUser->email(), "Confirm user claim on avocadia.net", "Just click the link, http://avocadia.net/claimuser.php?token=" . $theToken->tokentext(), "From: gilmae@avocadia.net\r\b") . "**");
      }
   }	 
?>
   
