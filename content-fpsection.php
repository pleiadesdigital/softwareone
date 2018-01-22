<?php /* The template used for displaying page content in template-fpsectin.php */ ?>

<?php $featured_image = get_the_post_thumbnail_url(get_the_ID(), 'thumbnail'); ?>

<div class="fpsectioncover">
	<section id="" class="fpsection">
		<div id="post-<?php the_ID(); ?>" <?php post_class('col-full fpsectionwrap'); ?>>
			<div class="col-full">

				<?php the_title('<h2>', '</h2>'); ?>

				<?php the_content(); ?>

			</div>
		</div><!-- #post-## -->
	</section>
</div><!-- class="fpsectioncover" -->

