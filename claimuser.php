<?
   include("gf.inc");
   include("Token.php");
   include("User.php");

   $theEngine = new TokenEngine(Connect());
   if ($theToken = $theEngine->GetTokenByToken($_GET["token"]))
   {
      $theUserIds = explode("|", $theToken->data());

      $theUserEngine = new UserEngine(Connect());

      $thePrimeUser = $theUserEngine->GetUser($theUserIds[0]);

      if (count($theUserIds) > 1)
         for ($ii=1; $ii<count($theUserIds);$ii++)
	    if ($theUser = $theUserEngine->GetUser($theUserIds[$ii]))
	       if ($theUser->trusted() && $theUser->id() < $thePrimeUser->id())
	       {
	          $theUserEngine->MergeUserWithUser($theUser, $thePrimeUser);
   	          $thePrimeUser = $theUser;
	       }
	       else
	          $theUserEngine->MergeUserWithUser($thePrimeUser, $theUser);
	 
      $theEngine->DeleteToken($theToken);

      $thePrimeUser->WriteCookie();
      header("Location: whoami.php");
   }   
   else
      echo("Token not found.");
?>
