<?php
/**
 * @version   $Id: body_mainbody.php 58623 2012-12-15 22:01:32Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 */
defined('GANTRY_VERSION') or die();

gantry_import('core.gantrybodylayout');

/**
 *
 * @package    gantry
 * @subpackage html.layouts
 */
class GantryLayoutBody_MainBody extends GantryBodyLayout
{
	var $render_params = array(
		'schema'            => null,
		'pushPull'          => null,
		'classKey'          => null,
		'sidebars'          => '',
		'contentTop'        => null,
		'contentBottom'     => null,
		'component_content' => ''
	);

	function render($params = array())
	{
		global $gantry;

		$fparams = $this->_getParams($params);

		// logic to determine if the component should be displayed
		$display_component = !($gantry->get("component-enabled", true) == false);
		ob_start();
		// XHTML LAYOUT
		?>
		<div id="rt-main" class="<?php echo $fparams->classKey; ?>">
			<div class="rt-container">
				<div class="rt-grid-<?php echo $fparams->schema['mb']; ?> <?php echo $fparams->pushPull[0]; ?>">
					<?php if (isset($fparams->contentTop)) : ?>

						<div id="rt-content-top">
							<?php echo $fparams->contentTop; ?>
						</div>
					<?php endif; ?>

					<div class="rt-block">
						<?php if ($display_component) : ?>

							<div id="rt-mainbody">
								<?php
								if ('' == $fparams->component_content) {
									$this->include_type();
								} else {
									echo $fparams->component_content;
								}
								?>
							</div>
						<?php endif; ?>
					</div>
					<?php if (isset($fparams->contentBottom)) : ?>

						<div id="rt-content-bottom">
							<?php echo $fparams->contentBottom; ?>
						</div>
					<?php endif; ?>
				</div>
				<?php echo $fparams->sidebars; ?>

				<div class="clear"></div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}