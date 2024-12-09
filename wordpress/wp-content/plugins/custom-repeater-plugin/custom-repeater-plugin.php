<?php
/*
Plugin Name: Custom Repeater Plugin
Description: A plugin that adds a custom repeater field to your post types.
Version: 1.0
Author: Laza
*/

class CustomRepeaterPlugin {
    public function __construct() {
        add_action('admin_menu', array($this, 'create_plugin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('admin_init', array($this, 'save_repeater_fields'));
        add_action('add_meta_boxes', array($this,'add_repeater_meta_box'));
        add_action('save_post', array($this,'save_repeater_fields_meta'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_front_assets'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_static_field_assets'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_included_field_assets'));
    }

    public function create_plugin_menu() {
        add_menu_page(
            'Custom Repeater Settings',
            'Custom Repeater Settings',
            'manage_options',
            'custom-repeater-settings',
            array($this, 'render_settings_page'),
            'dashicons-admin-settings',
            100
        );
    }

    public function render_settings_page() {
        $selected_post_types = get_option('custom_repeater_post_types', array());

        $post_types = get_post_types(array('public' => true), 'objects');

        ?>
        <div class="wrap">
            <h1>Choisir un type de Publication</h1>
            <form method="post" action="options.php">
                <?php settings_fields('custom_repeater_settings'); ?>
                <?php do_settings_sections('custom-repeater-settings'); ?>
                
                <div id="post-type-selects">
                    <?php if (!empty($selected_post_types)) : ?>
                        <?php foreach ($selected_post_types as $index => $post_type): ?>
                            <div class="post-type-select">
                                <label for="custom_repeater_post_types[<?php echo $index; ?>]">Type de Publication</label>
                                <select name="custom_repeater_post_types[<?php echo $index; ?>]">
                                    <?php foreach ($post_types as $type) : ?>
                                        <option value="<?php echo $type->name; ?>" <?php selected($post_type, $type->name); ?>>
                                            <?php echo $type->label; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="button" class="remove-select button">Supprimer</button>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <button type="button" id="add-post-type-select" class="button">Ajouter un type de publication</button>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    public function register_settings() {
        register_setting('custom_repeater_settings', 'custom_repeater_post_types');
    }

    public function render_repeater_page() {
        $fields = get_option('custom_repeater_fields', array());
        ?>
        <div class="wrap">
            <h1>Custom Repeater Gallerie Fields</h1>
            <form method="post" action="">
                <div id="repeater-fields">
                    <?php if ($fields) : ?>
                        <?php foreach ($fields as $field) : ?>
                            <div class="repeater-field">
                                <input type="text" name="custom_repeater_fields[]" value="<?php echo esc_attr($field); ?>" />
                                <button type="button" class="remove-field button">Supprimer</button>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <button type="button" id="add-field" class="button">Ajouter un filtre</button>
                <input type="submit" class="button button-primary" value="Save Fields" />
            </form>
        </div>
        <?php
    }

    public function enqueue_admin_assets() {
        wp_enqueue_media();
        wp_enqueue_script('custom-repeater-script', plugins_url('admin/repeater.js', __FILE__), array('jquery'), null, true);
        wp_enqueue_style('custom-repeater-style', plugins_url('admin/repeater.css', __FILE__));

        wp_enqueue_script('repeater-settings-script', plugins_url('admin/repeater-settings.js', __FILE__), array('jquery'), null, true);
        $post_types = get_post_types(array('public' => true), 'objects');
        wp_localize_script('repeater-settings-script', 'customRepeaterData', array(
            'postTypes' => $post_types,
        ));
        wp_localize_script('custom-repeater-script', 'ClosePath', array(
            'closeimage' => plugin_dir_url( __FILE__ ).'close.png',
        ));

        $args = array(
            'post_type' => 'coontenu-itineraire',
            'posts_per_page' => -1,
            'post_status' => 'publish',
        );
        $itineraries = get_posts($args);

        $itinerary_options = array();
        foreach ($itineraries as $itineraire) {
            $itinerary_options[] = array(
                'id' => $itineraire->ID,
                'title' => $itineraire->post_title,
            );
        }

        wp_localize_script('custom-repeater-script', 'itineraryData', array(
            'options' => $itinerary_options,
        ));
    }

    public function enqueue_static_field_assets() {
        $plugin_url = plugins_url('static-field/',__FILE__);
        
        wp_enqueue_script(
            'static-field-script',
            $plugin_url . 'static-field.js',
            array('jquery'),
            null,
            true
        );
        
        wp_enqueue_style(
            'static-field-style',
            $plugin_url . 'static-field.css'
        );

        wp_enqueue_editor();
    }

    public function enqueue_included_field_assets() {
        $plugin_url = plugins_url('included/',__FILE__);
        
        wp_enqueue_script(
            'included-field-script',
            $plugin_url . 'included.js',
            array('jquery'),
            null,
            true
        );
        
        /*wp_enqueue_style(
            'included-field-style',
            $plugin_url . 'included.css'
        );*/

    }

    public function enqueue_front_assets(){
        wp_enqueue_script('tabslick-script', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js', array('jquery'));
        wp_enqueue_style('tabslick-style', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.css');
        wp_enqueue_script('tabs-script', plugins_url('public/tabs.js', __FILE__), array('jquery'), null, true);
        wp_enqueue_style('custom-repeater-style', plugins_url('public/tabs.css', __FILE__));
    }

    public function save_repeater_fields() {
        if (isset($_POST['custom_repeater_fields'])) {
            update_option('custom_repeater_fields', array_map('sanitize_text_field', $_POST['custom_repeater_fields']));
        }
    }
    
    public function add_repeater_meta_box() {
        $selected_post_types = get_option('custom_repeater_post_types', array());

        foreach ($selected_post_types as $post_type) {
            add_meta_box(
                'repeater_fields',
                'Itinéraires',
                array($this, 'render_repeater_meta_box'),
                $post_type,
                'normal',
                'default'
            );
        }
    }

    public function render_repeater_meta_box($post) {
        $fields = get_post_meta($post->ID, 'custom_repeater_fields', true) ?: array();
        wp_nonce_field('save_repeater_fields', 'repeater_nonce');

        $args = array(
            'post_type' => 'coontenu-itineraire',
            'posts_per_page' => -1,
            'post_status' => 'publish',
        );        
        $itineraries = get_posts($args);
        ?>
        <div id="repeater-fields">
            <?php foreach ($fields as $index => $field): ?>
                <?php $chosen = ($field['chosen']) ? $field['chosen'] : '';?>
                <div class="repeater-field" style="margin-bottom: 20px;">
                    <div style="flex: 0 0 80%;width:80%;">
                        <!-- Jour -->
                        <div>
                            <div><label for="custom_repeater_fields[<?php echo $index; ?>][jour]">Jour</label></div>
                            <div><input type="text" name="custom_repeater_fields[<?php echo $index; ?>][jour]" value="<?php echo esc_attr($field['jour']); ?>" placeholder="Jour" style="margin-bottom: 10px;"/></div>
                        </div>
                        <!-- Titre -->
                        <div>
                            <div><label for="custom_repeater_fields[<?php echo $index; ?>][titre]">Titre</label></div>
                            <div><input type="text" name="custom_repeater_fields[<?php echo $index; ?>][titre]" value="<?php echo esc_attr($field['titre']); ?>" placeholder="Titre" style="margin-bottom: 10px;"/></div>
                        </div>

                        <!-- Galerie de photos -->
                        <div>
                            <div><label for="custom_repeater_fields[<?php echo $index; ?>][gallery]">Galerie de photos</label></div>
                            <div><input type="hidden" name="custom_repeater_fields[<?php echo $index; ?>][gallery]" value="<?php echo esc_attr(implode(',', $field['gallery'] ?? array())); ?>" class="gallery-urls" /></div>
                            <button type="button" class="button select-gallery">Ajouter une image à la galerie</button>
                            <div class="gallery-preview">
                                <?php if (!empty($field['gallery'])): ?>
                                    <?php foreach ($field['gallery'] as $image_url): ?>
                                        <div class="backoffice_image_item" style="position: relative;">
                                            <div style="position: absolute;top: 5px;right: 5px;height: 20px;display: flex;justify-content: flex-end;">
                                                <img class="remove_dis_image" src="<?php echo plugin_dir_url( __FILE__ ).'close.png';?>" style="width: 15px;height: 15px;object-fit:cover;cursor: pointer;">
                                                </div>
                                            <img class="the_image_backoffice_image_item" src="<?php echo esc_url($image_url); ?>" style="max-width: 202px; margin-right: 10px; height: 202px" />
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Champ WYSIWYG -->
                        <div>
                            <div><label for="custom_repeater_fields[<?php echo $index; ?>][description]">Description</label></div>
                            <?php 
                                $content = isset($field['description']) ? $field['description'] : '';
                                wp_editor($content, 'custom_repeater_fields_' . $index . '_description', array(
                                    'textarea_name' => 'custom_repeater_fields[' . $index . '][description]',
                                    'textarea_rows' => 4,
                                    'media_buttons' => true,
                                ));
                            ?>
                        </div>

                        <!-- Champ de sélection multiple -->
                        <div class="choix_des_content" style="display:flex;justify-content:start;align-items:center;gap:15px;">
                            <div style="flex:0 0 30%;gap:15px;">
                                <label>Contenus Itinéraires</label>
                                <select class="content_itineraire_choices" style="width: 100%;">
                                    <option value="">Choisir</option>
                                    <?php
                                        if(strpos($chosen , '|')){
                                            $existing_items = explode('|' , $chosen);
                                            foreach ($itineraries as $itin) {
                                                if (in_array($itin->ID, $existing_items)) {
                                                 ?>
                                                 <option value="<?php echo $itin->ID;?>" disabled="disabled"><?php echo get_the_title($itin->ID);?></option>
                                                 <?php
                                                }else{
                                                 ?>
                                                 <option value="<?php echo $itin->ID;?>"><?php echo get_the_title($itin->ID);?></option>
                                                 <?php
                                                }
                                            }
                                        }else{
                                            $existing_items = $chosen;
                                            foreach ($itineraries as $itin) {
                                                if ($itin->ID == $existing_items) {
                                                ?>
                                                 <option value="<?php echo $itin->ID;?>" disabled="disabled"><?php echo get_the_title($itin->ID);?></option>
                                                <?php
                                                }else{
                                                ?>
                                                 <option value="<?php echo $itin->ID;?>"><?php echo get_the_title($itin->ID);?></option>
                                                <?php
                                                }
                                            }
                                        }
                                    ?>
                                </select>
                            </div>
                            <div style="flex:0 0 60%;">
                                    <div>
                                        <label for="">Les contenus choisis</label>
                                    </div>
                                    <div class="fake_textarea" style="border:1px solid #8c8f94;width:100%;height:80px;background:#fff;display:flex;flex-wrap:wrap;justify-content:start;gap:5px;">
                                    <?php
                                    //var_dump($chosen);
                                        if(strpos($chosen , '|')){
                                            $chosen_items = explode('|' , $chosen);
                                            
                                            foreach ($chosen_items as $key => $value) {
                                                ?>   
                                                <div id="<?php echo $value;?>" class="fake_textarea_item" style="display:flex;height: fit-content;width: fit-content;background: yellow;justify-content: center;padding: 3px 7px;gap: 5px;align-items:start;">
                                                    <div style="flex: 0 0 72%;">
                                                    <?php echo get_the_title($value);?>
                                                    </div>
                                                    <div style="flex: 0 0 15%;height: 10px;align-content: center;align-items: center;justify-content: center;display: flex;gap: 5px;">
                                                        <img src="<?php echo plugin_dir_url( __FILE__ ).'close.png';?>" class="deselect_contenu" style="width: 10px;10px: inherit;object-fit:cover;cursor: pointer;">
                                                    </div>
                                                </div>
                                            <?php
                                                
                                            }
                                        }else{
                                            $chosen_items = $chosen;
                                            ?>
                                                <div id="<?php echo $chosen_items;?>" class="fake_textarea_item" style="display:flex;height: fit-content;width: fit-content;background: yellow;justify-content: center;padding: 3px 7px;gap: 5px;align-items:start;">
                                                    <div style="flex: 0 0 72%;">
                                                    <?php echo get_the_title($chosen_items);?>
                                                    </div>
                                                    <div style="flex: 0 0 15%;height: 10px;align-content: center;align-items: center;justify-content: center;display: flex;gap: 5px;">
                                                        <img src="<?php echo plugin_dir_url( __FILE__ ).'close.png';?>" class="deselect_contenu" style="width: 10px;10px: inherit;object-fit:cover;cursor: pointer;">
                                                    </div>
                                                </div>
                                            <?php
                                        }
                                    ?>  
                                    </div>
                                    <?php
                                        if ($chosen != '') {
                                            ?>
                                                <div>
                                                    <input class="chosen_itineraires" name="custom_repeater_fields[<?php echo $index; ?>][chosen]" value="<?php echo $chosen;?>" type="hidden">
                                                </div>
                                            <?php
                                        }else{
                                            ?>
                                                <div>
                                                    <input class="chosen_itineraires" name="custom_repeater_fields[<?php echo $index; ?>][chosen]" value="" type="hidden">
                                                </div>
                                            <?php
                                        }
                                    ?>
                            </div>
                        </div>
                    </div>
                    
                    <div style="flex: 0 0 10%;width:10%;">
                        <button type="button" class="remove-field button">Supprimer</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" id="add-field" class="button">Ajouter un itinéraire</button>
        <?php
    }



    public function save_repeater_fields_meta($post_id) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!isset($_POST['repeater_nonce']) || !wp_verify_nonce($_POST['repeater_nonce'], 'save_repeater_fields')) return;

        if (isset($_POST['custom_repeater_fields'])) {
            $fields = array_map(function($field) {
                return array(
                    'jour' => sanitize_text_field($field['jour']),
                    'titre' => sanitize_text_field($field['titre']),
                    'gallery' => !empty($field['gallery']) ? explode(',', $field['gallery']) : array(),
                    'description' => wp_kses_post($field['description']),
                    'chosen' => sanitize_text_field($field['chosen']),
                );
            }, $_POST['custom_repeater_fields']);
            
            update_post_meta($post_id, 'custom_repeater_fields', $fields);
        } else {
            delete_post_meta($post_id, 'custom_repeater_fields');
        }
    }


}

new CustomRepeaterPlugin();

require_once plugin_dir_path(__FILE__) . 'static-field/static-field.php';
require_once plugin_dir_path(__FILE__) . 'included/included.php';