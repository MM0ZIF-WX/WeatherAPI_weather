<?php
/*
Plugin Name: WeatherAPI Weather
Description: Displays weather warnings and today's weather for a user-specified location using WeatherAPI.
Version: 1.0
Author: Marcus Hazel-McGown - MM0ZIF - https://mm0zif.radio
License: GPL2
*/

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Register admin settings page
function wapi_register_settings() {
    add_options_page(
        'WeatherAPI Weather Settings',
        'WeatherAPI Weather',
        'manage_options',
        'weatherapi-weather',
        'wapi_settings_page'
    );
}
add_action('admin_menu', 'wapi_register_settings');

// Settings page content
function wapi_settings_page() {
    ?>
    <div class="wrap">
        <h1>WeatherAPI Weather Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('wapi_settings_group');
            do_settings_sections('wapi_settings_group');
            ?>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="wapi_api_key">WeatherAPI Key</label></th>
                    <td><input type="text" id="wapi_api_key" name="wapi_api_key" value="<?php echo esc_attr(get_option('wapi_api_key')); ?>" size="50" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="wapi_location">Location</label></th>
                    <td>
                        <input type="text" id="wapi_location" name="wapi_location" value="<?php echo esc_attr(get_option('wapi_location', 'Stornoway')); ?>" size="50" />
                        <p class="description">Enter the location name (e.g., "Stornoway") or coordinates (e.g., "58.213,-6.331").</p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
        <p>Register for an API key at <a href="https://www.weatherapi.com" target="_blank">WeatherAPI</a>.</p>
    </div>
    <?php
}

// Register settings
function wapi_register_options() {
    register_setting('wapi_settings_group', 'wapi_api_key', 'sanitize_text_field');
    register_setting('wapi_settings_group', 'wapi_location', 'sanitize_text_field');
}
add_action('admin_init', 'wapi_register_options');

// Fetch weather alerts
function wapi_get_weather_alerts() {
    $location = get_option('wapi_location', 'Stornoway');
    if (empty($location)) {
        return 'Please enter a location in the plugin settings.';
    }

    $transient_key = 'wapi_alerts_' . md5($location);
    $alerts = get_transient($transient_key);

    if (false === $alerts) {
        $api_key = get_option('wapi_api_key');
        if (empty($api_key)) {
            return 'Please enter your WeatherAPI key in the plugin settings.';
        }

        $url = "http://api.weatherapi.com/v1/alerts.json?key=" . esc_attr($api_key) . "&q=" . urlencode($location);
        $response = wp_remote_get($url, array('timeout' => 10));

        if (is_wp_error($response)) {
            $cached = get_transient($transient_key . '_fallback');
            if ($cached) {
                return $cached;
            }
            return 'Error fetching alerts: ' . esc_html($response->get_error_message());
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (!isset($data['alert']) || empty($data['alert'])) {
            $alerts = [['title' => 'No alerts', 'content' => "No weather alerts for {$location} at this time."]];
        } else {
            $alerts = [];
            foreach ($data['alert'] as $alert) {
                $alerts[] = [
                    'title' => $alert['headline'],
                    'updated' => $alert['effective'],
                    'content' => $alert['desc'],
                ];
            }
        }

        set_transient($transient_key, $alerts, HOUR_IN_SECONDS); // Cache for 1 hour
        set_transient($transient_key . '_fallback', $alerts, 12 * HOUR_IN_SECONDS); // Fallback for 12 hours
    }

    return $alerts;
}

// Fetch today's weather
function wapi_get_todays_weather() {
    $location = get_option('wapi_location', 'Stornoway');
    if (empty($location)) {
        return 'Please enter a location in the plugin settings.';
    }

    $transient_key = 'wapi_weather_' . md5($location);
    $weather = get_transient($transient_key);

    if (false === $weather) {
        $api_key = get_option('wapi_api_key');
        if (empty($api_key)) {
            return 'Please enter your WeatherAPI key in the plugin settings.';
        }

        $url = "http://api.weatherapi.com/v1/forecast.json?key=" . esc_attr($api_key) . "&q=" . urlencode($location) . "&days=1";
        $response = wp_remote_get($url, array('timeout' => 10));

        if (is_wp_error($response)) {
            $cached = get_transient($transient_key . '_fallback');
            if ($cached) {
                return $cached;
            }
            return 'Error fetching weather: ' . esc_html($response->get_error_message());
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (!isset($data['forecast']['forecastday'][0])) {
            $cached = get_transient($transient_key . '_fallback');
            if ($cached) {
                return $cached;
            }
            return 'Error: No weather data available for today.';
        }

        $today = $data['forecast']['forecastday'][0];
        $weather = [
            'date' => $today['date'],
            'temp' => $today['day']['maxtemp_c'] . '°C',
            'weather' => $today['day']['condition']['text'],
            'wind' => $today['day']['maxwind_mph'] . 'mph ' . $data['current']['wind_dir'],
            'precip' => $today['day']['daily_chance_of_rain'] . '%',
        ];

        set_transient($transient_key, $weather, 3 * HOUR_IN_SECONDS); // Cache for 3 hours
        set_transient($transient_key . '_fallback', $weather, 12 * HOUR_IN_SECONDS); // Fallback for 12 hours
    }

    return $weather;
}

// Shortcode to display alerts and weather
function wapi_weather_shortcode() {
    $location = get_option('wapi_location', 'Stornoway');
    $output = '<div class="wapi-weather">';

    // Display alerts
    $alerts = wapi_get_weather_alerts();
    $output .= '<h2>Weather Alerts for ' . esc_html($location) . '</h2>';
    if (is_string($alerts)) {
        $output .= '<p>' . esc_html($alerts) . '</p>';
    } else {
        foreach ($alerts as $alert) {
            $output .= '<div class="alert">';
            $output .= '<h3>' . esc_html($alert['title']) . '</h3>';
            $output .= '<p>' . esc_html($alert['content']) . '</p>';
            if (isset($alert['updated'])) {
                $output .= '<p><em>Updated: ' . esc_html($alert['updated']) . '</em></p>';
            }
            $output .= '</div>';
        }
    }

    // Display today's weather
    $output .= '<h2>Today\'s Weather in ' . esc_html($location) . '</h2>';
    $weather = wapi_get_todays_weather();
    if (is_string($weather)) {
        $output .= '<p>' . esc_html($weather) . '</p>';
    } else {
        $output .= '<p><strong>Date:</strong> ' . esc_html($weather['date']) . '</p>';
        $output .= '<p><strong>Temperature:</strong> ' . esc_html($weather['temp']) . '</p>';
        $output .= '<p><strong>Weather:</strong> ' . esc_html($weather['weather']) . '</p>';
        $output .= '<p><strong>Wind:</strong> ' . esc_html($weather['wind']) . '</p>';
        $output .= '<p><strong>Precipitation Probability:</strong> ' . esc_html($weather['precip']) . '</p>';
    }

    $output .= '</div>';

    return $output;
}
add_shortcode('weather_forecast', 'wapi_weather_shortcode');

// Enqueue basic CSS
function wapi_enqueue_styles() {
    wp_enqueue_style('wapi-styles', plugins_url('style.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'wapi_enqueue_styles');
?>