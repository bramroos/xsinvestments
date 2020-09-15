<?php

/**
 * User Roles Profile Edit Part
 *
 * @package bbPress
 * @subpackage Dante
 */

?>

<div>
	<label for="role"><?php _e( 'Blog Role', 'dante' ) ?></label>

	<?php bbp_edit_user_blog_role(); ?>

</div>

<div>
	<label for="forum-role"><?php _e( 'Forum Role', 'dante' ) ?></label>

	<?php bbp_edit_user_forums_role(); ?>

</div>
