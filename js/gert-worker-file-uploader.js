(function( $, wpmdb ) {

    var reader = {};
    var file = {};
    var slice_size = 1000 * 1024;

    $('#gert-file-upload').on('change', function() {
        var fileName = $('#file-name');
        var file = $('#gert-file-upload')[0].files[0].name;
        fileName.text(file);    
    });

    function start_upload( event ) {
        event.preventDefault();
        
        reader = new FileReader();
        file = document.querySelector( '#gert-file-upload' ).files[0];

        upload_file( 0 );
    }
    $( '#gert-file-upload-submit' ).on( 'click', start_upload );

    function upload_file( start ) {
        var next_slice = start + slice_size + 1;
        var blob = file.slice( start, next_slice );

        reader.onloadend = function( event ) {
            if ( event.target.readyState !== FileReader.DONE ) {
                return;
            }
            
            $.ajax( {
                url: ajaxurl,
                type: 'POST',
                dataType: 'json',
                cache: false,
                data: {
                    action: 'gert_upload_file',
                    file_data: event.target.result,
                    file: file.name,
                    file_type: file.type,
                    nonce: gert_vars.upload_file_nonce
                },
                error: function( jqXHR, textStatus, errorThrown ) {
                    console.log( jqXHR, textStatus, errorThrown );
                },
                success: function( data ) {
                    var size_done = start + slice_size;
                    var percent_done = Math.floor( ( size_done / file.size ) * 100 );                    
                    if ( next_slice < file.size ) {
                        $( '#gert-upload-progress' ).html( '<div class="progress"><div class="progress-bar" style="width:' + (percent_done + 3) + '%;"><span>'+ percent_done +'%</span><div class="progress-shadow"></div></div></div>'  );                        
                        upload_file( next_slice );                    
                    } else {                        
                        $( '#gert-upload-progress' ).html( '<div class="wrapper"> <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 98.5 98.5" enable-background="new 0 0 98.5 98.5" xml:space="preserve"> <path class="checkmark" fill="none" stroke-width="8" stroke-miterlimit="10" d="M81.7,17.8C73.5,9.3,62,4,49.2,4 C24.3,4,4,24.3,4,49.2s20.3,45.2,45.2,45.2s45.2-20.3,45.2-45.2c0-8.6-2.4-16.6-6.5-23.4l0,0L45.6,68.2L24.7,47.3"/></svg> </div><div><h3 class="gert-upload-complete-heading">Upload Complete!</div>' );
                    }
                }
            } );
        };

        reader.readAsDataURL( blob );
    }

})( jQuery );
