<?php
/**
 * @version   $Id: commentstempl_basic.php 59361 2013-03-13 23:10:27Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 */
defined('GANTRY_VERSION') or die();

gantry_import('core.gantrylayout');

/**
 *
 * @package    gantry
 * @subpackage html.layouts
 */
class GantryLayoutCommentsTempl_Basic extends GantryLayout
{
	var $render_params = array(
		'commentLayout' => 'basic'
	);

	function render($params = array())
	{
		global $wp_query, $withcomments, $post, $wpdb, $id, $comment, $user_login, $user_ID, $user_identity, $overridden_cpage;
		/** @global $gantry Gantry */
		global $gantry;

		$fparams             = $this->_getParams($params);
		$comment_layout_name = 'comment_' . $fparams->commentLayout;
		$layout              = $gantry->_getLayout($comment_layout_name);
		$className           = 'GantryLayout' . ucfirst($comment_layout_name);


		// Do not delete these lines

		ob_start();
		if (post_password_required()) {
			?>
			<span class="alert"><?php _ge('This post is password protected. Enter the password to view comments.') ?></span>
			<?php
			return ob_get_clean();
		}
		?>
		<!-- You can start editing here. -->
		<?php if (have_comments()) : ?>
		<br/>
		<div class="comment-section">
			<div class="contentheading"><?php comments_number(_g('No Comments'), _g('1 Comment'), _g('% Comments'));?></div>
		</div>
		<ol class="commentlist">
			<?php wp_list_comments(array(
			                            'style'      => 'ol',
			                            'callback'   => array($className, 'render_comment'),
			                            'reply_text' => _g('Reply')
			                       )); ?>
		</ol>
		<div class="rt-pagination nav">
			<div class="alignleft"><?php next_comments_link('&laquo; ' . _g('Older Comments')); ?></div>
			<div class="alignright"><?php previous_comments_link(_g('Newer Comments') . ' &raquo;') ?></div>
			<div class="clear"></div>
		</div>
	<?php else : // this is displayed if there are no comments so far     ?>
		<?php if (comments_open()) : ?>
			<!-- If comments are open, but there are no comments. -->
		<?php else : // comments are closed     ?>
			<!-- If comments are closed. -->
			<div class="attention">
				<div class="icon"><?php _ge('Comments are closed.'); ?></div>
			</div>
		<?php endif; ?>
	<?php endif; ?>
		<!-- RESPOND -->
		<?php if (comments_open()) : ?>
		<div id="respond">
			<div class="comment-section">
				<div class="contentheading"><?php comment_form_title(_g('Leave a Reply'), _g('Leave a Reply to %s')); ?></div>
			</div>
			<div class="cancel-comment-reply">
				<small><?php cancel_comment_reply_link(); ?></small>
			</div>
			<?php if (get_option('comment_registration') && !is_user_logged_in()) : ?>
				<div class="attention">
					<div class="icon"><?php _ge('You must be'); ?> <a
							href="<?php echo wp_login_url(get_permalink()); ?>"><?php _ge('logged in'); ?></a> <?php _ge('to post a comment.'); ?>
					</div>
				</div>
			<?php else : ?>
				<!-- Begin Form -->
				<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">
					<?php if (is_user_logged_in()) : ?>
						<p><?php _ge('Logged in as'); ?> <a
								href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>.
							<a href="<?php echo wp_logout_url(get_permalink()); ?>"
							   title="<?php _ge('Log out of this account'); ?>"><?php _ge('Log out'); ?> &raquo;</a></p>
					<?php else : ?>
						<p>
							<input type="text" name="author" id="author"
							       onblur="if(this.value=='') this.value='<?php _ge('Name (Required)'); ?>';"
							       onfocus="if(this.value=='<?php _ge('Name (Required)'); ?>') this.value='';"
							       value="Name (Required)" size="22"
							       tabindex="1" <?php if ($req) echo "aria-required='true'"; ?> />
						</p>
						<p>
							<input type="text" name="email" id="email"
							       onblur="if(this.value=='') this.value='<?php _ge('E-mail (Required)'); ?>';"
							       onfocus="if(this.value=='<?php _ge('E-mail (Required)'); ?>') this.value='';"
							       value="E-mail (Required)" size="22"
							       tabindex="2" <?php if ($req) echo "aria-required='true'"; ?> />
						</p>
						<p>
							<input type="text" name="url" id="url"
							       onblur="if(this.value=='') this.value='<?php _ge('Website'); ?>';"
							       onfocus="if(this.value=='<?php _ge('Website'); ?>') this.value='';" value="Website" size="22"
							       tabindex="3"/>
						</p>
					<?php endif; ?>
					<!--<p><small><strong>XHTML:</strong> You can use these tags: <code><?php echo allowed_tags(); ?></code></small></p>-->
					<p style="margin: 0;">
						<textarea name="comment" id="comment" cols="100%" rows="10" tabindex="4"></textarea>
					</p><br/>

					<input class="button" name="submit" type="submit" id="submit" tabindex="5"
					       value="<?php _ge('Submit'); ?>"/>

					<div class="clear"></div>
					<?php comment_id_fields(); ?>
					<?php do_action('comment_form', $post->ID); ?>
				</form>
				<!-- End Form -->
			<?php endif; // If registration required and not logged in ?>
		</div>
	<?php endif; // if you delete this the sky will fall on your head    ?>
		<?php
		return ob_get_clean();
	}
}