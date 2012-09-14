/**
 * @version   1.26 September 14, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

Gantry.Assignments={opacity:{overlay:0.7,label:0.5},init:function(){Gantry.Assignments.assigned=document.id("assigned_override_items").get("value").unserialize()||{};Gantry.Assignments.fireUp();Gantry.Assignments.overlays();Gantry.Assignments.blocks();Gantry.Assignments.loadDefaults();},overlays:function(){var a=new Element("div",{"class":"inherit-overlay"});var b=$$(".gantry-field").getFirst(".wrapper").filter(function(f){if(!f){return false;}var c=f.getParent(".gantry-field").getElement(".inherit-checkbox input[type=checkbox]");if(c){f.store("gantry:inherit_input",c);}else{var e=a.clone().inject(f,"top");var d=f.getParent(".gantry-field").getElement("label");e.setStyle("height",f.getParent(".gantry-field").getSize().y-2).setStyle("opacity",Gantry.Assignments.opacity.overlay);if(d){d.setStyle("opacity",Gantry.Assignments.opacity.label);}}return c;});b.each(function(h){var d=h.retrieve("gantry:inherit_input");var e=d.getParent(".field-label").getElement(".base-label label");var g=a.clone().inject(h,"top");var c=d.getParent(".gantry-field").getElements("div.wrapper input, div.wrapper select"),f={};c.each(function(j){var k=j.get("id"),i=j.get("value");if(j.hasClass("toggle")){i=j.getPrevious().get("value");}if(k){f[k]=i;}j.store("gantry:override_checkbox",d);});d.store("gantry:fields",f);g.setStyle("height",h.getParent(".gantry-field").getSize().y-2).setStyle("opacity",Gantry.Assignments.opacity.overlay);d.addEvent("click",function(){if(this.get("checked")){e.setStyle("opacity",1);g.setStyles({display:"none",visibility:"hidden"});Gantry.Assignments.updateBadge("+",this);c.each(function(i){i.fireEvent("set",f[i.id]);});}else{e.setStyle("opacity",Gantry.Assignments.opacity.label);g.setStyles({display:"block",visibility:"visible"});Gantry.Assignments.updateBadge("-",this);c.each(function(i){f[i.id]=i.get("value");i.fireEvent("set",Gantry.Assignments.defaults.get(i.id));});}});if(d.get("checked")){g.setStyles({display:"none",visibility:"hidden"});}else{e.setStyles({display:"block",opacity:Gantry.Assignments.opacity.label});}});},blocks:function(){Gantry.Assignments.blocks=$$(".assignments-block");Gantry.Assignments.List=document.id("assigned-list");Gantry.Assignments.ClearList=document.id("selection-list").getElement(".footer-block a");Gantry.Assignments.Empty=new Element("li",{"class":"empty"}).set("text","No Item.");Gantry.Assignments.blocks.each(function(a){Gantry.Assignments.manageBlock(a);});if(Gantry.Assignments.ClearList){Gantry.Assignments.ClearList.addEvent("click",function(b){b.stop();var a=Gantry.Assignments.List.getChildren();if(a.length==1&&a[0].hasClass("empty")){return;}a.each(function(c){c.getElement(".delete-assigned").fireEvent("click");});});}},loadDefaults:function(){Gantry.Assignments.defaultsXHR=new Request({url:AdminURI,onSuccess:function(a){Gantry.Assignments.defaults=new Hash(JSON.decode(a));}}).post({action:"gantry_admin",model:"overrides",gantry_action:"get_base_values"});},manageBlock:function(e,c){var b=e.getElement(".select-all"),f=e.getElement(".add-to-assigned"),d=e.getElement("h2 .assignment-checkbox");var a=(c)?e.getElements(c):e.getElements(".inside ul .assignment-checkbox");e.getElements("a.no-link-item").addEvent("click",function(g){g.stop();});if(d){d.store("gantry:in_list",false);}a.store("gantry:in_list",false);if(!b||!f){return;}b.addEvent("click",function(h){h.stop();var g=a.get("checked");if(!g.contains(true)||!g.contains(false)){a.fireEvent("click");}else{g.each(function(j,k){if(!j){a[k].fireEvent("click");}});}});if(d){d.addEvent("click",function(){var g=this.getParent("div").getElements(".inside label, .select-all");if(this.checked||this.retrieve("gantry:in_list")){g.setStyle("display","none");}else{g.setStyle("display","block");}});}f.addEvent("click",function(i){var h=Gantry.Assignments.List;if(h){i.stop();if(d&&d.get("checked")&&!d.retrieve("gantry:in_list")){d.fireEvent("click");Gantry.Assignments.addAssigned(h,d,true);}else{var g=a.filter(function(j){var k=j.get("checked")&&!j.retrieve("gantry:in_list");if(j.get("checked")){j.fireEvent("click");}return k;}).reverse();g.each(function(j){Gantry.Assignments.addAssigned(h,j);});}}});},updateBadge:function(f,e){var b=document.id(e).getParent(".gantry-panel").className.replace(/[panel|\-|\s|gantry]/g,"").toInt()-1;var d=Gantry.tabs[b];if(d){var c=d.getElement(".overrides-involved");var a=c.getElement("span");var g=a.get("text").toInt();if(f=="+"){g+=1;}else{g-=1;}if(g<0){g=0;}a.set("text",g);if(!g){c.getParent(".badges-involved").removeClass("double-badge");c.setStyle("display","none");}else{if(c.getPrevious(".presets-involved").getStyle("display")=="block"){c.getParent(".badges-involved").addClass("double-badge");}else{c.getParent(".badges-involved").removeClass("double-badge");}c.setStyles({display:"block",opacity:1});}}},fireUp:function(){for(var d in Gantry.Assignments.assigned){for(var a in Gantry.Assignments.assigned[d]){if(typeof Gantry.Assignments.assigned[d][a]=="object"){var c=[];for(var b in Gantry.Assignments.assigned[d][a]){c.push(Gantry.Assignments.assigned[d][a][b].toInt());}Gantry.Assignments.assigned[d][a]=c;}}}document.id("assigned-list").getElements(".link a, .link span").each(function(g){var e=g.getParent("li"),i=document.id("assigned-list");var j=e.getElement(".delete-assigned");var h=(g.get("rel")||g.get("class"));var l=document.getElement("a[rel="+h+"], span[class="+h+"]");var k=(g.get("tag")=="span");if(l){var f=l;if(f){f.store("gantry:in_list",true);j.addEvent("click",function(){Gantry.Assignments.exclude(e);e.empty().dispose();f.store("gantry:in_list",false);if(k){f.getParent(".assignments-block").getElement("h2").removeClass("added");f.getParent(".assignments-block").getElements(".inside, .inside li").removeClass("added");var m=f.getParent("div").getElements(".inside label, .select-all");m.setStyle("display","block");}else{f.getParent("li").removeClass("added");}if(!i.getChildren().length){Gantry.Assignments.Empty.clone().inject(i);if(i.getNext(".footer-block")){i.getNext(".footer-block").setStyle("display","none");}}});}}else{}});},addAssigned:function(c,b,f){b.store("gantry:in_list",true);b.getParent((f)?"h2":"li").addClass("added");if(f){b.getParent(".assignments-block").getElement(".inside").addClass("added");}var e=new Element("span",{"class":"delete-assigned"}),g;if(!f){g=new Element("li").adopt(new Element("span",{"class":"type"}).set("text",b.getParent(".assignments-block").getElement("h2").className.replace(/\-/g," ")),e,new Element("span",{"class":"link"}).adopt(b.getParent("li").getElement("a").clone()));}else{var d=b.getParent("h2").getElement("span");g=new Element("li",{"class":"list-type"}).adopt(new Element("span",{"class":"type"}).set("text","Type"),e,new Element("span",{"class":"link"}).set("html",'<span class="'+d.className+'">'+d.get("text")+"</span>"));}e.addEvent("click",function(){var h=this.getParent("li");Gantry.Assignments.exclude(g);h.empty().dispose();b.store("gantry:in_list",false);b.getParent((f)?"h2":"li").removeClass("added");if(f){b.getParent(".assignments-block").getElements(".inside, .inside li").removeClass("added");}if(f){var i=b.getParent("div").getElements(".inside label, .select-all");i.setStyle("display","block");}if(!c.getChildren().length){Gantry.Assignments.Empty.clone().inject(c);if(c.getNext(".footer-block")){c.getNext(".footer-block").setStyle("display","none");}}});g.store("gantry:ref_item",b);if(c.getElement(".empty")){c.getElement(".empty").dispose();}if(c.getNext(".footer-block")){c.getNext(".footer-block").setStyle("display","block");}g.inject(c,"top");if(f){var a=Gantry.Assignments.List.getElements(".link a[rel^="+d.className+"::]");a.getParent("li").getElement(".delete-assigned").fireEvent("click");}Gantry.Assignments.include(g);},include:function(c,f){var b=Gantry.Assignments.assigned;var a=c.getElement(".link").getFirst();var e=a.get("rel")||a.className;e=e.split("::");var d={archetype:e[0],type:e[1],id:e[2]||-1};if(!b[d.archetype]){b[d.archetype]={};}if(!b[d.archetype][d.type]){b[d.archetype][d.type]=[];}if(!b[d.archetype][d.type].contains(d.id)){b[d.archetype][d.type].push(d.id.toInt());}if(b[d.archetype][d.type].length==1&&b[d.archetype][d.type][0]==-1){b[d.archetype][d.type]=true;}if(!f){document.id("assigned_override_items").set("text",serialize(b));}},exclude:function(c,f){var b=Gantry.Assignments.assigned;var a=c.getElement(".link").getFirst();var e=a.get("rel")||a.className;e=e.split("::");var d={archetype:e[0],type:e[1],id:e[2]||-1};if(b[d.archetype]){if(typeof b[d.archetype][d.type]=="array"){b[d.archetype][d.type].erase(d.id.toInt());if(!b[d.archetype][d.type].length){delete b[d.archetype][d.type];}}else{delete b[d.archetype][d.type];}}if(GantryObjIsEmpty(b[d.archetype])){delete b[d.archetype];}if(!f){document.id("assigned_override_items").set("text",serialize(b));}}};var GantryObjIsEmpty=function(b){for(var a in b){return false;}return true;};window.addEvent("domready",Gantry.Assignments.init);