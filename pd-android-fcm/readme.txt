=== pd Android FCM Push Notification ===
Contributors: Proficient Designers
Tags: Google Firebase Cloud Messaging service, send push notification from Wordpress site to android devices, FCM, push notification, android push notification, android devices, Firebase Cloud Messaging, FCM push notification, Wordpress push notification, Wordpress android push notification
Requires PHP: 5.6
Requires at least: 4.0
Tested up to: 5.5.1
Stable tag: 1.1.8
Support Link: https://proficientdesigners.in/creations/pd-android-fcm-push-notification/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

pd Android FCM Push Notification is a plugin through which you can send push notifications directly from your WordPress site to android devices via <a href='https://firebase.google.com/' target='_blank'>Firebase Cloud Messaging</a> service. When a new blog is posted or existing blog is updated, a push notification sent to android device.

== Description ==
pd Android FCM Push Notification is a plugin through which you can send push notifications directly from your WordPress site to android devices via <a href='https://firebase.google.com/' target='_blank'>Firebase Cloud Messaging</a> service. When a new blog is posted or existing blog is updated, a push notification sent to android device.

**Features Included:**

* Can send push notification for each blog post.
* Even can send custom notifications to the registered devices.
* Devices are subscribed in category wise, so that the notifications can also be sent based on the category.
* Featured image support is available (above android version 4.4).
* Push notifications can be scheduled.
* A checkbox is available at the right side to choose whether to send push notification in post publish or update.
* For more documentation and screenshots, please visit [proficientdesigners.in](https://proficientdesigners.in/creations/pd-android-fcm-push-notification/)

**Demo:**
[youtube https://youtu.be/_fffaw9fFwY]

**Using 3rd party service:**

Please note that this plugin is relying on a 3rd party service, which is the Google Firebase Cloud Messaging service (FCM) and your data is being sent through their servers via HTTP API *(https://fcm.googleapis.com/fcm/send)*. This is very legal to use the  Google Firebase Cloud Messaging service (FCM), based on their terms and conditions <a href='https://firebase.google.com/terms/'>https://firebase.google.com/terms/</a>.

**Demo Android App:**
We have a demo android app in the Google Play Store for this plugin's testing purpose. You can get the link from our official documentation page.

== Installation ==
1. Upload `pd-android-fcm` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= Why pd Android FCM? =
This plugin can send push notifications via Google FCM from your WordPress site to your Android App which should've functionality to receive the push notification message from Google FCM

= For whom this plugin is for? =
This plugin is primarily intended for android mobile developers who do not want to develop their server-side back-end. Supporting push notifications is incredibly complicated. This plugin lets you focus on creating the apps, without any hassle.

= Does this plugin uses any 3rd party services? =
Yes, its the Google FCM to send the push notifications. Visit to know more https://firebase.google.com/

= Is there any demo android app available for this plugin? =
Yes, there is a plugin available in Google Play Store and the link is given in our official documentation

== Screenshots ==

1. All Registered Devices
2. All Custom Push Notification
3. Custom Push Notification Inner Page
4. Subscriptions Page
5. Settings
6. Push Notification Received
7. Android - Lock Screen

== Changelog ==

= 1.1.8 =
* Demo environment added to support the android app available in Google Play Store.

= 1.1.7 =
* Broken functionality fixed.
* Compatibility check for WP version 5.5.3.

= 1.1.6 =
* Bug fixes and improvements.

= 1.1.5 =
* Bug fixed on db creation, if the db user is not allowed to create a table then the error message is shown.
* Compatibility check for WP version 5.5.1.

= 1.1.4 =
* Bug fixed on rest_route_api.
* Compatibility check for WP version 5.5.

= 1.1.3 =
* Bug Fixes.
* Compatibility check for WP version 5.4.2.

= 1.1.2 =
* Documentation changed.
* API Key feature is added, to register a device.
* API parameters are changed for the convenience.
* Bug Fixes.
* Compatibility check for WP version 5.4.1.

= 1.1.1 =
* Documentation link changed.

= 1.1.0 =
* A new checkbox added at the right side to choose whether to send push notification in post publish.
* Compatibility check for WP version 5.2.3.

= 1.0.9 =
* Bug Fixes.

= 1.0.8 =
* Compatibility check for WP version 5.1.1.

= 1.0.7 =
* Compatibility check for WP version 5.0.3. 

= 1.0.6 =
* Fixed: subscription categories are now available with the chosen Custom Post Type.

= 1.0.5 =
* Now can send Push Notification from New Custom Post.

== Upgrade Notice ==

= 1.1.8 =
* A new Demo environment added to support the android app available in Google Play Store.

= 1.1.2 =
* API Key feature is added, to register a device.
* API parameters are changed for the convenience.
* Compatibility check for WP version 5.4.1.