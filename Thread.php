<?
   include_once("PersistanceItem.php");
   include_once("Node.php");
   
   class ThreadFacade
   {
      var $myConn;
      var $myRoot;
      
      function ThreadFacade($aConn)
      {
         $this->myConn = $aConn;
      }

      function Retrieve($aThreadId)
      {
         $theEngine = new NodeEngine($this->myConn);
	 $this->myRoot = $theEngine->GetNode($aThreadId);
      }

      function AddToThread(&$aNode)
      {
	 $aNode->setParentId($this->myRoot->id());
         
	 $theEngine = new NodeEngine($this->myConn);
	 $theThreadingEngine = new ThreadingEngine($this->myConn);
         
	 $theEngine->Save($aNode);
         if ($theNode = $theEngine->GetNode($aNode->parentId()))
	 {
            $aNode->setNextSiblingId(-1);
	    $aNode->setPrevSiblingId($theNode->lastChild());

	    $theEngine->Save($aNode);
	    
	    if ($thePrevSibling = $theEngine->GetNode($theNode->lastChild()))
	    {
	       $thePrevSibling->setNextSiblingId($aNode->id());
	       $theEngine->Save($thePrevSibling);
	    }
            if ($theNode->childCount() == 0)
	       $theNode->setFirstChild($aNode->id());

	    $theNode->setLastChild($aNode->id());
	 
	    do
	    {
	       $theNode->setChildCount($theNode->childCount()+1);
	       $theEngine->Save($theNode);
	
  	       $theThreadings = $theThreadingEngine->GetThreadingsByBlock($aNode->parentId());
	       if (count($theThreadings) < 1 || $theThreadings=null)
	       {
                  $theThreading = new Threading();
	          $theThreading->setBlock($aNode->parentId());
	          $theThreading->setThread($theNode->id());
	          $theThreadingEngine->Save($theThreading);
	       }   
            } while ($theNode = $theEngine->GetNode($theNode->parentId()));
	 } 
      }
   }

   class Threading extends PersistanceItem
   {
      var $myThread;
      var $myBlock;
      
      function Threading($anId = -1, $aBlock = 0, $aThread = -1)
      {
         $this->PersistanceItem($anId);
	 if ($this->isNew())
	 {
            $this->setThread($aThread);
	    $this->setBlock($aBlock);
	 }
	 else
	 {
            $this->myThread = $aThread;
	    $this->myBlock = $aBlock;
	 }
      }

      function thread()
      {
         return $this->myThread;
      }
      function setThread($value)
      {
         $this->setProperty($this->myThread, $value);
      }

      function block()
      {
         return $this->myBlock;
      }
      function setBlock($value)
      {
         $this->setProperty($this->myBlock, $value);
      }
   }

   class ThreadingEngine
   {
      var $myConn;

      function ThreadingEngine($aConn)
      {
         $this->myConn = $aConn;
      }

      function Save(&$aNode)
      {
         if ($aNode->isNew() || $aNode->isDirty())
	 {
            if ($aNode->isNew())
	       $theQuery = "insert into Threading (ThreadID, BlockID) values (" . $aNode->thread() . ", " . $aNode->block() . ");";
	    else
	       $theQuery = "update Threading set ThreadId=" . $aNode->thread() . ", BlockId=" . $aNode->block() . " where ThreadingId=" . $aNode->id() . ";";
	    

	    if (mysqli_query($theQuery))
	       if ($aNode->isNew())
	          $aNode->setId(mysqli_insert_id());
	 }
      }

      function PopulateItem($aRec)
      {
         return new Threading($aRec["ThreadingId"], $aRec["BlockId"], $aRec["ThreadId"]);
      }

      function PopulateItems($aData)
      {
	 $theThreadings = array();
	 while ($theRec = mysqli_fetch_array($aData))
	    array_push($theThreadings, $this->PopulateItem($theRec));
	 return $theThreadings;   
      }

      function GetThreading($aThreadingId)
      {
         $theQuery = "select ThreadingId, ThreadId, BlockId from Threading where ThreadingId = " . $aThreadingId . ";";

	 if ($theData = mysqli_query($theQuery))
	    if ($theRec = mysqli_fetch_array($theData))
	       return $this->PopulateItem($theRec);
      }

      function GetThreadingsByBlock($aBlock)
      {
         $theQuery = "select ThreadingId, ThreadId, BlockId from Threading where BlockId = " . $aBlock . ";";

	 if ($theData = mysqli_query($theQuery))
	    return $this->PopulateItems($theData);
      }
   }   
?>
