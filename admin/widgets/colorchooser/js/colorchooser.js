/**
 * @version		1.31 December 18, 2012
 * @author		RocketTheme http://www.rockettheme.com
 * @copyright 	Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

var GantryColorChooser = {
	add: function(id, transparent) {
		var name = id.replace(/-/, '_'),
			rainbow;

		if (!window['moorainbow']) window['moorainbow'] = {};

		var fun = function() {
			var input = document.id(id);

			input.getParent().removeEvent('mouseenter', fun);
			rainbow = new MooRainbow('myRainbow_'+id+'_input', {
				id: 'myRainbow_' + id,
				startColor: document.id(id).get('value').hexToRgb(true) || [255, 255, 255],
				imgPath: GantryURL + '/admin/widgets/colorchooser/images/',
				transparent: transparent,
				onChange: function(color) {
					if (color == 'transparent') {
						input.getNext().getFirst().addClass('overlay-transparent').setStyle('background-color', 'transparent');
						input.value = 'transparent';
					} else {
						input.getNext().getFirst().removeClass('overlay-transparent').setStyle('background-color', color.hex);
						input.value = color.hex;
					}

					if (this.visible) this.okButton.focus();
				}
			});

			window['moorainbow']['r_' + name] = rainbow;

			rainbow.okButton.setStyle('outline', 'none');
			document.id('myRainbow_'+id+'_input').addEvent('click', function() {
				(function() {
					rainbow.okButton.focus();
				}).delay(10);
			});
			input.addEvent('keyup', function(e) {
				if (e) e = new Event(e);
				if ((this.value.length == 4 || this.value.length == 7) && this.value[0] == '#') {
					var rgb = new Color(this.value);
					var hex = this.value;
					var hsb = rgb.rgbToHsb();
					var color = {
						'hex': hex,
						'rgb': rgb,
						'hsb': hsb
					};
					rainbow.fireEvent('onChange', color);
					rainbow.manualSet(color.rgb);
				};
			}).addEvent('set', function(value) {
				this.value = value;
				this.fireEvent('keyup');
			});
			input.getNext().getFirst().setStyle('background-color', rainbow.sets.hex);
			GantryColorChooser.load('myRainbow_' + id);
		};

		if (name.contains('gradient') && (name.contains('from') || name.contains('to'))) {
			fun();
		} else {
			window.addEvent('domready', function() {
				document.id(id).getParent().addEvents({
					'mouseenter': fun,
					'mouseleave': function() {
						this.removeEvent('mouseenter', fun);
					}
				});
			});
		}
	},

	load: function(name, hex){
		if (hex) {
			document.id(name + '_input').getPrevious().value = hex;
			document.id(name + '_input').getFirst().setStyle('background-color', hex);
		}
	}
};
