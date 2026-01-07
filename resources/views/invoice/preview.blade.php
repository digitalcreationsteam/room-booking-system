<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice - {{ $booking->booking_number }}</title>

    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #000;
        }

        .invoice-box {
            width: 210mm;
            min-height: 297mm;
            margin: auto;
            padding: 20px;
            border: 1px solid #000;
        }

        h2, h3 {
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td, th {
            padding: 6px;
            border: 1px solid #000;
        }

        .no-border td {
            border: none;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        .invoice-header td {
            border: none;
        }

        .signature {
            height: 80px;
            vertical-align: bottom;
        }

        @media print {
            body {
                margin: 0;
            }
        }
    </style>
</head>
<body>

<div class="invoice-box">

    {{-- ================= HEADER ================= --}}
    <table class="invoice-header">
        <tr>
            <td width="60%">
                <h2>{{ config('app.name') }}</h2>
                <p>
                    Hotel Address Line 1<br>
                    City, State - Pincode<br>
                    Mobile: 9XXXXXXXXX<br>
                    GSTIN: 27XXXXXXXXX
                </p>
            </td>
            <td width="40%" class="right">
                <h3>TAX INVOICE</h3>
                <p>
                    Invoice No: <b>{{ $booking->booking_number }}</b><br>
                    Date: {{ now()->format('d-m-Y') }}
                </p>
            </td>
        </tr>
    </table>

    {{-- ================= CUSTOMER DETAILS ================= --}}
    <table class="no-border" style="margin-top:10px">
        <tr>
            <td width="50%">
                <b>Customer Name:</b> {{ $booking->customer_name }}<br>
                <b>Mobile:</b> {{ $booking->customer_mobile }}<br>
                <b>Address:</b> {{ $booking->customer_address }}
            </td>
            <td width="50%">
                <b>Check-In:</b> {{ $booking->check_in->format('d-m-Y H:i') }}<br>
                <b>Check-Out:</b> {{ $booking->check_out->format('d-m-Y H:i') }}<br>
                <b>No. of Guests:</b>
                {{ $booking->number_of_adults + $booking->number_of_children }}
            </td>
        </tr>
    </table>

    {{-- ================= ROOM DETAILS ================= --}}
    <table style="margin-top:10px">
        <thead>
            <tr class="center bold">
                <th>#</th>
                <th>Room No</th>
                <th>Room Type</th>
                <th>Nights</th>
                <th>Rate / Night</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @php
                $nights = max(1, $booking->check_in->diffInDays($booking->check_out));
            @endphp

            @foreach($booking->rooms as $index => $room)
                <tr class="center">
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $room->room_number }}</td>
                    <td>{{ $room->roomType->name }}</td>
                    <td>{{ $nights }}</td>
                    <td class="right">₹ {{ number_format($room->base_price, 2) }}</td>
                    <td class="right">
                        ₹ {{ number_format($room->base_price * $nights, 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ================= AMOUNT SUMMARY ================= --}}
    <table style="margin-top:10px">
        <tr>
            <td width="70%" rowspan="7">
                <b>Amount in Words:</b><br>
                {{ \App\Helpers\NumberToWords::convert($booking->total_amount) }}
            </td>
            <td width="30%" class="right">Room Charges</td>
            <td class="right">₹ {{ number_format($booking->room_charges, 2) }}</td>
        </tr>

        <tr>
            <td class="right">Discount</td>
            <td class="right">₹ {{ number_format($booking->discount_amount, 2) }}</td>
        </tr>

        <tr>
            <td class="right">GST ({{ $booking->gst_percentage }}%)</td>
            <td class="right">₹ {{ number_format($booking->gst_amount, 2) }}</td>
        </tr>

        <tr>
            <td class="right">Service Tax</td>
            <td class="right">₹ {{ number_format($booking->service_tax, 2) }}</td>
        </tr>

        <tr>
            <td class="right bold">Total Amount</td>
            <td class="right bold">
                ₹ {{ number_format($booking->total_amount, 2) }}
            </td>
        </tr>

        <tr>
            <td class="right">Advance Paid</td>
            <td class="right">₹ {{ number_format($booking->advance_payment, 2) }}</td>
        </tr>

        <tr>
            <td class="right bold">Balance Due</td>
            <td class="right bold">
                ₹ {{ number_format($booking->remaining_amount, 2) }}
            </td>
        </tr>
    </table>

    {{-- ================= SIGNATURE ================= --}}
    <table class="no-border" style="margin-top:40px">
        <tr>
            <td class="signature">
                Guest Signature
            </td>
            <td class="signature right">
                Authorised Signature
            </td>
        </tr>
    </table>

</div>

<script>
    window.print();
</script>

</body>
</html>
