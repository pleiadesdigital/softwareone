<?php /* The template used for displaying page content in template-fpsectin.php */ ?>

<?php
$featured_image = get_the_post_thumbnail_url(get_the_ID(), 'thumbnail');
?>

<div id="post-<?php the_ID(); ?>" <?php post_class('fpsection'); ?>>
	<div class="col-full">

		<?php the_title('<h1>', '</h1>'); ?>

	</div>
</div><!-- #post-## -->

