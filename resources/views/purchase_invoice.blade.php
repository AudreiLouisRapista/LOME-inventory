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
    <link rel="stylesheet" href="{{ asset('css/pages/purchase_invoice_style.css') }}">

    <div class="container-fluid px-4">
        <div class="main-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold m-0">Invoices & Payments</h5>
                <button type="button" class="btn btn-primary px-4 shadow-sm" data-bs-toggle="modal"
                    data-bs-target="#addSupplierModal" style="border-radius: 8px;">
                    <i class="fas fa-plus me-2"></i> Add Supplier
                </button>
            </div>

            <div class="filters">
                <div class="filter-group">
                    <label for="filterSupplier">Supplier Name</label>
                    <select id="filterSupplier" class="form-select shadow-none">
                        <option value="">All Suppliers</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->supplier_id }}">{{ $supplier->supplier_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label for="filterStatus">Payment Status</label>
                    <select id="filterStatus" class="form-select shadow-none">
                        <option value="">All Statuses</option>
                        <option value="unpaid">Unpaid</option>
                        <option value="partial">Partial</option>
                        <option value="paid">Paid</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table id="example2" class="table">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Supplier</th>
                            <th>Dates</th>
                            <th>Financials</th>
                            <th>Total Paid</th>
                            <th>Balance</th>
                            <th>Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($purchases as $row)
                            @php
                                $remaining = $row->net_amount - ($row->total_paid ?? 0);
                            @endphp
                            <tr>
                                <td class="fw-bold text-dark">{{ $row->invoice_number }}</td>
                                <td>{{ $row->supplier_name }}</td>
                                <td>
                                    <div class="small">Inv:
                                        {{ \Carbon\Carbon::parse($row->invoice_date)->format('M d, Y') }}</div>
                                    <div class="small text-danger fw-bold">Due:
                                        {{ \Carbon\Carbon::parse($row->due_date)->format('M d, Y') }}</div>
                                </td>
                                <td>
                                    <div class="small text-muted">Gross: {{ number_format($row->gross_amount, 2) }}</div>
                                    <div class="fw-bold">Net: ₱{{ number_format($row->net_amount, 2) }}</div>
                                </td>
                                <td class="text-success fw-bold">₱{{ number_format($row->total_paid ?? 0, 2) }}</td>
                                <td class="text-danger fw-bold">₱{{ number_format($remaining, 2) }}</td>
                                <td>
                                    <span class="status-badge status-{{ strtolower($row->status) }}">
                                        {{ $row->status }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2 justify-content-center">
                                        <button type="button" class="action-btn btn-view" data-bs-toggle="modal"
                                            data-bs-target="#viewItemsModal{{ $row->purchase_id }}">
                                            <i class="fas fa-eye"></i> View
                                        </button>

                                        @if ($remaining > 0.01)
                                            <button type="button" class="action-btn btn-pay pay-btn"
                                                data-id="{{ $row->purchase_id }}"
                                                data-invoice="{{ $row->invoice_number }}"
                                                data-remaining="{{ $remaining }}" data-net="{{ $row->net_amount }}">
                                                <i class="fas fa-credit-card"></i> Pay
                                            </button>
                                        @else
                                            <button type="button" class="action-btn btn-viewPaymentHistory"
                                                data-bs-toggle="modal" data-bs-target="#paymentHistoryModal"
                                                data-invoice="{{ $row->invoice_number }}"
                                                data-id="{{ $row->purchase_id }}">
                                                <i class="fas fa-history"></i> History
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <div class="modal fade" id="addSupplierModal" tabindex="-1" aria-labelledby="addSupplierModalLabel" aria-hidden="true">
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
                <div class="modal-header bg-white text-primary">

                    <h5 class="modal-title text-primary fw-bold" id="paymentModalLabel">Record Payment</h5>
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
                                <input type="number" name="amount_paid" id="amountPaid" step="0.01" min="0"
                                    class="form-control" required>
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
                        <button type="submit" class="btn btn-primary px-4">
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
                                        <th>Quantity Per Pcs</th>
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
                                                <td>{{ $item->tie_total }}</td>
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
                            <span class="h4 fw-bold text-primary">₱{{ number_format($purchase->net_amount, 2) }}</span>
                        </div>
                        <button type="button" class="btn btn-secondary px-4 shadow-sm"
                            data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach


    {{-- Payment History --}}
    <div class="modal fade" id="paymentHistoryModal" tabindex="-1" aria-labelledby="paymentHistoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-white border-bottom-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold" id="paymentHistoryModalLabel">
                        <i class="fas fa-receipt text-success me-2"></i> Payment History
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body px-4 pb-4">
                    <div class="d-flex justify-content-between align-items-center p-3 mb-4 rounded-3"
                        style="background-color: #f8f9fa; border: 1px solid #e9ecef;">
                        <div>
                            <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 10px;">Invoice
                                Number</small>
                            <span id="modal-invoice-no" class="fw-bold text-dark">-</span>
                        </div>
                        <div class="text-end">
                            <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 10px;">Total
                                Settled</small>
                            <span id="modal-total-paid" class="fw-bold text-success fs-5">₱ 0.00</span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="history-table">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0 text-muted" style="font-size: 12px;">DATE</th>
                                    <th class="border-0 text-muted" style="font-size: 12px;">REF NO.</th>
                                    <th class="border-0 text-muted" style="font-size: 12px;">METHOD</th>
                                    <th class="border-0 text-muted text-end" style="font-size: 12px;">AMOUNT</th>
                                </tr>
                            </thead>
                            <tbody id="payment-history-data">
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer border-top-0 px-4 pb-4">
                    <button type="button" class="btn btn-light fw-bold text-muted" data-bs-dismiss="modal"
                        style="font-size: 13px;">Close</button>
                    <button type="button" class="btn btn-primary fw-bold" onclick="window.print()"
                        style="font-size: 13px;">
                        <i class="fas fa-print me-1"></i> Print Receipt
                    </button>
                </div>
            </div>
        </div>
    </div>



@endsection

@section('scripts src')
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

            $('#paymentHistoryModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var invoice = button.data('invoice');
                var purchaseId = button.data('id');

                var modal = $(this);
                modal.find('#modal-invoice-no').text(invoice);

                $('#payment-history-data').html(
                    '<tr><td colspan="4" class="text-center">Loading...</td></tr>');

                $.ajax({
                    // Updated URL to match the specific payment route
                    url: '/admin/getPaymentHistory/' + purchaseId,
                    method: 'GET',
                    success: function(response) {
                        let rows = '';
                        let total = 0;

                        if (response.length === 0) {
                            rows =
                                '<tr><td colspan="4" class="text-center">No payments found.</td></tr>';
                        } else {
                            response.forEach(payment => {
                                // Use the correct column name from your DB (e.g., amount_paid)
                                let amt = parseFloat(payment.amount_paid) || 0;
                                total += amt;

                                rows += `
                        <tr>
                            <td class="small text-muted">${payment.payment_date}</td>
                            <td class="fw-semibold">${payment.reference_number ?? 'N/A'}</td>
                            <td><span class="badge bg-light text-dark border">${payment.payment_method}</span></td>
                            <td class="text-end fw-bold">₱ ${amt.toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                        </tr>
                    `;
                            });
                        }

                        $('#payment-history-data').html(rows);
                        $('#modal-total-paid').text('₱ ' + total.toLocaleString(undefined, {
                            minimumFractionDigits: 2
                        }));
                    },
                    error: function() {
                        $('#payment-history-data').html(
                            '<tr><td colspan="4" class="text-center text-danger">Error loading data.</td></tr>'
                        );
                    }
                });
            });
        });
    </script>




@endsection
