<?php
/**
 * Recommended way to include parent theme styles.
 * (Please see http://codex.wordpress.org/Child_Themes#How_to_Create_a_Child_Theme)
 *
 */  

add_action( 'wp_enqueue_scripts', 'twenty_twenty_five_enfant_style' );
		function twenty_twenty_five_enfant_style() {
			wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
			wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array('parent-style') );
						 
			// Charger le CSS et JavaScript de Slick Carousel depuis les fichiers locaux
			wp_enqueue_style('slick-carousel-css', get_stylesheet_directory_uri() . '/assets/css/slick.min.css', array(), '1.9.0');
				 
			// Charger le fichier JavaScript de Slick Carousel dans le footer
			wp_enqueue_script('slick-carousel-js', get_stylesheet_directory_uri() . '/assets/js/slick.min.js', array('jquery'), '1.9.0', true);
				 
			// Charger le fichier JavaScript personnalisé du thème enfant dans le footer
			wp_enqueue_script('child-main-js', get_stylesheet_directory_uri() . '/js/main.js', array('jquery', 'slick-carousel-js'), null, true);
				 
			// Charger un autre fichier CSS si nécessaire
			wp_enqueue_style('style-1', get_stylesheet_directory_uri() . '/css/style_1.css');
				
				}

/**
 * Your code goes below.
 */

