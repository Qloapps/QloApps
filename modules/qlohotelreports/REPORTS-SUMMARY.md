# QloHotelReports Module — Technical and Business Summary

## Overview

`qlohotelreports` is a QloApps stats module that renders 18 hotel analytics reports under the **AdminStats** left navigation panel. It is implemented as a standard `Module` (not `ModuleGrid` or `ModuleGraph`) that hooks into `AdminStatsModules` and renders its own Smarty templates.

- **Module class:** `QloHotelReports` (`modules/qlohotelreports/qlohotelreports.php`)
- **Version:** 1.0.0
- **Tab:** `analytics_stats`
- **Hooks registered:** `AdminStatsModules`, `actionAdminControllerSetMedia`
- **Assets:** `views/css/qlohotelreports.css`, `views/js/qlohotelreports.js`

Reports are organized into six groups. Each group maps to a Smarty template file and appears as a separate link in the AdminStats sidebar. Within each group, sub-reports are navigated via Bootstrap tab links rendered inside the template.

---

## Report Group Map

| Group key  | Sidebar label             | Template file            | Sub-reports                                              |
|------------|---------------------------|--------------------------|----------------------------------------------------------|
| `bookings` | Bookings                  | `group-bookings.tpl`     | Reservation, Cancellation, No-Show, Arrivals, Departures, In-House |
| `revenue`  | Revenue & Finance         | `group-revenue.tpl`      | Revenue, Refunds, Payments, Tax, Outstanding             |
| `occupancy`| Occupancy & Availability  | `group-occupancy.tpl`    | Occupancy, Availability, Room Status, Room Type Performance |
| `channels` | Channels                  | `group-channels.tpl`     | Booking Source, Payment Methods                          |
| `guests`   | Guests & Services         | `group-guests.tpl`       | Services, Guest Directory                                |
| `property` | Property & Summary        | `group-property.tpl`     | Daily Summary, Hotel Comparison, Out of Order Rooms      |

The active group is read from `?tab=<key>` (default: `bookings`). The active sub-report is read from `?report=<key>`. Both are validated against their respective registries before use.

---

## URL and Filter Parameters

All report URLs are built on `AdminStats?module=qlohotelreports`. Common parameters:

| Parameter        | Used by              | Meaning                                      |
|------------------|----------------------|----------------------------------------------|
| `tab`            | all groups           | Report group key                             |
| `report`         | all groups           | Sub-report key within the group              |
| `id_hotel`       | all groups           | Filter to a specific hotel branch (0 = all)  |
| `id_product`     | bookings, revenue, occupancy, property | Filter to a specific room type (product ID) |
| `booking_status` | bookings/reservation | HotelBookingDetail status constant           |
| `booking_type`   | bookings, channels   | ALLOTMENT_AUTO (1) or ALLOTMENT_MANUAL (2)   |
| `refund_status`  | revenue/refund       | OrderReturnState ID                          |
| `outstanding_status` | revenue/outstanding | HotelBookingDetail status constant        |
| `guest_type`     | guests/guest-directory | `new` or `returning`                       |
| `export`         | all groups           | When set, streams a CSV and exits            |

Date range comes from `$this->context->employee->stats_date_from` and `stats_date_to`, set by the AdminStats date picker (not from URL params).

---

## Report Reference

### Group: Bookings (`group-bookings.tpl`)

#### 1. Reservation Report

**Business purpose:** Full ledger of all room bookings created in the date range. Used by the front desk and finance teams to audit reservations, chase outstanding balances, and verify booking sources.

**Columns (20):** Reservation ID, Guest Name, Guest Contact (phone), Room Type, Room No., Check-in, Check-out, Nights, Adults, Children, Rate/Night (`unit_price_tax_excl`), Booking Source, Booking Status, Total excl Tax, Tax Amount, Grand Total, Balance Due, Payment Status, Created By, Booking Date

**Filters:** Room Type, Booking Status, Booking Source

**Data source:** `HotelBookingDetail::getBookings($params, detailed_info=true)`

Key computed columns in SQL: `DATEDIFF(date_to, date_from)` for nights; `total_price_tax_incl - total_price_tax_excl` for tax amount; `o.total_paid_tax_incl - o.total_paid_real` for balance due. `created_by` is resolved via a correlated subquery on `order_history` joining `employee`.

**Footer totals:** nights, adults, children, total excl tax, tax amount, grand total, total balance due.

---

#### 2. Cancellation Report

**Business purpose:** Log of all cancelled bookings in the period. Used to track refund processing and identify cancellation patterns.

**Columns (11):** Booking ID, Guest Name, Room Type, Room No., Check-in Date, Cancellation Date, Cancellation Reason, Cancellation Remark (shown as —), Refund Amount, Refund Status, Booking Date

**Data source:** `HotelBookingDetail::getCancellations($params, detailed_info=true)`

Joins `htl_booking_detail` to `order_return` (on `id_order`, date-filtered on `orr.date_add`) and `order_return_state_lang` for refund status label. `cancellation_reason` comes from `order_return.question`; `refunded_amount` and `payment_mode` from `order_return`.

**Footer total:** sum of refunded amounts.

---

#### 3. No-Show Report

**Business purpose:** Rooms that were allotted but the guest never checked in and the arrival date has passed. Used for penalty tracking and operational reporting.

**Columns (7):** Booking ID, Guest Name, Room Type, Room No., Check-in Date, Total Amount, Penalty Charged (shown as —)

**Data source:** `HotelBookingDetail::getArrivalsInfo($params)` filtered to `id_status = STATUS_ALLOTED`

The no-show definition used here is: bookings with `date_from` in the selected range, `id_status = STATUS_ALLOTED` (never checked in), and `date_from < CURDATE()`.

**Footer totals:** LOS (nights), adults, children, total incl tax.

---

#### 4–6. Arrivals, Departures, In-House Reports

These sub-reports are operational views used by the front desk. They share the same `group-bookings.tpl` template.

- **Arrivals:** guests with `date_from` in range — `HotelBookingDetail::getArrivalsInfo()`
- **Departures:** guests with `date_to` in range — `HotelBookingDetail::getDeparturesInfo()`
- **In-House:** guests currently checked in (no date range filter) — `HotelBookingDetail::getInHouseInfo()`

---

### Group: Revenue & Finance (`group-revenue.tpl`)

A summary KPI bar (room revenue, service revenue, discounts, total revenue, ADR, RevPAR, occupancy %) is rendered at the top of every sub-report in this group, sourced from scalar aggregate methods.

#### 4. Revenue Report

**Business purpose:** Daily breakdown of all revenue components. Primary tool for the finance team to reconcile daily income, compute key performance metrics, and report to management.

**Columns (13):** Date/Period, Rooms Sold, Total Bookings, Room Revenue excl Tax, Extra Services Revenue, Discount Amount, Tax Amount, Refund Amount (shown as —), Total Collection, Net Revenue, ADR, RevPAR, Occupancy %

**Data assembly:** Five separate data sources are merged per day using Unix timestamps as keys:
1. `HotelBookingDetail::getDatewiseRoomRevenue($params, true)` — returns `[ts => [room_revenue, tax_amount]]`
2. `ServiceProductOrderDetail::getDatewiseServiceRevenue($params)` — returns `[ts => float]`
3. `HotelBookingDetail::getTotalDiscounts($params, granularity='day')` — returns `[ts => float]`
4. `HotelBookingDetail::getTotalBookings($params, granularity='day')` — returns `[ts => int]`
5. `HotelBookingDetail::getOccupiedRoomsForDiscreteDates($params)` — returns `[ts => int]`

PHP computes `net_revenue = room_revenue + service_revenue - discounts`, `total_collection = net_revenue + tax_amount`, per-day ADR, RevPAR, and occupancy %.

---

#### 5. Refund Report

**Business purpose:** All refunds processed in the period, used by finance to verify refund completeness and reconcile with payment gateway records.

**Columns (12):** Refund Date, Refund ID, Booking ID, Guest Name, Original Booking Amount, Refund Amount, Refund Method, Refund Status, Processed Date (—), Processed By (—), Refund Reason, Remarks (—)

**Data source:** `HotelBookingDetail::getCancellations($params, detailed_info=true)` — filtered in PHP to rows where `refunded_amount > 0`. Optional further filter by `refund_status` (OrderReturnState ID).

---

#### 6. Payment Report

**Business purpose:** Full log of payment transactions received. Used for reconciliation against bank/gateway statements and to audit payment methods.

**Columns (12):** Payment Date, Payment ID, Booking ID, Booking Ref., Guest Name, Payment Method, Payment Type, Currency, Amount, Payment Status (always "Success"), Transaction Reference, Received By (—)

**Data source:** `OrderPayment::getTotalPaidAmount($params, detailedInfo=true)`

SQL joins `order_payment` to `orders`, `customer`, `currency`, and `htl_booking_detail`. Returns one row per `id_order_payment`, grouped to avoid duplicates from multi-room orders.

---

#### 7. Tax Report

**Business purpose:** Per-booking, per-tax-rule breakdown for VAT/GST filing and tax reconciliation.

**Columns (9 per row):** Booking Date, Booking Ref., Guest Name, Revenue Source, Room Type, Taxable Amount, Tax Name, Tax Rate %, Tax Amount. Plus a summary section grouped by tax name.

**Data source:** `HotelBookingDetail::getTaxBreakdown($params)`

SQL chain: `htl_booking_detail` → `orders` (valid=1) → `customer` → `order_detail` → `order_detail_tax` → `tax` → `tax_lang`. Produces one row per booking × tax rule. PHP then builds a `tax_by_name` summary array aggregating `taxable_amount` and `tax_amount` per tax name.

---

#### 8. Outstanding Balance Report

**Business purpose:** All orders with unpaid balances, ordered by balance due descending. Used by the accounts team to chase payments.

**Columns (14):** Booking ID, Guest Name, Email, Phone, Room Type, Room No., Check-in, Check-out, Total Charges, Total Paid, Outstanding Balance, Days Overdue, Last Payment Date, Booking Status

**Data source:** `Order::getOutstandingBalance($params)`

SQL aggregates `SUM(op.amount)` per order, computes `balance_due = total_paid_tax_incl - SUM(payments)`, filters to `HAVING balance_due > 0.01`. `days_overdue = GREATEST(0, DATEDIFF(CURDATE(), hbd.date_to))`.

---

### Group: Occupancy & Availability (`group-occupancy.tpl`)

#### 9. Occupancy Report

**Business purpose:** Daily occupancy metrics across the property. Used by revenue managers to track room utilization and set pricing strategy.

**Columns (11):** Date, Total Rooms Inventory, Available Rooms, Rooms Booked (STATUS_ALLOTED), Rooms Occupied (STATUS_CHECKED_IN), Out of Order, Complimentary (—), Occupancy Rate %, ADR, RevPAR, Total Room Revenue

**Data assembly:** Five data sources keyed by Unix timestamp:
1. `HotelRoomInformation::getOccupancyData($params)` — aggregate room/night counts
2. `HotelBookingDetail::getOccupiedRoomsForDiscreteDates($params)` — all occupied (any status)
3. Same with `id_status = STATUS_ALLOTED` for "booked"
4. Same with `id_status = STATUS_CHECKED_IN` for "checked-in"
5. `HotelRoomInformation::getAvailableRoomsForDiscreteDates($params)` — available rooms per day

`out_of_order = max(0, total_rooms - occupied - available)`. RevPAR and occupancy % computed in PHP.

---

#### 10. Room Type Availability Report

**Business purpose:** Per-room-type, per-day inventory breakdown. Used by reservations staff to quickly see which room types have space on which dates.

**Columns (8):** Date, Room Type, Total Rooms, Rooms Booked, Out of Order / Maintenance, Rooms Available, Rate (—), Occupancy %. Date subtotal rows are rendered in the template.

**Data source:** `HotelRoomInformation::getAvailabilityReport($params)`

Uses two SQL queries (room type inventory + active bookings in range) then a nested PHP loop iterating dates × room types. Booked count is computed via in-memory set of `id_room` values active on each day. Returns a flat array; the template renders date subtotal rows.

---

#### 11. Room Status Report

**Business purpose:** Point-in-time snapshot of every room's current occupancy state. Used by housekeeping and front desk at the start of each shift.

**Columns (7):** Room No., Room Type, Floor, Status (Vacant / Occupied / Under Maintenance), Housekeeping Status (—), Current Guest, Check-out Date

**Data source:** `HotelRoomInformation::getRoomCurrentStatus($params)`

Joins `htl_room_information` to `product`, `product_lang`, `htl_branch_info`, and LEFT JOIN `htl_booking_detail` (date-current window) and `customer`. The `floor` column was added in this refactor. A summary KPI row (Total / Available / Occupied / OOO) is computed in the template.

---

#### 12. Room Type Performance Report

**Business purpose:** Revenue and occupancy efficiency per room type, across hotels. Used by management to compare which room types drive the most revenue and identify underperforming inventory.

**Columns (14):** Room Type, Hotel, Total Rooms, Total Room Nights Available, Total Room Nights Sold, Occupancy Rate %, Total Price excl Tax, Tax Amount, Total Revenue, ADR, RevPAR, Cancellation Count, No-Show Count, Avg Length of Stay

**Data source:** `HotelRoomType::getRoomTypePerformance($params)`

One SQL query with conditional aggregation: `COUNT(CASE WHEN is_cancelled=0 ...)` for bookings, `COUNT(CASE WHEN is_cancelled=1 ...)` for cancellations, `COUNT(CASE WHEN id_status=1 AND date_from < CURDATE() ...)` for no-shows. PHP post-processes each row to compute `total_revenue`, `total_nights_available`, `adr`, `revpar`, `occupancy_pct`, `avg_los`.

---

### Group: Channels (`group-channels.tpl`)

#### 13. Booking Source Report

**Business purpose:** Revenue and booking volume breakdown by channel (Online/System vs Walk-in/Admin). Used to evaluate direct vs walk-in business mix and track cancellation rates by channel.

**Columns (14):** Booking Source, Total Bookings, Nights Sold, Room Revenue excl Tax, Extra Services Revenue (—), Discount Amount, Refund Amount, Tax Amount, Total Collection, Net Revenue, Cancellations, Cancel Rate %, ADR, Contribution %

**Data source:** `HotelBookingDetail::getBookings($params, group_by='channel')`

Uses a **two-level aggregation** pattern to avoid double-counting order-level discounts and refunds across multi-room bookings:
- **Inner query:** groups by `id_order`, summing room-level revenue and using `MAX()` for order-level discount and refund amounts
- **Outer query:** groups by `booking_type`, summing the per-order values

PHP post-processes each row to add `channel_label`, `tax_amount`, `adr`, `contribution_pct`, and `cancel_rate_pct`.

#### 14. Payment Methods Report

**Business purpose:** Booking and revenue breakdown by payment method/gateway. Assists finance teams in reconciliation and helps identify the most-used payment channels.

**Data source:** `HotelBookingDetail::getBookings($params, group_by='payment_method')`

Groups by `o.payment` and `o.module`, counting distinct orders and summing revenue.

---

### Group: Guests & Services (`group-guests.tpl`)

#### 15. Services / Extra Services Report

**Business purpose:** Itemized log of all add-on services purchased. Used by the F&B and services team to track ancillary revenue.

**Columns (11):** Date, Booking Ref. No., Guest Name, Room No., Service Name, Service Category, Quantity, Unit Price, Total Price excl Tax, Tax Amount, Grand Total

**Data source:** `ServiceProductOrderDetail::getTotalServiceRevenue($params, detailedInfo=true)`

`service_category` is resolved via `product.id_category_default` joined to `category_lang.name` by `id_lang`. `tax_amount = (total_price_tax_incl - total_price_tax_excl) / conversion_rate`. `unit_price = unit_price_tax_excl / conversion_rate`.

**Footer totals:** total excl tax, total tax, grand total.

---

#### 16. Guest Directory

**Business purpose:** Aggregate guest profile list with lifetime stay and revenue data. Used by CRM and marketing to identify VIPs, returning guests, and segment the guest base.

**Columns (17):** Guest ID, Guest Name, Email, Phone, Country, State, City, Company, VAT Number, Address, Postcode, Guest Type (New/Returning), Total Stays, Total Nights, Last Stay Date, Lifetime Revenue, Avg Spend Per Stay

**Filters:** Guest Type (`new` = total_stays == 1, `returning` = total_stays > 1) — applied in PHP after the query.

**Data source:** `HotelBookingDetail::getGuestDirectory($params)`

Groups by `id_customer`. Aggregates `COUNT(DISTINCT id_order)` for total stays, `SUM(DATEDIFF(...))` for nights, and `SUM(total_price_tax_incl / conversion_rate)` for lifetime revenue. Address data comes from the customer's most recent non-deleted address (subquery `MAX(id_address)`). Date range is applied as a `HAVING last_stay BETWEEN ...` filter (not a WHERE), so lifetime totals reflect all-time data for guests active in the period.

---

### Group: Property & Summary (`group-property.tpl`)

#### 17. Daily Summary

**Business purpose:** One row per day condensing all key metrics. Used in morning briefings and management dashboards for a quick operational overview.

**Columns (12):** Date, Total Rooms, Rooms Sold, Occupancy %, ADR, RevPAR, Total Revenue, Arrivals, Departures, In-house Guests, Cancellations, No-Shows (—)

**Data assembly:** Ten separate date-keyed data sources merged in a PHP date-walk loop (`while $current <= $dateTo`): `getDatewiseBookings`, `getDatewiseArrivals`, `getDatewiseDepartures`, `getDatewiseCancellations`, `getDatewiseOccupancyRate`, `getDatewiseAverageDailyRate`, `getDatewiseRevPAR`, `getDatewiseRoomRevenue`, `getOccupiedRoomsForDiscreteDates` (all statuses), `getOccupiedRoomsForDiscreteDates` (STATUS_CHECKED_IN only).

---

#### 18. Hotel Comparison

**Business purpose:** Side-by-side performance comparison of all hotel properties. Used by multi-property owners and group management to evaluate each property's contribution.

**Columns (15):** Hotel Name, Total Rooms, Rooms Sold, Occupancy %, Room Revenue, Extra Service Revenue, Gross Revenue, ADR, RevPAR, Total Bookings, Total Cancellations, Cancel Rate %, No-Shows, Avg LOS, Outstanding Balance

**Data assembly:** Iterates `HotelBranchInformation::hotelsNameAndId()`. For each hotel, makes individual scalar calls: `getTotalBookings`, `getTotalCancellations`, `getTotalRoomNights`, `getTotalRoomRevenue`, `ServiceProductOrderDetail::getTotalServiceRevenue`, `Order::getOutstandingBalance`, plus derived KPIs (`getOccupancyRate`, `getAverageDailyRate`, `getRevPAR`, `getTotalNoShows`). Each call passes `id_hotel` restricted to the current property.

**Note:** This pattern issues O(n × k) queries where n = number of hotels and k = number of KPI methods. For installations with many hotel branches, this report will be slow.

---

#### 19. Out of Order Rooms

**Business purpose:** All rooms with disable-date records overlapping the selected period. Used by maintenance and management to track room availability loss.

**Columns (13):** Room No., Floor, Room Type, OOO Status, Reason, Start Date, Expected End Date, Actual End Date (—), Duration Days, Current Status (Active/Resolved from dates), Marked By (—), Resolved By (—), Est. Revenue Loss (—)

**Data source:** `HotelRoomDisableDates::getDisabledRooms($params)`

SQL joins `htl_room_disable_dates` to `htl_room_information` (including `hri.floor`) → `product` → `product_lang` → `htl_branch_info`. Filtered by `date_from < dateTo AND date_to > dateFrom`. `disabled_days = DATEDIFF(date_to, date_from)`.

---

## Data Architecture

### Primary tables and class ownership

| Primary table                       | Owning class                     | Used for                                   |
|-------------------------------------|----------------------------------|--------------------------------------------|
| `htl_booking_detail`                | `HotelBookingDetail`             | All booking, revenue, channel, guest queries |
| `htl_room_information`              | `HotelRoomInformation`           | Room inventory, availability, room status  |
| `htl_room_type`                     | `HotelRoomType`                  | Room type performance                      |
| `htl_room_disable_dates`            | `HotelRoomDisableDates`          | Out of order / maintenance periods         |
| `service_product_order_detail`      | `ServiceProductOrderDetail`      | Ancillary services revenue                 |
| `order_payment`                     | `OrderPayment`                   | Payment transactions                       |
| `orders`                            | `Order`                          | Outstanding balance                        |

All SQL lives in the class that owns the primary `FROM` table. No query is written in the module's main PHP file or in templates.

### Common `$params` array keys

All data-fetching methods accept an array. Keys used across most methods:

| Key            | Type              | Default | Meaning                                 |
|----------------|-------------------|---------|-----------------------------------------|
| `date_from`    | string (Y-m-d)    | —       | Range start                             |
| `date_to`      | string (Y-m-d)    | `date_from` | Range end                           |
| `id_hotel`     | int or false      | false   | 0/false = all hotels                    |
| `id_product`   | int               | 0       | 0 = all room types                      |
| `id_lang`      | int               | 0       | 0 = resolved from Context               |
| `detailed_info`| bool              | false   | Returns expanded column set             |
| `group_by`     | string or false   | false   | `'channel'`, `'payment_method'`         |
| `granularity`  | string or false   | false   | `'day'`, `'month'` for time-series      |

Every `id_lang = 0` is resolved inside the method via `Context::getContext()->language->id`.

### Two-level aggregation (channel report)

The channel/booking-source report uses a subquery pattern to prevent double-counting. A booking for 3 rooms in a single order has one `o.total_discounts_tax_excl` value that must appear only once in the channel total. The inner query groups by `id_order` (using `MAX()` for order-level fields), and the outer query groups by `booking_type`. This pattern must be preserved if the channel report SQL is modified.

### Availability report loop

`HotelRoomInformation::getAvailabilityReport` uses two SQL queries followed by a PHP nested loop (dates × room types). Bookings are pre-loaded into a `$bookingsByProduct` map indexed by `id_product`. For each date, it checks each room's booking window using `date_from < $next AND date_to > $current`. This approach avoids N+1 queries but processes all bookings in PHP memory.

---

## Smarty Variable Conventions

All Smarty assignments use flat, top-level variables (no nested wrapper arrays). Templates access variables directly:

| Smarty variable        | Group / Report               | Type          |
|------------------------|------------------------------|---------------|
| `$reservations`        | bookings/reservation         | array of rows |
| `$reservation_totals`  | bookings/reservation         | array         |
| `$cancellations`       | bookings/cancellation        | array of rows |
| `$no_shows`            | bookings/no-show             | array of rows |
| `$daily_rows`          | revenue/revenue, occupancy, property/daily-summary | array of rows |
| `$revenue_totals`      | revenue/revenue              | array         |
| `$refunds`             | revenue/refund               | array of rows |
| `$payments`            | revenue/payment              | array of rows |
| `$tax_rows`            | revenue/tax                  | array of rows |
| `$tax_by_name`         | revenue/tax                  | array         |
| `$tax_totals`          | revenue/tax                  | array         |
| `$outstanding`         | revenue/outstanding          | array of rows |
| `$occupancy_data`      | occupancy/occupancy          | array         |
| `$availability_rows`   | occupancy/availability       | array of rows |
| `$rooms`               | occupancy/room-status        | array of rows |
| `$perf_rows`           | occupancy/room-perf          | array of rows |
| `$source_rows`         | channels/source              | array of rows |
| `$payment_rows`        | channels/payment-method      | array of rows |
| `$service_rows`        | guests/services              | array of rows |
| `$guests`              | guests/guest-directory       | array of rows |
| `$hotel_rows`          | property/hotel-comparison    | array of rows |
| `$oo_rows`             | property/out-of-order        | array of rows |

---

## CSV Export

Each report group has a private export method in the module class. Exports are triggered by `?export=1` in the URL. The export method calls the same data-fetching functions as the HTML report, streams CSV headers, writes rows via `fputcsv()`, and calls `exit`.

**Known gap:** CSV exports use a reduced column set compared to the HTML templates. The export column definitions were not updated as part of this refactor and do not match the full column specifications above.

| Report              | HTML columns | CSV columns | Gap                                            |
|---------------------|-------------|-------------|------------------------------------------------|
| Reservation         | 20          | 18          | Missing: Payment Status; balance_due is in the data but not exported |
| Revenue             | 13          | 8           | Missing: Total Collection, Net Revenue, ADR, RevPAR, Occupancy % |
| Payment             | 12          | 9           | Missing: Payment Type, Currency, Payment Status |
| Tax                 | 9+summary   | 11 (flat)   | No per-tax-rule rows; uses booking-level totals only |
| Occupancy           | 11          | 6           | Missing: Rooms Booked, Rooms Occupied, ADR, RevPAR, Total Room Revenue |
| Room Type Performance | 14        | 9           | Missing: Total Revenue, Total Nights Available, Cancel Count, No-Show Count, Avg LOS |
| Guest Directory     | 17          | 8           | Missing: phone, address, country/state/city, VAT, company, lifetime revenue |
| Daily Summary       | 12          | 9           | Missing: Total Rooms, Rooms Sold, In-house Guests |

---

## N/A Columns (shown as — in UI)

These columns appear in the HTML templates as placeholder dashes. The data is not currently stored in the database or is not derivable from existing fields:

| Column                  | Report(s)                         | Reason not implemented                              |
|-------------------------|-----------------------------------|-----------------------------------------------------|
| Cancellation Remark     | Cancellation                      | No separate remark field — only `question` (reason) |
| Penalty Charged         | No-Show                           | Penalty rules not enforced in booking engine        |
| Housekeeping Status     | Room Status                       | No housekeeping module; status not tracked in DB    |
| Complimentary Rooms     | Occupancy                         | No complimentary booking type in current schema     |
| Rate (per-date)         | Availability                      | Dynamic pricing per date not stored per-room-day    |
| Processed Date/By       | Refund                            | Not recorded in `order_return`                      |
| Received By             | Payment                           | Not recorded in `order_payment`                     |
| Actual End Date         | Out of Order                      | `htl_room_disable_dates` has no resolution date field |
| Marked By / Resolved By | Out of Order                      | Not recorded in `htl_room_disable_dates`            |
| Est. Revenue Loss       | Out of Order                      | Would require rate data per disabled date range     |
| No-Shows (daily)        | Daily Summary                     | `getTotalNoShows` counts by date_from range; not wired into the daily-summary loop |

---

## New and Modified Methods (Branch gli-2801)

### `HotelBookingDetail`

| Method                                  | Change                                                   |
|-----------------------------------------|----------------------------------------------------------|
| `getBookings(array $params)`            | Added `detailed_info=true` branch (balance_due, id_status, booking_type, created_by); added `group_by='channel'` two-level aggregation branch; added `group_by='payment_method'` branch |
| `getCancellations(array $params)`       | Rewritten with `INNER JOIN order_return` (date filter on `orr.date_add`); added `detailed_info` parameter adding refund_status, cancellation_reason, refunded_amount, booking_date |
| `getTaxBreakdown(array $params)`        | New method — joins to `order_detail`, `order_detail_tax`, `tax`, `tax_lang`; one row per booking × tax rule |
| `getGuestDirectory(array $params)`      | New method — aggregates per customer: total_stays, total_nights, lifetime_revenue, avg_spend_per_stay, last_stay |
| `getOccupiedRoomsForDiscreteDates()`    | Extended with optional `id_status` param to filter by STATUS_ALLOTED or STATUS_CHECKED_IN |
| `getTotalRoomNights(array $params)`     | New method — `SUM(DATEDIFF(date_to, date_from))` for room nights sold in range |
| `getTotalNoShows(array $params)`        | New method — count of STATUS_ALLOTED rooms with `date_from < CURDATE()` in range |

### `HotelRoomInformation`

| Method                                  | Change                                                   |
|-----------------------------------------|----------------------------------------------------------|
| `getAvailabilityReport(array $params)`  | New method — two SQL queries + PHP loop producing per-room-type, per-day availability array |
| `getRoomCurrentStatus(array $params)`   | Extended: added `hri.floor` to SELECT                    |

### `HotelRoomType`

| Method                                  | Change                                                   |
|-----------------------------------------|----------------------------------------------------------|
| `getRoomTypePerformance(array $params)` | Extended SQL: added `cancel_count` (is_cancelled=1), `no_show_count` (alloted + past checkout); PHP post-processing adds total_revenue, total_nights_available, revpar, avg_los |

### `HotelRoomDisableDates`

| Method                                  | Change                                                   |
|-----------------------------------------|----------------------------------------------------------|
| `getDisabledRooms(array $params)`       | Extended: added `hri.floor` to SELECT                    |

### `ServiceProductOrderDetail`

| Method                                  | Change                                                   |
|-----------------------------------------|----------------------------------------------------------|
| `getTotalServiceRevenue(array $params, $detailedInfo)` | Added `$detailedInfo` parameter; detailed branch adds `unit_price`, `service_category` (via `category_lang`), `tax_amount`; accepts `id_lang` |

### `Order` (core file)

| Method                                  | Change                                                   |
|-----------------------------------------|----------------------------------------------------------|
| `getOutstandingBalance(array $params)`  | Extended: added `c.email`, `a.phone`, `hbd.id_status`, `days_overdue`, `last_payment_date` to SELECT |

### `OrderPayment` (core file)

| Method                                  | Change                                                   |
|-----------------------------------------|----------------------------------------------------------|
| `getTotalPaidAmount(array $params, $detailedInfo)` | Added `$detailedInfo` parameter; detailed branch returns full payment row set with `payment_type`, `currency_iso` |

---

## File Locations

| File                                                                                     | Role                                      |
|------------------------------------------------------------------------------------------|-------------------------------------------|
| `modules/qlohotelreports/qlohotelreports.php`                                            | Module class, hook handler, CSV export    |
| `modules/qlohotelreports/views/templates/hook/admin-stats-modules.tpl`                   | Outer shell template (sidebar + include)  |
| `modules/qlohotelreports/views/templates/hook/group-bookings.tpl`                        | Reservations, Cancellations, No-Shows, Arrivals, Departures, In-House |
| `modules/qlohotelreports/views/templates/hook/group-revenue.tpl`                         | Revenue, Refund, Payment, Tax, Outstanding |
| `modules/qlohotelreports/views/templates/hook/group-occupancy.tpl`                       | Occupancy, Availability, Room Status, Room Type Performance |
| `modules/qlohotelreports/views/templates/hook/group-channels.tpl`                        | Booking Source, Payment Methods           |
| `modules/qlohotelreports/views/templates/hook/group-guests.tpl`                          | Services, Guest Directory                 |
| `modules/qlohotelreports/views/templates/hook/group-property.tpl`                        | Daily Summary, Hotel Comparison, Out of Order |
| `modules/qlohotelreports/views/css/qlohotelreports.css`                                  | Report-specific styles                    |
| `modules/qlohotelreports/views/js/qlohotelreports.js`                                    | Report-specific JS                        |
| `modules/hotelreservationsystem/classes/HotelBookingDetail.php`                          | Primary query class for booking data      |
| `modules/hotelreservationsystem/classes/HotelRoomInformation.php`                        | Room inventory and availability queries   |
| `modules/hotelreservationsystem/classes/HotelRoomType.php`                               | Room type performance queries             |
| `modules/hotelreservationsystem/classes/HotelRoomDisableDates.php`                       | Out-of-order room queries                 |
| `modules/hotelreservationsystem/classes/ServiceProductOrderDetail.php`                   | Ancillary services revenue queries        |
| `classes/order/Order.php`                                                                | Outstanding balance query (core)          |
| `classes/order/OrderPayment.php`                                                         | Payment transaction query (core)          |
