=== All-in-One WP Preloader ===
Contributors: monzuralam
Tags: preloader, loader, loading screen, page loader, animation
Requires at least: 6.6
Tested up to: 7.1
Requires PHP: 5.6
Stable tag: 1.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Add a beautiful loading screen to your site. Pick from 7 animated designs, set your logo and color, and see it live before you save.

== Description ==

All-in-One WP Preloader shows a loading screen while your page loads, then hides it with a smooth animation once everything is ready. No code needed: pick a design, add your logo, choose a color and you are done.

The settings page has a **live preview**, so you can see exactly what your visitors will see, and replay the hide animation, before saving anything.

= 7 preloader designs =

* **Loader 1:** Logo inside a spinning ring.
* **Loader 2:** Centered image that slides away.
* **Loader 3:** Image with a three-panel reveal.
* **Loader 4:** Logo with bouncing dots, fades out.
* **Loader 5:** Logo with a progress bar, slides up.
* **Loader 6:** Logo with an orbiting dot, curtains open.
* **Loader 7:** Logo with ripples, circle reveal.

= Features =

* Enable or disable the preloader with one switch.
* Use your own logo or image from the Media Library, or the built-in default.
* Set any background color.
* Live preview on the settings page, with a replay button for the hide animation.
* Modern settings page built with the WordPress components you already know.
* Pure CSS animations, responsive on every screen size.
* Works with classic and block themes.
* Optional: delete all plugin data when the plugin is uninstalled.
* Translation ready.

= Lightweight by design =

* The preloader CSS and JavaScript load only when the preloader is enabled.
* No external requests, no tracking, no extra database tables.
* All settings are stored in a single option.

== Installation ==

= From your WordPress dashboard =

1. Go to **Plugins > Add New Plugin** and search for "All-in-One WP Preloader".
2. Click **Install Now**, then **Activate**.
3. Go to **All-in-One WP Preloader** in the admin menu, turn on **Enable Preloader**, pick a design and click **Save Changes**.

= Manual upload =

1. Download the plugin zip file.
2. Go to **Plugins > Add New Plugin > Upload Plugin** and choose the zip file.
3. Click **Install Now**, then **Activate**.
4. Configure it from the **All-in-One WP Preloader** admin menu.

== Frequently Asked Questions ==

= Where are the settings? =

In your WordPress admin, click **All-in-One WP Preloader** in the left menu.

= The preloader does not show on my site. Why? =

First check that **Enable Preloader** is turned on and your changes are saved. The preloader is added through the `wp_body_open` hook, which is supported by block themes and most modern classic themes. If your theme is very old and does not call `wp_body_open()`, the preloader cannot be shown. Ask your theme author to add it, or switch to an up-to-date theme.

= When does the preloader hide? =

As soon as the browser has finished loading the page, including images, the preloader plays its hide animation and the page becomes visible.

= Can I use my own logo? =

Yes. Choose any image from the Media Library under **Preloader Image**. If no image is selected, the default image is used.

= Can I preview the preloader before turning it on? =

Yes. The live preview on the settings page updates as you change the design, image or color, even while the preloader is disabled. Click **Play Animation** to replay the hide animation.

= Will it slow down my website? =

The preloader adds one small CSS file and one small JavaScript file, and only when it is enabled. It makes no external requests. The script uses jQuery, which most themes already load.

= I am updating from version 1.0.0. Will I lose my settings? =

No. Your existing settings are moved to the new format automatically the first time the updated plugin runs. Version 1.0.1 requires WordPress 6.6 or newer.

= What happens to my settings if I delete the plugin? =

By default your settings are kept, so you can reinstall the plugin later without setting it up again. To remove all plugin data when the plugin is deleted, turn on **Delete data on uninstall** under **Advanced** in the plugin settings before deleting it. Deactivating the plugin always keeps your settings.

= Where can I get help? =

Please open a topic in the plugin's support forum on WordPress.org.

== Screenshots ==

1. Loader 1, Spinning Ring: your logo inside a spinning ring, slides away to the top right.
2. Loader 2, Slide Away: your logo on a clean background, slides away to the right.
3. Loader 3, Three-Panel Reveal: three panels pull back to reveal the page.
4. Loader 4, Bouncing Dots: your logo with bouncing dots, fades out smoothly.
5. Loader 5, Progress Bar: your logo with a progress bar, slides up out of view.
6. Loader 6, Split Curtain: your logo with an orbiting dot, curtains open from the center.
7. Loader 7, Circle Reveal: your logo with soft ripples, shrinks into a closing circle.

== Changelog ==

= 1.0.1 =
* New: Settings page rebuilt with React and WordPress components.
* New: Live preview with a replay button for the hide animation.
* New: 4 preloader designs: Bouncing Dots, Progress Bar, Split Curtain and Circle Reveal.
* New: "Delete data on uninstall" option (off by default) to remove all plugin data when the plugin is deleted.
* Improvement: Removed the Carbon Fields library, making the plugin much smaller.
* Improvement: Preloader CSS and JavaScript load only when the preloader is enabled.
* Improvement: Settings are stored in a single option. Existing settings are migrated automatically.
* Improvement: New plugin structure following the WordPress Coding Standards.
* Fix: Compatibility with jQuery 3.
* Requirement: WordPress 6.6 or newer.

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 1.0.1 =
New settings page with live preview and 4 new designs. Requires WordPress 6.6 or newer. Your existing settings are migrated automatically.
