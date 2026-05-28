# Cancellation & Refund: Full Implementation Analysis

**Branch:** `gli-2801`  
**Updated:** 2026-05-26  
**Scope:** `HotelBookingDetail` functions · `qlohotelreports` · `dashguestcycle` · `dashperformance` · `AdminStatsController` (reference)

---

## 1. The Two Flags: `is_refunded` vs `is_cancelled`

Both flags live on `htl_booking_detail`. They have distinct meanings and are set via different code paths.

| Flag | Value | Meaning |
|---|---|---|
| `is_refunded` | 0 | Active booking — room is occupied/reserved |
| `is_refunded` | 1 | Room has been freed: refund was processed OR order moved to a cancellation state |
| `is_cancelled` | 0 | Not a zero-payment cancellation |
| `is_cancelled` | 1 | Cancelled with no payment made (free cancellation) OR order status = `PS_OS_CANCELED` |

### When `is_refunded` is set to 1

**Path A — Admin processes refund via `processRefundInBookingTables()`:**
Called from:
- `AdminOrdersController` (admin accepts refund)
- `AdminOrderRefundRequestsController` (state change triggers processing)
- `OrderDetailController` front (customer initiates and it auto-processes)

This function also sets `is_cancelled = 1` conditionally: only when the order has **no cart rules AND no payment** (pure free cancellation).

**Path B — Order status hook `hookActionOrderStatusPostUpdate`:**
In `hotelreservationsystem.php`, when an order moves to any "free room" state (from `getOrderStatusToFreeBookedRoom()`), it calls `updateOrderRefundStatus($idOrder, ..., is_refunded=1)`. If the new state is specifically `PS_OS_CANCELED`, it also sets `is_cancelled = 1`.

### Possible flag combinations

| `is_refunded` | `is_cancelled` | Scenario |
|---|---|---|
| 0 | 0 | Active booking |
| 1 | 0 | Paid booking refunded (had payment) |
| 1 | 1 | Free/no-payment cancellation, or admin set PS_OS_CANCELED |

`is_refunded = 0, is_cancelled = 1` should not occur in normal flow.

---

## 2. Table Relationships

```
order_return
  ├─ id_order_return  (PK)
  ├─ id_order         → orders.id_order
  ├─ state            (refund request state: pending → processing → completed)
  ├─ refunded_amount
  ├─ payment_mode
  ├─ question         (cancellation reason)
  └─ date_add         (when refund request was created)

order_return_detail
  ├─ id_order_return  → order_return.id_order_return
  ├─ id_htl_booking   → htl_booking_detail.id
  └─ refunded_amount  (per-booking refund amount)

htl_booking_detail
  ├─ id               (PK)
  ├─ id_order         → orders.id_order
  ├─ is_refunded      (1 = room freed/refunded)
  ├─ is_cancelled     (1 = no-payment cancellation)
  └─ date_add         (when booking was created)
```

The link `order_return_detail.id_htl_booking → htl_booking_detail.id` is the bridge between the refund request and the specific room booking that was refunded.

---

## 3. Current Functions in HotelBookingDetail

### 3.1 `getCancellations(array $params)`

**Purpose:** Returns individual room-level cancellation/refund records for a date range.

**Core SQL logic:**
```sql
SELECT hbd.*, orr.*, customer.name, order_state.name
FROM order_return orr
  INNER JOIN order_return_detail ord
      ON ord.id_order_return = orr.id_order_return
  INNER JOIN htl_booking_detail hbd
      ON hbd.id = ord.id_htl_booking AND hbd.is_refunded = 1
  INNER JOIN orders o ON o.id_order = orr.id_order
  LEFT JOIN order_state_lang osl ON osl.id_order_state = o.current_state
  LEFT JOIN order_return_state_lang orsl ON orsl.id_order_return_state = orr.state
  LEFT JOIN customer c ON c.id_customer = hbd.id_customer
WHERE orr.date_add BETWEEN dateFrom AND dateTo
GROUP BY ord.id_htl_booking
ORDER BY orr.date_add DESC
```

**Key conditions:**
- Date filter on `orr.date_add` (refund request creation date)
- `hbd.is_refunded = 1` via INNER JOIN — only completed/processed refunds
- `GROUP BY ord.id_htl_booking` — one row per room booking

**With `detailed_info = true`** (used by reports): also returns `cancellation_reason`, `refunded_amount`, `refund_method`, `refund_status`, `cancellation_date`, `booking_date`, `total_price_tax_incl`.

**Callers:**

| Caller | `detailed_info` | Purpose |
|---|---|---|
| `qlohotelreports` — cancellation report | true | Full cancellation list for date range |
| `qlohotelreports` — refund report | true | Same data, then filtered by `refunded_amount > 0` and optionally by refund status |
| `qlohotelreports` — CSV export (cancellations) | true | Export same data |
| `qlohotelreports` — CSV export (refunds) | true | Export same data |
| `dashguestcycle::getCancellationsTableContentsByDate()` | false (default) | Today's cancellation list on dashboard |

---

### 3.2 `getTotalCancellations(array $params)`

**Purpose:** Count of distinct orders that had cancellations in the date range.

**Core SQL:**
```sql
SELECT COUNT(DISTINCT orr.id_order)
FROM order_return orr
  INNER JOIN order_return_detail ord ON ord.id_order_return = orr.id_order_return
  INNER JOIN htl_booking_detail hbd
      ON hbd.id = ord.id_htl_booking AND hbd.is_refunded = 1
WHERE orr.date_add BETWEEN dateFrom AND dateTo
```

**Key conditions:** Same as `getCancellations()` — counts orders, requires `is_refunded = 1`.

**Callers:**

| Caller | Purpose |
|---|---|
| `qlohotelreports` — daily summary report | Cancellations-per-day column |
| `qlohotelreports` — hotel comparison report | Per-hotel cancellation count |
| `qlohotelreports` — CSV exports (same two reports) | Same |
| `dashguestcycle` — `dgc_cancelled_bookings` KPI | Today's cancellation count |

---

### 3.3 `getDatewiseCancellations(array $params)`

**Purpose:** Returns `[timestamp => count]` array — cancellations per day across a date range.

**Logic:** Loops `dateFrom` to `dateTo` day by day, runs `getTotalCancellations()` logic for each date, builds array keyed by `strtotime($date)`.

**Callers:**

| Caller | Purpose |
|---|---|
| `qlohotelreports` — daily summary report | Feeds the cancellations-per-day row |
| `qlohotelreports` — CSV export (daily summary) | Same |

---

### 3.4 `getCancellationRate(array $params)`

**Purpose:** Returns cancellation rate as percentage: `(cancelled / total) * 100`.

**Core SQL (two queries):**
```sql
-- Total bookings created in date range
SELECT COUNT(id) FROM htl_booking_detail
WHERE date_add BETWEEN dateFrom AND dateTo

-- Cancelled/refunded bookings created in same range
SELECT COUNT(id) FROM htl_booking_detail
WHERE is_refunded = 1 AND date_add BETWEEN dateFrom AND dateTo
```

**Key difference from the other three functions:** Uses `hbd.date_add` (booking creation date), NOT `order_return.date_add`. This means "of all bookings made in this period, what % were eventually refunded."

**Callers:**

| Caller | Purpose |
|---|---|
| `dashperformance` — `dp_cancellation_rate` KPI | Cancellation rate on performance dashboard |
| `qlohotelreports` — room type performance report | Per room-type cancel count (uses cancel_count from a different function) |

---

## 4. AdminStatsController: The Original Reference

These are the pre-existing functions in `AdminStatsController.php` that our functions were modelled on or are intended to replace.

### 4.1 `AdminStatsController::getCancellationRate($dateFrom, $dateTo, $idHotel)`

```sql
-- Total
SELECT COUNT(id) FROM htl_booking_detail
WHERE date_add BETWEEN dateFrom AND dateTo

-- Cancelled
SELECT COUNT(id) FROM htl_booking_detail
WHERE is_refunded = 1 AND date_add BETWEEN dateFrom AND dateTo
```

**Comparison with `HotelBookingDetail::getCancellationRate()`:**

| Aspect | AdminStatsController | HotelBookingDetail (ours) |
|---|---|---|
| Date field | `hbd.date_add` | `hbd.date_add` ✓ same |
| Cancelled condition | `is_refunded = 1` | `is_refunded = 1` ✓ same |
| Hotel filter | `HotelBranchInformation::addHotelRestriction()` | same ✓ |
| Return type | raw float | raw float ✓ same |

**Result: identical logic.** Our `HotelBookingDetail::getCancellationRate()` is a clean port.

---

### 4.2 `AdminStatsController::getCancellationsInfoByDate($date, $idHotel)`

```sql
SELECT orr.id_order_return, orr.id_customer, hbd.room_num, hbd.id_product,
       hbd.room_type_name, o.with_occupancy, customer.name,
       hbd.id_hotel, hbd.hotel_name, SUM(guests), hbd.date_from, hbd.date_to, orr.id_order
FROM order_return orr
  LEFT JOIN order_return_detail ord ON ord.id_order_return = orr.id_order_return
  LEFT JOIN orders o ON o.id_order = orr.id_order
  LEFT JOIN htl_booking_detail hbd ON hbd.id = ord.id_htl_booking   -- LEFT JOIN, no is_refunded check
  LEFT JOIN customer c ON c.id_customer = orr.id_customer
WHERE orr.date_add BETWEEN date AND date
  AND orr.state = ORDER_RETRUN_FIRST_STATUS       -- PENDING/INITIAL state only
GROUP BY ord.id_htl_booking
ORDER BY orr.date_add DESC
```

**Comparison with `HotelBookingDetail::getCancellations()`:**

| Aspect | AdminStatsController | HotelBookingDetail (ours) |
|---|---|---|
| JOIN type on hbd | LEFT JOIN | INNER JOIN |
| `is_refunded` check | None | `hbd.is_refunded = 1` |
| `order_return.state` filter | `= FIRST_STATUS` (pending) | None (any state) |
| Returns | Pending refund requests | Completed/processed refunds |
| Date field | `orr.date_add` | `orr.date_add` ✓ same |
| Joins `orders` | YES | YES |
| Hotel filter | YES | YES |

**Semantic difference:** AdminStats shows *refund requests that just came in* (state = pending). Ours shows *refunds that have been processed* (`is_refunded = 1`). These are fundamentally different business questions.

---

## 5. Correctness Assessment

### 5.1 Is `getCancellations()` correct?

**For reports: YES.** The cancellation report and refund report both need completed/processed refunds. Showing pending requests in a report would be misleading (money hasn't moved yet).

**For dashboard (`dashguestcycle` today's view): ACCEPTABLE but different from original AdminStats.**
- Original AdminStats showed pending requests as they arrived (useful for front-desk: "we got 3 cancellation requests today")
- Our version shows processed refunds (useful for accounting: "3 refunds were completed today")
- For most hotel operations, processing happens same-day, so the practical difference is small
- The only gap: if admin hasn't processed today's requests yet, today's count on the dashboard will show 0

**Recommendation for future:** If a "pending refund requests" view is needed (front-desk use case), it should be a separate function using `orr.state = FIRST_STATUS` and LEFT JOIN without `is_refunded` check — do not modify the existing function.

### 5.2 Is `getTotalCancellations()` correct?

**YES** for all current callers. Counting distinct orders with processed refunds is the right metric for:
- Daily summary report (how many bookings were cancelled/refunded in this period)
- Hotel comparison (which hotel had more refunds)
- Dashboard KPI (today's processed cancellations)

**Potential edge case:** A single order with 3 rooms — all 3 rooms refunded → counts as 1 cancellation (DISTINCT `id_order`). This is correct for order-level counting. If room-level counting is ever needed, `GROUP BY ord.id_htl_booking` (like `getCancellations()`) would be used instead.

### 5.3 Is `getDatewiseCancellations()` correct?

**YES**, with a known performance tradeoff: the N+1 loop executes one query per day in the date range. For a 1-year range, that's 365 queries. Per the previous decision, this was kept intentionally (user confirmed). For reports covering a few weeks/months, it's acceptable.

### 5.4 Is `getCancellationRate()` correct?

**YES** and it exactly matches `AdminStatsController::getCancellationRate()`.

**Important semantic note:** This rate answers "of bookings MADE in this period, what % were eventually cancelled/refunded?" — not "of all time bookings, what % were cancelled in this period?" The denominator is bounded to the same date range as the numerator (both use `hbd.date_add`). This is the standard ADR-equivalent interpretation used in hotels.

---

## 6. The `is_refunded` vs `is_cancelled` Usage in Queries

Other parts of the codebase filter on these flags in specific ways. All new cancellation functions should be consistent:

| Use case | Correct filter |
|---|---|
| Room availability check | `is_refunded = 0 AND is_cancelled = 0` |
| Count active bookings | `is_refunded = 0` |
| Count all cancellations (incl. partial refunds) | `is_refunded = 1` |
| Count zero-payment/free cancellations only | `is_cancelled = 1` |
| Reports and dashboard cancellation counts | `is_refunded = 1` (via order_return join) |

`AdminStatsController::getDistinctRoomBookingsCount()` correctly uses `is_refunded = 0 AND is_cancelled = 0`. Our functions follow the same pattern.

---

## 7. Known Gaps and Future Risks

### Gap 1: Pending refund requests not surfaced

`getCancellations()` and `getTotalCancellations()` only return records where the refund has been **processed** (`is_refunded = 1`). If a customer or admin creates a refund request but the request sits in "pending" state, it will NOT appear in:
- The cancellation report
- The dashboard today's count

**Risk level:** Low for reports (correct behaviour). Medium for real-time dashboard if the hotel needs to see incoming requests immediately.

**If needed later:** Add a separate `getPendingCancellations()` function with `LEFT JOIN`, no `is_refunded` check, filter `orr.state = ORDER_RETRUN_FIRST_STATUS`.

### Gap 2: `getDatewiseCancellations()` N+1 loop

One DB query per day. For date ranges > 60 days, consider rewriting to a single aggregated query:
```sql
SELECT DATE(orr.date_add) as day, COUNT(DISTINCT orr.id_order) as count
FROM order_return orr
  INNER JOIN order_return_detail ord ON ...
  INNER JOIN htl_booking_detail hbd ON hbd.id = ord.id_htl_booking AND hbd.is_refunded = 1
WHERE orr.date_add BETWEEN dateFrom AND dateTo
GROUP BY DATE(orr.date_add)
```
Then map the result to the `[timestamp => count]` format. **Do not change now** — noted for future refactor.

### Gap 3: `getCancellationRate()` date semantics mismatch

`getCancellationRate()` uses `hbd.date_add` (booking date) while all other functions use `orr.date_add` (refund date). This is intentional and matches the industry standard for cancellation rate, but it means:
- If you filter Jan 1–31, the rate = (refunded bookings made in Jan) / (total bookings made in Jan)
- A booking made in December that was refunded in January does NOT appear
- A booking made in January that will be refunded in February does NOT appear yet

This is correct. Do not change `getCancellationRate()` to use `orr.date_add` — it would break the metric's meaning.

### Gap 4: Cancellations in `source` report

`qlohotelreports` channel/source report (`group-channels.tpl`) shows a `cancellations` column and `cancel_rate_pct`. These come from a separate query in the source report logic (in `qlohotelreports.php` around the booking source section). Verify this uses the same `order_return`+`is_refunded` approach and not an ad-hoc query. If it uses `orders.current_state IN (PS_OS_CANCELED, PS_OS_REFUND)` it would be inconsistent.

---

## 8. Summary: What Changed vs What Existed

| | Previous (pre-gli-2801) | Current (gli-2801) |
|---|---|---|
| `getCancellations` | Did not exist as standalone; used `AdminStatsController::getCancellationsInfoByDate()` | INNER JOIN + `is_refunded = 1` + any `orr.state` |
| `getTotalCancellations` | Used `orders.date_upd + current_state IN (CANCELED, REFUND)` | `order_return.date_add + is_refunded = 1` |
| `getDatewiseCancellations` | Used `orders.date_upd + current_state` per-day | `order_return.date_add + is_refunded = 1` per-day |
| `getCancellationRate` | Lived only in `AdminStatsController` | Ported to `HotelBookingDetail`, logic identical |

**Why the change was correct:**
- `orders.date_upd` changes on every status change (payment, packing, etc.) — not a reliable cancellation timestamp
- `order_return.date_add` is the authoritative timestamp for when a cancellation/refund request was initiated
- `is_refunded = 1` is set precisely when the room is freed, not just when the order state changes

---

## 9. Callers Reference Map

```
HotelBookingDetail::getCancellations()
    └─ qlohotelreports: cancellation report (detailed_info=true)
    └─ qlohotelreports: refund report (detailed_info=true, filtered by refunded_amount > 0)
    └─ qlohotelreports: CSV export — cancellations
    └─ qlohotelreports: CSV export — refunds
    └─ dashguestcycle::getCancellationsTableContentsByDate()  → dashboard today's table

HotelBookingDetail::getTotalCancellations()
    └─ qlohotelreports: daily summary report (per-hotel-per-day count)
    └─ qlohotelreports: hotel comparison report (per-hotel count)
    └─ qlohotelreports: CSV exports (same two)
    └─ dashguestcycle: dgc_cancelled_bookings KPI (today's count)

HotelBookingDetail::getDatewiseCancellations()
    └─ qlohotelreports: daily summary report (day-by-day array)
    └─ qlohotelreports: CSV export — daily summary

HotelBookingDetail::getCancellationRate()
    └─ dashperformance: dp_cancellation_rate KPI

AdminStatsController::getCancellationRate()  [UNCHANGED, legacy]
    └─ Used by core AdminStats dashboard (not our module)

AdminStatsController::getCancellationsInfoByDate()  [UNCHANGED, legacy]
    └─ Used by core AdminStats dashboard (shows PENDING requests, different semantics)
```
