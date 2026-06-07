# FEATURE: DYNAMIC ROOM HEADERS WITH NUMBERS AND REALTIME ASRAMA BADGE DECREASE

## 1. FRONTEND: ALPINE.JS BOX HEADER BINDING
- Open the form step 2 blade file where the room slots are rendered (shown in the uploaded image).
- Locate the `x-for` loop that displays the room boxes containing adult and child icon selectors.
- Update the room title element. Replace the static or simple index header `KAMAR 1` with a dynamic Alpine.js expression:
  `x-text="'KAMAR ' + (index + 1) + (availableRooms && availableRooms[index] ? ' (' + availableRooms[index] + ')' : '')"`
- Ensure that `availableRooms` is correctly populated as a flat array of strings (e.g., `["A-01", "A-02"]`) immediately after the date or room type is selected.

## 2. BACKEND: LANDING PAGE & ASRAMA BADGE QUERY FIX
- Open the controller responsible for rendering the room cards/asrama details page.
- Fix the query that calculates the available room badge count.
- The badge count MUST subtract any active bookings that are 'approved' or 'pending' for the current date range.
- If the calculated available room count hits 0:
  - Change the badge text to "Kamar Penuh" and style it with a red background.
  - Disable the "Booking Now" button (add `disabled` attribute and gray background class `bg-gray-400`).
  - Add an `@click` trigger to show a browser alert: "Maaf, semua kamar pada tipe ini sudah penuh untuk tanggal yang Anda pilih." if a user attempts to click the disabled button.