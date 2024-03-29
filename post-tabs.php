<?php
wp_enqueue_style('fep-style');
wp_enqueue_script('fep-script');
wp_enqueue_media();

$current_user = wp_get_current_user();
$status = isset($_GET['fep_type']) ? $_GET['fep_type'] : 'publish';
$paged = isset($_GET['fep_page']) ? $_GET['fep_page'] : 1;
$per_page = (isset($fep_misc['posts_per_page']) && is_numeric($fep_misc['posts_per_page'])) ? $fep_misc['posts_per_page'] : 10;
$author_posts = new WP_Query(array('posts_per_page' => $per_page, 'paged' => $paged, 'orderby' => 'DESC', 'author' => $current_user->ID, 'post_status' => $status));
$old_exist = ($paged * $per_page) < $author_posts->found_posts;
$new_exist = $paged > 1;
?>
<div id="fep-posts">
	<div id="fep-message"></div>
	<ul>
		<li><a <?php if ($status == 'publish'): ?>class="active"<?php endif; ?>
			   href="?fep_type=publish"><?php _e('Online', 'frontend-publishing'); ?></a></li>
		<li><a <?php if ($status == 'pending'): ?>class="active"<?php endif; ?>
			   href="?fep_type=pending"><?php _e('Pending Review', 'frontend-publishing'); ?></a></li>
		<li><a <?php if ($status == 'future'): ?>class="active"<?php endif; ?>
			   href="?fep_type=future"><?php _e('Scheduled', 'frontend-publishing'); ?></a></li>
		<li><a <?php if ($status == 'trash'): ?>class="active"<?php endif; ?>
			   href="?fep_type=trash"><?php _e('Rejected', 'frontend-publishing'); ?></a></li>
	</ul>
	<div id="fep-post-table-container">
		<?php if (!$author_posts->have_posts()): ?>
			<?php _e('Nothing found.', 'frontend-publishing'); ?>
		<?php else: ?>
			<p><?php printf(__('%s article(s).', 'frontend-publishing'), $author_posts->found_posts); ?></p>
		<?php endif; ?>
		<table>
			<?php
			while ($author_posts->have_posts()) : $author_posts->the_post();
				$postid = get_the_ID();
				?>
				<tr id="fep-row-<?= $postid ?>" class="fep-row">
					<td><?php the_title(); ?></td>
					<?php if ($status == 'publish'): ?>
						<td class="fep-fixed-td"><a href="<?php the_permalink(); ?>"
													title="<?php _e('View Post', 'frontend-publishing'); ?>"><?php _e('View', 'frontend-publishing'); ?></a>
						</td><?php endif; ?>
					<td class="fep-fixed-td"><a
							href="?fep_action=edit&fep_id=<?= $postid; ?><?= (isset($_SERVER['QUERY_STRING']) ? '&' . $_SERVER['QUERY_STRING'] : '') ?>"><?php _e('Edit', 'frontend-publishing'); ?></a>
					</td>
					<td class="post-delete fep-fixed-td"><img id="fep-loading-img-<?= $postid ?>"
															  class="fep-loading-img"
															  src="<?php echo plugins_url('static/img/ajax-loading.gif', dirname(__FILE__)); ?>"><a
							href="#"><?php _e('Delete', 'frontend-publishing'); ?></a><input type="hidden"
																							 class="post-id"
																							 value="<?= $postid ?>">
					</td>
				</tr>
			<?php endwhile; ?>
		</table>
		<?php wp_nonce_field('fepnonce_delete_action', 'fepnonce_delete'); ?>
		<div class="fep-nav">
			<?php if ($new_exist): ?>
				<a class="fep-nav-link fep-nav-link-left" href="?fep_type=<?= $status ?>&fep_page=<?= ($paged - 1) ?>">
					&#10094; <?php _e('Newer Posts', 'frontend-publishing'); ?></a>
			<?php endif; ?>
			<?php if ($old_exist): ?>
				<a class="fep-nav-link fep-nav-link-right"
				   href="?fep_type=<?= $status ?>&fep_page=<?= ($paged + 1) ?>"><?php _e('Older Posts', 'frontend-publishing'); ?>
					&#10095;</a>
			<?php endif; ?>
			<div style="clear:both;"></div>
		</div>
		<?php wp_reset_query();
		wp_reset_postdata(); ?>
	</div>
</div>