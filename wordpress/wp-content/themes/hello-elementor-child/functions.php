<?php
/**
 * Recommended way to include parent theme styles.
 * (Please see http://codex.wordpress.org/Child_Themes#How_to_Create_a_Child_Theme)
 */

add_action( 'wp_enqueue_scripts', 'hello_elementor_child_style' );
function hello_elementor_child_style() {
    // Charger le fichier CSS du thème parent
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );

    // Charger le fichier CSS du thème enfant après celui du parent
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'parent-style' ) );
    
    // Charger le CSS Slick Carousel depuis le CDN
    wp_enqueue_style( 'slick-carousel-css', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.css', array(), '1.9.0' );
    
    // Charger le fichier JavaScript Slick Carousel depuis le CDN
    wp_enqueue_script( 'slick-carousel-js', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js', array( 'jquery' ), '1.9.0', true );

    // Charger le fichier JavaScript de votre thème enfant après le script Slick
    wp_enqueue_script( 'child-main-js', get_stylesheet_directory_uri() . '/js/main.js', array( 'jquery', 'slick-carousel-js' ), null, true );
    
    // Charger un autre fichier CSS si nécessaire
    wp_enqueue_style( 'style-1', get_stylesheet_directory_uri() . '/css/style_1.css' );
}

add_shortcode( 'shortcode_slider', 'shortcode_slider' );
function shortcode_slider() {
    // Utilisation de Heredoc pour générer le HTML du slider
    $html = <<<HTML
    <div class="main">
        <div class="slider slider-for">
            <div><h3>1</h3></div>
            <div><h3>2</h3></div>
            <div><h3>3</h3></div>
            <div><h3>4</h3></div>
            <div><h3>5</h3></div>
        </div>
        <div class="slider slider-nav">
            <div><h3>1</h3></div>
            <div><h3>2</h3></div>
            <div><h3>3</h3></div>
            <div><h3>4</h3></div>
            <div><h3>5</h3></div>
        </div>
        <div class="action">
            <a href="#" data-slide="3">Go to slide 3</a>
            <a href="#" data-slide="4">Go to slide 4</a>
        </div>
    </div>
HTML;

    return $html;
}
?>
