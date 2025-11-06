![Mobile Detect](http://demo.mobiledetect.net/logo-github.png)

MobileDetect, PHP mobile detection class
========================================

![Workflow status](https://img.shields.io/github/actions/workflow/status/serbanghita/Mobile-Detect/4.8.x-test.yml?style=flat-square)
![Latest tag](https://img.shields.io/github/v/tag/serbanghita/Mobile-Detect?filter=4.*&style=flat-square)
![Monthly Downloads](https://img.shields.io/packagist/dm/mobiledetect/mobiledetectlib?style=flat-square&label=installs)
![Total Downloads](https://img.shields.io/packagist/dt/mobiledetect/mobiledetectlib?style=flat-square&label=installs)
![MIT License](https://img.shields.io/packagist/l/mobiledetect/mobiledetectlib?style=flat-square)

Mobile Detect is a lightweight PHP class for detecting mobile devices (including tablets).
It uses the User-Agent string combined with specific HTTP headers to detect the mobile environment.

## Before you install

There are three versions of MobileDetect. 
`4.8.x` is the main version that is ALWAYS going to be updated first.

| Version | Tests                                                                                                                                                                                                 | Namespace | Code                                                             | PHP Version | Status               |
|---------|-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|-----------|------------------------------------------------------------------|-------------|----------------------|
| 2.8.x   | [![5x](https://img.shields.io/github/actions/workflow/status/serbanghita/Mobile-Detect/2.8.x-test.yml?style=flat-square)](https://github.com/serbanghita/Mobile-Detect/actions/workflows/test.yml)  | `\Mobile_Detect`       | [2.8](https://github.com/serbanghita/Mobile-Detect/tree/2.8.x)   | \>=5.0,<7.0 | Deprecated           |
| 3.74.x  | [![7x](https://img.shields.io/github/actions/workflow/status/serbanghita/Mobile-Detect/3.74.x-test.yml?style=flat-square)](https://github.com/serbanghita/Mobile-Detect/actions/workflows/test.yml) | `Detection\MobileDetect`        | [3.74](https://github.com/serbanghita/Mobile-Detect/tree/3.74.x) | \>=7.4,<8.0 | LTS                  |
| 4.8.x   | [![7x](https://img.shields.io/github/actions/workflow/status/serbanghita/Mobile-Detect/4.8.x-test.yml?style=flat-square)](https://github.com/serbanghita/Mobile-Detect/actions/workflows/test.yml)  | `Detection\MobileDetect`        | [4.8](https://github.com/serbanghita/Mobile-Detect/tree/4.8.x)   | \>=8.0      | Current, **Recommended** |

## 🤝 Supporting

If you are using Mobile Detect open-source package in your production apps, in presentation demos, hobby projects, school projects or so, you can sponsor my work by [donating a small amount :+1:](https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=mobiledetectlib%40gmail%2ecom&lc=US&item_name=Mobile%20Detect&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donate_SM%2egif%3aNonHosted). I'm currently paying for hosting and spend a lot of my family time to maintain the project and planning the future releases. I would highly appreciate any money donations.

Special thanks to:

* the community :+1: for donations, submitting patches and issues
* the JetBrains team for the open-source licenses for [PHPStorm IDE](https://www.jetbrains.com/phpstorm/)
* [Gitbook](https://www.gitbook.com/) team for the open-source license for their technical documentation tool.


## 📃 Documentation

The entire documentation is available on Gitbook: [https://docs.mobiledetect.net](https://docs.mobiledetect.net)

## 👾 Demo

Point your device to:
[https://demo.mobiledetect.net](https://demo.mobiledetect.net)

## 🐛 Testing

``` bash
vendor/bin/phpunit -v -c tests/phpunit.xml --coverage-html .coverage
```

## 🤝 Contributing

Please see the [Contribute guide](https://mobile-detect.gitbook.io/home/contribute) for details.

## 🔒  Security

If you discover any security related issues, please email serbanghita@gmail.com instead of using the issue tracker.

## 🎉 Credits

- [Serban Ghita](https://github.com/serbanghita)
- [All Contributors](https://mobile-detect.gitbook.io/home/credits)
