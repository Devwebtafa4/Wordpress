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
    wp_enqueue_style( 'slick-carousel-css', '/cdnjs/css/slick.min.js', array(), '1.9.0' );
    
    // Charger le fichier JavaScript Slick Carousel depuis le CDN
    wp_enqueue_script( 'slick-carousel-js', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js', array( 'jquery' ), '1.9.0', true );

    // Charger le fichier JavaScript de votre thème enfant après le script Slick
    wp_enqueue_script( 'child-main-js', get_stylesheet_directory_uri() . '/js/main.js', array( 'jquery', 'slick-carousel-js' ), null, true );
    
    // Charger un autre fichier CSS si nécessaire
    wp_enqueue_style( 'style-1', get_stylesheet_directory_uri() . '/css/style_1.css' );
}

add_shortcode( 'shortcode_slider', 'shortcode_slider' );
function shortcode_slider() {
    $html = <<<HTML
    <div class="carousel-container">
      <div class="carousel">
        <div class="carousel-item">
          <img src="http://localhost/TEST/wordpress/wp-content/uploads/2024/12/Programme-Construction-dune-ecole-maternelle-4.jpg" alt="Image 1">
          <div class="carousel-caption">Text for Image 1</div>
        </div>
        <div class="carousel-item">
          <img src="http://localhost/TEST/wordpress/wp-content/uploads/2024/12/Programme-Construction-dune-ecole-maternelle-3.jpg" alt="Image 2">
          <div class="carousel-caption">Text for Image 2</div>
        </div>
        <div class="carousel-item">
          <img src="http://localhost/TEST/wordpress/wp-content/uploads/2024/12/Programme-Construction-dune-ecole-maternelle-2.jpg" alt="Image 3">
          <div class="carousel-caption">Text for Image 3</div>
        </div>
      </div>
      <button class="prev-btn">&#10094;</button>
      <button class="next-btn">&#10095;</button>
    </div>
HTML;

    return $html;
}

add_shortcode('teste_recuperation_acf', 'recuperations_acf');
function recuperations_acf() {
    global $post;

    // Récupérer l'ID du post actuel
    $id_pos = $post->ID;

    // Récupérer les données du Repeater (nom du champ : 'services')
    $data = get_field('services', $id_pos); // 'services' est le nom du champ Repeater

    // Débogage : afficher les données pour voir ce qui est récupéré
    echo '<pre>';
    var_dump($data); 
    echo '</pre>';

    // Si le Repeater contient des données
    if ($data) {
        foreach ($data as $item) {
            // Supposons que chaque item du Repeater ait les sous-champs 'titre', 'description' et 'image'
            echo '<div class="service-item">';
            echo '<h3>' . esc_html($item['titre']) . '</h3>'; // Récupérer le sous-champ 'titre'
            echo '<p>' . esc_html($item['description']) . '</p>'; // Récupérer le sous-champ 'description'
            if (!empty($item['image'])) {
                echo '<img src="' . esc_url($item['image']['url']) . '" alt="' . esc_attr($item['image']['alt']) . '" />'; // Récupérer l'image
            }
            echo '</div>';
        }
    } else {
        echo 'Aucune donnée trouvée dans le champ Repeater personnalisé.';
    }
}

