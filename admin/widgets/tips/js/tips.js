/**
 * @version		1.26 September 14, 2012
 * @author		RocketTheme http://www.rockettheme.com
 * @copyright 	Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

var GantryTips={init:function(){var a=document.getElements(".gantrytips");if(!a){return;}a.each(function(c,e){var d=c.getElements(".gantrytips-controller .gantrytips-left, .gantrytips-controller .gantrytips-right");var g=c.getElement(".current-tip");var f=g.get("html").toInt();var b=c.getElements(".gantrytips-tip");b.each(function(j,h){j.set("opacity",(h==f-1)?1:0);});d.addEvents({click:function(){var i=this.hasClass("gantrytips-left");var h=f;if(i){f-=1;if(f<=0){f=b.length;}}else{f+=1;if(f>b.length){f=1;}}this.fireEvent("jumpTo",[f,h]);},jumpTo:function(j,i){if(!i){i=f;}f=j;if(!b[f-1]||!b[i-1]){return;}var k=c.getElement(".gantrytips-wrapper");var h=b[f-1].getSize().y+15;b.fade("out");if(h>=190){k.tween("height",h);}b[f-1].fade("in");g.set("text",f);},jumpById:function(k,i){if(!i){i=f;}f=b.indexOf(document.id(k))||0;if(f==-1){return;}var j=c.getElement(".gantrytips-wrapper");var h=b[f].getSize().y+15;b.fade("out");if(h>=190){j.tween("height",h);}b[f].fade("in");f+=1;g.set("text",f);},selectstart:function(h){h.stop();}});d[0].fireEvent("jumpTo",1);d[1].fireEvent("jumpTo",1);});}};window.addEvent("domready",GantryTips.init);