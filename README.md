<div align="center">
	<a href="https://www.qloapps.com"><img src="https://forums.qloapps.com/assets/uploads/system/site-logo.png?v=hkl8e1230fo" alt="QloApps"></a>
	<br>
	<p>
		<b>QloApps - An open source and free platform to launch your own hotel booking website</b>
	</p>
</div>

<p align="center">
	<a href="https://qloapps.com/download/"><img src="https://img.shields.io/badge/Download-Download%20QloApps%20-brightgreen" alt="Download"></a>
	<a href="https://qloapps.com/qlo-reservation-system/"><img src="https://img.shields.io/badge/Documentation-Blog-yellowgreen" alt="Documentation"></a>
	<a href="https://forums.qloapps.com/"><img src="https://img.shields.io/badge/Forum-Help%2FSupport-green" alt="Forum"></a>
	<a href="https://qloapps.com/addons/"><img src="https://img.shields.io/badge/Addons-Plugins-blueviolet" alt="Addons"></a>
	<a href="https://qloapps.com/contact/"><img src="https://img.shields.io/badge/Contact-Get%20In%20Touch-blue" alt="Contact us"></a>
	<a href="https://github.com/webkul/hotelcommerce/blob/develop/LICENSE"><img src="https://img.shields.io/badge/license-MIT-yellowgreen" alt="License"></a>
</p>

## Topics
1. [Introduction](#introduction)
2. [Requirements](#requirements)
3. [Installation & Configuration](#installation-and-configuration)
4. [License](#license)
5. [Security Vulnerabilities](#security-vulnerabilities)
6. [Documentation & Demo](#documentation--demo)
7. [Contribute](#contribute)
8. [Credits](#credits)


### Introduction

QloApps also known as Qlo is an **Open-source and Free hotel reservation system** and booking engine. <br>
With the help of QloApps, you can launch your hotel booking website without any cost and take & manage online bookings . You can manage your online & On-Desk booking easily with QloApps.

### Requirements

In order to install QloApps you will need the following server configurations for hosted and local serves.
The system compatibility will also be checked by the system with installation and if the server is not compatible then the installation will not move ahead.

#### Hosted Server Configurations

* **Web server**: Apache 1.3, Apache 2.x, Nginx or Microsoft IIS
* **PHP  version**: 5.4+
* **MySQL version**:  5.0+ and below 5.7 installed with a database created
* SSH or FTP access (ask your hosting service for your credentials)
* In the PHP configuration ask your provider to set memory_limit to "128M", upload_max_filesize to "16M" ,    max_execution_time to "500" and allow_url_fopen "on"
* SSL certificate if you plan to process payments internally (not using PayPal for instance)
* **Required PHP extensions**: cURL, SimpleXML, SOAP

#### Local Server Configurations

* **Supported operating system**: Windows, Mac, and Linux
* **A prepared package**: WampServer (for Windows), Xampp (for Windows and Mac) or EasyPHP (for Windows)
* **Web server**: Apache 1.3, Apache 2.x, Nginx or Microsoft IIS
* **PHP**: 5.4+ and below PHP 7.0
* **MySQL** 5.0+ and below 5.7 installed with a database created
* In the PHP configuration, set memory_limit to "128M", upload_max_filesize to "16M" and max_execution_time to "500"
* **Required PHP extensions**: cURL, SimpleXML, SOAP

### Installation and Configuration

**1.** You can install QloApps easily after downloading QloApps. There are easy steps for the installation process. Please visit [QloApps Installation Guide](https://qloapps.com/install-qloapps/) and follow the steps for the successful installation.

**2.** Or you can install QloApps with docker image. For the docker image of QloApps, please visit [Dockerize image of QloApps](https://hub.docker.com/r/webkul/qloapps_docker) <br>
* Docker pull command
~~~
docker pull webkul/qloapps_docker
~~~

### License

QloApps is a truly opensource Hotel-Commerce platform which will always be free under the [MIT License](https://github.com/webkul/hotelcommerce/blob/develop/LICENSE).

### Security Vulnerabilities

Please don't disclose security vulnerabilities publicly. If you find any security vulnerability in QloApps then please email us: mailto:support@qloapps.com.

### Documentation & Demo

#### QloApps Documentation [https://qloapps.com/qlo-reservation-system](https://qloapps.com/qlo-reservation-system)
#### QloApps Demo
**FrontEnd** : https://demo.qloapps.com </br>
**Backend** : https://demo.qloapps.com/adminhtl/index.php </br>
**username** : demo@demo.com </br>
**Password** : demodemo </br>

### Contribute
As a PHP developer who has command on PHP and MySQL and also knows how to use Git or GitHub efficiently, can contribute to code enhancements via pull requests.<br>
For more information about the contribution process please visit **[Contribute to QloApps](https://qloapps.com/how-to-contribute-to-qloapps-project/)**

### Credits
Crafted with :heart: at [Webkul](https://webkul.com)
