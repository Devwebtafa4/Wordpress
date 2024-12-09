<?php

class CustomRepeaterIncluded {

    public function __construct() {
        add_action('add_meta_boxes', array($this, 'add_included_field_meta_box'));
        add_action('save_post', array($this, 'save_included_fields_meta'));
        //add_action('admin_enqueue_scripts', array($this, 'enqueue_included_field_assets'));
    }

    public function add_included_field_meta_box() {
        $selected_post_types = get_option('custom_repeater_post_types', array());
        foreach ($selected_post_types as $post_type) {
            add_meta_box(
                'included_field',
                'Thèmes :',
                array($this, 'render_included_field_meta_box'),
                $post_type,
                'normal',
                'default'
            );
        }
    }

    public function enqueue_included_field_assets() {
        wp_enqueue_media();
        wp_enqueue_script('included-script', plugins_url('included/included.js', __FILE__), array('jquery'), null, true);
        //wp_enqueue_style('included-style', plugins_url('included/included.css', __FILE__));
    }

    public function render_included_field_meta_box($post) {
        $fields = get_post_meta($post->ID, 'custom_included_fields', true);
        //var_dump($fields);
        wp_nonce_field('save_included_fields', 'included_field_nonce');
        ?>

        <div id="included-field-group">
            <?php if (!empty($fields)) : ?>
                <?php foreach ($fields as $index => $field) : ?>
                    <div class="included-field-item" style="display: flex;align-items:end;gap:15px;margin-bottom: 10px;">
                        <div style="flex: 0 0 80%;">
                            <div><label for="custom_included_fields[<?php echo $index; ?>][title]">Titre</label></div>
                            <div><input type="text" name="custom_included_fields[<?php echo $index; ?>][title]" value="<?php echo esc_attr($field['title']); ?>" style="width: 100%;" /></div>
                        </div>
                        <div style="flex: 0 0 15%;">
                            <button type="button" class="remove-included-field button">Supprimer</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <button type="button" id="add-included-field" class="button">Ajouter un élément</button>
        </div>

        <?php
    }


    public function save_included_fields_meta($post_id) {
        // Vérifier si c'est une autosave pour ne pas écraser les données involontairement
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

        // Vérifier que les champs ont été soumis
        if (isset($_POST['custom_included_fields']) && is_array($_POST['custom_included_fields'])) {
            // Nettoyer et traiter les champs soumis

            $fields = array_map(function($field) {
                return array(
                    'title' => sanitize_text_field($field['title']),
                );
            }, $_POST['custom_included_fields']);
            update_post_meta($post_id, 'custom_included_fields', $fields);
        } else {
            delete_post_meta($post_id, 'custom_included_fields');
        }
    }

}


new CustomRepeaterIncluded();