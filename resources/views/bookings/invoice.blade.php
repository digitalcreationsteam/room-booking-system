<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice - {{ $booking->booking_number }}</title>

    <style>
        @page { size: A4 portrait; margin: 0; }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            width: 210mm;
            min-height: 297mm;
            padding: 10mm;
            margin: 0 auto;
            background: #fff;
            font-size: 11pt;
            color: #333;
        }

        /* ================= HEADER ================= */

        .header {
            text-align: center;
            border-bottom: 1px solid #999;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header h1 {
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
            display: flex;
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
        }

        .items-table th,
        .items-table td {
            border: 1px solid #ddd;
            padding: 7px;
        }

        .items-table th {
            background: #f4f4f4;
        }

        /* ================= BOTTOM FIXED ================= */

        .bottom-fixed {
            position: fixed;
            bottom: 15mm;
            left: 10mm;
            right: 10mm;
            background: #fff;
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
        }

        .signature {
            width: 45%;
            display: inline-block;
            text-align: center;
            font-size: 10pt;
        }

        .signature .line {
            border-top: 1px solid #333;
            margin-top: 35px;
            padding-top: 5px;
        }
    </style>
</head>

<body>

<!-- HEADER -->
<div class="header">
    <h1>HOTEL SHREE SAMARTH</h1>
    <div class="address">
        BHADRAKALI FRUIT MARKET, NASHIK<br>
        Mob: 8275610326 | Tel: (0253) 2576103 / 2506103<br>
        <strong>GSTIN:</strong> 27AAIHS1179J1Z |
        <strong>L.T.NO.:</strong> H:23 A 00085
    </div>
</div>

<!-- DETAILS -->
<div class="details-box">

    <div class="details-column">
        <div class="detail-row"><span class="label">Name</span><span class="value">{{ $booking->customer_name }}</span></div>
        <div class="detail-row"><span class="label">Address</span><span class="value">{{ $booking->customer_address }}</span></div>
        <div class="detail-row"><span class="label">Mobile</span><span class="value">{{ $booking->customer_mobile }}</span></div>
        <div class="detail-row"><span class="label">GST No</span><span class="value">{{ $booking->gst_number }}</span></div>
    </div>

    <div class="details-column right">
        <div class="detail-row"><span class="label">Date</span><span class="value">{{ now()->format('d M Y') }}</span></div>
        <div class="detail-row"><span class="label">Invoice No</span><span class="value">{{ $booking->booking_number }}</span></div>
        <div class="detail-row"><span class="label">Arrival</span><span class="value">{{ $booking->check_in->format('d M Y h:i A') }}</span></div>
        <div class="detail-row"><span class="label">Departure</span><span class="value">{{ $booking->check_out->format('d M Y h:i A') }}</span></div>
    </div>

</div>

<!-- ROOMS TABLE -->
<table class="items-table">
    <thead>
        <tr>
            <th>Room No</th>
            <th>Room Type</th>
            <th align="right">Rate / Night</th>
            <th align="center">Nights</th>
            <th align="right">Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach($booking->bookingRooms as $room)
        <tr>
            <td>{{ $room->room->room_number }}</td>
            <td>{{ $room->room->roomType->name }}</td>
            <td align="right">₹{{ number_format($room->room_price, 2) }}</td>
            <td align="center">{{ $booking->number_of_nights }}</td>
            <td align="right">₹{{ number_format($room->room_price * $booking->number_of_nights, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<!-- BOTTOM FIXED -->
<div class="bottom-fixed">

    <div class="two-column-row">

        <div class="left-column left-split">
            <div class="left-top">
                hereby certify that our Registration certificate Under the B.S.T.Act 1959 is in
                force on the date on which the sales of the good specified in this bill/ cash memorandum 
                is made by me/us and that the transaction of sale covered by this bill/cash Memorandum has 
                been effected by me/us in the regular course of my/our business.
            </div>

            <div class="left-bottom">
                <strong>Payment Mode:</strong>
                {{ ucfirst(str_replace('_',' ',$booking->payment_mode)) }}<br><br>
                <strong>Amount in Words:</strong><br>
                -
            </div>
        </div>

        <div class="right-column">
            <div class="summary-box">
                <div class="summary-row"><span>Gross Amount</span><span>₹{{ number_format($booking->room_charges, 2) }}</span></div>
                <div class="summary-row"><span>Discount</span><span>₹{{ number_format($booking->discount_amount, 2) }}</span></div>
                <div class="summary-row"><span>GST</span><span>₹{{ number_format($booking->gst_amount, 2) }}</span></div>
                <div class="summary-row"><span>Advance Paid</span><span>₹{{ number_format($booking->advance_payment, 2) }}</span></div>
                <div class="summary-row"><strong>Net Amount</strong><strong>₹{{ number_format($booking->total_amount, 2) }}</strong></div>
                <div class="summary-row"><span>Balance Due</span><span>₹{{ number_format($booking->remaining_amount, 2) }}</span></div>
            </div>
        </div>

    </div>

    <div class="signatures">
        <div class="signature">
            <div class="line"></div>
            Cashier Signature
        </div>

        <div class="signature" style="float:right;">
            <div class="line"></div>
            Guest Signature
        </div>
    </div>

</div>

</body>
</html>
