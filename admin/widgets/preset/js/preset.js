/**
 * @version   1.31 December 18, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

var PresetDropdown = {
	list: {},
	init: function(cls) {
		PresetDropdown.list[cls] = document.id(GantryParamsPrefix + cls);

		var objs = selectboxes.getObjects(PresetDropdown.list[cls].getPrevious());
		objs.real.addEvent('change', PresetDropdown.select.bind(PresetDropdown, cls));

		//PresetsBadges.init(cls);
	},

	newItem: function(cls, key, value) {
		if (!PresetDropdown.list[cls] && $$('.' + cls).length) return Scroller.addBlock(cls, key, value);

		var li = new Element('li').set('text', value);
		var option = new Element('option', {value: key}).set('text', value);
		var objs = selectboxes.getObjects(PresetDropdown.list[cls].getPrevious());

		var dup = null;

		objs.real.getChildren().each(function(child, i) {
			if (child.value == key) dup = i;
		});

		if (dup == null) {
			option.inject(PresetDropdown.list[cls]);
			li.inject(PresetDropdown.list[cls].getPrevious().getLast().getElement('ul'));
			PresetDropdown.attach(cls);
		} else {
			var real_option = objs.real.getChildren()[dup], real_list = PresetDropdown.list[cls].getPrevious().getLast().getElement('ul').getChildren()[dup];

			real_option.replaceWith(option);
			real_list.replaceWith(li);

			PresetDropdown.attach(cls, dup);
		}

		return true;
	},

	attach: function(cls, index) {
		var objs = selectboxes.getObjects(PresetDropdown.list[cls].getPrevious()), self = this;

		if (index == null) index = objs.list.length - 1;
		var el = objs.list[index];

		el.addEvents({
			'mouseenter': function() {
				objs.list.removeClass('hover');
				this.addClass('hover');
			},
			'mouseleave': function() {
				this.removeClass('hover');
			},
			'click': function() {
				objs.list.removeClass('active');
				this.addClass('active');
				this.fireEvent('select', [objs, index]);
			},
			select: selectboxes.select.pass(selectboxes, [objs, index])
		});
		selectboxes.updateSizes(PresetDropdown.list[cls].getPrevious());
		el.fireEvent('select');
	},

	select: function(cls) {
		var preset = Presets[cls].get(PresetDropdown.list[cls].getPrevious().getElement('.selected span').get('text'));

		var master = document.id('master-items');
		if (master) master = master.hasClass('active');

		$H(preset).each(function(value, key) {
			var el = document.id(GantryParamsPrefix + key);

			var type = el.get('tag');

			if (master && Gantry.MenuItemHead) Gantry.MenuItemHead.getCheckbox(key).fireEvent('switchon');

			switch(type) {
				case 'select':
					var values = el.getElements('option').getProperty('value');
					var objs = selectboxes.getObjects(el.getParent());
					selectboxes.select(objs, values.indexOf(value));

					break;

				case 'input':
					var cls = el.getProperty('class');
					el.set('value', value);

					if (cls.contains('picker-input')) {
						document.id(el).getParent().fireEvent('mouseenter');
						el.fireEvent('set', value);
					} else if (cls.contains('slider')) {
						var slider = window['slider' + key];
						slider.set(slider.list.indexOf(value));
					} else if (cls.contains('toggle')) {
						var n = key.replace("-", '');
						window['toggle' + n].set(value.toInt());
						window['toggle' + n].fireEvent('onChange', value.toInt());
					}

					break;

			}

		});
	}
};

var Scroller = {
	init: function(cls) {
		Scroller.wrapper = $$('.' + cls + ' .scroller .wrapper')[0];
		Scroller.bar = $$('.' + cls + ' .bar')[0];

		if (!Scroller.wrapper || !Scroller.bar) return;

		document.id('contextual-preset-wrap').setStyles({'position': 'absolute', 'top': -3000, 'display': 'block'});

		Scroller.childrens = Scroller.wrapper.getChildren();

		var size = Scroller.wrapper.getParent().getSize();
		var wrapSize = Scroller.wrapper.getSize();
		Scroller.barWrapper = new Element('div', {
			'styles': {
				'position': 'absolute',
				'left': 0,
				'bottom': 0,
				'width': Scroller.bar.getStyle('width'),
				'height': Scroller.bar.getStyle('height')
			}
		}).inject(Scroller.bar, 'before');

		Scroller.getBarSize();
		Scroller.bar.inject(Scroller.barWrapper).setStyles({'bottom': 1, 'left': 0});

		Scroller.children(cls);

		var deleters = $$('.delete-preset');

		deleters.each(function(deleter) {
			deleter.addEvent('click', function(e) {
				new Event(e).stop();
				Scroller.deleter(this, cls);
			});
		});

		//PresetsBadges.init(cls);

		if (Scroller.size > size.x) return;

		Scroller.bar.setStyle('width', Scroller.size);
		Scroller.drag(Scroller.wrapper, Scroller.bar);

		document.id('contextual-preset-wrap').setStyles({'position': 'relative', 'top': 0, 'display': 'none'});
	},

	deleter: function(item, cls) {
		var key = item.id.replace('keydelete-', '');
		new Request.HTML({
			url: AdminURI + '?option=com_admin&tmpl=gantry-ajax-admin',
			onSuccess: function(r) {Scroller.deleteAction(r, item, cls, key);}
		}).post({
			'action': 'gantry_admin',
			'model': 'presets-saver',
			'gantry_action': 'delete',
			'template': Gantry.PresetsSaver.Template,
			'preset-title': cls,
			'preset-key': key
		});
	},

	deleteAction: function(r, item, cls, key) {
		if (PresetsKeys[cls].contains(key)) {
			item.dispose();
		} else {
			var block = item.getParent();
			Scroller.childrens.erase(block);
			var blockSize = block.getSize().x;
			block.empty().dispose();

			var last = Scroller.childrens.getLast().addClass('last');
			var first = Scroller.childrens[0].addClass('first');

			var wrapperSize = Scroller.wrapper.getStyle('width').toInt();
			Scroller.wrapper.setStyle('width', wrapperSize - blockSize);
			var overflow = Math.abs(Scroller.bar.getStyle('width').toInt() - Scroller.getBarSize());
			Scroller.bar.setStyle('width', Scroller.getBarSize());
			if (Scroller.bar.getStyle('left').toInt() > Scroller.getBarSize() / 2) Scroller.bar.setStyle('left', Scroller.bar.getStyle('left').toInt() - overflow);
		}

		Scroller.wrapper.getParent().scrollTo(wrapperSize);

		if (typeof CustomPresets != 'undefined' && CustomPresets[key]) delete CustomPresets[key];
	},

	getBarSize: function() {
		var size = Scroller.wrapper.getParent().getSize();
		var wrapSize = Scroller.wrapper.getSize();
		Scroller.size = size.x * Scroller.barWrapper.getStyle('width').toInt() / wrapSize.x;

		return Scroller.size;
	},

	addBlock: function(cls, key, value) {
		var preset = Presets[cls].get(value);
		if (!preset) {
			if (document.id('contextual-preset-wrap').getStyle('display') == 'none') {
				document.id('contextual-preset-wrap').setStyles({'position': 'absolute', 'top': -3000, 'display': 'block'});
			}
			var last = Scroller.childrens[Scroller.childrens.length - 1], length = Scroller.childrens.length;
			var newBlock = last.clone();
			newBlock.inject(last, 'after').addClass('last').className = "";
			newBlock.className = 'preset' + (length + 1) + ' block last';
			newBlock.getElement('span').set('html', value);
			last.removeClass('last');

			var bg = newBlock.getFirst().getStyle('background-image');
			var tmp = bg.split("/");

			var img = tmp[tmp.length - 1];
			var end = key + '.png")';
			var fin = tmp.join("/").replace(img, end);

			newBlock.getFirst().setStyle('background-image', '');
			newBlock.getFirst().setStyle('background-image', fin);

			var wrapperSize = Scroller.wrapper.getStyle('width').toInt();
			var blockSize = newBlock.getSize().x;
			Scroller.wrapper.setStyle('width', wrapperSize + 198);

			Scroller.bar.setStyle('width', Scroller.getBarSize());
			Scroller.childrens.push(newBlock);

			Scroller.child(cls, newBlock);

			var x = new Element('div', {id: 'keydelete-' + key, 'class': 'delete-preset'}).set('html', '<span>X</span>').inject(newBlock);
			x.addEvent('click', function(e) {
				new Event(e).stop();
				Scroller.deleter(this, cls);
			});

			if (document.id('contextual-preset-wrap').getStyle('display') == 'block' && document.id('contextual-preset-wrap').getStyle('top').toInt() == -3000) {
				document.id('contextual-preset-wrap').setStyles({'position': 'relative', 'top': 0, 'display': 'none'});
			}
		}
	},

	drag: function(wrapper, bar) {
		Scroller.dragger = new Drag.Move(bar, {
			container: Scroller.barWrapper,
			onDrag: function() {
				var parent = Scroller.wrapper.getParent();
				var size = parent.getSize();
				var x = this.value.now.x * parent.getScrollSize().x / size.x;
				if (x > x / 2) x += 10;
				else x -= 10;
				parent.scrollTo(x);
			}
		});
		Scroller.wrapper.getParent().scrollTo(0);
	},

	child: function(cls, child) {
		child.getFirst().setStyle('border', '1px solid #000');
		var fx = new Fx.Tween(child.getFirst(), {duration: 300}).set('border-color', '#000');
		child.addEvent('click', function(e) {

			new Event(e).stop();

		fx.start('border-color', '#fff')
			.chain(function() {this.start('border-color', '#000');})
			.chain(function() {this.start('border-color', '#fff');})
			.chain(function() {this.start('border-color', '#000');});

			Scroller.updateParams(cls, child);
		});
	},

	children: function(cls) {
		Scroller.childrens.each(function(child, i) {
			child.getFirst().setStyle('border', '1px solid #000');
			var fx = new Fx.Tween(child.getFirst(), {duration: 300}).set('border-color', '#000');

			Scroller.labs = new Hash({});
			Scroller.involved = $$('.presets-involved');
			Scroller.involvedFx = [];
			Scroller.involved.each(function(inv) {
				Scroller.involvedFx.push(new Fx.Tween(inv, {link: 'cancel'}).set('opacity', 0));
			});

			child.addEvent('click', function(e) {
				new Event(e).stop();

				fx.start('border-color', '#fff')
					.chain(function() {this.start('border-color', '#000');})
					.chain(function() {this.start('border-color', '#fff');})
					.chain(function() {this.start('border-color', '#000');});


				Scroller.updateParams(cls, child, i);
			});
		});
	},

	updateParams: function(cls, child, index) {
		var keyPreset = child.getElement('span').get('text');
		var preset = Presets[cls].get(keyPreset);

		var del = child.getElement('.delete-preset');
		if (del) {
			var customKey = del.id.replace("keydelete-", "");
			if (CustomPresets[customKey]) preset = CustomPresets[customKey];
		}


		var master = document.id('master-items');
		if (master) master = master.hasClass('active');


		var currentParams = {};
		var labels = Scroller.labs;

		labels.each(function(labelsList) {
			labelsList.each(function(label) {
				var txt = label.retrieve('gantry:text', false);
				if (txt) {
					label.set('text', txt);
					label.store('gantry:notice', false);
				}
				Scroller.involved.set('text', 0);
			});
		});

		$H(preset).each(function(value, key) {

			if (key == 'name') return;
			var el = document.id(GantryParamsPrefix + key.replace(/-/, '_'));
			if (!el) return;

			if (!labels.get(keyPreset)) labels.set(keyPreset, []);
			var type = el.get('tag');

			var panel = el.getParent('.gantry-panel').className.replace(/[panel|\-|\s|gantry]/g, '').toInt() - 1;
//			panel = panel.trim().clean() - 1;

			if (!currentParams[panel]) currentParams[panel] = 0;
			currentParams[panel]++;
			Scroller.involved[panel].set('text', currentParams[panel]);

			var label;
			if (el.getParent('.gantry-field').getElement('.base-label')) label = el.getParent('.gantry-field').getElement('.base-label label');
			else label = el.getParent('.gantry-field').getElement('label');
			var lKey = labels.get(keyPreset);

			if (!lKey.contains(label)) lKey.push(label);
			if (!label.retrieve('gantry:notice', false)) {
				label.store('gantry:text', label.get('text'));
				label.set('html', '<span class="preset-info">&#9679;</span> ' + label.retrieve('gantry:text'));
				label.store('gantry:notice', true);
			}

			if (master && Gantry.MenuItemHead) Gantry.MenuItemHead.getCheckbox(key).fireEvent('switchon');

			var inherit_input = el.retrieve('gantry:override_checkbox'),
				inherit_input_obj = null;

			if (inherit_input){
				inherit_input_obj = inherit_input.retrieve('gantry:fields');
				inherit_input_obj[el.id] = value;
				inherit_input.store('gantry:fields', inherit_input_obj);
			}

			switch(type) {
				case 'select':
					var values = el.getElements('option').getProperty('value');
					var objs = selectboxes.getObjects(el.getParent());
					selectboxes.select(objs, values.indexOf(value));

					break;

				case 'input':
					var cls = el.getProperty('class');
					el.set('value', value);

					if (cls.contains('picker-input')) {
						document.id(el).getParent().fireEvent('mouseenter');
						el.fireEvent('set', value);
					} else if (cls.contains('slider')) {
						var slider = window.sliders[(GantryParamsPrefix + key.replace(/-/, '_')).replace("-", "_")];
						slider.set(slider.list.indexOf(value));
						slider.hiddenEl.fireEvent('set', value);
					} else if (cls.contains('toggle')) {
						var n = (GantryParamsPrefix + key.replace(/-/, '_')).replace("-", '');
						window['toggle' + n].set(value.toInt());
						window['toggle' + n].fireEvent('onChange', value.toInt());
					}

					break;

			}

		});

		Scroller.involved.each(function(inv, i) {
			var value = inv.get('text').toInt();
			if (!value) {
				Scroller.involvedFx[i].element.getParent().removeClass('double-badge');
				Scroller.involvedFx[i].cancel().start('opacity', [1, 0]).chain(function() { Scroller.involvedFx[i].element.setStyle('visibility', 'hidden'); this.element.setStyle('display', 'none');});
				return;
			}

			var overrides = Scroller.involvedFx[i].element.getNext('span');
			if (overrides && overrides.getStyle('display') == 'block') Scroller.involvedFx[i].element.getParent().addClass('double-badge');
			else Scroller.involvedFx[i].element.getParent().removeClass('double-badge');
			inv.setStyle('display', 'block');
			Scroller.involvedFx[i].element.setStyle('visibility', 'visible');
			Scroller.involvedFx[i].start('opacity', [0, 1]);
		});
	}
};


var PresetsBadges = {
	init: function(cls) {
		if (!PresetsBadges.list) PresetsBadges.list = new Hash();

		var label = PresetsBadges.getLabel(cls);
		var params = [];

		PresetsBadges.list.set(cls, []);

		Presets[cls].each(function(value, key) {
			if (!params.length) {
				for (var p in value) {
					params.push(p);
					var labelChild = PresetsBadges.getLabel(p);
					if (labelChild) {
						var badge = PresetsBadges.build(p, labelChild, label, false);
						PresetsBadges.list.get(cls).push(badge);
					}
				}
			}
		});

		if (!PresetsBadges.buttons) PresetsBadges.buttons = [];

		var button = PresetsBadges.build(cls, label, false, params.length);
		PresetsBadges.buttons.push(button);

		button.addEvents({
			'click': function(e) {
				new Event(e).stop();

				this.fireEvent('toggle');
			},

			'show': function() {
				this.getElement('.number').setStyle('visibility', 'visible');
				$$(PresetsBadges.list.get(cls)).setStyle('display', 'block');

				this.showing = true;
			},

			'hide': function() {
				this.getElement('.number').setStyle('visibility', 'hidden');
				$$(PresetsBadges.list.get(cls)).setStyle('display', 'none');

				this.showing = false;
			},

			'toggle': function() {
				PresetsBadges.buttons.each(function(b) {
					if (b != button) b.fireEvent('hide');
				});

				if (this.showing) this.fireEvent('hide');
				else this.fireEvent('show');
			}
		});
	},

	build: function(cls, label, parent, count) {
		var children = label.getChildren(), height = label.getSize().y, badge;

		var wrapper = label.getElement('.presets-wrapper');
		if (!wrapper) {
			wrapper = new Element('div', {'class': 'presets-wrapper', 'styles': {'position': 'relative'}}).inject(label, 'top');
			children.each(wrapper.adopt.bind(wrapper));
			wrapper.setStyle('height', height + 15);
			label.getElement('.hasTip').setStyle('line-height', height + 15);
		}

		var text = (parent) ? parent.getElement('.hasTip').innerHTML : GantryLang['show_parameters'];

		badge = new Element('div', {'class': 'presets-badge'}).inject(wrapper, 'top');

		var left = new Element('span', {'class': 'left'}).inject(badge);
		var right = new Element('span', {'class': 'right'}).inject(left).set('text', text);

		if (count != null) {
			var number = new Element('span', {'class': 'number'}).inject(right);
			number.set('text', count).setStyle('visibility', 'hidden');
			badge.setStyle('cursor', 'pointer').addClass('parent');
		} else {
			badge.setStyle('display', 'none');
			var layer = label.getNext().getFirst().getLast();
			if (layer) {
				var top = layer.getStyle('top').toInt();
				layer.setStyle('top', top - 10);
			}
		}

		return badge;

	},

	getLabel: function(cls) {
		var search = document.id(GantryParamsPrefix + cls);
		if (search) {
			var parent = search.getParent(), match = null;
			while (parent && parent.get('tag') != 'table') {
				if (parent.get('tag') == 'tr') match = parent;
				parent = parent.getParent();
			}

			return match.getFirst();
		} else {
			return null;
		}
	}
};
