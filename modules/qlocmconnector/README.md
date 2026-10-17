### QloApps PMS & Channel Manager Connector

### User Guide Documentation link:
https://qloapps.com/qloapps-pms-channel-manager-connector/

### Support Policy:
https://store.webkul.com/support.html/

### Store link:
https://store.webkul.com/qloapps-pms-channel-manager-connector.html

### Explore Modules:
https://store.webkul.com/Qloapps.html


### Explore Addons:
https://qloapps.com/addons/


### Module Versions and Compatibility:

- **Current version:** `5.0.6`

- Module version `5.0.6` compatible with QloApps version `1.8.x`

- Module version `5.0.5` compatible with QloApps version `1.7.x`

- Module version `5.0.4` compatible with QloApps version `1.7.x`

- Module version `5.0.3` compatible with QloApps version `1.7.x`

- Module version `5.0.2` compatible with QloApps version `1.7.x`

- Module version `5.0.1` compatible with QloApps version `1.7.x`

- Module version `5.0.0` compatible with QloApps version `1.7.x`

- Module version `4.0.2` compatible with QloApps version `1.7.x`

- Module version `4.0.1` compatible with QloApps version `1.7.x`

- Module version `4.0.0` compatible with QloApps version `1.7.x`

- Module version `1.0.0`, `1.1.0`, `1.1.1`, `1.1.2`, `1.2.0`, `2.0.0`, `2.0.1`, compatible with QloApps version `1.6.1.0`




### Refund Policy:
https://store.webkul.com/refund-policy.html/


---

### QloApps Hook Integration (v5.0.4+)

This module adds a **Rate Plan** column to the order rooms table in the admin order detail view. For this to work, the following two hooks must be present in QloApps core template files.

#### 1. Column Header Hook — `displayOrderRoomsBookingsTableHeading`

**File:**
```
adminhtl/themes/default/template/controllers/orders/_rooms_informaion_table.tpl
```
**Line:** 43 (May Vary)

Add inside the `<thead><tr>` block, after the last `<th>`:
```smarty
{hook h='displayOrderRoomsBookingsTableHeading' order=$order}
```

---

#### 2. Row Data Hook — `displayOrderRoomsBookingsTableData`

**File:**
```
adminhtl/themes/default/template/controllers/orders/_product_line.tpl
```
**Line:** 88 (May Vary)

Add inside the `<tr class="product-line-row">` block, before `</tr>`:
```smarty
{hook h='displayOrderRoomsBookingsTableData' order=$order data=$data}
```

---


