<?php
/**
 * Recommended way to include parent theme styles.
 * (Please see http://codex.wordpress.org/Child_Themes#How_to_Create_a_Child_Theme)
 */

add_action('wp_enqueue_scripts', 'twenty_twenty_four_child_style');
function twenty_twenty_four_child_style() {
    // Charger le style du thème parent
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');

    // Charger le style du thème enfant
    wp_enqueue_style('child-style', get_stylesheet_directory_uri() . '/style.css', array('parent-style'));

    // Charger le CSS et JavaScript de Slick Carousel depuis les fichiers locaux
    wp_enqueue_style('slick-carousel-css', get_stylesheet_directory_uri() . '/assets/css/slick.min.css', array(), '1.9.0');
    wp_enqueue_script('slick-carousel-js', get_stylesheet_directory_uri() . '/assets/js/slick.min.js', array('jquery'), '1.9.0', true);

    // Charger le fichier JavaScript personnalisé du thème enfant
    wp_enqueue_script('child-main-js', get_stylesheet_directory_uri() . '/js/main.js', array('jquery', 'slick-carousel-js'), null, true);

    // Charger un autre fichier CSS si nécessaire
    wp_enqueue_style('style-1', get_stylesheet_directory_uri() . '/css/style_1.css');
}

// Shortcode pour afficher le slider
add_shortcode('shortcode_slider', 'shortcode_slider');
function shortcode_slider() {
    $html = <<<HTML
    <div class="carousel-container">
        <div class="carousel">
            <div class="carousel-item">
                <img src="http://localhost/TEST/wordpress/wp-content/uploads/2024/12/Programme-Construction-dune-ecole-maternelle-4.jpg" alt="Image 1">
                <div class="carousel-caption">Slide text 1</div>
            </div>
            <div class="carousel-item">
                <img src="http://localhost/TEST/wordpress/wp-content/uploads/2024/12/Programme-Construction-dune-ecole-maternelle-3.jpg" alt="Image 2">
                <div class="carousel-caption">Slide text 2</div>
            </div>
            <div class="carousel-item">
                <img src="http://localhost/TEST/wordpress/wp-content/uploads/2024/12/Programme-Construction-dune-ecole-maternelle-2.jpg" alt="Image 3">
                <div class="carousel-caption">Slide text 3</div>
            </div>
        </div>
        <button class="prev-btn">&#10094;</button>
        <button class="next-btn">&#10095;</button>
    </div>
HTML;
    return $html;
}

// Fonction pour créer la table dans la base de données à l'activation du thème
function create_contact_form_table() {
    global $wpdb;

    // Nom de la table avec le préfixe de la base de données WordPress
    $table_name = $wpdb->prefix . 'formulaire_contact';
    $charset_collate = $wpdb->get_charset_collate();

    // Requête SQL pour créer la table
    $sql = "CREATE TABLE $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        nom varchar(255) NOT NULL,
        email varchar(255) NOT NULL,
        message text NOT NULL,
        date_submitted datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    // Inclure le fichier de mise à jour de la base de données de WordPress
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    // Utiliser dbDelta pour créer la table ou mettre à jour la structure
    dbDelta($sql);
}
// Appeler la fonction lors de l'activation du thème
register_activation_hook(__FILE__, 'create_contact_form_table');

// Shortcode pour le formulaire de contact
add_shortcode('formulaire_contact', 'funct_formulaire_contact');
function funct_formulaire_contact() {
    // Formulaire HTML
    $html = '<div class="contact-form-container">
                <h3>Contactez-nous</h3>
                <form id="contact-form" method="post" action="">
                    <div class="form-group">
                        <label for="name">Nom:</label>
                        <input type="text" id="name" name="name" required />
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required />
                    </div>
                    <div class="form-group">
                        <label for="message">Message:</label>
                        <textarea id="message" name="message" rows="4" required></textarea>
                    </div>
                    <div class="form-group">
                        <button type="submit" name="submit_contact_form" id="submit-contact-form">Envoyer</button>
                    </div>
                    ' . wp_nonce_field('submit_contact_form_nonce', 'contact_form_nonce', false, false) . '
                </form>
                <div id="response">
            
                </div>
            </div>';

    return $html;
}

// Fonction de traitement AJAX pour le formulaire de contact
function traiter_formulaire_contact_ajax() {
    // Vérifier le nonce pour la sécurité
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'submit_contact_form_nonce')) {
        wp_send_json_error(array('message' => 'Nonce invalide'));
    }

    // Vérifier si les champs sont bien remplis
    if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['message'])) {
        global $wpdb;

        // Sécuriser les données
        $nom = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);
        $message = sanitize_textarea_field($_POST['message']);

        // Validation de l'email
        if (!is_email($email)) {
            wp_send_json_error(array('message' => 'Adresse email invalide.'));
        }
        // Insérer les données dans la base de données
        $table_name = $wpdb->prefix . 'formulaire_contact';
        $wpdb->insert(
            $table_name,
            array(
                'nom' => $nom,
                'email' => $email,
                'message' => $message
            )
        );
    // Réponse en cas de succès
    // Inclure les données $_POST dans la réponse pour débogage
        wp_send_json_success(array(
            'message' => 'Merci pour votre message ! Nous vous répondrons sous peu.',
            'post_data' => print_r($_POST, true) // Ajout des données $_POST pour le débogage
        ));
    } else {
        wp_send_json_error(array('message' => 'Tous les champs sont requis.'));
    }
}
// Ajouter l'action AJAX pour les utilisateurs connectés et non connectés
add_action('wp_ajax_submit_contact_form', 'traiter_formulaire_contact_ajax');
add_action('wp_ajax_nopriv_submit_contact_form', 'traiter_formulaire_contact_ajax');

// Enregistrer le script JavaScript pour gérer l'AJAX
function enqueue_contact_form_script() {
    wp_enqueue_script('contact-form-ajax', get_stylesheet_directory_uri() . '/js/contact-form.js', array('jquery'), null, true);

    // Ajouter la variable AJAX pour permettre à JavaScript de faire des requêtes AJAX via admin-ajax.php
    wp_localize_script('contact-form-ajax', 'contact_form_ajax_obj', array(
        'ajax_url' => admin_url('admin-ajax.php'), // L'URL de l'admin AJAX
        'nonce' => wp_create_nonce('submit_contact_form_nonce') // Le nonce pour la sécurité
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_contact_form_script');

// Shortcode pour afficher les champs ACF
add_shortcode('acf_champ', function() {
    global $post;
    // Utilisation de get_fields pour récupérer tous les champs ACF du post
    $fields = get_fields($post->ID);

    if ($fields) {
        $output = '';
        foreach ($fields as $key => $field_value) {
            $output .= <<<HTML
            <div class="acf-field">
                <h3>{$key}</h3>
                <p>{$field_value}</p>
            </div>
HTML;        
        }
        return $output;
    }
});
add_shortcode('test', function() {
  global $post;
        $id_testess = $results[0]->ID;
      
        $args = array(
            'post_type'      => 'services',
            // 'post__in'=>[$id_testess],
            'posts_per_page' => -1,
        );
        $the_query = new WP_Query( $args );
        $value = $the_query->posts;
        foreach($value as $key => $val){
            $affich= $val->ID;

             if($affich == 22){
                $post_detail =get_post($affich);

                // Exemple d'affichage des informations spécifiques du post
                echo '<p style="color:red">' . esc_html($post_detail->post_title) . '</p>';
                echo '<p><a href="' . esc_url($post_detail->guid) . '">lien 1 vers Services</a></p>';
             }
             elseif($affich == 15){
                $post_detail =get_post($affich);
    
                // Exemple d'affichage des informations spécifiques du post
                echo '<p style="color:red">' . esc_html($post_detail->post_title) . '</p>';
                echo '<p><a href="' . esc_url($post_detail->guid) . '">lien 2 vers Services </a></p>';
             }
        }
        wp_reset_postdata();
});

add_shortcode('recuperation_media', function(){
    $args = array(
        'post_type'      => 'attachment',
        'posts_per_page' => 10, // Limite le nombre de résultats
        'post_status'    => 'inherit', // Les médias ont un statut 'inherit'
    ); 
    $query = new WP_Query($args);
   
    if ($query->have_posts()) :
        while ($query->have_posts()) : $query->the_post();
            $attachment_id = get_the_ID();
            $attachment_url = wp_get_attachment_url($attachment_id);
            $attachment_title = get_the_title();
            $attachment_date = get_the_date();
    
            $html .= <<<HTML
            <section class="gallery-item">
                <div id="images_aff">
                    <img src="{$attachment_url}" alt="{$attachment_title}" />
                </div>
            </section>
    HTML;
 
        endwhile;
    else :
        echo 'No media found.';
    endif;
    
    // Reset post data
    wp_reset_postdata();
    return $html;
});

add_shortcode('filtrer_nos_publication', function() {
    $args = array(
        'post_type'      => 'services', 
        'posts_per_page' => -1,      
    );
    $query = new WP_Query($args);

    $values = $query->posts;
    foreach($values as $key=>$value){
       $affichage = $value->ID;

       if($affichage == 22){
        $post_detail = get_post($affichage);
      
        // Exemple d'affichage des informations spécifiques du post
        echo '<p style="color:green">' . esc_html($post_detail->post_title) . '</p>';
        echo '<p><a href="' . esc_url($post_detail -> guid) . '"> lien 2 vers Services </a></p>';
       }
    }
    return $output;
});



// // Enregistrement du shortcode et des actions pour la gestion de la connexion
// function register_login_form_shortcode() {
   
//     add_action('admin_post_action_login', 'handle_custom_login');
//     add_action('admin_post_nopriv_action_login', 'handle_custom_login');
// }
// add_action('init', 'register_login_form_shortcode');

// // Traitement de la soumission du formulaire de connexion
// function handle_custom_login() {
//     // Vérification du nonce pour la sécurité
//     if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'custom_login_nonce')) {
//         wp_redirect(home_url('/register')); // Redirection en cas d'erreur de nonce
//         exit;
//     }

//     // Vérification que les champs sont remplis
//     if (!isset($_POST['txtUserName']) || !isset($_POST['password'])) {
//         wp_redirect(home_url('/register')); // Redirection en cas d'erreur de champs manquants
//         exit;
//     }

//     // Préparer les données de connexion
//     $credentials = [
//         'user_login'    => sanitize_text_field($_POST['txtUserName']),
//         'user_password' => $_POST['password'],
//         'remember'      => true,
//     ];

//     // Essayer de connecter l'utilisateur
//     $user = wp_signon($credentials, is_ssl());

//     if (is_wp_error($user)) {
//         // En cas d'erreur, rediriger avec un message d'erreur
//         wp_redirect(home_url('/register')); // Vous pouvez rediriger vers une page d'erreur spécifique
//         exit;
//     }

//     // Connexion réussie, rediriger vers l'accueil ou une page spécifique
//     wp_redirect(home_url()); // Vous pouvez rediriger vers une page spécifique
//     exit;
// }

// // Affichage du formulaire de connexion
add_shortcode('forme_compte', function() {
    // Générer un nonce pour la sécurité
    // $nonce = wp_nonce_field('custom_login_nonce', '_wpnonce', true, false);

    // Formulaire de connexion
    $HTML = '
        <div id="forme_compte">
            <form method="POST" action="' . esc_url(admin_url('admin-post.php')) . '">
                ' . $nonce . '
                <input type="hidden" name="action" value="action_login">
                <div class="input-group">
                    <label for="txtUserName">Identifiant ou e-mail<span>*</span></label>
                    <input type="text" class="form-control" name="txtUserName" required/>
                </div>
                <div class="input-group">
                    <label for="txtPassword">Mot de passe<span>*</span></label>
                    <div class="ligne1">
                        <input type="password" id="txtPassword" class="form-control" name="password" required>
                        <button type="button" id="btnToggle" class="toggle"><i id="eyeIcon" class="fa fa-eye"></i></button>
                    </div>
                </div>
                <div class="boutton">
                    <button class="btn btn-lg btn-primary btn-block" type="submit">Se connecter</button>
                </div>
            </form>
        </div>';

    return $HTML;
});


