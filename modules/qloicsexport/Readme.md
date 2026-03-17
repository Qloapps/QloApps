### User Guide Documentation link:
https://qloapps.com/qloapps-booking-icalendar-ics-file-export/

### Support Policy:
https://store.webkul.com/support.html

### Store link:
https://store.webkul.com/QloApps-Booking-iCalendar.html

### Explore Modules:
https://store.webkul.com/Qloapps.html

### Explore Addons:
https://qloapps.com/addons/


**QloApps Booking iCalendar (.ics) File Export 4.0.0**

- Module V4.0.0 compatible with QloApps version  V1.7.x

- Module V1.0.3 compatible with QloApps version  V1.6.x

- Module V1.0.2 compatible with QloApps version  V1.5.x , V1.6.x AND  V1.7.x

- Module V1.0.1 compatible with QloApps version V1.6.x AND V1.5.x

- Module V1.0.0 compatible with QloApps version V1.6.x AND V1.5.x


### Refund Policy:
https://store.webkul.com/refund-policy.html/


## Important Notes:

For the proper functioning of the module in QloApps 1.7.0., please add new hook on the following path:

classes/Mail.php:

##### Mofify the the following function at line number 417 (may vary)

Hook::exec('actionMailAlterMessageBeforeSend', array(
    'template' => &$template,
    'message' => &$email,
    'template_vars' => $template_vars,
));


##### Please make sure you add this line after and before the following code:
$email->from(new Address($from, (string) $from_name));

//add here

$mailer->send($email);
ShopUrl::resetMainDomainCache();