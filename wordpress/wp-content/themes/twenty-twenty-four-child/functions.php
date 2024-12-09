<?php
/**
 * Recommended way to include parent theme styles.
 * (Please see http://codex.wordpress.org/Child_Themes#How_to_Create_a_Child_Theme)
 *
 */  

add_action( 'wp_enqueue_scripts', 'twenty_twenty_four_child_style' );
function twenty_twenty_four_child_style() {
    // Charger le style du thème parent
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );

    // Charger le style du thème enfant
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'parent-style' ) );

    // Charger le CSS et JavaScript de Slick Carousel depuis les fichiers locaux
    wp_enqueue_style( 'slick-carousel-css', get_stylesheet_directory_uri() . '/assets/css/slick.min.css', array(), '1.9.0' );
    wp_enqueue_script( 'slick-carousel-js', get_stylesheet_directory_uri() . '/assets/js/slick.min.js', array( 'jquery' ), '1.9.0', true );

    // Charger le fichier JavaScript personnalisé du thème enfant
    wp_enqueue_script( 'child-main-js', get_stylesheet_directory_uri() . '/js/main.js', array( 'jquery', 'slick-carousel-js' ), null, true );

    // Charger un autre fichier CSS si nécessaire
    wp_enqueue_style( 'style-1', get_stylesheet_directory_uri() . '/css/style_1.css' );
}

// Shortcode pour afficher le slider
add_shortcode( 'shortcode_slider', 'shortcode_slider' );
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
register_activation_hook( __FILE__, 'create_contact_form_table' );


add_shortcode( 'formulaire_contact', 'funct_formulaire_contact' );

function funct_formulaire_contact() {
    // Traitement du formulaire
    if (isset($_POST['submit_contact_form'])) {
        traiter_formulaire_contact();
    }

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
					' . wp_nonce_field( 'submit_contact_form_nonce', 'contact_form_nonce' ) . '
				</form>
				<div id="response"></div>
			</div>';
             

    return $html;
}


function traiter_formulaire_contact() {
    global $wpdb;

    // Vérifier le nonce pour la sécurité
    if (!isset($_POST['contact_form_nonce']) || !wp_verify_nonce($_POST['contact_form_nonce'], 'submit_contact_form_nonce')) {
        echo '<p>Erreur de sécurité, le formulaire n\'a pas été soumis correctement.</p>';
        return;
    }

    // Sécuriser les données soumises
    $nom = sanitize_text_field($_POST['name']);
    $email = sanitize_email($_POST['email']);
    $message = sanitize_textarea_field($_POST['message']);

    // Validation des données
    if (!is_email($email)) {
        echo '<p>Adresse email invalide.</p>';
        return;
    }

    // Insérer les données dans la base de données
    $table_name = $wpdb->prefix . 'formulaire_contact';

    // Insérer dans la table
    $wpdb->insert(
        $table_name,
        array(
            'nom' => $nom,
            'email' => $email,
            'message' => $message
        )
    );

    // Affichage d'un message de succès
    echo '<p>Merci pour votre message ! Nous vous répondrons sous peu.</p>';

    // Ne pas rediriger (retourner à la même page)
    // Utilisation de wp_redirect pour forcer la non-redirection
    exit;
}

function enqueue_contact_form_script() {
    wp_enqueue_script('contact-form-ajax', get_stylesheet_directory_uri() . '/js/contact-form.js', array('jquery'), null, true);

    // Ajouter la variable AJAX pour permettre à JavaScript de faire des requêtes AJAX via admin-ajax.php
    wp_localize_script('contact-form-ajax', 'contact_form_ajax_obj', array(
        'ajax_url' => admin_url('admin-ajax.php'), // L'URL de l'admin AJAX
        'nonce' => wp_create_nonce('submit_contact_form_nonce') // Le nonce pour la sécurité
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_contact_form_script');

// 3. Fonction qui traite l'envoi du formulaire avec AJAX
function traiter_formulaire_contact_ajax() {
    // Vérifier le nonce pour la sécurité
    if ( !isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'submit_contact_form_nonce') ) {
        echo 'Nonce invalide';
        die();
    }

    // Vérifier si les champs sont bien remplis
    if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['message'])) {
        global $wpdb;

		echo '<pre>';
		echo var_dump ($_POST);
		echo '</pre>';
        // Sécuriser les données
        $nom = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);
        $message = sanitize_textarea_field($_POST['message']);

        // Validation de l'email
        if (!is_email($email)) {
            echo 'Adresse email invalide.';
            die();
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
        echo 'Merci pour votre message ! Nous vous répondrons sous peu.';
    } else {
        echo 'Tous les champs sont requis.';
    }
    // Terminer l'exécution pour retourner la réponse à AJAX
    die();
}

// Ajouter l'action AJAX pour les utilisateurs connectés et non connectés
add_action('wp_ajax_submit_contact_form', 'traiter_formulaire_contact_ajax');
add_action('wp_ajax_nopriv_submit_contact_form', 'traiter_formulaire_contact_ajax');