/**
 * @package		Gantry Template Framework - RocketTheme
 * @version		1.26 September 14, 2012
 * @author		RocketTheme http://www.rockettheme.com
 * @copyright 	Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license		http://www.rockettheme.com/legal/license.php RocketTheme Proprietary Use License
 */

var Gantry = {
	init: function() {
		if (document.id('gantry-mega-form')) document.id('gantry-mega-form').set('autocomplete', 'off');
		Gantry.cookie = Cookie.read('gantry-admin');
		Gantry.cleanance();
		Gantry.initTabs();
		Gantry.selectedSets();
		Gantry.inputs();
		Gantry.Overlay = new Gantry.Layer();
		Gantry.Tips.init();
		Gantry.dropdown();
		Gantry.notices();
	},

	load: function() {
	},

	notices: function() {
		var notices = $$('.gantry-notice');
		if (notices.length) {
			notices.each(function(notice) {
				var close = notice.getElement('.close');
				if (close) {
					var fx = new Fx.Tween(notice, {duration: 200, link: 'ignore', onComplete: function() {
						if (document.id(notice)) notice.dispose();
					}});
					close.addEvent('click', fx.start.pass(['opacity', 0], fx));
				}
			});
		}

		var deletOverride = $$('.overrides-button.button-del');
		deletOverride.addEvent('click', function(e) {
			var del = confirm(GantryLang['are_you_sure']);
			if (!del) e.stop();
		});
	},

	dropdown: function() {
		var inside = document.id('overrides-inside'), first = document.id('overrides-first'), delay = null;
		var slide = new Fx.Slide('overrides-inside', {
			duration: 100,
			onStart: function() {
				var width = document.id('overrides-actions').getSize().x - 4;
				inside.setStyle('width', width);
				this.wrapper.setStyle('width', width + 4);
			},
			onComplete: function() {
				if (!this.open) first.removeClass('slide-down');
			}
		}).hide();
		inside.setStyle('display', 'block');

		var enterFunction = function() {
			if (inside.hasClass('slidedown')) {
				slide.slideIn();
				first.addClass('slide-down');
			}
		};

		var leaveFunction = function() {
			if (inside.hasClass('slideup')) {
				slide.slideOut();
			}
		};


		$$('#overrides-toggle, #overrides-inside').addEvents({
			'mouseenter': function() {
				$clear(delay);
				inside.removeClass('slideup').addClass('slidedown');
				delay = enterFunction();
			},
			'mouseleave': function() {
				$clear(delay);
				inside.removeClass('slidedown').addClass('slideup');
				leaveFunction.delay(300);
			}
		});

		Gantry.dropdownActions();

	},

	dropdownActions: function() {
		var dropdown = document.id('overrides-actions'), tools = document.id('overrides-toolbar'), first = document.id('overrides-first');
		var toggle = document.id('overrides-toggle');
		if (tools) {
			var add = tools.getElement('.button-add'), del = tools.getElement('.button-del'), edit = tools.getElement('.button-edit');
			if (edit) {
				edit.addEvent('click', function() {
					if (first.getElement('input')) {
						first.getElement('input').empty().dispose();
						toggle.removeClass('hidden');
						return;
					}
					toggle.addClass('hidden');
					var input = new Element('input', {'type': 'text', 'class': 'add-edit-input', 'value': first.get('text').clean().trim()});
					input.addEvent('keydown', function(e) {
						if (e.key == 'esc') {
							this.empty().dispose();
							toggle.removeClass('hidden');
						}
						else if (e.key == 'enter') {
							e.stop();
							var list = document.id('overrides-inside').getElements('a');
							var index = list.get('text').indexOf(this.value);
							if (index != -1) {
								this.highlight('#ff4b4b', '#fff');
								return;
							}
							document.getElement('input[name=override_name]').set('value', this.value);
							index = list.get('text').indexOf(first.get('text').clean().trim());
							if (index != -1) list[index].set('text', this.value);
							this.empty().dispose();
							toggle.removeClass('hidden');
							first.getElement('a').set('text', this.value);
						}
					});
					input.inject(first, 'top').focus();
				});
			}
		}
	},

	inputs: function() {
		var inputs = $$('.text-short, .text-medium, .text-long, .text-color');
		inputs.addEvents({
			'attach': function() {
				this.removeClass('disabled');
			},

			'detach': function() {
				this.addClass('disabled');
			},

			'set': function(value) {
				this.value = value;
			},

			'keydown': function(e) {
				if (this.hasClass('disabled')) { e.stop(); return; }
			},

			'focus': function() {
				if (this.hasClass('disabled')) this.blur();
			},

			'keyup': function(e) {
				if (this.hasClass('disabled')) { e.stop(); return; }
				if (Gantry.MenuItemHead) {
					var cache = Gantry.MenuItemHead.Cache[Gantry.Selection];
					if (!cache) cache = new Hash({});
					cache.set(this.id.replace('params', ''), this.value);
				}
			}
		});
	},

		selectedSets: function(){
		var sets = $$('.selectedset-switcher select');
		var setsToggle;

		sets.each(function(set, i){
			var id = set.id.replace('_type', '');
			//setsToggle = document.getElement('.selectedset-enabler input[id^='+id+']');
			set.store('gantry:values', set.getElements('option').get('value'));
			set.addEvent('change', function(){
				this.retrieve('gantry:values').each(function(value){
					var layer = document.id('set-' + value);
					if (layer){
						layer.removeClass('selectedset-hidden-field');
						layer.setStyle('display', (value == this.value) ? 'block' : 'none');

						if (window.selectboxes && value == this.value){
							layer.getElements('.selectbox-wrapper').each(function(wrapper){
								wrapper.getElements('.selectbox-top, .selectbox-dropdown').set('style', '');
								window.selectboxes.updateSizes(wrapper);
							});
						}
					}
				}, this);

			});
		});

		$$('.selectedset-enabler input[id]').each(function(set, j){
			set.store('gantry:values', sets[j].retrieve('gantry:values'));
			set.addEvent('onChange', function(){
				this.retrieve('gantry:values').each(function(value){
					var layer = document.id('set-' + value);
					if (layer){
						if (!this.value.toInt()) layer.setStyle('display', 'none');
						else {
							layer.removeClass('selectedset-hidden-field');
							layer.setStyle('display', (value == sets[j].get('value')) ? 'block' : 'none');
						}
					}
				}, this);
			});
		});

		var menu = document.id(GantryParamsPrefix + 'menu_type');
		if (menu) menu.fireEvent('change');
	},

	cleanance: function() {
		Gantry.overridesBadges();
		Gantry.tabs = [];
		Gantry.panels = [];
		var paneSlider = document.getElement('.pane-sliders') || document.getElement('#gantry-panel');
		var items = paneSlider.getChildren();
		var fieldsets = items.getElement('.panelform');

		Gantry.tabs = document.getElements('#gantry-tabs li');

		if (!wrapper) {
			var wrapper = document.getElement('.gantry-wrapper');
		}

		if (!container) {
			var container = document.getElement('#gantry-panel');
		}

		var widgets = document.getElements('#widget-list .widget .widget-top, #wp_inactive_widgets .widget .widget-top');
		if (widgets.length) {
			widgets.each(function(widget) {
				var parent = widget.getParent();
				if (parent.id.contains('gantrydivider')) parent.addClass('gantry-divider');
			});
		}

		var innerTabs = fieldsets.getElements('.inner-tabs ul').flatten();

		innerTabs.each(function(innertab){
			var tabs = innertab.getElements('li'),
				panels = innertab.getParent('.innertabs-field').getElements('.inner-panels .inner-panel');

			tabs.each(function(tab, i) {
				tab.addEvents({
					'mouseenter': function() {this.addClass('hover');},
					'mouseleave': function() {this.removeClass('hover');},
					'click': function() {
						panels.setStyle('position', 'absolute');
						panels.fade('out');
						panels[i].setStyles({'position': 'relative', 'float': 'left', 'top': 0, 'z-index': 5}).fade('in');
						//Gantry.container.tween('height', panel.retrieve('gantry:height'));
						tabs.removeClass('active');
						this.addClass('active');
					}
				});
			});
		}, this);

		Gantry.panels = $$('.gantry-panel');
		Gantry.wrapper = wrapper;
		Gantry.container = container;
		Gantry.tabs = $$(Gantry.tabs);

		var dashboard = new Hash({'contextual-help-link-wrap': 'contextual-help-wrap', 'screen-options-link-wrap': 'screen-options-wrap'});
		dashboard.each(function(wrap, lnk) {
			var button = document.id(lnk);
			var wrapper = document.id(wrap);
			if (!button || !wrapper) return;

			var others = $$('#screen-meta-links > div[id!='+lnk+']');
			button.addEvent('mouseup', function() {
				if (!wrapper.hasClass('contextual-help-open')) others.setStyle('visibility', 'hidden');
				else others.setStyle('visibility', 'visible');
			});
		});

		var clearCache = document.id('cache-clear-wrap');
		if (clearCache) {
			var ajaxloading = new Asset.image('images/wpspin_dark.gif', {
				onload: function() {this.setStyles({'display': 'none'}).addClass('ajax-loading').inject(clearCache, 'top');}
			});
			clearCache.addEvent('click', function(e) {
				e.stop();
				new Request.HTML({
					url: AdminURI,
					onRequest: function() { clearCache.addClass('disabled'); ajaxloading.setStyle('display', 'block'); },
					onSuccess: function() { window.location.reload(); }
				}).post({
					'action': 'gantry_admin',
					'model': 'cache',
					'gantry_action': 'clear'
				});
			});
		}
	},

	overridesBadges: function() {
		$$('.overrides-involved').filter(function(badge) {
		    return badge.get('text').trim().clean().toInt();
		}).setStyles({'display': 'block', 'opacity': 1});
	},

	initTabs: function() {
		var max = 0;
		Gantry.panels.setStyle('position', 'absolute');
		var pan = document.getElement('#gantry-panel .active-panel');
		(pan || Gantry.panels[0]).setStyle('position', 'relative');
		Gantry.panels.set('tween', {duration: 'short', onComplete: function() {
			if (!this.to[0].value) this.element.setStyle('display', 'none');
		}});

		Gantry.panels.each(function(panel, i) {
			var height = panel.retrieve('gantry:height');

			Gantry.tabs[i].addEvents({
				'mouseenter': function() {this.addClass('hover');},
				'mouseleave': function() {this.removeClass('hover');},
				'click': function() {
					Cookie.write('gantry-admin-tab', i);
					if (this.hasClass('active')) return;
					Gantry.panels.setStyle('position', 'absolute');
					Gantry.panels.setStyles({'visibility': 'hidden', 'opacity': 0, 'z-index': 5});
					panel.set('morph', {duration: 330});
					panel.setStyles({'display': 'inline-block', 'position': 'relative', 'top': -20, 'z-index': 15}).morph({'top': 0, 'opacity': 1});
					//Gantry.container.tween('height', panel.retrieve('gantry:height'));
					Gantry.tabs.removeClass('active');
					this.addClass('active');
				}
			});
		});

		//Gantry.tabs[Cookie.read('gantry-admin-tab') || 0].fireEvent('click');
	}
};

Gantry.Tips = {
	init: function() {
		var panels = $$('.gantry-panel'), labels;
		if (document.id(document.body).getElement('.defaults-wrap')) {
			labels = panels.getElements('.gantry-panel-left .gantry-field > label:not(.rokchecks), .gantry-panel-left .gantry-field span[class!=chain-label][class!=group-label] > label:not(.rokchecks)');
		}
		else {
			labels = panels.getElements('.gantry-panel-left .gantry-field .base-label label');
		}
		labels.each(function(labelsList, i){
			if (labelsList.length) {
				labelsList.addEvent('mouseenter', function() {
					var index = labelsList.indexOf(this);
					var panel = panels[i];
					if (panel) {
						var id = (!this.id) ? false : 'tip-' + this.id.replace(GantryParamsPrefix, '').replace(/-lbl$/, '');
						var tipArrow = panel.getElement('.gantrytips-left');
						if (tipArrow) {
							if (!id || !document.id(id)) tipArrow.fireEvent('jumpTo', index + 1);
							else tipArrow.fireEvent('jumpById', id);
						}
					}
				});
			}
		});
	}
};

Gantry.ToolBar = {
	'add': function(meta_name) {
		var screenMeta = document.id('screen-meta');
		if (screenMeta) document.id('contextual-'+meta_name+'-wrap').inject(screenMeta, 'top');
		(function($){
			var othersmeta = $('#screen-meta-links > div[id!=meta-'+meta_name+'-link-wrap]');
			$('#meta-'+meta_name+'-link').click(function () {
				if (!$('#contextual-'+meta_name+'-wrap').hasClass('contextual-'+meta_name+'-open'))
					$('#screen-meta-links > div[id!=meta-'+meta_name+'-link-wrap]').css('visibility', 'hidden');

				$('#contextual-'+meta_name+'-wrap').slideToggle('fast', function() {
					if ($(this).hasClass('contextual-'+meta_name+'-open')) {
						$('#meta-'+meta_name+'-link').css({'backgroundPosition':'top right'});
						othersmeta.css('visibility', '');
						$(this).removeClass('contextual-'+meta_name+'-open');
					} else {
						$('#meta-'+meta_name+'-link').css({'backgroundPosition':'bottom right'});
						$(this).addClass('contextual-'+meta_name+'-open');
					}
				});

				return false;
			});
		})(jQuery);
	}
};

Gantry.Layer = new Class({
	Implements: [Events, Options],
	options: {
		duration: 200,
		opacity: 0.8
	},

	initialize: function(options) {
		var self = this;

		this.setOptions(options);

		this.id = new Element('div', {id: 'gantry-layer'}).inject(document.body);
		this.fx = new Fx.Tween(this.id, {
			'duration': this.options.duration,
			'wait': false,
			'onComplete': function() {
				if (!this.to[0].value) {
					self.open = false;
				} else {
					self.open = true;
					self.fireEvent('show');
				}
			}
		}).set('opacity', 0);
		this.open = false;

	},

	show: function() {
		this.calcSizes();
		this.fx.start('opacity', this.options.opacity);
	},

	hide: function() {
		this.fireEvent('hide');
		this.fx.start('opacity', 0);
	},

	toggle: function() {
		this[this.open ? 'hide' : 'show']();
	},

	calcSizes: function() {
		this.id.setStyles({
			'width': window.getScrollSize().x,
			'height': window.getScrollSize().y
		});
	}
});

window.addEvent('domready', Gantry.init);
window.addEvent('load', Gantry.load);
var Tips = new Class({});
