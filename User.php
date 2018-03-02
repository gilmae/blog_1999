<?
   include_once ("PersistanceItem.php");
   include("Node.php");

   class User extends PersistanceItem
   {
      var $myName;
      var $myEmail;
      var $myUrl;
      var $myTrusted;

      function User($anId = -1, $aName = "", $anEmail = "", $aUrl = "", $aTrusted = 0)
      {
         $this->PersistanceItem($anId);
	 if ($this->isNew)
	 {
            $this->setName($aName);
            $this->setEmail($anEmail);
            $this->setUrl($aUrl);
	    $this->setTrusted($aTrusted);
	 }
	 else
	 {
            $this->myName = $aName;
            $this->myEmail = $anEmail;
            $this->myUrl = $aUrl;
	    $this->myTrusted = $aTrusted;
	 }
      }
      
      function name()
      {
      	 return $this->myName;
      }
      function setName($value)
      {
      	 $this->setProperty($this->myName,$value);
      }
      
      function email()
      {
      	 return $this->myEmail;
      }
      function setEmail($value)
      {
      	 $this->setProperty($this->myEmail, $value);
      }
      
      function url()
      {
      	 return $this->myUrl;
      }
      function setUrl($value)
      {
      	 $this->setProperty($this->myUrl, $value);
      }

      function trusted()
      {
         return $this->myTrusted;
      }
      function setTrusted($value)
      {
         $this->setProperty($this->myTrusted, $value);
      }

      function MakeNode()
      {
	 $theNode = new Node();
         $theNode->setAuthorId($this->id());
	 $theNode->setPublic($this->trusted());
         return $theNode;
      }
   }

   class UserEngine
   {
      var $myConn;

      function UserEngine($aConn)
      {
         $this->myConn = $aConn;
      }

      function GetUser($aUserId)
      {
         if ($theData = mysql_query("select Counter, userName, email, url, trusted from users where counter=" . $aUserId . ";"))
            if ($theRec = mysql_fetch_array($theData))
               return $this->PopulateItem($theRec);
      }

      function GetUserByIdentification($aName, $anEmail, $aUrl)
      {
         
	 $theQuery = "select Counter, userName, email, url, trusted from users where userName = '" . $aName . "' and email = '" . $anEmail . "';";

	 if ($theData = mysql_query($theQuery))
	    return $this->PopulateItems($theData);
      }	    
      function GetUsersByEmail($anEmail)
      {
         $theQuery = "select Counter, userName, email, url, trusted from users where email='" . $anEmail . ";";
	 if ($theData = mysql_query($theQuery))
	    return $this->PopulateItems($theData);
      }
      
      function PopulateItem($aRec)
      {
         return new User($aRec["Counter"], $aRec["userName"], $aRec["email"], $aRec["url"], $aRec["trusted"]);
      }

      function PopulateItems($aData)
      {
         $theUsers = array();
	 while ($theRec = mysql_fetch_array($aData))
	    array_push($theUsers, $this->PopulateItem($theRec));
	 return $theUsers;   
      }
      
      function Save(&$aUser)
      {
         if ($aUser->isDirty())
         {
            if ($aUser->isNew())
               $sql = "insert into users (userName, email, url, trusted) values ('" . $aUser->name() . "', '" . $aUser->email() . "', '" . $aUser->url() . "', " . $aUser->trusted() . ");";
            else   
               $sql = "update users set userName = '" . $aUser->name() . "', email = '" . $aUser->email() . "', url='" . $aUser->url() . "', trusted = " . $aUser->trusted() . "';";         
            
       
            if (mysql_query($sql))
               if ($aUser->isNew())
   	          $aUser->setId(mysql_insert_id());
	}	  
      }
   }
?>
