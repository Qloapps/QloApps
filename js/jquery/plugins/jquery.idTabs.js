/* idTabs ~ Sean Catchpole - Version 1.0 */ 
 
/* Options (in any order): 
 
 start (number|string) 
    Index number of default tab. ex: idTabs(0) 
    String of id of default tab. ex: idTabs("#tab1") 
    default: class "selected" or index 0 
 
 return (boolean) 
    True - Url will change. ex: idTabs(true) 
    False - Url will not change. ex: idTabs(false) 
    default: false 
 
 click (function) 
    Function will be called when a tab is clicked. ex: idTabs(foo) 
    If the function returns true, idTabs will show/hide content (as usual). 
    If the function returns false, idTabs will not take any action. 
    The function is passed three variables: 
      The id of the element to be shown 
      an array of all id's that can be shown 
      and the element containing the tabs 
  
  MODIFIED BY PRESTASHOP
  
*/ 

(function($){$.fn.idTabs=function(){var s={"start":null,"return":false,"click":null};for(var i=0;i<arguments.length;++i){var n={},a=arguments[i];switch(typeof a){case"object":$.extend(n,a);break;case"number":case"string":n.start=a;break;case"boolean":n["return"]=a;break;case"function":n.click=a;break;};$.extend(s,n);}
var self=this;var list=$("a[href^='#']",this).click(function(){if($("a.selected",self)[0]==this)
return s["return"];var id="#"+this.href.split('#')[1];var aList=[];var idList=[];$("a",self).each(function(){if(this.href.match(/#/)){aList[aList.length]=this;idList[idList.length]="#"+this.href.split('#')[1];}});if(s.click&&!s.click(id,idList,self))return s["return"];for(i in aList)$(aList[i]).removeClass("selected");for(i in idList){$(idList[i]).addClass('block_hidden_only_for_screen');}
$(this).addClass("selected");$(id).removeClass('block_hidden_only_for_screen');return s["return"];});var test;if(typeof s.start=="number"&&(test=list.filter(":eq("+s.start+")")).length)
test.click();else if(typeof s.start=="string"&&(test=list.filter("[href='#"+s.start+"']")).length)
test.click();else if((test=list.filter(".selected")).length)
test.removeClass("selected").click();else list.filter(":first").click();return this;};$(function(){$(".idTabs").each(function(){$(this).idTabs();});});})(jQuery);
