/**
 * @version		1.19 September 20, 2011
 * @author		RocketTheme http://www.rockettheme.com
 * @copyright 	Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

var InputsExclusion=[".content_vote"];var InputsMorph={version:1.7,init:function(d,e){if(!d){d="all";}if(!e){e="";}else{e+=" ";}InputsMorph.rtl=document.body.getStyle("direction")=="rtl";InputsMorph.list=new Hash({all:[]});if(d=="radio"||d=="all"){var a=$$(e+"input[type=radio]");var f=$$(InputsExclusion.join(" input[type=radio], ")+" input[type=radio]");f.each(function(b){a=a.erase(b);});a.each(function(b,c){InputsMorph.setArray("list","all",b);if(InputsMorph.list.has(b.name)){InputsMorph.setArray("list",b.name,b);}else{InputsMorph.list.set(b.name,[b]);}InputsMorph.morph(b,"radios").addEvent(b,"radios");});}if(d=="checkbox"||d=="all"){a=$$(e+"input[type=checkbox]");f=$$(InputsExclusion.join(" input[type=checkbox], ")+" input[type=checkbox]");f.each(function(b){a=a.erase(b);});a.each(function(b,c){InputsMorph.setArray("list","all",b);if(InputsMorph.list.has(b.name)){InputsMorph.setArray("list",b.name,b);}else{InputsMorph.list.set(b.name,[b]);}InputsMorph.morph(b,"checks").addEvent(b,"checks");});}},morph:function(f,e){var j=f.getNext(),h=f.getParent(),g=f.name.replace("[","").replace("]","");if(j&&j.get("tag")=="label"){f.setStyles({position:"absolute",left:"-10000px"});if(InputsMorph.rtl&&Browser.Engine.gecko){f.setStyles({position:"absolute",right:"-10000px"});}else{f.setStyles({position:"absolute",left:"-10000px"});}if(InputsMorph.rtl&&(Browser.Engine.presto)){f.setStyle("display","none");}if(Browser.Engine.trident5){f.setStyle("display","none");}j.addClass("rok"+e+" rok"+g);if(f.checked){j.addClass("rok"+e+"-active");}}else{if(h&&h.get("tag")=="label"){if(InputsMorph.rtl&&Browser.Engine.gecko){f.setStyles({position:"absolute",right:"-10000px"});}else{f.setStyles({position:"absolute",left:"-10000px"});}if(InputsMorph.rtl&&(Browser.Engine.presto)){f.setStyle("display","none");}h.addClass("rok"+e+" rok"+g);if(f.checked){h.addClass("rok"+e+"-active");}}else{var i=new Element("label").wraps(f);if(InputsMorph.rtl&&Browser.Engine.gecko){f.setStyles({position:"absolute",right:"-10000px"});}else{f.setStyles({position:"absolute",left:"-10000px"});}if(InputsMorph.rtl&&(Browser.Engine.presto)){f.setStyle("display","none");}i.addClass("rok"+e+" rok"+g);if(f.checked){i.addClass("rok"+e+"-active");}}}return InputsMorph;},addEvent:function(e,d){e.addEvent("click",function(){InputsMorph.switchReplacement(e,d);});if(Browser.Engine.trident){var g=e.getNext(),f=e.getParent().getParent();if(f&&f.get("tag")=="li"){f.addEvent("click",function(){e.fireEvent("click");});}}return InputsMorph;},switchReplacement:function(l,k){if(k=="checks"){var j=l.getNext(),c=l.getParent(),a="rok"+k+"-active";var i=((j)?j.get("tag")=="label":false);var b=((c)?c.get("tag")=="label":false);if(i||b){if(i){if(j.hasClass(a)&&i){j.removeClass(a);l.removeProperty("checked");}else{if(!j.hasClass(a)&&i){j.addClass(a);l.setProperty("checked","checked");}}}else{if(b){if(c.hasClass(a)&&b){c.removeClass(a);l.removeProperty("checked");}else{if(!c.hasClass(a)&&b){c.addClass(a);l.setProperty("checked","checked");}}}}}}else{InputsMorph.list.get(l.name).each(function(e){var d=e.getNext(),f=e.getParent();var h=l.getNext(),g=l.getParent();$$(d,f).removeClass("rok"+k+"-active");if(d&&d.get("tag")=="label"&&h==d){e.setProperty("checked","checked");d.addClass("rok"+k+"-active");}else{if(f&&f.get("tag")=="label"&&g==f){f.addClass("rok"+k+"-active");e.setProperty("checked","checked");}}});}},setArray:function(f,e,h){var g=InputsMorph[f].get(e);g.push(h);return InputsMorph[f].set(e,g);}};