function init() {
  lists = document.getElementsByTagName("ul");
  for (ii=0;ii<lists.length;ii++) {
    if (lists[ii].className == "nodes" && lists[ii].getElementsByTagName("li") && lists[ii].getElementsByTagName("li").length > 0) {
      lists[ii].getElementsByTagName("li")[0].className += " first_child";
    }
    
    // Shift the tail of recents posts up beside the first child
    if (lists[ii].parentNode.id == "content") {
       items = lists[ii].getElementsByTagName("li");
       height = 0;
       for (ij=1;ij<items.length;ij++) {
          if (items[ij].parentNode.id == lists[ii].id) {
             items[ij].style.top = "-" + (lists[ii].clientHeight-350) + "px"; // why 350?!?
             height += items[ij].clientHeight;
          }
       }
       
       lists[ii].style.height = (lists[ii].parentNode.clientHeight - height) + "px";
    }
  }
}