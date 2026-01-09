<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic Invoice Generator</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Editor Section */
        .editor-section {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 30px;
            margin-bottom: 20px;
        }

        .header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e5e7eb;
        }

        .header-bar h1 {
            color: #1f2937;
            font-size: 28px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: #3b82f6;
            color: white;
        }

        .btn-primary:hover {
            background: #2563eb;
        }

        .btn-success {
            background: #10b981;
            color: white;
        }

        .btn-success:hover {
            background: #059669;
        }

        .btn-danger {
            background: #ef4444;
            color: white;
            padding: 8px 12px;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        .btn-warning {
            background: #f59e0b;
            color: white;
        }

        .btn-warning:hover {
            background: #d97706;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 15px;
        }

        /* Search Section */
        .search-section {
            background: #f0f9ff;
            border: 2px solid #3b82f6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .search-bar {
            display: flex;
            gap: 10px;
            align-items: end;
        }

        .search-bar .form-group {
            flex: 1;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            display: none;
        }

        .alert.show {
            display: block;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #10b981;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #ef4444;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-size: 13px;
            font-weight: 500;
            color: #6b7280;
            margin-bottom: 6px;
        }

        .form-group input,
        .form-group select {
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            transition: border 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #3b82f6;
        }

        .form-group input:read-only {
            background: #f9fafb;
            color: #6b7280;
        }

        /* Room Items */
        .room-items {
            margin-bottom: 30px;
        }

        .room-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .room-card {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
        }

        .room-card-grid {
            display: grid;
            grid-template-columns: 1fr 2fr 1.5fr auto;
            gap: 10px;
            align-items: end;
        }

        /* Summary */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }

        .summary-item {
            background: #f9fafb;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #3b82f6;
        }

        .summary-label {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 5px;
        }

        .summary-value {
            font-size: 20px;
            font-weight: 700;
            color: #1f2937;
        }

        .loader {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .loader.show {
            display: block;
        }

        .spinner {
            border: 3px solid #f3f4f6;
            border-top: 3px solid #3b82f6;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .hidden {
            display: none;
        }

        /* ================= INVOICE PREVIEW - MATCHING Invoice.blade.php ================= */
        .invoice-preview {
            background: white;
            display: none;
        }

        .invoice-preview.active {
            display: block;
        }

        .preview-actions {
            margin-bottom: 20px;
            text-align: center;
        }

        /* A4 Page Styling */
        @page { size: A4 portrait; margin: 0; }

        .invoice-page {
            font-family: Arial, sans-serif;
            width: 210mm;
            min-height: 297mm;
            padding: 10mm;
            margin: 0 auto;
            background: #fff;
            font-size: 11pt;
            color: #333;
            position: relative;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        /* ================= HEADER ================= */
        .invoice-header {
            text-align: center;
            border-bottom: 1px solid #999;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .invoice-header h1 {
            font-size: 16pt;
            color: #2c3e50;
            margin-bottom: 6px;
        }

        .address {
            font-size: 10pt;
            line-height: 1.4;
        }

        /* ================= DETAILS ================= */
        .details-box {
            display: flex;
            gap: 20px;
            margin-bottom: 25px;
            border-bottom: 1px solid #999;
            padding-bottom: 10px;
        }

        .details-column {
            width: 50%;
            font-size: 10pt;
        }

        .details-column.right {
            text-align: right;
        }

        .detail-row {
            margin-bottom: 6px;
        }

        .details-column.right .detail-row {
            justify-content: flex-end;
            gap: 10px;
        }

        .label {
            min-width: 130px;
            font-weight: 600;
            color: #555;
        }

        .label::after {
            content: " : ";
        }

        .value {
            color: #000;
        }

        /* ================= TABLE ================= */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
            margin-top: 10px;
            margin-bottom: 20px;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #ddd;
            padding: 7px;
        }

        .items-table th {
            background: #f4f4f4;
        }

        .items-table td.right {
            text-align: right;
        }

        .items-table td.center {
            text-align: center;
        }

        /* ================= BOTTOM SECTION ================= */
        .bottom-section {
            margin-top: 30px;
            page-break-inside: avoid;
        }

        .two-column-row {
            display: flex;
            gap: 15px;
            border-top: 1px dashed #999;
            padding-top: 12px;
            align-items: stretch;
        }

        .left-column,
        .right-column {
            width: 50%;
        }

        /* LEFT SPLIT */
        .left-split {
            display: flex;
            flex-direction: column;
            height: 100%;
            font-size: 9.5pt;
        }

        .left-top {
            flex: 1;
            padding-bottom: 10px;
            border-bottom: 1px solid #ccc;
            line-height: 1.5;
        }

        .left-bottom {
            flex: 1;
            padding-top: 10px;
            font-size: 10pt;
        }

        /* SUMMARY */
        .summary-box {
            font-size: 10pt;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
        }

        .summary-row strong {
            font-size: 11pt;
        }

        /* ================= SIGNATURE ================= */
        .signatures {
            margin-top: 25px;
            width: 100%;
            display: flex;
            justify-content: space-between;
        }

        .signature {
            width: 45%;
            text-align: center;
            font-size: 10pt;
        }

        .signature .line {
            border-top: 1px solid #333;
            margin-top: 35px;
            padding-top: 5px;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }
            .editor-section,
            .preview-actions {
                display: none !important;
            }
            .invoice-page {
                box-shadow: none;
                page-break-after: always;
            }
        }
    </style>
</head>
<body>

    <!-- Editor Section -->
    <div class="container">
        <div class="editor-section" id="editorSection">
            <div class="header-bar">
                <h1>📄 Dynamic Invoice Generator</h1>
                <button class="btn btn-success" onclick="showPreview()">👁️ Preview Invoice</button>
            </div>

            <!-- Alert Messages -->
            <div id="alertMessage" class="alert"></div>

            <!-- Search Section -->
            <div class="search-section">
                <div class="section-title">🔍 Search Booking</div>
                <div class="search-bar">
                    <div class="form-group">
                        <label>Enter Booking Number</label>
                        <input type="text" id="searchBookingNumber" placeholder="e.g., BK-2026-001">
                    </div>
                    <button class="btn btn-warning" onclick="searchBooking()">🔍 Search</button>
                    <button class="btn btn-secondary" onclick="resetForm()">🔄 Reset</button>
                </div>
                <div class="loader" id="loader">
                    <div class="spinner"></div>
                    <p style="margin-top: 10px; color: #6b7280;">Searching...</p>
                </div>
            </div>

            <!-- Customer Details -->
            <div class="section-title">Customer Details</div>
            <div class="form-grid">
                <div class="form-group">
                    <label>Customer Name</label>
                    <input type="text" id="customerName" value="John Doe" oninput="updatePreview()">
                </div>
                <div class="form-group">
                    <label>Mobile Number</label>
                    <input type="text" id="customerMobile" value="9876543210" oninput="updatePreview()">
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <input type="text" id="customerAddress" value="123 Main Street, Mumbai" oninput="updatePreview()">
                </div>
                <div class="form-group">
                    <label>GST Number</label>
                    <input type="text" id="gstNumber" value="27XXXXX1234X1Z5" oninput="updatePreview()">
                </div>
                <div class="form-group">
                    <label>Booking Number</label>
                    <input type="text" id="bookingNumber" value="BK-2026-001" readonly>
                </div>
                <div class="form-group">
                    <label>Registration No</label>
                    <input type="text" id="registrationNo" value="REG-12345" oninput="updatePreview()">
                </div>
            </div>

            <!-- Booking Details -->
            <div class="section-title">Booking Details</div>
            <div class="form-grid">
                <div class="form-group">
                    <label>Check-In Date & Time</label>
                    <input type="datetime-local" id="checkIn" value="2026-01-01T14:00" oninput="updatePreview()">
                </div>
                <div class="form-group">
                    <label>Check-Out Date & Time</label>
                    <input type="datetime-local" id="checkOut" value="2026-01-03T12:00" oninput="updatePreview()">
                </div>
                <div class="form-group">
                    <label>Number of Nights</label>
                    <input type="text" id="nights" value="2" readonly>
                </div>
                <div class="form-group">
                    <label>Payment Mode</label>
                    <select id="paymentMode" onchange="updatePreview()">
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="upi">UPI</option>
                        <option value="bank_transfer">Bank Transfer</option>
                    </select>
                </div>
            </div>

            <!-- Rooms -->
            <div class="room-items">
                <div class="room-header">
                    <div class="section-title">Rooms</div>
                    <button class="btn btn-primary" onclick="addRoom()">+ Add Room</button>
                </div>
                <div id="roomsContainer"></div>
            </div>

            <!-- Financial Details -->
            <div class="section-title">Financial Details</div>
            <div class="form-grid">
                <div class="form-group">
                    <label>Discount Amount (₹)</label>
                    <input type="number" id="discountAmount" value="0" min="0" step="0.01" oninput="updatePreview()">
                </div>
                <div class="form-group">
                    <label>Discount Type</label>
                    <select id="discountType" onchange="updatePreview()">
                        <option value="flat">Flat Amount</option>
                        <option value="percentage">Percentage</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>GST Rate (%)</label>
                    <input type="number" id="gstRate" value="12" min="0" max="100" step="0.01" oninput="updatePreview()">
                </div>
                <div class="form-group">
                    <label>Service Tax (₹)</label>
                    <input type="number" id="serviceTax" value="0" min="0" step="0.01" oninput="updatePreview()">
                </div>
                <div class="form-group">
                    <label>Extra Charges (₹)</label>
                    <input type="number" id="extraCharges" value="0" min="0" step="0.01" oninput="updatePreview()">
                </div>
                <div class="form-group">
                    <label>Other Charges (₹)</label>
                    <input type="number" id="otherCharges" value="0" min="0" step="0.01" oninput="updatePreview()">
                </div>
                <div class="form-group">
                    <label>Advance Payment (₹)</label>
                    <input type="number" id="advancePayment" value="2000" min="0" step="0.01" oninput="updatePreview()">
                </div>
            </div>

            <!-- Summary -->
            <div class="section-title">Summary</div>
            <div class="summary-grid">
                <div class="summary-item">
                    <div class="summary-label">Room Charges</div>
                    <div class="summary-value" id="summaryRoomCharges">₹0.00</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Total Amount</div>
                    <div class="summary-value" id="summaryTotal">₹0.00</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Balance Due</div>
                    <div class="summary-value" id="summaryBalance">₹0.00</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoice Preview - Matching Invoice.blade.php Exactly -->
    <div class="invoice-preview" id="invoicePreview">
        <div class="container">
            <div class="preview-actions">
                <button class="btn btn-secondary" onclick="hidePreview()">← Back to Editor</button>
                <button class="btn btn-primary" onclick="window.print()">🖨️ Print / Save PDF</button>
            </div>

            <div class="invoice-page">
                <!-- HEADER -->
                <div class="invoice-header">
                    <h1 id="prevHotelName">HOTEL SHREE SAMARTH</h1>
                    <div class="address">
                        <span id="prevHotelAddress">BHADRAKALI FRUIT MARKET, NASHIK</span><br>
                        Mob: <span id="prevHotelMobile">8275610326</span> | Tel: <span id="prevHotelTelephone">(0253) 2576103 / 2506103</span><br>
                        <strong>GSTIN:</strong> <span id="prevHotelGST">27AAIHS1179J1Z</span> |
                        <strong>L.T.NO.:</strong> <span id="prevHotelLT">H:23 A 00085</span>
                    </div>
                </div>

                <!-- DETAILS -->
                <div class="details-box">
                    <div class="details-column">
                        <div class="detail-row"><span class="label">Name</span><span class="value" id="prevCustomerName"></span></div>
                        <div class="detail-row"><span class="label">Address</span><span class="value" id="prevCustomerAddress"></span></div>
                        <div class="detail-row"><span class="label">Mobile</span><span class="value" id="prevCustomerMobile"></span></div>
                        <div class="detail-row"><span class="label">GST No</span><span class="value" id="prevGstNumber"></span></div>
                    </div>

                    <div class="details-column right">
                        <div class="detail-row"><span class="label">Date</span><span class="value" id="prevCurrentDate"></span></div>
                        <div class="detail-row"><span class="label">Registration No</span><span class="value" id="prevRegistrationNo"></span></div>
                        <div class="detail-row"><span class="label">Invoice No</span><span class="value" id="prevBookingNumber"></span></div>
                        <div class="detail-row"><span class="label">Arrival</span><span class="value" id="prevCheckIn"></span></div>
                        <div class="detail-row"><span class="label">Departure</span><span class="value" id="prevCheckOut"></span></div>
                    </div>
                </div>

                <!-- ROOMS TABLE -->
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Room No</th>
                            <th>Room Type</th>
                            <th>Rate / Night</th>
                            <th>Nights</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody id="prevRoomsTable"></tbody>
                </table>

                <!-- BOTTOM SECTION -->
                <div class="bottom-section">
                    <div class="two-column-row">
                        <div class="left-column left-split">
                            <div class="left-top">
                                I/We hereby certify that our Registration certificate Under the B.S.T.Act 1959 is in
                                force on the date on which the sales of the good specified in this bill/ cash memorandum
                                is made by me/us and that the transaction of sale covered by this bill/cash Memorandum has
                                been effected by me/us in the regular course of my/our business.
                            </div>

                            <div class="left-bottom">
                                <strong>Payment Mode:</strong> <span id="prevPaymentMode"></span><br><br>
                                <strong>Amount in Words:</strong>
                                <span id="prevAmountWords"></span>
                            </div>
                        </div>

                        <div class="right-column">
                            <div class="summary-box">
                                <div class="summary-row">
                                    <span>Room Charges</span>
                                    <span id="prevRoomCharges"></span>
                                </div>

                                <div class="summary-row">
                                    <span>
                                        Discount
                                        (<span id="prevDiscountDisplay"></span>)
                                    </span>
                                    <span id="prevDiscount"></span>
                                </div>

                                <div class="summary-row">
                                    <span>GST (<span id="prevGstRate"></span>%)</span>
                                    <span id="prevGstAmount"></span>
                                </div>

                                <div class="summary-row">
                                    <span>Service Tax</span>
                                    <span id="prevServiceTax"></span>
                                </div>

                                <div class="summary-row" id="extraChargesRow" style="display: none;">
                                    <span>Extra Charges</span>
                                    <span id="prevExtraCharges"></span>
                                </div>

                                <div class="summary-row" id="otherChargesRow" style="display: none;">
                                    <span>Other Charges</span>
                                    <span id="prevOtherCharges"></span>
                                </div>

                                <div class="summary-row total">
                                    <strong>Total Amount</strong>
                                    <strong id="prevTotalAmount"></strong>
                                </div>

                                <div class="summary-row paid">
                                    <span>Advance Paid</span>
                                    <span id="prevAdvance"></span>
                                </div>

                                <div class="summary-row balance">
                                    <span>Balance Due</span>
                                    <span id="prevBalanceDue"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="signatures">
                        <div class="signature">
                            <div class="line"></div>
                            Cashier Signature
                        </div>

                        <div class="signature">
                            <div class="line"></div>
                            Guest Signature
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let rooms = [
            { id: 1, roomNumber: '101', roomType: 'Deluxe AC', ratePerNight: 2000 },
            { id: 2, roomNumber: '102', roomType: 'Standard Non-AC', ratePerNight: 1500 }
        ];

        // Search Booking Function with Auto-fill
        async function searchBooking() {
            const bookingNumber = document.getElementById('searchBookingNumber').value.trim();

            if (!bookingNumber) {
                showAlert('Please enter a booking number', 'error');
                return;
            }

            document.getElementById('loader').classList.add('show');
            hideAlert();

            try {
                // Make API call to fetch booking data
                const response = await fetch(`/invoice/booking/${bookingNumber}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    }
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    fillBookingData(data.booking);
                    showAlert('Booking found and loaded successfully! ✓', 'success');
                } else {
                    showAlert(data.message || 'Booking not found. Please check the booking number.', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('Error connecting to server. Please try again.', 'error');
            } finally {
                document.getElementById('loader').classList.remove('show');
            }
        }

        // Fill form with booking data from API
        function fillBookingData(booking) {
            // Customer Details
            document.getElementById('customerName').value = booking.customer_name || '';
            document.getElementById('customerMobile').value = booking.customer_mobile || '';
            document.getElementById('customerAddress').value = booking.customer_address || '';
            document.getElementById('gstNumber').value = booking.gst_number || '';
            document.getElementById('bookingNumber').value = booking.booking_number || '';
            document.getElementById('registrationNo').value = booking.registration_no || '';

            // Booking Details - Check-in and Check-out
            if (booking.check_in) {
                document.getElementById('checkIn').value = formatDateTimeForInput(booking.check_in);
            }
            if (booking.check_out) {
                document.getElementById('checkOut').value = formatDateTimeForInput(booking.check_out);
            }

            // Payment Mode
            document.getElementById('paymentMode').value = booking.payment_mode || 'cash';

            // Financial Details
            const discountAmount = parseFloat(booking.discount_amount || booking.discount_value || 0);
            document.getElementById('discountAmount').value = discountAmount;

            // Discount Type
            const discountType = booking.discount_type || 'flat';
            document.getElementById('discountType').value = discountType;

            document.getElementById('gstRate').value = parseFloat(booking.gst_percentage || 12);
            document.getElementById('serviceTax').value = parseFloat(booking.service_tax || 0);
            document.getElementById('extraCharges').value = parseFloat(booking.extra_charges || 0);
            document.getElementById('otherCharges').value = parseFloat(booking.other_charges || 0);
            document.getElementById('advancePayment').value = parseFloat(booking.advance_payment || 0);

            // Rooms Data
            if (booking.booking_rooms && booking.booking_rooms.length > 0) {
                rooms = booking.booking_rooms.map((bookingRoom, index) => ({
                    id: index + 1,
                    roomNumber: bookingRoom.room?.room_number || bookingRoom.room_number || '',
                    roomType: bookingRoom.room?.room_type?.name || bookingRoom.room?.roomType?.name || bookingRoom.room_type || '',
                    ratePerNight: parseFloat(bookingRoom.room_price || bookingRoom.room?.base_price || 0)
                }));
            } else if (booking.rooms && booking.rooms.length > 0) {
                // Alternative rooms structure
                rooms = booking.rooms.map((room, index) => ({
                    id: index + 1,
                    roomNumber: room.room_number || room.room?.room_number || '',
                    roomType: room.room_type || room.room?.room_type?.name || room.room?.roomType?.name || '',
                    ratePerNight: parseFloat(room.room_price || room.base_price || room.room?.base_price || 0)
                }));
            }

            // Render rooms and update preview
            renderRooms();
            updatePreview();
        }

        // Format datetime string to input format (YYYY-MM-DDTHH:MM)
        function formatDateTimeForInput(dateString) {
            const date = new Date(dateString);
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            return `${year}-${month}-${day}T${hours}:${minutes}`;
        }

        function resetForm() {
            document.getElementById('searchBookingNumber').value = '';
            document.getElementById('customerName').value = 'John Doe';
            document.getElementById('customerMobile').value = '9876543210';
            document.getElementById('customerAddress').value = '123 Main Street, Mumbai';
            document.getElementById('gstNumber').value = '27XXXXX1234X1Z5';
            document.getElementById('bookingNumber').value = 'BK-2026-001';
            document.getElementById('registrationNo').value = 'REG-12345';
            document.getElementById('checkIn').value = '2026-01-01T14:00';
            document.getElementById('checkOut').value = '2026-01-03T12:00';
            document.getElementById('paymentMode').value = 'cash';
            document.getElementById('discountAmount').value = '0';
            document.getElementById('discountType').value = 'flat';
            document.getElementById('gstRate').value = '12';
            document.getElementById('serviceTax').value = '0';
            document.getElementById('extraCharges').value = '0';
            document.getElementById('otherCharges').value = '0';
            document.getElementById('advancePayment').value = '2000';

            rooms = [
                { id: 1, roomNumber: '101', roomType: 'Deluxe AC', ratePerNight: 2000 },
                { id: 2, roomNumber: '102', roomType: 'Standard Non-AC', ratePerNight: 1500 }
            ];

            renderRooms();
            updatePreview();
            hideAlert();
        }

        function showAlert(message, type) {
            const alert = document.getElementById('alertMessage');
            alert.textContent = message;
            alert.className = `alert alert-${type} show`;
        }

        function hideAlert() {
            document.getElementById('alertMessage').classList.remove('show');
        }

        function calculateNights() {
            const checkIn = new Date(document.getElementById('checkIn').value);
            const checkOut = new Date(document.getElementById('checkOut').value);
            const diffTime = Math.abs(checkOut - checkIn);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            return diffDays || 1;
        }

        function calculateTotals() {
            const nights = calculateNights();
            const roomCharges = rooms.reduce((sum, room) => sum + (room.ratePerNight * nights), 0);

            const discountValue = parseFloat(document.getElementById('discountAmount').value) || 0;
            const discountType = document.getElementById('discountType').value;

            let discountAmount = 0;
            if (discountType === 'percentage') {
                discountAmount = (roomCharges * discountValue) / 100;
            } else {
                discountAmount = discountValue;
            }

            const afterDiscount = roomCharges - discountAmount;
            const gstRate = parseFloat(document.getElementById('gstRate').value) || 0;
            const gstAmount = (afterDiscount * gstRate) / 100;
            const serviceTax = parseFloat(document.getElementById('serviceTax').value) || 0;
            const extraCharges = parseFloat(document.getElementById('extraCharges').value) || 0;
            const otherCharges = parseFloat(document.getElementById('otherCharges').value) || 0;

            const totalAmount = afterDiscount + gstAmount + serviceTax + extraCharges + otherCharges;
            const advancePayment = parseFloat(document.getElementById('advancePayment').value) || 0;
            const balanceDue = totalAmount - advancePayment;

            return {
                roomCharges,
                discountAmount,
                discountValue,
                discountType,
                gstAmount,
                serviceTax,
                extraCharges,
                otherCharges,
                totalAmount,
                balanceDue,
                nights,
                gstRate
            };
        }

        function numberToWords(amount) {
            const ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine'];
            const tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
            const teens = ['Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];

            if (amount === 0) return 'Zero Rupees Only';

            let num = Math.floor(amount);
            let words = '';

            // Crores
            if (num >= 10000000) {
                words += convertLessThanThousand(Math.floor(num / 10000000)) + ' Crore ';
                num %= 10000000;
            }

            // Lakhs
            if (num >= 100000) {
                words += convertLessThanThousand(Math.floor(num / 100000)) + ' Lakh ';
                num %= 100000;
            }

            // Thousands
            if (num >= 1000) {
                words += convertLessThanThousand(Math.floor(num / 1000)) + ' Thousand ';
                num %= 1000;
            }

            // Hundreds
            if (num > 0) {
                words += convertLessThanThousand(num);
            }

            function convertLessThanThousand(n) {
                let str = '';

                if (n >= 100) {
                    str += ones[Math.floor(n / 100)] + ' Hundred ';
                    n %= 100;
                }

                if (n >= 20) {
                    str += tens[Math.floor(n / 10)] + ' ';
                    n %= 10;
                } else if (n >= 10) {
                    str += teens[n - 10] + ' ';
                    return str.trim();
                }

                if (n > 0) {
                    str += ones[n] + ' ';
                }

                return str.trim();
            }

            return words.trim() + ' Rupees Only';
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            const day = String(date.getDate()).padStart(2, '0');
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const month = months[date.getMonth()];
            const year = date.getFullYear();
            let hours = date.getHours();
            const minutes = String(date.getMinutes()).padStart(2, '0');
            const ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12 || 12;
            return `${day} ${month} ${year} ${String(hours).padStart(2, '0')}:${minutes} ${ampm}`;
        }

        function renderRooms() {
            const container = document.getElementById('roomsContainer');
            container.innerHTML = rooms.map((room) => `
                <div class="room-card">
                    <div class="room-card-grid">
                        <div class="form-group">
                            <label>Room No</label>
                            <input type="text" value="${room.roomNumber}"
                                onchange="updateRoom(${room.id}, 'roomNumber', this.value)">
                        </div>
                        <div class="form-group">
                            <label>Room Type</label>
                            <input type="text" value="${room.roomType}"
                                onchange="updateRoom(${room.id}, 'roomType', this.value)">
                        </div>
                        <div class="form-group">
                            <label>Rate per Night (₹)</label>
                            <input type="number" value="${room.ratePerNight}" min="0" step="0.01"
                                onchange="updateRoom(${room.id}, 'ratePerNight', parseFloat(this.value))">
                        </div>
                        <button class="btn btn-danger" onclick="removeRoom(${room.id})"
                            ${rooms.length === 1 ? 'disabled' : ''}>🗑️</button>
                    </div>
                </div>
            `).join('');
        }

        function addRoom() {
            const newId = rooms.length > 0 ? Math.max(...rooms.map(r => r.id)) + 1 : 1;
            rooms.push({ id: newId, roomNumber: '', roomType: '', ratePerNight: 0 });
            renderRooms();
            updatePreview();
        }

        function removeRoom(id) {
            if (rooms.length > 1) {
                rooms = rooms.filter(room => room.id !== id);
                renderRooms();
                updatePreview();
            }
        }

        function updateRoom(id, field, value) {
            const room = rooms.find(r => r.id === id);
            if (room) {
                room[field] = value;
                updatePreview();
            }
        }

        function updatePreview() {
            const nights = calculateNights();
            document.getElementById('nights').value = nights;

            const totals = calculateTotals();

            // Update editor summary
            document.getElementById('summaryRoomCharges').textContent = `₹${totals.roomCharges.toFixed(2)}`;
            document.getElementById('summaryTotal').textContent = `₹${totals.totalAmount.toFixed(2)}`;
            document.getElementById('summaryBalance').textContent = `₹${totals.balanceDue.toFixed(2)}`;

            // Update preview - Customer Details
            document.getElementById('prevCustomerName').textContent = document.getElementById('customerName').value;
            document.getElementById('prevCustomerAddress').textContent = document.getElementById('customerAddress').value;
            document.getElementById('prevCustomerMobile').textContent = document.getElementById('customerMobile').value;
            document.getElementById('prevGstNumber').textContent = document.getElementById('gstNumber').value;

            // Update preview - Booking Details
            document.getElementById('prevBookingNumber').textContent = document.getElementById('bookingNumber').value;
            document.getElementById('prevRegistrationNo').textContent = document.getElementById('registrationNo').value;

            const today = new Date();
            document.getElementById('prevCurrentDate').textContent = formatDate(today);
            document.getElementById('prevCheckIn').textContent = formatDate(document.getElementById('checkIn').value);
            document.getElementById('prevCheckOut').textContent = formatDate(document.getElementById('checkOut').value);

            // Update preview - Rooms Table
            const roomsTableBody = document.getElementById('prevRoomsTable');
            roomsTableBody.innerHTML = rooms.map(room => `
                <tr>
                    <td>${room.roomNumber}</td>
                    <td>${room.roomType}</td>
                    <td class="right">₹${room.ratePerNight.toFixed(2)}</td>
                    <td class="center">${nights > 0 ? nights : ''}</td>
                    <td class="right">₹${(nights > 0 ? room.ratePerNight * nights : room.ratePerNight).toFixed(2)}</td>
                </tr>
            `).join('');

            // Update preview - Payment and Financial Summary
            const paymentMode = document.getElementById('paymentMode').value;
            document.getElementById('prevPaymentMode').textContent = paymentMode.replace('_', ' ').charAt(0).toUpperCase() + paymentMode.replace('_', ' ').slice(1);
            document.getElementById('prevAmountWords').textContent = numberToWords(totals.totalAmount);

            document.getElementById('prevRoomCharges').textContent = `₹${totals.roomCharges.toFixed(2)}`;

            // Display discount
            if (totals.discountType === 'percentage') {
                document.getElementById('prevDiscountDisplay').textContent = `${totals.discountValue}%`;
            } else {
                document.getElementById('prevDiscountDisplay').textContent = `₹${totals.discountValue}`;
            }
            document.getElementById('prevDiscount').textContent = `- ₹${totals.discountAmount.toFixed(2)}`;

            document.getElementById('prevGstRate').textContent = totals.gstRate;
            document.getElementById('prevGstAmount').textContent = `₹${totals.gstAmount.toFixed(2)}`;
            document.getElementById('prevServiceTax').textContent = `₹${totals.serviceTax.toFixed(2)}`;

            // Show/hide extra and other charges
            if (totals.extraCharges > 0) {
                document.getElementById('extraChargesRow').style.display = 'flex';
                document.getElementById('prevExtraCharges').textContent = `₹${totals.extraCharges.toFixed(2)}`;
            } else {
                document.getElementById('extraChargesRow').style.display = 'none';
            }

            if (totals.otherCharges > 0) {
                document.getElementById('otherChargesRow').style.display = 'flex';
                document.getElementById('prevOtherCharges').textContent = `₹${totals.otherCharges.toFixed(2)}`;
            } else {
                document.getElementById('otherChargesRow').style.display = 'none';
            }

            document.getElementById('prevTotalAmount').textContent = `₹${totals.totalAmount.toFixed(2)}`;
            document.getElementById('prevAdvance').textContent = `₹${parseFloat(document.getElementById('advancePayment').value).toFixed(2)}`;
            document.getElementById('prevBalanceDue').textContent = `₹${totals.balanceDue.toFixed(2)}`;
        }

        function showPreview() {
            updatePreview();
            document.getElementById('editorSection').classList.add('hidden');
            document.getElementById('invoicePreview').classList.add('active');
            window.scrollTo(0, 0);
        }

        function hidePreview() {
            document.getElementById('editorSection').classList.remove('hidden');
            document.getElementById('invoicePreview').classList.remove('active');
            window.scrollTo(0, 0);
        }

        // Initialize
        renderRooms();
        updatePreview();

        // Allow Enter key to search
        document.getElementById('searchBookingNumber').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchBooking();
            }
        });
    </script>
</body>
</html>
