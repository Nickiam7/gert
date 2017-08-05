<?php

class Gert_File_Uploader {
    public function __construct() {
        add_action( 'admin_enqueue_scripts', array( $this, 'gert_enqueue_scripts' ) );
        add_action( 'admin_menu', array( $this, 'gert_add_to_admin_menu' ) );
        register_activation_hook( GERT_PLUGIN_PATH . 'gert-file-uploader.php', array( $this, 'gert_add_custom_uploads_directory' ) );
    }

    public function gert_enqueue_scripts() {
        $src = GERT_PLUGIN_PATH . 'js/gert-worker-file-uploader.js';
        
        wp_enqueue_script( 'gert-worker-js', $src, array( 'jquery' ), false, true );
        wp_localize_script( 'gert-file-uploader', 'gert_vars', array(
            'upload_file_nonce' => wp_create_nonce( 'gert-file-upload' ),
            )
        );
    }

    function gert_add_custom_uploads_directory() {       
        $upload = wp_upload_dir();
        $upload_dir = $upload['basedir'];
        $upload_dir = $upload_dir . '/gert';
        if ( ! is_dir( $upload_dir ) ) {
           wp_mkdir_p( $upload_dir );
        }
    }
    
    public function gert_add_to_admin_menu() {
        add_menu_page(
            'Gert! Upload Large Files easily',
            'Gert', 
            'manage_options',
            'gert',
            array( $this, 'gert_render_plugin_page' ),
            'dashicons-arrow-up-alt', 
            10
        );
    }

    public function gert_render_plugin_page() { 
        ?>
        <form>
            <p id="gert-upload-progress">Hey There Gert!</p>
            <input id="gert-file-upload" type="file" name="gert_import_file" /><br><br>
            <input id="gert-file-upload-submit" class="button button-primary" type="submit" value="Upload" />
        </form>
        <?php
    }
}

new Gert_File_Uploader();