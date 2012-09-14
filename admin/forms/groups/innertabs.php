<?php
/**
 * @version   1.26 September 14, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('GANTRY_VERSION') or die;

gantry_import('core.config.gantryformgroup');


class GantryFormGroupInnerTabs extends GantryFormGroup
{

    protected $type = 'innertabs';
    protected $baseetype = 'group';

    public function getInput()
    {
        return '';
    }


    public function render($callback)
    {
        ob_start();
        ?>
            <div class="innertabs-panel">
                <div class="gantry-field <?php echo $this->type; ?>-field">
                    <div>
                        <div class="inner-tabs">
                            <ul>
                                <?php
                                $i = 0;
                                foreach ($this->fields as $field):
                                    $classes = '';
                                    if (!$i) $classes .= "first active";
                                    if ($i == count($this->fields) - 1) $classes .= ' last';
                                 ?>
                                    <li class="<?php echo $classes;?>"><span><?php _ge($field->getLabel());?></span></li>

                                <?php
                                    $i++;
                                    endforeach;
                                ?>
                            </ul>
                        </div>
                        <div class="inner-panels">
                            <?php
                            $i = 0;
                            foreach ($this->fields as $field):
                                $i++;
                                ?>
                                <div class="inner-panel inner-panel-<?php echo $i;?>">
                                    <?php if ($field->type == 'hidden'): ?>
                                    <?php echo $field->getInput(); ?>
                                    <?php else: ?>
                                    <?php echo $field->render($callback); ?>
                                    <?php endif;?>
                                </div>
                            <?php endforeach;?>
                        </div>
						<div class="clear"></div>
                    </div>
                </div>
            </div>
        <?php
        $buffer = ob_get_clean();
        return $buffer;
    }
}
