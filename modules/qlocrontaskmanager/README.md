## Overview

The **QloApps Cron Task Manager** module enables centralized management and execution of scheduled (cron) tasks within QloApps. It allows developers and administrators to define, register, and execute automated jobs efficiently.

**QloApps Cron Task Manager 1.0.0** 

- Current version: 1.0.0 

- Module V1.0.0 compatible with QloApps version 1.7.0 and V1.8.x.



## Usage (QloApps version 1.7.0)

To register a cron task, add a new hook inside your module's main class file.

### Step 1: Register Cron Tasks Hook

```php
public function hookRegisterCronTasks()
{
    return array(
        array(
            'name' => 'cron_success_probe',
            'description' => $this->l('Sample cron task for scheduler verification.'),
            'cron' => '* * * * *',
            'callback' => 'cronSuccessProbe',
        )
    );
}

```

### Support Policy:
https://store.webkul.com/support.html


### Explore Addons:
https://qloapps.com/addons/

### Refund Policy:
https://store.webkul.com/refund-policy.html/