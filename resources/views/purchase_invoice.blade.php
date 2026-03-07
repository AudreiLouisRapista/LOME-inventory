@extends('themes.main')

{{-- 1. DEFINE PAGE TITLE --}}
@section('title', 'Supplier Payment Tracking')

{{-- 2. DEFINE CONTENT HEADER (Breadcrumbs) --}}
@section('content_header')
    <div class="content-header">
        <h1>Supplier Payment Tracking</h1>
        <p>Manage supplier information and payments</p>
    </div>
@endsection

{{-- 3. DEFINE MAIN CONTENT --}}
@section('content')
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .content-wrapper {
            background: #f5f7fa;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .page-header h1 {
            color: #2c3e50;
            font-size: 28px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .filters {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-group label {
            margin-bottom: 5px;
            font-weight: 600;
            color: #555;
            font-size: 14px;
        }

        .filter-group input,
        .filter-group select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .filter-group input:focus,
        .filter-group select:focus {
            outline: none;
            border-color: #3498db;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }



        th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
        }

        td {
            padding: 12px 15px;
            border-bottom: 1px solid #ecf0f1;
            font-size: 14px;
        }

        tbody tr {
            transition: background-color 0.2s;
        }

        tbody tr:hover {
            background-color: #f8f9fa;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-unpaid {
            background: #e74c3c;
            color: white;
        }

        .status-partial {
            background: #f39c12;
            color: white;
        }

        .status-paid {
            background: #27ae60;
            color: white;
        }

        .status-overdue {
            background: #c0392b;
            color: white;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-primary {
            background: #3498db;
            color: white;
        }

        .btn-primary:hover {
            background: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(52, 152, 219, 0.3);
        }

        .btn-success {
            background: #27ae60;
            color: white;
        }

        .btn-success:hover {
            background: #229954;
        }

        .btn-secondary {
            background: #95a5a6;
            color: white;
        }

        .btn-secondary:hover {
            background: #7f8c8d;
        }

        .btn-add {
            background: #4caf50;
            color: white;
            padding: 12px 24px;
            font-size: 15px;
        }

        .btn-add:hover {
            background: #3e8e41;
            color: white !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(155, 89, 182, 0.3);
        }

        .btn-add::before {
            content: "+ ";
            font-size: 18px;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 99999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.3s;
            overflow-y: auto;
        }

        .modal.show {
            display: block !important;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .modal-content {
            background-color: white;
            margin: 3% auto;
            padding: 0;
            border-radius: 10px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
            animation: slideDown 0.3s;
            position: relative;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            padding: 20px 30px;
            background: #cc0c19;
            color: white;
            border-radius: 10px 10px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header.add-invoice {
            background: #cc0c19;
        }

        .modal-header h2 {
            font-size: 22px;
            margin: 0;
        }

        .close {
            color: white;
            font-size: 32px;
            font-weight: bold;
            cursor: pointer;
            line-height: 20px;
            transition: color 0.3s;
        }

        .close:hover {
            color: #ecf0f1;
        }

        .modal-body {
            padding: 30px;
            max-height: 70vh;
            overflow-y: auto;
        }

        .invoice-details {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }

        .invoice-details h3 {
            margin-bottom: 15px;
            color: #2c3e50;
            font-size: 18px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 600;
            color: #555;
        }

        .detail-value {
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            font-family: inherit;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .error-message {
            color: #e74c3c;
            font-size: 13px;
            margin-top: 5px;
            display: none;
        }

        .modal-footer {
            padding: 20px 30px;
            background: #f8f9fa;
            border-radius: 0 0 10px 10px;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .amount-highlight {
            font-size: 18px;
            font-weight: bold;
            color: #27ae60;
        }

        .required::after {
            content: " *";
            color: #e74c3c;
        }

        .info-box {
            background: #e8f4f8;
            border-left: 4px solid #3498db;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-size: 13px;
            color: #2c3e50;
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }

            .filters {
                grid-template-columns: 1fr;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            table {
                font-size: 12px;
            }

            th,
            td {
                padding: 8px;
            }

            .modal-content {
                width: 95%;
                margin: 5% auto;
            }

            .page-header {
                flex-direction: column;
                align-items: stretch;
            }

            .btn-add {
                width: 100%;
            }
        }
    </style>

    <div class="container">
        <div class="page-header">
            <h1>Supplier Payment Tracking</h1>
            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
                    <i class="fas fa-plus"></i> Add Supplier
                </button>




            </div>
        </div>

        <div class="filters">
            <div class="filter-group">
                <label for="filterSupplier">Supplier</label>
                <select id="filterSupplier">
                    <option value="">All Suppliers</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->supplier_id }}">{{ $supplier->supplier_name }}</option>
                    @endforeach
                </select>
            </div>

            @include('layout.partials.alerts')
            <div class="filter-group">
                <label for="filterStatus">Status</label>
                <select id="filterStatus">
                    <option value="">All Status</option>
                    <option value="unpaid">Unpaid</option>
                    <option value="partial">Partial</option>
                    <option value="paid">Paid</option>
                </select>
            </div>
        </div>

        <div class="card-body">
            <table id="example2" class="table table-bordered table-hover display block" style="width:100%">
                <thead style="text-align: cent ">
                    <tr>
                        <th>Invoice #</th>
                        <th>Supplier</th>
                        <th>Invoice Date</th>
                        <th>Due Date</th>
                        <th>Gross Amount</th>
                        <th>VAT amount</th>
                        <th>Net Amount</th>
                        <th>Total Paid</th>
                        <th>Remaining Balance</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody style="font-family: 'Inter', sans-serif; text-align: center;">
                    @foreach ($purchases as $row)
                        @php
                            $remaining = $row->net_amount - ($row->total_paid ?? 0);
                        @endphp
                        <tr>
                            <td>{{ $row->invoice_number }}</td>
                            <td>{{ $row->supplier_name }}</td>
                            <td>{{ \Carbon\Carbon::parse($row->invoice_date)->format('Y-m-d') }}</td>
                            <td>{{ \Carbon\Carbon::parse($row->due_date)->format('Y-m-d') }}</td>
                            <td>{{ number_format($row->gross_amount, 2) }}</td>
                            <td>{{ number_format($row->vat_amount, 2) }}</td>
                            <td>₱{{ number_format($row->net_amount, 2) }}</td>
                            <td>₱{{ number_format($row->total_paid ?? 0, 2) }}</td>
                            <td class="text-danger">₱{{ number_format($remaining, 2) }}</td>
                            <td>
                                <span class="status-badge status-{{ strtolower($row->status) }}">
                                    {{ $row->status }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1 justify-content-center">
                                    <button type="button" class="btn btn-sm btn-info text-white" data-bs-toggle="modal"
                                        data-bs-target="#viewItemsModal{{ $row->purchase_id }}">
                                        View Item
                                    </button>

                                    @if ($remaining > 0.01)
                                        <button type="button" class="btn btn-sm btn-primary pay-btn"
                                            data-id="{{ $row->purchase_id }}" data-invoice="{{ $row->invoice_number }}"
                                            data-remaining="{{ $row->net_amount - ($row->total_paid_sum ?? 0) }}"
                                            data-net="{{ $row->net_amount }}"> {{-- IMPORTANT --}}
                                            Pay
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>



        <div class="modal fade" id="addSupplierModal" tabindex="-1" aria-labelledby="addSupplierModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="addSupplierModalLabel text-white">Add Supplier</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form id="addSupplierForm">
                        <div class="modal-body">
                            <div class="alert alert-info py-2 small">
                                Fields marked with <span class="text-danger">*</span> are required.
                            </div>
                            <div class="mb-3">
                                <label for="supplierName" class="form-label small fw-bold">Supplier Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="supplierName" name="supplier_name"
                                    placeholder="e.g., ABC Trading" required>
                            </div>
                            <div class="mb-3">
                                <label for="supplierAddress" class="form-label small fw-bold">Address <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="supplierAddress" name="address"
                                    placeholder="Supplier address" required>
                            </div>
                            <div class="mb-3">
                                <label for="supplierContact" class="form-label small fw-bold">Contact No. <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="supplierContact" name="contact_no"
                                    placeholder="e.g., 09xxxxxxxxx" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="saveNewSupplier">Save Supplier</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Payment Modal -->
        <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title text-white" id="paymentModalLabel">Record Payment</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <form action="{{ route('storePayment') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="p-3 mb-3 border rounded bg-light">
                                <h6 class="border-bottom pb-2 mb-3">Invoice Details</h6>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted small">Invoice Number:</span>
                                    <span class="fw-bold small text-dark" id="modalInvoiceNumber"></span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted small">Remaining Balance:</span>
                                    <span class="fw-bold text-danger" id="modalRemainingBalance"></span>
                                </div>
                            </div>

                            <input type="hidden" name="purchase_id" id="modal_purchase_id">
                            <input type="hidden" name="old_remaining_balance" id="old_remaining_balance">
                            <div class="mb-3">
                                <label for="paymentDate" class="form-label small fw-bold">Payment Date</label>
                                <input type="date" name="payment_date" id="paymentDate" class="form-control"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="amountPaid" class="form-label small fw-bold">Amount Paid</label>
                                <div class="input-group">
                                    <span class="input-group-text">₱</span>
                                    <input type="number" name="amount_paid" id="amountPaid" step="0.01"
                                        min="0" class="form-control" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="paymentMethod" class="form-label small fw-bold">Payment Method</label>
                                <select name="payment_method" id="paymentMethod" class="form-select" required>
                                    <option value="">-- Select Method --</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Check">Check</option>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                    <option value="Mobile Payment">Mobile Payment</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="referenceNumber" class="form-label small fw-bold">Reference Number</label>
                                <input type="text" name="reference_number" id="referenceNumber" class="form-control"
                                    placeholder="e.g. Check #, Trans ID">
                            </div>



                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success px-4">
                                Save Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        {{-- View item --}}

        @foreach ($purchases as $purchase)
            <div class="modal fade" id="viewItemsModal{{ $purchase->purchase_id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg">
                        <div class="modal-header bg-dark text-white py-3">
                            <h5 class="modal-title d-flex align-items-center">
                                <i class="fas fa-file-invoice me-2 text-info"></i>
                                Invoice: <span class="ms-2">{{ $purchase->invoice_number }}</span>
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body p-4">
                            <div class="row mb-4 border-bottom pb-3">
                                <div class="col-6">
                                    <small class="text-muted d-block text-uppercase fw-bold small">Supplier</small>
                                    <span class="fw-bold h6">{{ $purchase->supplier_name }}</span>
                                </div>
                                <div class="col-6 text-end">
                                    <small class="text-muted d-block text-uppercase fw-bold small">Date Received</small>
                                    <span>{{ $purchase->invoice_date }}</span>
                                </div>
                            </div>

                            <div class="table-responsive rounded border shadow-sm">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light text-uppercase small fw-bold">
                                        <tr>
                                            <th>Quantity</th>
                                            <th>UOM</th>
                                            <th>Description</th>
                                            <th>U. Price</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Access the grouped items using the purchase ID as the key --}}
                                        @if (isset($purchase_items[$purchase->purchase_id]))
                                            @foreach ($purchase_items[$purchase->purchase_id] as $item)
                                                <tr>
                                                    <td>{{ $item->uom_quantity }}</td>
                                                    <td>{{ $item->uom_title }}</td>
                                                    <td>{{ $item->product_name }}</td>
                                                    <td>₱{{ number_format($item->unit_price, 2) }}</td>
                                                    <td class="fw-bold">₱{{ number_format($item->total_price, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">No items found for this
                                                    invoice.</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="modal-footer bg-light p-3">
                            <div class="me-auto">
                                <small class="text-muted text-uppercase small d-block">Net Amount</small>
                                <span
                                    class="h4 fw-bold text-primary">₱{{ number_format($purchase->net_amount, 2) }}</span>
                            </div>
                            <button type="button" class="btn btn-secondary px-4 shadow-sm"
                                data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach


    @endsection

    @section('tables')
        <script>
            $(document).ready(function() {
                // 1. Initialize DataTable once
                var table = $('#example2').DataTable({
                    "paging": true,
                    "searching": true,
                    "ordering": true,
                    "responsive": true,
                    "destroy": true
                });

                // 2. Pay Button Handler (Only ONE handler for the table buttons)
                $(document).on('click', '.pay-btn', function(e) {
                    // Prevent action if this is the "Save" button inside the modal
                    if ($(this).closest('.modal-footer').length > 0) return;

                    let id = $(this).data('id');
                    let invoice = $(this).data('invoice');
                    let balance = $(this).data('remaining');
                    let net = $(this).data('net'); // Original net value

                    // Fill the modal fields
                    $('#modal_purchase_id').val(id);
                    $('#old_remaining_balance').val(net);
                    $('#modalInvoiceNumber').text(invoice);

                    // Formatting for display
                    let formattedBalance = parseFloat(balance).toLocaleString(undefined, {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });

                    $('#modalRemainingBalance').text('₱' + formattedBalance);
                    $('#amountPaid').val(parseFloat(balance).toFixed(2));

                    // Open modal
                    $('#paymentModal').modal('show');
                });

                // 3. Filter Handler
                $('#filterSupplier').on('change', function() {
                    table.column(1).search(this.value).draw();
                });
            });
        </script>




    @endsection
