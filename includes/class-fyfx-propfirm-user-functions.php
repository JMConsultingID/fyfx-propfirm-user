<?php
// Add plugin settings page
function fyfx_your_propfirm_plugin_settings_page() {
    add_options_page(
        'FYFX Your Propfirm User Plugin',
        'FYFX Your Propfirm',
        'manage_options',
        'fyfx_your_propfirm_plugin',
        'fyfx_your_propfirm_plugin_settings_page_content'
    );
}
add_action('admin_menu', 'fyfx_your_propfirm_plugin_settings_page');

// Render settings page content
function fyfx_your_propfirm_plugin_settings_page_content() {
    ?>
    <div class="wrap">
        <h2>FYFX Your Propfirm User Plugin Settings</h2>
        <form method="post" action="options.php">
            <?php
                settings_fields('fyfx_your_propfirm_plugin_settings');
                do_settings_sections('fyfx_your_propfirm_plugin_settings');
                submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Add plugin settings fields
function fyfx_your_propfirm_plugin_settings_fields() {
    add_settings_section(
        'fyfx_your_propfirm_plugin_general',
        'General Settings',
        'fyfx_your_propfirm_plugin_general_section_callback',
        'fyfx_your_propfirm_plugin_settings'
    );

    add_settings_field(
        'fyfx_your_propfirm_plugin_endpoint_url',
        'Endpoint URL',
        'fyfx_your_propfirm_plugin_endpoint_url_callback',
        'fyfx_your_propfirm_plugin_settings',
        'fyfx_your_propfirm_plugin_general'
    );

    add_settings_field(
        'fyfx_your_propfirm_plugin_api_key',
        'API Key',
        'fyfx_your_propfirm_plugin_api_key_callback',
        'fyfx_your_propfirm_plugin_settings',
        'fyfx_your_propfirm_plugin_general'
    );

    register_setting(
        'fyfx_your_propfirm_plugin_settings',
        'fyfx_your_propfirm_plugin_endpoint_url'
    );

    register_setting(
        'fyfx_your_propfirm_plugin_settings',
        'fyfx_your_propfirm_plugin_api_key'
    );
}
add_action('admin_init', 'fyfx_your_propfirm_plugin_settings_fields');

// Render endpoint URL field
function fyfx_your_propfirm_plugin_endpoint_url_callback() {
    $endpoint_url = esc_attr(get_option('fyfx_your_propfirm_plugin_endpoint_url'));
    echo '<input type="text" name="fyfx_your_propfirm_plugin_endpoint_url" value="' . $endpoint_url . '" style="width: 400px;" />';
}

// Render API Key field
function fyfx_your_propfirm_plugin_api_key_callback() {
    $api_key = esc_attr(get_option('fyfx_your_propfirm_plugin_api_key'));
    echo '<input type="text" name="fyfx_your_propfirm_plugin_api_key" value="' . $api_key . '" style="width: 400px;" />';
}

// Render general settings section callback
function fyfx_your_propfirm_plugin_general_section_callback() {
    echo 'Configure the general settings for the FYFX Your Propfirm User Plugin.';
}

// Create user via API when successful payment is made
function fyfx_your_propfirm_plugin_create_user($order_id) {
    // Get the order object
    $order = wc_get_order($order_id);

    // Memeriksa apakah pembayaran telah selesai
    if ($order->is_paid()) {
        $user_email = $order->get_billing_email();
        $user_first_name = $order->get_billing_first_name();
        $user_last_name = $order->get_billing_last_name();

        // Set additional user details
        $program_id = '649fd5c99c1c3b382bc5ad01';
        $mt_version = 'MT4';
        $user_address = $order->get_billing_address_1();
        $user_city = $order->get_billing_city();
        $user_zip_code = $order->get_billing_postcode();
        $user_country = $order->get_billing_country();
        $user_phone = $order->get_billing_phone();

        // Menyiapkan data untuk dikirim ke API
        $api_data = array(
            'email' => $user_email,
            'firstname' => $user_first_name,
            'lastname' => $user_last_name,
            'programId' => $program_id,
            'mtVersion' => $mt_version,
            'addressLine' => $user_address,
            'city' => $user_city,
            'zipCode' => $user_zip_code,
            'country' => $user_country,
            'phone' => $user_phone
        );

        // Mengirim data ke API menggunakan cURL
        $api_url = 'https://bqsyp740n4.execute-api.ap-southeast-1.amazonaws.com/client/v1/users';
        $headers = array(
            'Accept: application/json',
            'Content-Type: application/json',
            'X-Client-Key: 18c98a659a174bd68c6380751ff821ac686b0f6dcba14e2497a01702d7f0584d'
        );

        $ch = curl_init($api_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($api_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, true); // Mengambil header respons        
        $response = curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Mendapatkan kode stat
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE); // Mendapatkan ukuran header
        curl_close($ch);

        // Menggunakan respons dari API jika diperlukan
        $api_response = substr($response, $header_size);

        if ($http_status == 201) {
            // Jika pengguna berhasil dibuat (kode respons: 201)
            wc_add_notice('User created successfully.' . $api_response, 'success');
        } elseif ($http_status == 400) {
            // Jika terjadi kesalahan saat membuat pengguna (kode respons: 400)
            $error_message = isset($api_response['error']) ? $api_response['errors'] : 'An error occurred while creating the user A.';
            wp_die($error_message, 'Error A', array('response' => $http_status));
        } elseif ($http_status == 409) {
            // Jika terjadi kesalahan saat membuat pengguna (kode respons: 400)
            $error_message = isset($api_response['error']) ? $api_response['errors'] : 'An error occurred while creating the user B.';
            wp_die($error_message, 'Error B', array('response' => $http_status));
        } elseif ($http_status == 500) {
            // Jika terjadi kesalahan saat membuat pengguna (kode respons: 400)
            $error_message = isset($api_response['error']) ? $api_response['errors'] : 'An error occurred while creating the user C.';
            wp_die($error_message, 'Error C', array('response' => $http_status));
        } else {
            // Menampilkan pemberitahuan umum jika kode respons tidak dikenali
            wp_die($error_message, 'Error D', array('response' => $http_status));
        }

         // Menambahkan header Access-Control-Expose-Headers untuk mengizinkan akses ke header respons
        header('Access-Control-Expose-Headers: X-Response');

        // Menampilkan respons API dalam header X-Response
        header('X-Response: ' . $api_response);
    }
}

add_action('woocommerce_payment_complete', 'fyfx_your_propfirm_plugin_create_user');

// Menampilkan pemberitahuan pada halaman "Thank You"
function display_order_notices() {
    wc_print_notices();
}
add_action('woocommerce_thankyou', 'display_order_notices');
