<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

        .form-grid.full {
            grid-template-columns: 1fr;
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

        /* Loader */
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

        /* Invoice Preview */
        .invoice-preview {
            background: white;
            display: none;
        }

        .invoice-preview.active {
            display: block;
        }

        .invoice-container {
            width: 210mm;
            height: 297mm;
            padding: 10mm;
            margin: 0 auto;
            background: white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            position: relative;
            display: flex;
            flex-direction: column;
        }

        .invoice-content {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .invoice-header {
            text-align: center;
            border-bottom: 2px solid #999;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .invoice-header h1 {
            font-size: 20pt;
            color: #2c3e50;
            margin-bottom: 8px;
        }

        .invoice-address {
            font-size: 10pt;
            line-height: 1.6;
            color: #555;
        }

        .invoice-details {
            display: flex;
            gap: 20px;
            margin-bottom: 25px;
            border-bottom: 1px solid #999;
            padding-bottom: 15px;
            font-size: 10pt;
        }

        .invoice-details > div {
            flex: 1;
        }

        .invoice-details .right {
            text-align: right;
        }

        .detail-row {
            margin-bottom: 8px;
        }

        .detail-row strong {
            color: #555;
            min-width: 120px;
            display: inline-block;
        }

        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10pt;
        }

        .invoice-table th,
        .invoice-table td {
            border: 1px solid #ddd;
            padding: 10px;
        }

        .invoice-table th {
            background: #f4f4f4;
            font-weight: 600;
        }

        .invoice-table .right {
            text-align: right;
        }

        .invoice-table .center {
            text-align: center;
        }

        .invoice-footer {
            margin-top: 30px;
            border-top: 1px dashed #999;
            padding-top: 15px;
        }

        .footer-grid {
            display: flex;
            gap: 20px;
        }

        .footer-grid > div {
            flex: 1;
        }

        .certification {
            font-size: 9pt;
            line-height: 1.6;
            color: #555;
            padding-bottom: 15px;
            border-bottom: 1px solid #ccc;
            margin-bottom: 15px;
        }

        .payment-details {
            font-size: 10pt;
        }

        .payment-details strong {
            display: block;
            margin-bottom: 5px;
        }

        .summary-box {
            font-size: 10pt;
        }

        .summary-box-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .summary-box-row.total {
            font-weight: 700;
            font-size: 11pt;
            border-top: 2px solid #333;
            border-bottom: 2px solid #333;
        }

        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        .signature-box {
            width: 45%;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-bottom: 8px;
            padding-top: 40px;
        }

        .hidden {
            display: none;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }
            .editor-section {
                display: none;
            }
            .invoice-container {
                box-shadow: none;
                page-break-after: always;
            }
            .btn {
                display: none;
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
                    <label>GST Rate (%)</label>
                    <input type="number" id="gstRate" value="12" min="0" max="100" step="0.01" oninput="updatePreview()">
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
                    <div class="summary-label">Gross Amount</div>
                    <div class="summary-value" id="summaryGross">₹0.00</div>
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

    <!-- Invoice Preview -->
    <div class="invoice-preview" id="invoicePreview">
        <div class="container">
            <div style="margin-bottom: 20px; text-align: center;">
                <button class="btn btn-secondary" onclick="hidePreview()">← Back to Editor</button>
                <button class="btn btn-primary" onclick="window.print()">🖨️ Print / Save PDF</button>
            </div>

            <div class="invoice-container">
                <div class="invoice-content">
                    <!-- Invoice Header -->
                    <div class="invoice-header">
                        <h1>HOTEL SHREE SAMARTH</h1>
                        <div class="invoice-address">
                            BHADRAKALI FRUIT MARKET, NASHIK<br>
                            Mob: 8275610326 | Tel: (0253) 2576103 / 2506103<br>
                            <strong>GSTIN:</strong> 27AAIHS1179J1Z | <strong>L.T.NO.:</strong> H:23 A 00085
                        </div>
                    </div>

                    <!-- Invoice Details -->
                    <div class="invoice-details">
                        <div>
                            <div class="detail-row"><strong>Name:</strong> <span id="prevCustomerName"></span></div>
                            <div class="detail-row"><strong>Address:</strong> <span id="prevCustomerAddress"></span></div>
                            <div class="detail-row"><strong>Mobile:</strong> <span id="prevCustomerMobile"></span></div>
                            <div class="detail-row"><strong>GST No:</strong> <span id="prevGstNumber"></span></div>
                        </div>
                        <div class="right">
                            <div class="detail-row"><strong>Date:</strong> <span id="prevCurrentDate"></span></div>
                            <div class="detail-row"><strong>Invoice No:</strong> <span id="prevBookingNumber"></span></div>
                            <div class="detail-row"><strong>Arrival:</strong> <span id="prevCheckIn"></span></div>
                            <div class="detail-row"><strong>Departure:</strong> <span id="prevCheckOut"></span></div>
                        </div>
                    </div>

                    <!-- Rooms Table -->
                    <table class="invoice-table">
                        <thead>
                            <tr>
                                <th>Room No</th>
                                <th>Room Type</th>
                                <th class="right">Rate / Night</th>
                                <th class="center">Nights</th>
                                <th class="right">Amount</th>
                            </tr>
                        </thead>
                        <tbody id="prevRoomsTable"></tbody>
                    </table>
                </div>

                <!-- Invoice Footer -->
                <div class="invoice-footer">
                    <div class="footer-grid">
                        <div>
                            <div class="certification">
                                hereby certify that our Registration certificate Under the B.S.T.Act 1959 is in force on the date on which the sales of the good specified in this bill/ cash memorandum is made by me/us and that the transaction of sale covered by this bill/cash Memorandum has been effected by me/us in the regular course of my/our business.
                            </div>
                            <div class="payment-details">
                                <strong>Payment Mode:</strong>
                                <span id="prevPaymentMode"></span><br><br>
                                <strong>Amount in Words:</strong><br>
                                <span id="prevAmountWords"></span>
                            </div>
                        </div>
                        <div>
                            <div class="summary-box">
                                <div class="summary-box-row">
                                    <span>Gross Amount</span>
                                    <span id="prevGrossAmount"></span>
                                </div>
                                <div class="summary-box-row">
                                    <span>Discount</span>
                                    <span id="prevDiscount"></span>
                                </div>
                                <div class="summary-box-row">
                                    <span>GST (<span id="prevGstRate"></span>%)</span>
                                    <span id="prevGstAmount"></span>
                                </div>
                                <div class="summary-box-row">
                                    <span>Advance Paid</span>
                                    <span id="prevAdvance"></span>
                                </div>
                                <div class="summary-box-row total">
                                    <span>Net Amount</span>
                                    <span id="prevNetAmount"></span>
                                </div>
                                <div class="summary-box-row">
                                    <span>Balance Due</span>
                                    <span id="prevBalanceDue"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Signatures -->
                    <div class="signatures">
                        <div class="signature-box">
                            <div class="signature-line"></div>
                            Cashier Signature
                        </div>
                        <div class="signature-box">
                            <div class="signature-line"></div>
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

        // Search Booking Function
        async function searchBooking() {
            const bookingNumber = document.getElementById('searchBookingNumber').value.trim();

            if (!bookingNumber) {
                showAlert('Please enter a booking number', 'error');
                return;
            }

            // Show loader
            document.getElementById('loader').classList.add('show');
            hideAlert();
            // /invoice/booking/{bookingNumber}
            try {
                const response = await fetch(`invoice/booking/${bookingNumber}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    fillBookingData(data.booking);
                    showAlert('Booking found and loaded successfully!', 'success');
                } else {
                    showAlert(data.message || 'Booking not found', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('Error searching booking. Please try again.', 'error');
            } finally {
                document.getElementById('loader').classList.remove('show');
            }
        }

        // Fill form with booking data
        function fillBookingData(booking) {
            // Customer Details
            document.getElementById('customerName').value = booking.customer_name || '';
            document.getElementById('customerMobile').value = booking.customer_mobile || '';
            document.getElementById('customerAddress').value = booking.customer_address || '';
            document.getElementById('gstNumber').value = booking.gst_number || '';
            document.getElementById('bookingNumber').value = booking.booking_number || '';

            // Booking Details
            if (booking.check_in) {
                document.getElementById('checkIn').value = formatDateTimeForInput(booking.check_in);
            }
            if (booking.check_out) {
                document.getElementById('checkOut').value = formatDateTimeForInput(booking.check_out);
            }

            document.getElementById('paymentMode').value = booking.payment_mode || 'cash';

            // Financial Details
            document.getElementById('discountAmount').value = booking.discount_amount || 0;
            document.getElementById('gstRate').value = booking.gst_percentage || 0;
            document.getElementById('advancePayment').value = booking.advance_payment || 0;

            // Rooms
            if (booking.rooms && booking.rooms.length > 0) {
                rooms = booking.rooms.map((room, index) => ({
                    id: index + 1,
                    roomNumber: room.room_number || room.room?.room_number || '',
                    roomType: room.room_type || room.room?.room_type?.name || '',
                    ratePerNight: parseFloat(room.room_price || room.room?.base_price || 0)
                }));
                renderRooms();
            }

            updatePreview();
        }

        // Format datetime for input field
        function formatDateTimeForInput(dateString) {
            const date = new Date(dateString);
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            return `${year}-${month}-${day}T${hours}:${minutes}`;
        }

        // Reset form to default values
        function resetForm() {
            document.getElementById('searchBookingNumber').value = '';

            // Reset to default values
            document.getElementById('customerName').value = 'John Doe';
            document.getElementById('customerMobile').value = '9876543210';
            document.getElementById('customerAddress').value = '123 Main Street, Mumbai';
            document.getElementById('gstNumber').value = '27XXXXX1234X1Z5';
            document.getElementById('bookingNumber').value = 'BK-2026-001';

            document.getElementById('checkIn').value = '2026-01-01T14:00';
            document.getElementById('checkOut').value = '2026-01-03T12:00';
            document.getElementById('paymentMode').value = 'cash';

            document.getElementById('discountAmount').value = '0';
            document.getElementById('gstRate').value = '12';
            document.getElementById('advancePayment').value = '2000';

            // Reset rooms
            rooms = [
                { id: 1, roomNumber: '101', roomType: 'Deluxe AC', ratePerNight: 2000 },
                { id: 2, roomNumber: '102', roomType: 'Standard Non-AC', ratePerNight: 1500 }
            ];

            renderRooms();
            updatePreview();
            hideAlert();
        }

        // Show/Hide Alert
        function showAlert(message, type) {
            const alert = document.getElementById('alertMessage');
            alert.textContent = message;
            alert.className = `alert alert-${type} show`;
        }

        function hideAlert() {
            const alert = document.getElementById('alertMessage');
            alert.classList.remove('show');
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
            const grossAmount = rooms.reduce((sum, room) => sum + (room.ratePerNight * nights), 0);
            const discountAmount = parseFloat(document.getElementById('discountAmount').value) || 0;
            const discountedAmount = grossAmount - discountAmount;
            const gstRate = parseFloat(document.getElementById('gstRate').value) || 0;
            const gstAmount = (discountedAmount * gstRate) / 100;
            const totalAmount = discountedAmount + gstAmount;
            const advancePayment = parseFloat(document.getElementById('advancePayment').value) || 0;
            const balanceDue = totalAmount - advancePayment;

            return { grossAmount, discountAmount, gstAmount, totalAmount, balanceDue, nights, gstRate };
        }

        function numberToWords(num) {
            const ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine'];
            const tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
            const teens = ['Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];

            if (num === 0) return 'Zero Rupees Only';

            function convertLessThanThousand(n) {
                if (n === 0) return '';
                if (n < 10) return ones[n];
                if (n < 20) return teens[n - 10];
                if (n < 100) return tens[Math.floor(n / 10)] + (n % 10 ? ' ' + ones[n % 10] : '');
                return ones[Math.floor(n / 100)] + ' Hundred' + (n % 100 ? ' ' + convertLessThanThousand(n % 100) : '');
            }

            function convert(n) {
                if (n === 0) return 'Zero';
                if (n < 1000) return convertLessThanThousand(n);
                if (n < 100000) return convertLessThanThousand(Math.floor(n / 1000)) + ' Thousand' + (n % 1000 ? ' ' + convertLessThanThousand(n % 1000) : '');
                if (n < 10000000) return convertLessThanThousand(Math.floor(n / 100000)) + ' Lakh' + (n % 100000 ? ' ' + convert(n % 100000) : '');
                return convertLessThanThousand(Math.floor(n / 10000000)) + ' Crore' + (n % 10000000 ? ' ' + convert(n % 10000000) : '');
                }

                return convert(Math.floor(num)) + ' Rupees Only';
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
        container.innerHTML = rooms.map((room, index) => `
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

        // Update summary
        document.getElementById('summaryGross').textContent = `₹${totals.grossAmount.toFixed(2)}`;
        document.getElementById('summaryTotal').textContent = `₹${totals.totalAmount.toFixed(2)}`;
        document.getElementById('summaryBalance').textContent = `₹${totals.balanceDue.toFixed(2)}`;

        // Update preview
        document.getElementById('prevCustomerName').textContent = document.getElementById('customerName').value;
        document.getElementById('prevCustomerAddress').textContent = document.getElementById('customerAddress').value;
        document.getElementById('prevCustomerMobile').textContent = document.getElementById('customerMobile').value;
        document.getElementById('prevGstNumber').textContent = document.getElementById('gstNumber').value;
        document.getElementById('prevBookingNumber').textContent = document.getElementById('bookingNumber').value;

        const today = new Date();
        document.getElementById('prevCurrentDate').textContent = formatDate(today);
        document.getElementById('prevCheckIn').textContent = formatDate(document.getElementById('checkIn').value);
        document.getElementById('prevCheckOut').textContent = formatDate(document.getElementById('checkOut').value);

        // Update rooms table
        const roomsTableBody = document.getElementById('prevRoomsTable');
        roomsTableBody.innerHTML = rooms.map(room => `
            <tr>
                <td>${room.roomNumber}</td>
                <td>${room.roomType}</td>
                <td class="right">₹${room.ratePerNight.toFixed(2)}</td>
                <td class="center">${nights}</td>
                <td class="right">₹${(room.ratePerNight * nights).toFixed(2)}</td>
            </tr>
        `).join('');

        // Update payment and summary
        const paymentMode = document.getElementById('paymentMode').value;
        document.getElementById('prevPaymentMode').textContent = paymentMode.replace('_', ' ').toUpperCase();
        document.getElementById('prevAmountWords').textContent = numberToWords(totals.totalAmount);

        document.getElementById('prevGrossAmount').textContent = `₹${totals.grossAmount.toFixed(2)}`;
        document.getElementById('prevDiscount').textContent = `₹${totals.discountAmount.toFixed(2)}`;
        document.getElementById('prevGstRate').textContent = totals.gstRate;
        document.getElementById('prevGstAmount').textContent = `₹${totals.gstAmount.toFixed(2)}`;
        document.getElementById('prevAdvance').textContent = `₹${parseFloat(document.getElementById('advancePayment').value).toFixed(2)}`;
        document.getElementById('prevNetAmount').textContent = `₹${totals.totalAmount.toFixed(2)}`;
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
