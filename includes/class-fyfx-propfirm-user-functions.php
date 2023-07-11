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
    // Retrieve endpoint URL and API Key from plugin settings
    $endpoint_url = esc_attr(get_option('fyfx_your_propfirm_plugin_endpoint_url'));
    $api_key = esc_attr(get_option('fyfx_your_propfirm_plugin_api_key'));

    // Check if endpoint URL and API Key are provided
    if (empty($endpoint_url) || empty($api_key)) {
        return;
    }

    // Get the order object
    $order = wc_get_order($order_id);

    // Check if the order is paid
    if ($order->is_paid()) {
        // Get user details from the order
        $user_email = $order->get_billing_email();
        $user_firstname = $order->get_billing_first_name();
        $user_lastname = $order->get_billing_last_name();

        // Set additional user details
        $program_id = '649fd5c99c1c3b382bc5ad01';
        $mt_version = 'MT4';
        $address_line = $order->get_billing_address_1();
        $city = $order->get_billing_city();
        $zip_code = $order->get_billing_postcode();
        $country = $order->get_billing_country();
        $phone = $order->get_billing_phone();

        // Prepare the data to be sent in the API request
        $data = array(
            'email' => $user_email,
            'firstname' => $user_firstname,
            'lastname' => $user_lastname,
            'programId' => $program_id,
            'mtVersion' => $mt_version,
            'addressLine' => $address_line,
            'city' => $city,
            'zipCode' => $zip_code,
            'country' => $country,
            'phone' => $phone
        );

        // Send the API request
        $response = wp_remote_post(
            $endpoint_url,
            array(
                'headers' => array(
                	'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'X-Client-Key' =>  $api_key
                ),
                'body' => json_encode($data)
            )
        );

        // Check the API response code
        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code == 201) {
            // User created successfully
            wc_add_notice('User created successfully.', 'success');
        } elseif ($response_code == 400) {
            // Error creating user
            wc_add_notice('Error creating user. Please try again.', 'error');
        }
    }
}
add_action('woocommerce_thankyou', 'fyfx_your_propfirm_plugin_create_user');
