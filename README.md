About PrestaShop
--------

PrestaShop is a free and open-source e-commerce web application, committed to providing the best shopping cart experience for both merchants and customers. It is written in PHP, is highly customizable, supports all the major payment services, is translated in many languages and localized for many countries, has a fully responsive design (both front and back office), etc. [See all the available features][1].

<p align="center">
  <img src="http://www.prestashop.com/images/banners/general/ps161-screenshot-github.png" alt="PrestaShop's back office dashboard"/>
</p>

To download the latest stable public version of PrestaShop, please go to [the download page][2] on the official PrestaShop site.


About this repository
--------

This repository contains the source code for the latest version of PrestaShop 1.6.

Clicking the "Download ZIP" button from the root of this repository will download the current state of PrestaShop 1.6 -- which is in active development, and cannot be considered stable. If you want the latest stable version, go to [the download page][2].

Note that the ZIP file does not contain the default modules: you need to make a recursive clone using your local Git client in order to download their files too. See [CONTRIBUTING.md][7] for more information about using Git and GitHub.

Also, the ZIP file contains resources for developers and designers that are not in the public archive, for instance the SCSS sources files for the default themes (in the /default-bootstrap/sass folder) or the unit testing files (in the /tests folder).


Server configuration
--------

To install PrestaShop, you need a web server running PHP 5.2+ and any flavor of MySQL 5.0+ (MySQL, MariaDB, Percona Server, etc.).  
You will also need a database administration tool, such as phpMyAdmin, in order to create a database for PrestaShop.
We recommend the Apache or Nginx web servers.  
You can find more information on the [System Administrator Guide][19].

If your host does not offer PHP 5 by default, [here are a few explanations][3] about PHP 5 or the `.htaccess` file for certain hosting services (1&amp;1, Free.fr, OVH, Infomaniak, Amen, GoDaddy, etc.).

If you want your own store with nothing to download and install, you should use [PrestaShop Cloud][4], our 100% free and fully-hosted PrestaShop service: it lets you create your online store in less than 10 minutes without any technical knowledge. Learn more about the [difference between PrestaShop Cloud and PrestaShop Download][10].


Installation
--------

Once the files in the PrestaShop archive have been decompressed and uploaded on your hosting space, go to the root of your PrestaShop directory with your web browser, and the PrestaShop installer will start automatically. Follow the instructions until PrestaShop is installed.

If you get any PHP error, it might be that you do not have PHP 5 on your web server, or that you need to activate it. See [this page for explanations about PHP 5][3], or contact your web host directly.  
If you do not find any solution to start the installer, please post about your issue on [the PrestaShop forums][5].


User documentation
--------

The official PrestaShop documentation is available online [on its own website][6]

First-time users will be particularly interested in the following guides:
* [Getting Started][13]: How to install PrestaShop, and what you need to know.
* [User Guide][14]: All there is to know to put PrestaShop to good use.
* [Updating Guide][15]: Switching to the newest version is not trivial. Make sure you do it right.
* [Merchant's Guide][16]: Tips and tricks for first-time online sellers.
* The [FAQ][17] and the [Troubleshooting][18] pages should also be of tremendous help to you.


Contributing
--------

PrestaShop is an Open Source project, and it wouldn't be possible without the help of the [hundreds of contributors][21], who submitted improvements and bugfixes over the years. Thank you all!

If you want to contribute code to PrestaShop, read the [CONTRIBUTING.md][7] file in this repository or read the [tutorials about contribution][8] on the documentation site.

Current [Travis](https://travis-ci.org/) status: [![Travis](https://travis-ci.org/PrestaShop/PrestaShop.svg?branch=master)](https://travis-ci.org/PrestaShop/PrestaShop) (The Unit Tests are being implemented, so the status might be broken).

If you want to help translate PrestaShop in your language, [join us on Crowdin][9]!

Current Crowdin status (for 69 registered languages): [![Crowdin](https://crowdin.net/badges/prestashop-official/localized.png)](https://crowdin.net/project/prestashop-official)


Extending PrestaShop
--------

PrestaShop is a very extensible e-commerce platform, both through modules and themes. Developers can even override the default components and behaviors. Learn more about this using the [Developer Guide][11] and the [Designer Guide][12].

Themes and modules can be obtained (and sold!) from [PrestaShop Addons][20], the official marketplace for PrestaShop.


Community forums
--------

You can discuss about e-commerce, help other merchants and get help, and contribute to improving PrestaShop together with the PrestaShop community on [the PrestaShop forums][5].


Getting support
--------

If you need help using PrestaShop 1.6, contact the PrestaShop support team: http://support.prestashop.com/.


Thank you for downloading and using the PrestaShop open-source e-commerce solution!

[1]: https://www.prestashop.com/en/online-store-builder
[2]: http://www.prestashop.com/en/download
[3]: http://doc.prestashop.com/display/PS16/Misc.+information#Misc.information-ActivatingPHP5
[4]: http://www.prestashop.com
[5]: http://www.prestashop.com/forums/
[6]: http://doc.prestashop.com
[7]: CONTRIBUTING.md
[8]: http://doc.prestashop.com/display/PS16/Contributing+to+PrestaShop
[9]: https://crowdin.net/project/prestashop-official
[10]: https://www.prestashop.com/en/ecommerce-software
[11]: http://doc.prestashop.com/display/PS16/Developer+Guide
[12]: http://doc.prestashop.com/display/PS16/Designer+Guide
[13]: http://doc.prestashop.com/display/PS16/Getting+Started
[14]: http://doc.prestashop.com/display/PS16/User+Guide
[15]: http://doc.prestashop.com/display/PS16/Updating+PrestaShop
[16]: http://doc.prestashop.com/display/PS16/Merchant%27s+Guide
[17]: http://doc.prestashop.com/display/PS16/FAQ
[18]: http://doc.prestashop.com/display/PS16/Troubleshooting
[19]: http://doc.prestashop.com/display/PS16/System+Administrator+Guide
[20]: http://addons.prestashop.com/
[21]: CONTRIBUTORS.md
