<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://js.paystack.co/v1/inline.js"></script>

    <title>Client Account Statement</title>

    <style>
        :root {
            --primary: #1e40af;
            --text: #1f2937;
            --muted: #6b7280;
            --border: #e5e7eb;
            --background: #f3f4f6;
            --success: #15803d;
            --danger: #dc2626;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 30px 15px;
            background: var(--background);
            color: var(--text);
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
        }

        .container {
            max-width: 1050px;
            margin: auto;
        }

        .actions {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
        }

        .print-btn {
            background: var(--primary);
            color: #fff;
            padding: 12px 18px;
            border: 0;
            border-radius: 7px;
            cursor: pointer;
            font-size: 14px;
        }

        .statement {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, .06);
        }

        .header {
            background: var(--primary);
            color: #fff;
            padding: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .header h1 {
            font-size: 26px;
            margin: 0 0 8px;
        }

        .header p {
            margin: 0;
            opacity: .85;
        }

        .header-right {
            text-align: right;
            line-height: 1.8;
        }

        .content {
            padding: 30px;
        }

        .section {
            margin-bottom: 35px;
        }

        .section-title {
            font-size: 18px;
            margin: 0 0 18px;
        }

        .client-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 15px;
        }

        .detail {
            padding: 15px;
            border: 1px solid var(--border);
            background: #f9fafb;
            border-radius: 8px;
            overflow-wrap: anywhere;
        }

        .detail-label {
            color: var(--muted);
            font-size: 12px;
            margin-bottom: 7px;
        }

        .detail-value {
            font-weight: 600;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 15px;
        }

        .summary-card {
            padding: 20px;
            border-radius: 10px;
            border: 1px solid var(--border);
        }

        .summary-card.invoiced {
            background: #eff6ff;
        }

        .summary-card.paid {
            background: #f0fdf4;
        }

        .summary-card.balance {
            background: #fff7ed;
        }

        .summary-card.credit {
            background: #eff6ff;
        }

        .summary-label {
            color: var(--muted);
            font-size: 12px;
            margin-bottom: 12px;
        }

        .summary-amount {
            font-size: 23px;
            font-weight: 700;
            overflow-wrap: anywhere;
        }

        .status {
            display: inline-block;
            margin-top: 12px;
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            background: #ffedd5;
            color: #9a3412;
        }

        .status.paid {
            background: #dcfce7;
            color: #166534;
        }

        .status.credit {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
            border: 1px solid var(--border);
            border-radius: 8px;
        }

        table {
            width: 100%;
            min-width: 550px;
            border-collapse: collapse;
        }

        th, td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--border);
            text-align: left;
        }

        th {
            background: #f9fafb;
            color: #4b5563;
            font-size: 12px;
            text-transform: uppercase;
        }

        tbody tr:last-child td {
            border-bottom: 0;
        }

        .text-right {
            text-align: right;
        }

        .empty {
            text-align: center;
            color: var(--muted);
            padding: 25px;
        }

        .footer {
            padding: 22px 30px;
            border-top: 1px solid var(--border);
            background: #f9fafb;
            color: var(--muted);
            display: flex;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            font-size: 12px;
        }

        @media (max-width: 650px) {
            body {
                padding: 12px 8px;
            }

            .header {
                flex-direction: column;
                align-items: flex-start;
                padding: 22px 18px;
            }

            .header-right {
                text-align: left;
            }

            .content {
                padding: 22px 16px;
            }

            .client-grid,
            .summary-grid {
                grid-template-columns: 1fr;
            }

            .header h1 {
                font-size: 22px;
            }

            .summary-amount {
                font-size: 21px;
            }
        }

        @media print {
            body {
                padding: 0;
                background: #fff;
            }

            .container {
                max-width: none;
            }

            .statement {
                box-shadow: none;
            }

            .actions {
                display: none;
            }

            .table-wrapper {
                overflow: visible;
            }

            table {
                min-width: 0;
            }

            .section,
            .summary-card,
            .detail {
                break-inside: avoid;
            }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

</head>

<body>

<div class="container">

    <div class="actions">
        <button class="print-btn" onclick="window.print()">
            Print / Save PDF
        </button>
    </div>

    <div class="statement">

        <header class="header">
            <div>
                <h1>TCLUB Systems</h1>
                <h1>Tpos App</h1>
                <h1>Tel/Momo : 059 896 3370</h1>
            </div>

            <div class="header-right">
                <div>Statement Date</div>
                <strong>{{ now()->format('d M Y') }}</strong>

                <div>
                    Client ID: {{ $client->clientid }}
                </div>
            </div>
        </header>

        <main class="content">

            Client Information
            <section class="section">
                <h2 class="section-title">Client Information</h2>

                <div class="client-grid">

                    <div class="detail">
                        <div class="detail-label">Business Name</div>
                        <div class="detail-value">
                            {{ $client->business_name ?? $client->fullname ?? 'N/A' }}
                        </div>
                    </div>

                    <div class="detail">
                        <div class="detail-label">Contact Person</div>
                        <div class="detail-value">
                            {{ $client->business_owner }}
                        </div>
                    </div>

                    <div class="detail">
                        <div class="detail-label">Client ID</div>
                        <div class="detail-value">
                            {{ $client->clientid }}
                        </div>
                    </div>

                    <div class="detail">
                        <div class="detail-label">Email Address</div>
                        <div class="detail-value">
                            {{ $client->email ?? 'N/A' }}
                        </div>
                    </div>

                    <div class="detail">
                        <div class="detail-label">Phone Number</div>
                        <div class="detail-value">
                            {{ $client->telephone ?? $client->phone_number ?? 'N/A' }}
                        </div>
                    </div>

                </div>
            </section>

            <section class="section">
                <h2 class="section-title">Account Summary</h2>

                <div class="summary-grid">

{{--                    <div class="summary-card invoiced">--}}
{{--                        <div class="summary-label">Total Invoices</div>--}}
{{--                        <div class="summary-amount">--}}
{{--                            {{ number_format($totalInvoices, 2) }}--}}
{{--                        </div>--}}
{{--                    </div>--}}


{{--                    <div class="summary-card paid">--}}
{{--                        <div class="summary-label">Total Payments</div>--}}
{{--                        <div class="summary-amount">--}}
{{--                            {{ number_format($totalPayments, 2) }}--}}
{{--                        </div>--}}
{{--                    </div>--}}

                    <div class="summary-card invoiced">
                        <div class="summary-label">Message</div>
                        <div class="summary-Details">In order to serve you better, kindly settle the unpaid bills below. If you are using mobile phone to view this page, scroll the invoices to the left to view the pay button for unpaid bills.</div>

                    </div>


                    <div class="summary-card {{ $balance < 0 ? 'credit' : 'balance' }}">
                        <div class="summary-label">
                            {{ $balance < 0 ? 'Client Credit' : 'Outstanding Balance' }}
                        </div>

                        <div class="summary-amount">
                            {{ number_format(abs($balance), 2) }}
                        </div>

                        @if($balance > 0)
                            <span class="status">Payment Due</span>
                        @elseif($balance < 0)
                            <span class="status credit">Overpaid</span>
                        @else
                            <span class="status paid">Fully Paid</span>
                        @endif
                    </div>

                </div>
            </section>

            <section class="section">
                <h2 class="section-title">Invoice History</h2>

                <div class="table-wrapper">
                    <table>
                        <thead>
                        <tr>
{{--                            <th>#</th>--}}
{{--                            <th>Ref</th>--}}
                            <th>Date</th>
                            <th>Description</th>
                            <th class="text-right">Amount</th>
                            <th class="text-right">Paid</th>
                            <th class="text-right">Balance</th>
                            <th class="text-right">Action</th>
                        </tr>
                        </thead>

                        <tbody>
                        @forelse($invoices as $invoice)
                            <tr>
{{--                                <td>{{ $loop->iteration }}</td>--}}

{{--                                <td>--}}
{{--                                    {{ $invoice->invoice_number ?? $invoice->invoicenumber ?? $invoice->id }}--}}
{{--                                </td>--}}

                                <td>
                                    {{ $invoice->invoice_date
                                        ? \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y')
                                        : ($invoice->created_at?->format('d M Y') ?? 'N/A') }}
                                </td>

                                <td>
                                    {{ $invoice->description }}
                                </td>

                                <td class="text-right">
                                    {{ number_format((float) ($invoice->totalamount ?? 0), 2) }}
                                </td>
                                <td class="text-right">
                                    {{ number_format((float) ($invoice->paidamount ?? 0), 2) }}
                                </td>
                                <td class="text-right">
                                    {{ number_format((float) ($invoice->totalamount - $invoice->paidamount ?? 0), 2) }}
                                </td>
{{--                                <td class="text-right">--}}
{{--                                    @if (($invoice->totalamount - $invoice->paidamount) > 0)--}}
{{--                                        <button type="button" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded">--}}
{{--                                            Pay--}}
{{--                                        </button>--}}
{{--                                    @else--}}
{{--                                        {{ number_format(0, 2) }}--}}
{{--                                    @endif--}}
{{--                                </td>--}}


                                <td class="text-right">
                                    @if (($invoice->totalamount - $invoice->paidamount) > 0)
                                        @php
                                            $outstanding = $invoice->totalamount - $invoice->paidamount;
                                        @endphp
                                        <button
                                            type="button"
                                            class="pay-btn bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded"
                                            data-amount="{{ $outstanding }}"
                                            data-invoiceid="{{ $invoice->id }}"
                                            data-invoiceref="{{ $invoice->invoiceno }}"
                                            data-email="{{ $client->email }}"
                                            data-item="{{$invoice->item}}"
                                            data-description="{{$invoice->description}}"
                                        >
                                            Pay
                                        </button>
                                    @else
                                        {{ number_format(0, 2) }}
                                    @endif
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="empty">
                                    No invoices found.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="section">
                <h2 class="section-title">Payment History</h2>

                <div class="table-wrapper">
                    <table>
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Payment Reference</th>
                            <th>Date</th>
                            <th class="text-right">Amount</th>
                        </tr>
                        </thead>

                        <tbody>
                        @forelse($payments as $payment)
                            <tr>
                                <td>{{ $loop->iteration }}</td>

                                <td>
                                    {{ $payment->payment_reference ?? $payment->reference ?? $payment->id }}
                                </td>

                                <td>
                                    {{ $payment->payment_date
                                        ? \Carbon\Carbon::parse($payment->payment_date)->format('d M Y')
                                        : ($payment->created_at?->format('d M Y') ?? 'N/A') }}
                                </td>

                                <td class="text-right">
                                    {{ number_format((float) ($payment->amount ?? 0), 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="empty">
                                    No payments found.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

        </main>

        <footer class="footer">
            <span>TPOS Client Account Statement</span>
            <span>Generated on {{ now()->format('d M Y') }}</span>
        </footer>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const PAYSTACK_PUBLIC_KEY = "{{ config('services.paystack.public_key') }}";
       // const PAYSTACK_PUBLIC_KEY = "pk_test_1135ac82f739e73525044a2d24d29aa83be72488"

        const CSRF_TOKEN = "{{ csrf_token() }}";

        document.querySelectorAll('.pay-btn').forEach(function (button) {
            button.addEventListener('click', function () {
                const amount       = parseFloat(button.dataset.amount);
                const invoiceId    = button.dataset.invoiceid;
                const invoiceRef   = button.dataset.invoiceref;
                const email        = button.dataset.email;
                const description = button.dataset.description
                const item = button.dataset.item

                if (!email) {
                    alert('No email address on file. Please contact support.');
                    return;
                }

                if (!amount || amount <= 0) {
                    alert('Invalid payment amount.');
                    return;
                }

                const handler = PaystackPop.setup({
                    key: PAYSTACK_PUBLIC_KEY,
                    email: email,
                    amount: Math.round(amount * 100), // Paystack uses kobo/pesewas (smallest unit)
                    currency: 'GHS', // Change to 'NGN', 'ZAR', 'USD', 'KES' as needed
                    ref: "{{ $client->clientid }}" +'-'+ invoiceRef + '-' + Date.now(),
                    metadata: {
                        custom_fields: [
                            {
                                display_name: "Invoice Reference",
                                variable_name: "invoice_ref",
                                value: invoiceRef
                            },
                            {
                                display_name: "Client ID",
                                variable_name: "client_id",
                                value: "{{ $client->clientid }}"
                            },
                            {
                                display_name: "description",
                                variable_name: "description",
                                value: description
                            },
                            {
                                display_name: "item",
                                variable_name: "item",
                                value: item
                            }

                        ]
                    },
                    callback: function (response) {
                        // Send reference to your backend for verification
                        fetch("{{ route('paystack.verify') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': CSRF_TOKEN
                            },
                            body: JSON.stringify({
                                reference: response.reference,
                                invoice_id: invoiceId,
                                amount: amount
                            })
                        })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    alert('Payment successful! Reference: ' + response.reference);
                                    window.location.reload();
                                } else {
                                    alert('Payment verification failed: ' + (data.message || 'Unknown error'));
                                }
                            })
                            .catch(err => {
                                console.error(err);
                                alert('Payment received but verification failed. Please contact support with reference: ' + response.reference);
                            });
                    },
                    onClose: function () {
                        console.log('Payment window closed.');
                    }
                });

                handler.openIframe();
            });
        });
    });
</script>

</body>
</html>
