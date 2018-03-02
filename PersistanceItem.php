<?
/*
 * Created on Dec 4, 2004
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class PersistanceItem
 {
    var $myId;
    var $myIsDirty = false;
    
    function PersistanceItem($anId = -1)
    {
       $this->myId = $anId;
    }
    
    function setId($value)
    {
       $this->setProperty($this->myId,$value);	
    }
    function id()
    {
    	return $this->myId;
    }
    
    function isNew()
    {
    	return $this->myId == -1;
    }
    	
    function isDirty()
    {
    	return $this->myIsDirty;
    }
    
    function setProperty(&$aProperty, $value)
    {
    	if ($aProperty != $value)
    	{
	   $aProperty = $value;
    	   if (!$this->myIsDirty)
    	      $this->myIsDirty = true;
    	}   
    }	
}

class PersistanceEngine
{
   var $myConn;

   function PersisianceEngine($aConn)
   {
      $myConn = $aConn;
   }

   function Save($aCommand)
   {

   }

   function Get($aCommand)
   {

   }
}
?>
