<?php

class Gert_File_Uploader {

    public function __construct() {
        register_activation_hook( GERT_PLUGIN_PATH . 'gert-file-uploader.php', array( $this, 'gert_add_custom_uploads_directory' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'gert_enqueue_scripts' ) );
        add_action( 'admin_menu', array( $this, 'gert_add_to_admin_menu' ) );
        add_action( 'wp_ajax_gert_upload_file', array( $this, 'gert_ajax_upload_file' ) );
    }

    public function gert_enqueue_scripts() {
        $src = plugins_url( '/js/gert-worker-file-uploader.js', GERT_PLUGIN_PATH . '/gert' );

        wp_enqueue_script( 'gert-file-uploader', $src, array( 'jquery' ), false, true );
        wp_localize_script( 'gert-file-uploader', 'gert_vars', array(
            'upload_file_nonce' => wp_create_nonce( 'gert-file-upload' ),
            )
        );
    }

    public function gert_add_custom_uploads_directory() {       
        $upload     = wp_upload_dir();
        $upload_dir = $upload['basedir'];
        $upload_dir = $upload_dir . '/gert';
        if ( ! is_dir( $upload_dir ) ) {
            wp_mkdir_p( $upload_dir );
        }
    }

    public function gert_add_to_admin_menu() {
        add_menu_page(
            'Gert - Upload Large File',
            'Gert',
            'manage_options',
            'gert',
            array( $this, 'gert_render_pluign_page' ),
            'dashicons-arrow-up-alt', 
            10
        );
    }

    public function gert_render_pluign_page() {
        ?>
        <form>
            <p id="gert-upload-progress">Please select a file and click "Upload" to continue.</p>
            <input id="gert-file-upload" type="file" name="gert_import_file" /><br>
            <input id="gert-file-upload-submit" class="button button-primary" type="submit" value="Upload" />
        </form>
        <?php           
    }

    public function gert_ajax_upload_file() {
        check_ajax_referer( 'gert-file-upload', 'nonce' );

        $wp_upload_dir = wp_upload_dir();
        $file_path     = trailingslashit( $wp_upload_dir['basedir'] ) . 'gert/' . $_POST['file'];
        $file_data     = $this->decode_chunk( $_POST['file_data'] );

        if ( false === $file_data ) {
            wp_send_json_error();
        }

        file_put_contents( $file_path, $file_data, FILE_APPEND );

        wp_send_json_success();
    }

    public function decode_chunk( $data ) {
        $data = explode( ';base64,', $data );

        if ( ! is_array( $data ) || ! isset( $data[1] ) ) {
            return false;
        }

        $data = base64_decode( $data[1] );
        if ( ! $data ) {
            return false;
        }

        return $data;
    }

}
new Gert_File_Uploader();