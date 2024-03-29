<?php
/**
 * @package WordPress
 * @subpackage P2
 */
if ( function_exists( 'instapost_render' ) ) {
	global $current_user; ?>

	<div id="instapost">
		<div class="avatar"><?php echo get_avatar( $current_user->user_email, 48 ) ?></div>

		<h2><?php p2_user_prompt(); ?></h2>

		<?php instapost_render() ?>
	</div><?php

} else {
	?>
	<script type="text/javascript">
	/* <![CDATA[ */
		jQuery(document).ready(function($) {
			jQuery('#post_format').val($('#post-types a.selected').attr('id'));
			$('#post-types a').click(function(e) {
				jQuery('.post-input').hide();
				$('#post-types a').removeClass('selected');
				jQuery(this).addClass('selected');
				if ($(this).attr('id') == 'post') {
					jQuery('#posttitle').val("<?php echo esc_js( __('Post Title', 'p2') ); ?>");
				} else {
					jQuery('#posttitle').val('');
				}
				jQuery('#postbox-type-' + $(this).attr('id')).show();
				jQuery('#post_format').val($(this).attr('id'));
				return false;
			});
		});
	/* ]]> */
	</script>

	<?php $post_format = isset( $_GET['p'] ) ? $_GET['p'] : 'status'; ?>
	<div id="postbox">
			<ul id="post-types">
				<li><a id="status"<?php if ( 'status' == $post_format ) : ?> class="selected"<?php endif; ?> href="<?php echo site_url( '?p=status' ); ?>" title="<?php esc_attr_e( 'Status Update', 'p2' ); ?>"><?php _e( 'Status Update', 'p2' ); ?></a></li>
				<li><a id="post"<?php if ( 'post' == $post_format || 'standard' == $post_format ) : ?> class="selected"<?php endif; ?> href="<?php echo site_url( '?p=post' ); ?>" title="<?php esc_attr_e( 'Blog Post', 'p2' ); ?>"><?php _e( 'Blog Post', 'p2' ); ?></a></li>
				<li><a id="quote"<?php if ( 'quote' == $post_format ) : ?> class="selected"<?php endif; ?> href="<?php echo site_url( '?p=quote' ); ?>" title="<?php esc_attr_e( 'Quote', 'p2' ); ?>"><?php _e( 'Quote', 'p2' ); ?></a></li>
				<li><a id="link"<?php if ( 'link' == $post_format ) : ?> class="selected"<?php endif; ?> href="<?php echo site_url( '?p=link' ); ?>" title="<?php esc_attr_e( 'Link', 'p2' ); ?>"><?php _e( 'Link', 'p2' ); ?></a></li>
			</ul>

			<div class="avatar">
				<?php echo get_avatar( get_current_user_id(), 48 ); ?>
			</div>

			<div class="inputarea">

				<form id="new_post" name="new_post" method="post" action="<?php echo site_url(); ?>/">
					<?php if ( 'status' == $post_format || empty( $post_format ) ) : ?>
					<label for="posttext">
						<?php p2_user_prompt(); ?>
					</label>
					<?php endif; ?>

					<div id="postbox-type-post" class="post-input <?php if ( 'post' == $post_format || 'standard' == $post_format ) echo ' selected'; ?>">
						<input type="text" name="posttitle" id="posttitle" tabindex="1" value=""
							onfocus="this.value=(this.value=='<?php echo esc_js( __( 'Post Title', 'p2' ) ); ?>') ? '' : this.value;"
							onblur="this.value=(this.value=='') ? '<?php echo esc_js( __( 'Post Title', 'p2' ) ); ?>' : this.value;" />
					</div>
					<?php if ( current_user_can( 'upload_files' ) ): ?>
					<div id="media-buttons" class="hide-if-no-js">
						<?php p2_media_buttons(); ?>
					</div>
					<?php endif; ?>
					<textarea class="expand70-200" name="posttext" id="posttext" tabindex="1" rows="4" cols="60"></textarea>
					<div id="postbox-type-quote" class="post-input <?php if ( 'quote' == $post_format ) echo " selected"; ?>">
						<label for="postcitation" class="invisible"><?php _e( 'Citation', 'p2' ); ?></label>
							<input id="postcitation" name="postcitation" type="text" tabindex="2"
								value="<?php esc_attr_e( 'Citation', 'p2' ); ?>"
								onfocus="this.value=(this.value=='<?php echo esc_js( __( 'Citation', 'p2' ) ); ?>') ? '' : this.value;"
								onblur="this.value=(this.value=='') ? '<?php echo esc_js( __( 'Citation', 'p2' ) ); ?>' : this.value;" />
					</div>
					<label class="post-error" for="posttext" id="posttext_error"></label>
					<div class="postrow">
						<input id="tags" name="tags" type="text" tabindex="2" autocomplete="off"
							value="<?php esc_attr_e( 'Tag it', 'p2' ); ?>"
							onfocus="this.value=(this.value=='<?php echo esc_js( __( 'Tag it', 'p2' ) ); ?>') ? '' : this.value;"
							onblur="this.value=(this.value=='') ? '<?php echo esc_js( __( 'Tag it', 'p2' ) ); ?>' : this.value;" />
						<input id="submit" type="submit" tabindex="3" value="<?php esc_attr_e( 'Post it', 'p2' ); ?>" />
					</div>
					<input type="hidden" name="post_format" id="post_format" value="<?php echo esc_attr( $post_format ); ?>" />
					<span class="progress" id="ajaxActivity">
						<img src="<?php echo str_replace( WP_CONTENT_DIR, content_url(), locate_template( array( 'i/indicator.gif' ) ) ); ?>"
							alt="<?php esc_attr_e( 'Loading...', 'p2' ); ?>" title="<?php esc_attr_e( 'Loading...', 'p2' ); ?>"/>
					</span>

					<?php do_action( 'p2_post_form' ); ?>

					<input type="hidden" name="action" value="post" />
					<?php wp_nonce_field( 'new-post' ); ?>
				</form>

			</div>

			<div class="clear"></div>

	</div> <!-- // postbox -->
<?php
}

