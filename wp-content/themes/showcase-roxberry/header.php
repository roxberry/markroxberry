<!DOCTYPE HTML>
<html <?php language_attributes(); ?>>
	<head>
    <meta charset="utf-8">
		<title><?php bloginfo('name'); ?></title>
		<?php global $is_IE; ?>
		<?php if ($is_IE): ?>

	  <?php endif; ?>
	  <?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?><!-- comment-reply -->
		<?php wp_head(); ?>
	</head>
<body <?php body_class(); ?>>
	<header>
		<div id="header-wrap">
			<div id="header" class="row">
				<div id="title" class="grid_13 column">
                        <h1 class="title"><a href="<?php echo home_url(); ?>" title="<?php bloginfo('name'); ?>"><?php bloginfo('name'); ?></a></h1><p class="subtitle"><?php bloginfo ( 'description' ); ?></p>
                </div>
				<nav>
					<div id="nav" class="grid_14 column">
						<?php wp_nav_menu(array(
							'menu' => 'top-menu'

						)); ?>
					</div>
				</nav>
			</div>
		</div>
	</header>