<?php
class StaticFieldPlugin {
    public function __construct() {
        add_action('add_meta_boxes', array($this, 'add_static_field_meta_box'));
        add_action('save_post', array($this, 'save_static_fields_meta'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_static_field_assets'));
    }

    public function add_static_field_meta_box() {
        $selected_post_types = get_option('custom_repeater_post_types', array()); // Utilisation des mêmes types de posts
        foreach ($selected_post_types as $post_type) {
            add_meta_box(
                'static_field',
                'Ajouter une description courte',
                array($this, 'render_static_field_meta_box'),
                $post_type,
                'normal',
                'default'
            );
        }
    }

    public function enqueue_static_field_assets() {
        wp_enqueue_media();
        wp_enqueue_script('static-field-script', plugins_url('static-field/static-field.js', __FILE__), array('jquery'), null, true);
        wp_enqueue_style('static-field-style', plugins_url('static-field/static-field.css', __FILE__));
    }

    public function render_static_field_meta_box($post) {
        $fields = get_post_meta($post->ID, 'custom_short_desc_fields', true);
        wp_nonce_field('save_static_fields', 'static_field_nonce');
        ?>

        <div id="static-field-group">
            <?php if (!empty($fields)) : ?>
                <div class="static-field-item">
                    <div>
                    <div><label for="custom_short_desc_fields[title]">Titre de la description</label></div>
                    <div><input type="text" name="custom_short_desc_fields[title]" value="<?php echo esc_attr($fields['title']); ?>" style="width: 100%;" /></div>
                    </div>

                    <div>
                    <div><label for="custom_short_desc_fields[gallery]">Galerie photos</label></div>
                    <div><input type="hidden" name="custom_short_desc_fields[gallery]" value="<?php echo esc_attr(implode(',', $fields['gallery'] ?? array())); ?>" class="static-field-gallery-urls" /></div>
                    <div><button type="button" class="button select-static-gallery">Ajouter une image à la galerie</button></div>
                    <div class="gallery-preview">
                        <?php if (!empty($fields['gallery'])): ?>
                            <?php foreach ($fields['gallery'] as $image_url): ?>
                                <div class="backoffice_image_item" style="position: relative;">
                                    <div style="position: absolute;top: 5px;right: 5px;height: 20px;display: flex;justify-content: flex-end;"><img src="<?php echo plugin_dir_url( __FILE__ ).'close.png';?>" class="remove_dis_static_image" style="width: 15px;height: 15px;object-fit:cover;cursor: pointer;">
                                    </div>
                                    <img class="the_image_backoffice_image_item" src="<?php echo esc_url($image_url); ?>" style="max-width: 202px; margin-right: 10px; height: 202px;" />
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    </div>

                    <div><label for="custom_short_desc_fields[description]">Contenu de la description</label></div>
                    <?php
                        $content = !empty($fields['description']) ? $fields['description'] : '';
                        wp_editor($content, 'custom_short_desc_fields_description', array(
                            'textarea_name' => 'custom_short_desc_fields[description]',
                            'media_buttons' => true,
                            'textarea_rows' => 5,
                        ));
                    ?>
                </div>
            <?php else : ?>
                <button type="button" id="add-short-description" class="button">Ajouter une description courte</button>
            <?php endif; ?>
        </div>

        <?php
    }

    public function save_static_fields_meta($post_id) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!isset($_POST['static_field_nonce']) || !wp_verify_nonce($_POST['static_field_nonce'], 'save_static_fields')) return;

        if (isset($_POST['custom_short_desc_fields'])) {
            $fields = array(
                'title' => sanitize_text_field($_POST['custom_short_desc_fields']['title']),
                'gallery' => !empty($_POST['custom_short_desc_fields']['gallery']) ? explode(',', $_POST['custom_short_desc_fields']['gallery']) : array(),
                'description' => wp_kses_post($_POST['custom_short_desc_fields']['description']),
            );
            update_post_meta($post_id, 'custom_short_desc_fields', $fields);
        } else {
            delete_post_meta($post_id, 'custom_short_desc_fields');
        }
    }
}

new StaticFieldPlugin();


