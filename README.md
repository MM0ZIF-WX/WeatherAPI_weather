WeatherAPI Weather WordPress Plugin

Help support this plugin and others in development by donating here - https://mm0zif.radio/current/fundraising-campaign/

A WordPress plugin that displays weather warnings and today's weather forecast for a user-specified location using the WeatherAPI service.

Overview

The WeatherAPI Weather plugin integrates with the WeatherAPI (https://www.weatherapi.com) to fetch and display weather alerts and daily forecasts on your WordPress site. It supports customizable locations (e.g., "Stornoway" for the Isle of Lewis) and includes caching to optimize API calls, making it efficient even on the free tier (1 million calls/month). The plugin uses a shortcode [weather_forecast] to embed weather data in pages, posts, or widgets.
Features

Fetch weather alerts and today's forecast (temperature, weather condition, wind, precipitation probability).
Configurable via an admin settings page for API key and location.
Caches data (3 hours for forecasts, 1 hour for alerts, 12-hour fallback) to minimize API usage.
Responsive design with basic styling included.

Requirements

WordPress 6.0 or higher.
PHP 7.4 or higher.
A WeatherAPI key (free tier available at https://www.weatherapi.com).

Installation

Download the Plugin:

Clone or download this repository to your local machine.
Extract the weatherapi-weather folder.


Upload via FTP:

Connect to your WordPress site via FTP.
Upload the weatherapi-weather folder to wp-content/plugins/.
Ensure the structure is wp-content/plugins/weatherapi-weather/ with weatherapi-weather.php and style.css inside.


Upload via WordPress Admin:

Go to Plugins > Add New in your WordPress admin panel.
Click Upload Plugin, select the weatherapi-weather.zip file (if packaged), and click Install Now.
Activate the plugin after installation.


Verify Activation:

The plugin should appear in Plugins > Installed Plugins. Activate it if not already active.



Configuration

Obtain a WeatherAPI Key:

Sign up at https://www.weatherapi.com to get a free API key.


Set Up Plugin Settings:

Go to Settings > WeatherAPI Weather in the WordPress admin panel.
Enter your WeatherAPI Key in the provided field.
Set the Location field to your desired location (e.g., "Stornoway" or coordinates like "58.213,-6.331" for the Isle of Lewis).
Click Save Changes.


Use the Shortcode:

Add [weather_forecast] to any page, post, or widget to display the weather data.
Example output:Weather Alerts for Stornoway
No weather alerts for Stornoway at this time.

Today's Weather in Stornoway
Date: 2025-05-22
Temperature: 10.5°C
Weather: Sunny
Wind: 17.9mph NE
Precipitation Probability: 0%





Usage

Embed the [weather_forecast] shortcode in your content to show real-time weather updates.
The plugin caches data to reduce API calls (approximately 32 calls/day with default settings).
Customize the appearance by editing the style.css file in the plugin directory.

Caching

Forecasts: Cached for 3 hours to reflect slow weather changes.
Alerts: Cached for 1 hour to ensure timely updates.
Fallback Cache: Stored for 12 hours to provide data during API errors or rate limits.

Contributing
Contributions are welcome! To contribute:

Fork this repository.
Create a new branch (git checkout -b feature-branch).
Make your changes and commit them (git commit -m "Add new feature").
Push to the branch (git push origin feature-branch).
Open a Pull Request with a description of your changes.

Please ensure code follows WordPress coding standards and includes tests if applicable.
License
This plugin is released under the GNU General Public License v2 (GPL-2.0). Feel free to use, modify, and distribute it according to the terms of this license.
Support
For issues or questions:

Check the WeatherAPI documentation for API-related problems.
Report bugs or request features by opening an issue in this repository.
Contact the author via the repository's issue tracker.

Acknowledgments

Powered by WeatherAPI.
Developed with assistance from the xAI community.

Last updated: May 22, 2025

Developed by: Marcus Hazel-McGown - MM0ZIF - https://mm0zif.radio/ 