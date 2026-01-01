<!-- File: resources/views/bookings/invoice.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice - {{ $booking->booking_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .header { text-align: center; border-bottom: 3px solid #333; padding-bottom: 20px; margin-bottom: 30px; }
        .details { margin-bottom: 30px; }
        .details table { width: 100%; }
        .details td { padding: 5px; }
        .items-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .items-table th, .items-table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .items-table th { background-color: #f4f4f4; }
        .total { text-align: right; font-size: 18px; font-weight: bold; margin-top: 20px; }
        .footer { margin-top: 50px; border-top: 2px solid #333; padding-top: 20px; text-align: center; }
        @media print { body { margin: 0; } }
    </style>
</head>
<body>
    <div class="header">
        <h1>🏨 HOTEL INVOICE</h1>
        <p>Invoice Number: <strong>{{ $booking->booking_number }}</strong></p>
        <p>Date: {{ now()->format('d M Y') }}</p>
    </div>

    <div class="details">
        <table>
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <h3>Customer Details:</h3>
                    <strong>{{ $booking->customer_name }}</strong><br>
                    {{ $booking->customer_address }}<br>
                    Mobile: {{ $booking->customer_mobile }}<br>
                    @if($booking->customer_email)
                        Email: {{ $booking->customer_email }}<br>
                    @endif
                    ID Proof: {{ ucfirst($booking->id_proof_type) }} - {{ $booking->id_proof_number }}

                    @if($booking->company_name)
                        <br><br>
                        <strong>Company Details:</strong><br>
                        {{ $booking->company_name }}<br>
                        GST No: {{ $booking->gst_number }}
                    @endif
                </td>
                <td style="width: 50%; vertical-align: top; text-align: right;">
                    <h3>Booking Details:</h3>
                    Check-in: {{ $booking->check_in->format('d M Y, h:i A') }}<br>
                    Check-out: {{ $booking->check_out->format('d M Y, h:i A') }}<br>
                    Number of Nights: {{ $booking->number_of_nights }}<br>
                    Number of Adults: {{ $booking->number_of_adults }}<br>
                    Number of Children: {{ $booking->number_of_children }}<br>
                    Status: <strong>{{ ucfirst($booking->booking_status) }}</strong>
                </td>
            </tr>
        </table>
    </div>

    <h3>Room Details:</h3>
    <table class="items-table">
        <thead>
            <tr>
                <th>Room Number</th>
                <th>Room Type</th>
                <th>Rate/Night</th>
                <th>Nights</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($booking->bookingRooms as $br)
                <tr>
                    <td>{{ $br->room->room_number }}</td>
                    <td>{{ $br->room->roomType->name }}</td>
                    <td>₹{{ number_format($br->room_price, 2) }}</td>
                    <td>{{ $booking->number_of_nights }}</td>
                    <td>₹{{ number_format($br->room_price * $booking->number_of_nights, 2) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="4" style="text-align: right;"><strong>Room Charges:</strong></td>
                <td><strong>₹{{ number_format($booking->room_charges, 2) }}</strong></td>
            </tr>
        </tbody>
    </table>

    @if($booking->extraCharges->count() > 0)
        <h3>Extra Charges:</h3>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Description</th>
                    <th>Date</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($booking->extraCharges as $charge)
                    <tr>
                        <td>{{ ucfirst($charge->charge_type) }}</td>
                        <td>{{ $charge->description }}</td>
                        <td>{{ $charge->charge_date->format('d M Y') }}</td>
                        <td>₹{{ number_format($charge->amount, 2) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="3" style="text-align: right;"><strong>Total Extra Charges:</strong></td>
                    <td><strong>₹{{ number_format($booking->extra_charges, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>
    @endif

    <div style="margin: 30px 0;">
        <table style="width: 100%; border: 2px solid #333; padding: 20px;">
            <tr>
                <td style="padding: 10px;">Room Charges:</td>
                <td style="text-align: right; padding: 10px;">₹{{ number_format($booking->room_charges, 2) }}</td>
            </tr>
            <tr>
                <td style="padding: 10px;">GST ({{ $booking->bookingRooms->first()->room->gst_percentage }}%):</td>
                <td style="text-align: right; padding: 10px;">₹{{ number_format($booking->gst_amount, 2) }}</td>
            </tr>
            @if($booking->service_tax > 0)
                <tr>
                    <td style="padding: 10px;">Service Tax:</td>
                    <td style="text-align: right; padding: 10px;">₹{{ number_format($booking->service_tax, 2) }}</td>
                </tr>
            @endif
            @if($booking->other_charges > 0)
                <tr>
                    <td style="padding: 10px;">Other Charges:</td>
                    <td style="text-align: right; padding: 10px;">₹{{ number_format($booking->other_charges, 2) }}</td>
                </tr>
            @endif
            @if($booking->extra_charges > 0)
                <tr>
                    <td style="padding: 10px;">Extra Charges:</td>
                    <td style="text-align: right; padding: 10px;">₹{{ number_format($booking->extra_charges, 2) }}</td>
                </tr>
            @endif
            <tr style="background-color: #f4f4f4; font-size: 18px; font-weight: bold;">
                <td style="padding: 10px;">GRAND TOTAL:</td>
                <td style="text-align: right; padding: 10px;">₹{{ number_format($booking->total_amount, 2) }}</td>
            </tr>
            <tr style="color: green;">
                <td style="padding: 10px;">Advance Paid:</td>
                <td style="text-align: right; padding: 10px;">₹{{ number_format($booking->advance_payment, 2) }}</td>
            </tr>
            <tr style="color: red; font-weight: bold;">
                <td style="padding: 10px;">BALANCE DUE:</td>
                <td style="text-align: right; padding: 10px;">₹{{ number_format($booking->remaining_amount, 2) }}</td>
            </tr>
        </table>
    </div>

    @if($booking->payment_mode)
        <p><strong>Payment Mode:</strong> {{ ucfirst(str_replace('_', ' ', $booking->payment_mode)) }}</p>
    @endif

    <div class="footer">
        <p><strong>Thank you for your business!</strong></p>
        <p>This is a computer-generated invoice.</p>
    </div>

    <script>
        window.onload = function() { window.print(); }
    </script>
</body>
</html>
