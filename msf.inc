<?
   // mySQL functions
   
   include("dbc.inc");
   
   function AddLink($xConn, $xLinkName, $xLink, $xLinkRSS, $xLinkDescription)
   {
      $sql = "INSERT INTO Links (LinkName, Link, LinkRSS, LinkDescription) VALUES ('$xLinkName', '$xLink', '$xLinkRSS', '$xLinkDescription');";
      if (mysql_query($sql, $xConn))
         return mysql_insert_id($xConn);
      else
         return -1;
   }
   
   function AddNode($xConn, $xTitle, $xBody, $xPrecise, $xType, $xParentNode, $xPostedBy, $public)
   {
       $datetime = Now2Counter();
       $sql = "INSERT INTO nodes (nodeTitle, nodeBody, nodePrecise, datetime, nodeType, childNodes, parentNode, postedBy, public) VALUES ('$xTitle', '$xBody', '$xPrecise', '$datetime', '$xType', 0, $xParentNode, $xPostedBy, $public);";
       if ($xParentNode != -1)
       {
          IncrementChildCount($xConn, $xParentNode);
       }   
       if (mysql_query($sql, $xConn))
          return mysql_insert_id($xConn);
   }

   function AddToThreading($xConn, $xBlockID, $xThreadID)
   {
      $sql = "INSERT INTO Threading (ThreadID, BlockID) VALUES($xThreadID, $xBlockID);";
      return mysql_query($sql, $xConn);
   }

   function AdjustNodeChildNodes($xConn, $xNodeID, $xDeltaChildNum)
   {
      $sql = "UPDATE nodes SET childNodes = childNodes + $xDeltaChildNum WHERE nodeID = $xNodeID;";
      return mysql_query($sql, $xConn);
   }

   function Close($xConn)
   {
      return mysql_close($xConn);
   }

   function Connect()
   {
      global $sServer;
      global $sUsername;
      global $sPassword;
      global $sDatabase;
      if ($tmpConn =  mysqli_connect($sServer, $sUsername, $sPassword))
      {
         mysql_select_db($sDatabase, $tmpConn);
         return $tmpConn;
      }
      else
         return $tmpConn;
   }
   
   function DecrementChildCount($xConn, $xNodeID)
   {
      $sql = ("SELECT parentNode FROM nodes where nodeID = $xNodeID;");
      $oRS = mysql_query($sql, $xConn);
      if ($oRec = mysql_fetch_array($oRS))
      {
         $sql = "UPDATE nodes SET childNodes = childNodes - 1 WHERE nodeID = $xNodeID;";
         mysql_query($sql, $xConn);
         if ($oRec["parentNode"] <> -1)
         {
            DecrementChildCount($xConn, $oRec["parentNode"]);
         }
      }
   }
   
   function DeleteDescendents($xParentNode)
   {
      $oConn = Connect();
      $sql = "SELECT nodeID FROM nodes where parentNode = $xParentNode;";
      $oRS = mysql_query($sql, $oConn);
      if (mysql_num_rows($oRS) > 0)
         while ($oRec = mysql_fetch_array($oRS))
            DeleteDescendents($oRec["nodeID"]);
      $sql = "DELETE FROM nodes WHERE parentNode = $xParentNode;";
      mysql_query($sql, $oCOnn);
   }      
   
   function DeleteLink($xConn, $xLinkID)
   {
      $sql = "DELETE FROM Links WHERE LinkID = $xLinkID;";
      return mysql_query($sql, $xConn);
   }
   
   function DeleteNode($xConn, $xNID)
   {
       $sql = "DELETE FROM nodes WHERE nodeID = $xNID;";
       if (mysql_query($sql, $xConn))
       {
          DecrementChildCount($xConn, $xNID);
          return 1;
       }
       else
          return 0;
   }

   function DeleteNodeCategories($xConn, $xNodeID)
   {
      $sql = "DELETE FROM NodeCategories WHERE nodeID = $xNodeID;";
       return mysql_query($sql, $xConn);
   }

   function EditNode($xConn, $xTitle, $xBody, $xNodePrecise, $xNID, $xPublic, $xNodeType)
   {
       $sql = "UPDATE nodes set nodeTitle = '$xTitle', nodeBody = '$xBody', nodePrecise = '$xNodePrecise', public = $xPublic, nodeType = '$xNodeType', Edited = '1' WHERE nodeID = $xNID;";
       return mysql_query($sql, $xConn);
   }

   function EditLink($xConn, $xLinkID, $xLinkName, $xLink, $xLinkDescription, $xLinkRSS)
   {
      $sql = "UPDATE Links SET LinkName='$xLinkName', Link='$xLink', LinkDescription='$xLinkDescription', LinkRSS='$xLinkRSS' WHERE LinkID = $xLinkID;";
      return mysql_query($sql, $xConn);
   }

   function FindParentInThreading($xConn, $xParentID)
   {
      $sql = "SELECT BlockID FROM Threading WHERE BlockID = $xParentID;";
      return mysql_query($sql, $xConn);
   }

   function GetNodeTypes($xConn)
   {
      return mysql_query("SELECT * FROM nodeTypes", $xConn);
   }
   
   function IncrementChildCount($xConn, $xNodeID)
   {
      $sql = ("SELECT parentNode FROM nodes where nodeID = $xNodeID;");
      $oRS = mysql_query($sql, $xConn);
      if ($oRec = mysql_fetch_array($oRS))
      {
         $sql = "UPDATE nodes SET childNodes = childNodes + 1 WHERE nodeID = $xNodeID;";
         mysql_query($sql, $xConn);
         if ($oRec["parentNode"] <> -1)
            IncrementChildCount($xConn, $oRec["parentNode"]);
      }
   }

   function IncrementPings($xConn, $xNodeID)
   {
      $sql = "UPDATE nodes SET Pings = Pings + 1 WHERE nodeID = $xNodeID;";
      return mysql_query($sql, $xConn);
   }   
   
   function InsertNodeEdit($xConn, $xNodeID, $xTitle, $xBody, $xPrecise, $xEditDate, $xUID)
   {
      $sql = "INSERT INTO NodeEdits (NodeID, NodeTitle, NodeBody, NodePrecise, EditDate, EditedBy) VALUES ($xNodeID, '$xTitle', '$xBody', '$xPrecise', '$xEditDate', $xUID);";
      mysql_query($sql, $xConn);
      return mysql_insert_id($xConn);
   }   
   
   function InsertNodeCategory($xConn, $xNodeID, $xCategoryID)
   {
      $sql = "INSERT INTO NodeCategories (nodeID, CategoryID) VALUES ($xNodeID, $xCategoryID);";
      mysql_query($sql, $xConn);
      return mysql_insert_id($xConn);
   }   
   
   function InsertPing($xConn, $xNodeID, $xSource, $xTitle)
   {
      $sql = "INSERT INTO Pingback (NodeID, Source, Title) VALUES ($xNodeID, '" . $xSource . "', '" . $xTitle . "');";
      mysql_query($sql, $xConn);
      return mysql_insert_id($xConn);
   }
   
   function InsertUser($xConn, $xUID, $xName, $xEmail, $xURL, $xAdmin)
   {
      $sql = "REPLACE INTO users (userID, userName, email, url, admin) values ('$xUID', '$xName', '$xEmail', '$xURL', '$xAdmin');";
      mysql_query($sql, $xConn);
      return mysql_insert_id($xConn);
   }   
      
   function SelectCategories($xConn)
   {
      $sql = "SELECT CategoryID, Category, nodeType FROM Category ORDER BY Category;";
      return mysql_query($sql, $xConn);
   }
   
   function SelectCategoryNodes($xConn, $xCategoryID)
   {
      $sql = "SELECT n.*, u.userName, u.email, u.url, c.Category FROM Category c, NodeCategories nc, nodes n left join users u ON n.postedBy = u.counter WHERE n.nodeID = nc.nodeID AND nc.categoryID = c.CategoryID AND c.CategoryID = $xCategoryID ORDER BY datetime DESC;";
      return mysql_query($sql, $xConn);
   }
   
   function SelectLink($xConn, $xLID)
   {
      return mysql_query("SELECT * FROM Links WHERE LinkID = $xLID ORDER BY LinkName;", $xConn);
   }
   
   function SelectLinks($xConn)
   {
      return mysql_query("SELECT * FROM Links ORDER BY LinkName;", $xConn);
   }
   
   function SelectNewTopLevelThreadsThreads($xConn, $xType, $xDatetime)
   {
      $sql = "SELECT nodeID, nodeTitle FROM nodes WHERE  datetime > '$xDatetime' AND nodeType LIKE '$xType'  and parentNode = -1 GROUP BY nodeID, nodeTitle ORDER BY datetime DESC;";
      return mysql_query($sql, $xConn);
   }  
   
   function SelectNode($xConn, $xNodeID)
   {
       $sql = "SELECT n.*, u.userName, u.email, u.url FROM nodes n left join users u on n.postedBy = u.counter WHERE nodeID = $xNodeID;";
       return mysql_query($sql, $xConn);
   }
   
   function SelectNodeCategories($xConn, $xNodeID)
   {
      $sql = "SELECT nc.*, c.Category FROM NodeCategories nc LEFT JOIN Category c ON nc.CategoryID = c.CategoryID WHERE nc.nodeID = $xNodeID;";
      return mysql_query($sql, $xConn);
   }
   
   function SelectNodeEdits($xConn, $xNodeID)
   {
      $sql = "SELECT ne.*, u.userName FROM NodeEdits ne LEFT JOIN users u ON ne.EditedBy = u.counter WHERE ne.NodeID = $xNodeID ORDER BY EditDate DESC;";
      return mysql_query($sql, $xConn);
   }

   function SelectNodeType($xConn, $xNodeType)
   {
      $sql = "SELECT * FROM nodeTypes WHERE typeCode = '$xNodeType';";
      return mysql_query($sql, $xConn);
   }
   
   function SelectNodeTypeCategories($xConn, $xNodeType, $xNodeID)
   {
      $sql = "SELECT c.CategoryID, COALESCE(c.Category, '') AS Category, COALESCE(c.CategoryDesc, '') AS CategoryDesc, c.nodeType, COALESCE(nc.NodeCategoryID, -1) AS NodeCategoryID FROM Category c LEFT JOIN NodeCategories nc ON c.CategoryID = nc.CategoryID AND nc.NodeID = $xNodeID WHERE c.NodeType = '$xNodeType';";
      return mysql_query($sql, $xConn);
   }
      
   function SelectNodeTypeTopLevelNodes($xConn, $xNodeType)
   {
      $sql = "SELECT nodeID, nodeTitle FROM nodes WHERE nodeType = '$xNodeType';";
      return mysql_query($sql, $xConn);
   }
   
   function SelectPingbacks($xConn, $xNodeID)
   {
      $sql = "SELECT * FROM Pingback WHERE NodeID = $xNodeID;";
      return mysql_query($sql, $xConn);
   }
   
   function SelectPingbackByNodeAndSource($xConn, $xNodeID, $xSourceURL)
   {
      $sql = "SELECT 1 FROM Pingback WHERE NodeID = $xNodeID and Source='$xSourceURL';";
      return mysql_query($sql, $xConn);
   }
      
   function SelectSomeOrderedNodes($xConn, $xWhere, $Order)
   {
      $sql = "SELECT n.*, t.typeName, u.userName, u.email, u.url FROM nodeTypes t, nodes n LEFT JOIN users u ON n.postedBy = u.counter WHERE $xWhere AND t.typeCode = n.nodeType ORDER BY $Order;";
      return mysql_query($sql, $xConn);
   }
   
   function SelectSomeNodeCategoryNodes($xConn, $xWhereStr, $xOrderStr)
   {
      $sql = "SELECT n.*, u.username, u.url, u.email FROM NodeCategories nc LEFT JOIN nodes n ON nc.NodeID = n.nodeID LEFT JOIN users u ON n.postedBy = u.counter WHERE $xWhereStr ORDER BY $xOrderStr;";
      return mysql_query($sql, $xConn);
   }
   
   function SelectSmartLinks($xConn)
   {
      $sql = "SELECT * FROM SmartLinks;";
      return mysql_query($sql, $xConn);
   }
   
   function SelectThreading($xConn, $xNodeID)
   {
      $sql = "SELECT n.*, u.userName, u.email, u.url FROM Threading t LEFT JOIN nodes n ON t.BlockID = n.parentNode LEFT JOIN users u on n.postedBy = u.counter WHERE t.ThreadID = $xNodeID order by n.datetime;";
      return mysql_query($sql, $xConn);
   }   

   function SelectThreadRoot($xConn, $xNodeID)
   {
      $sql = "SELECT COALESCE(FirstChild, -1) AS FirstChild, COALESCE(LastChild, -1) AS LastChild FROM nodes WHERE nodeID = $xNodeID;";
      return mysql_query($sql, $xConn);
   }
   
   function SelectTypeCategories($xConn, $xNodeType)
   {
      $sql = "SELECT CategoryID, COALESCE(Category, '') AS Category, COALESCE(CategoryDesc, '') AS CategoryDesc, nodeType FROM Category WHERE NodeType = '$xNodeType';";
      return mysql_query($sql, $xConn);
   }
   
   function SelectUpdatedThreads($xConn, $xNodes, $xDatetime)
   {
      $sql = "SELECT t.ThreadID, count(*) as new FROM Threading t LEFT JOIN nodes n ON t.BlockID = n.parentNode WHERE t.ThreadID IN $xNodes AND n.datetime > $xDatetime GROUP BY t.ThreadID;";
      return mysql_query($sql, $xConn);
   }   
   
   function SelectUser($xConn, $xUserID)
   {
      $sql = "SELECT * FROM users WHERE userID='$xUserID';";
      return mysql_query($sql, $xConn);
   }
   
   function SetNextSiblingNodePrevSibling($xConn, $xNodeID, $xPrevSibling)
   {
      $sql = "UPDATE nodes SET prevSibling = $xPrevSibling WHERE nodeID = $xNodeID;";
      return mysql_query($sql, $xConn);
   }

   function SetNodeSiblings($xConn, $xNodeID, $xPrevSibling, $xNextSibling)
   {
      $sql = "UPDATE nodes SET prevSibling = $xPrevSibling, nextSibling=$xNextSibling WHERE nodeID = $xNodeID;";
      return mysql_query($sql, $xConn);
   }

   function SetPrevSiblingNodeNextSibling($xConn, $xNodeID, $xNextSibling)
   {
      $sql = "UPDATE nodes SET nextSibling = $xNextSibling WHERE nodeID = $xNodeID;";
      return mysql_query($sql, $xConn);
   }

   function SetThreadFirstChild($xConn, $xThreadID, $xFirstChildID)
   {
      $sql = "UPDATE nodes SET FirstChild = $xFirstChildID WHERE nodeID = $xThreadID;";
      return mysql_query($sql, $xConn);
   }

   function SetThreadLastChild($xConn, $xThreadID, $xLastChildID)
   {
      $sql = "UPDATE nodes SET LastChild = $xLastChildID WHERE nodeID = $xThreadID;";
      return mysql_query($sql, $xConn);
   }

   function UpdateLink($xConn, $xLinkID, $xRSS, $xDateRead)
   {
      $sql = "UPDATE Links SET currentRSS = \"$xRSS\", dateRead = \"$xDateRead\" WHERE LinkID = $xLinkID;";
      return mysql_query($sql, $xConn);
   }

   function UpdateUser($xConn, $xUID, $xName, $xEmail, $xURL, $xCounter)
   {
      $sql = "UPDATE users SET userID = '$xUID', userName = '$xName', email = '$xEmail', url = '$xURL' WHERE counter = $xCounter;";
      return mysql_query($sql, $xConn);
   }   

   function VerifyUser($xConn, $xUID, $xPWD)
   {
      $sql = "SELECT counter FROM users WHERE userid='$xUID' AND password='$xPWD' and admin=1;";
      $tmpRS = mysql_query($sql, $xConn);
      if (mysql_num_rows($tmpRS) > 0)
      {
         $tmpRec = mysql_fetch_array($tmpRS);
         return $tmpRec["counter"];
      }
      else
         return -1;
   }
?>
