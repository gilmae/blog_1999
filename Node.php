<?php
/*
 * Created on Dec 3, 2004
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
   require_once("PersistanceItem.php");
   
   class Node extends PersistanceItem
   {
      var $myTitle;
      var $myPrecise;
      var $myBody;
      var $myParentId;
      var $myType;
      var $myChildCount;
      var $myAuthorId;
      var $myDateIssued;
      var $myPublic;
      var $myNextSibling;
      var $myPrevSibling;
      var $myFirstChild;
      var $myLastChild;

      function Node($anId = -1, $aBody = "", $aTitle = "", $aPrecise = "", $aParentId = -1, $aChildCount=0, $anAuthorId = -1, $aDateIssued = "", $aPublic = 0, $aNextSibling = -1, $aPrevSibling = -1, $aFirstChild = -1, $aLastChild = -1)
      {
         $this->PersistanceItem($anId);
         if ($aDateIssued == "")
	    $this->myDateIssued = Time(); 
	 else
	    $this->myDateIssued = $aDateIssued;
         $this->myTitle = $aTitle;
         $this->myPrecise = $aPrecise;
         $this->myBody = $aBody;
         $this->myType = 'j';
         $this->myParentId = $aParentId;
         $this->myChildCount = $aChildCount;
	 $this->myPublic = $aPublic;
	 $this->myAuthorId = $anAuthorId;
	 $this->myNextSibling = $aNextSibling;
	 $this->myPrevSibling = $aPrevSibling;
	 $this->myFirstChild = $aFirstChild;
	 $this->myLastChild = $aLastChild;
      }
      
      function setTitle($value)
      {
         $this->setProperty($this->myTitle, $value);	
      }
      function title()
      {
      	return $this->myTitle;
      }
      
      function setPrecise($value)
      {
         $this->setProperty($this->myPrecise, $value);
      }
      function precise()
      {
      	 return $this->myPrecise;
      }
      
      function setBody($value)
      {
      	 $this->setProperty($this->myBody, $value);
      }
      function body()
      {
      	 return $this->myBody;
      }
      
      function setType($value)
      {
      	 $this->setProperty($this->myType, $value);
      }
      function type()
      {
      	 return $this->myType;
      }
      
      function setParentId($value)
      {
      	 $this->setProperty($this->myParentId, $value);
      }
      function parentId()
      {
      	 return $this->myParentId;
      }
      
      function setChildCount($value)
      {
      	 $this->setProperty($this->myChildCount, $value);
      }
      function childCount()
      {
      	 return $this->myChildCount;
      }
      
      function setAuthorId($value)
      {
      	 $this->setProperty($this->myAuthorId, $value);
      }
      function authorId()
      {
      	 return $this->myAuthorId;
      }
      
      function setDateIssued($value)
      {
      	 $this->setProperty($this->myDateIssued, $value);
      }
      function dateIssued()
      {
      	 return $this->myDateIssued;
      }

      function public()
      {
         return $this->myPublic;
      }

      function setPublic($aValue)
      {
         $this->setProperty($this->myPublic, $aValue); 
      }

      function nextSiblingId()
      {
         return $this->myNextSibling;
      }
      function setNextSiblingId($value)
      {
         $this->setProperty($this->myNextSibling, $value);
      }
      
      function prevSiblingId()
      {
         return $this->myPrevSibling;
      }
      function setPrevSiblingId($value)
      {
         $this->setProperty($this->myPrevSibling, $value);
      }

      function firstChild()
      {
         return $this->myFirstChild;
      }
      function setFirstChild($value)
      {
         return $this->setProperty($this->myFirstChild, $value);
      }

      function lastChild()
      {
         return $this->myLastChild;
      }
      function setLastChild($value)
      {
         $this->setProperty($this->myLastChild, $value);
      }
      

      function MakeParentOf(&$aNode)
      {
	 $aNode->setParentId($this->id());
      }

   }

   class NodeEngine
   {
      var $myConn;

      function NodeEngine($aConn)
      {
         $this->myConn = $aConn;
      }

      function PopulateItem($aRec)
      {
	 return new Node($aRec["nodeID"], $aRec["nodeBody"], $aRec["nodeTitle"], $aRec["nodePrecise"], $aRec["parentNode"], $aRec["childNodes"], $aRec["postedBy"], $aRec["datetime"], $aRec["public"], $aRec["nextSibling"], $aRec["prevSibling"], $aRec["FirstChild"], $aRec["LastChild"]);
      }
      
      function GetNode($aNodeID)
      {
         $sql = "select nodeID, nodeTitle, nodeBody, nodePrecise, parentNode, childNodes, postedBy, datetime , public, nextSibling, prevSibling, FirstChild, LastChild from nodes where nodeID=" . $aNodeID . ";";
	 
	 if ($myData = mysqli_query($sql))
            if ($myRec = mysqli_fetch_array($myData))
               return $this->PopulateItem($myRec);
	 
      }
      function Save(&$aNode)
      {
         if ($aNode->isNew())
	    $theQuery = "insert into nodes (nodeTitle, nodeBody, nodePrecise, datetime, nodeType, postedBy, public, parentNode, childNodes, nextSibling, prevSibling, FirstChild, LastChild) values('" . str_replace("'", "&#039;", $aNode->title()) . "', '". str_replace("'", "&#039;", $aNode->body()) . "', '". str_replace("'", "&#039;", $aNode->precise()) . "', '" . $aNode->dateIssued() . "', '" . $aNode->type() . "', " . $aNode->authorId() . ", " . $aNode->public() . ", " . $aNode->parentId() . ", " . $aNode->childCount() . ", " . $aNode->nextSiblingId() . ", " . $aNode->prevSiblingId() . ", " . $aNode->firstChild() . ", " . $aNode->lastChild() . ");";
         else
	    $theQuery = "update nodes set nodeTitle='" . str_replace("'", "&#039;", $aNode->title()) . "', nodeBody='" . str_replace("'", "&#039;", $aNode->body()) . "', nodePrecise='" . str_replace("'", "&#039;", $aNode->precise()) . "', datetime='" . $aNode->dateIssued() . "', nodeType='" . $aNode->type() . "',  postedBy=" . $aNode->authorId() . ", public=" . $aNode->public() . ", parentNode=" . $aNode->parentId() . ", childNodes=" . $aNode->childCount() . ", nextSibling=" . $aNode->nextSiblingId() . ", prevSibling=" . $aNode->prevSiblingId() . ", FirstChild=" . $aNode->firstChild() . ", LastChild=" . $aNode->lastChild() . "  where nodeId=" . $aNode->id() . ";";
	    
	 if (mysqli_query($theQuery))
   	    if ($aNode->isNew())
	       $aNode->setId( mysqli_insert_id());
      }
   }
?>
