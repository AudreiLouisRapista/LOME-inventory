@extends('themes.main')

{{-- 1. DEFINE PAGE TITLE --}}
@section('title', 'Supplier Payment Tracking')

{{-- 2. DEFINE CONTENT HEADER (Breadcrumbs) --}}
@section('content_header')
    <div class="content-header">
        <h1>Supplier Payment Tracking</h1>
        <p>Manage supplier invoices and payments</p>
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
            <h1>💰 Supplier Payment Tracking</h1>
            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
                    <i class="fas fa-plus"></i> Add Supplier
                </button>

                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addInvoiceModal">
                    <i class="fas fa-file-invoice"></i> Add New Invoice
                </button>

                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#addItemModal">
                    <i class="fas fa-cart-plus"></i> Add Items
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


                </tbody>
            </table>
        </div>

        <div class="modal fade" id="addInvoiceModal" tabindex="-1" aria-labelledby="addInvoiceModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title" id="addInvoiceModalLabel text-white">Add New Invoice</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form id="addInvoiceForm">
                        <div class="modal-body">
                            <div class="alert alert-info py-2 small">
                                Fill in the invoice details below. Fields marked with <span class="text-danger">*</span> are
                                required.
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="invoiceNumber" class="form-label small fw-bold">Invoice Number <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="invoiceNumber" name="invoice_number"
                                        placeholder="e.g., 10700003440" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="supplier" class="form-label small fw-bold">Supplier <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="supplier" name="supplier_id" required>
                                        <option value="" disabled selected>Select Supplier</option>
                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier->supplier_id }}">{{ $supplier->supplier_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold small">Invoice Date</label>
                                    <input type="date" name="invoice_date" class="form-control"
                                        value="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold small">Due Date</label>
                                    <input type="date" name="due_date" class="form-control" required>
                                </div>
                            </div>

                            <hr>

                            <div class="row">

                                <div class="col-md-4 mb-3">
                                    <label for="new_gross" class="form-label small fw-bold">Gross Amount <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">₱</span>
                                        <input type="number" class="form-control" id="new_gross" name="gross_amount"
                                            step="0.01" min="0.01" required>
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="new_vat" class="form-label small fw-bold">VAT Amount <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">₱</span>
                                        <input type="number" class="form-control" id="new_vat" name="vat_amount"
                                            step="0.01" min="0" required>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="new_net" class="form-label small fw-bold text-primary">Net Amount
                                        (Auto)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">₱</span>
                                        <input type="number" class="form-control bg-light" id="new_net"
                                            name="net_amount" step="0.01" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-success" id="saveInvoiceForm">Save Invoice</button>
                        </div>
                    </form>
                </div>
            </div>
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
        <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="paymentModalLabel text-white">Record Payment</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <form id="paymentForm">
                        <div class="modal-body">
                            <div class="p-3 mb-3 border rounded bg-light">
                                <h6 class="border-bottom pb-2 mb-3">Invoice Details</h6>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted small">Invoice Number:</span>
                                    <span class="fw-bold small" id="modalInvoiceNumber"></span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted small">Remaining Balance:</span>
                                    <span class="fw-bold text-danger" id="modalRemainingBalance"></span>
                                </div>
                            </div>

                            <input type="hidden" name="purchase_id" id="modal_purchase_id">

                            <div class="mb-3">
                                <label for="paymentDate" class="form-label small fw-bold">Payment Date</label>
                                <input type="date" id="paymentDate" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="amountPaid" class="form-label small fw-bold">Amount Paid</label>
                                <div class="input-group">
                                    <span class="input-group-text">₱</span>
                                    <input type="number" id="amountPaid" step="0.01" min="0"
                                        class="form-control" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="paymentMethod" class="form-label small fw-bold">Payment Method</label>
                                <select id="paymentMethod" class="form-select" required>
                                    <option value="">-- Select Method --</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Check">Check</option>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                    <option value="Mobile Payment">Mobile Payment</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="referenceNumber" class="form-label small fw-bold">Reference Number</label>
                                <input type="text" id="referenceNumber" class="form-control"
                                    placeholder="e.g. Check #, Trans ID">
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-success px-4" id="savePaymentBtn">Save Payment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Add Items Modal -->
        <div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title" id="addItemLabel">
                            <i class="fas fa-plus-circle me-2"></i>Add New Item to Invoice
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <form id="purchaseItemsForm">
                        @csrf
                        <div class="modal-body p-4">
                            <div class="mb-4">
                                <label class="form-label fw-bold small text-uppercase text-muted">Target Invoice *</label>
                                <select name="purchase_id" id="select_purchase_id" class="form-select border-info"
                                    required>
                                    <option value="">-- Select Invoice Number --</option>
                                    @foreach ($purchases as $purchase)
                                        <option value="{{ $purchase->purchase_id }}">
                                            INV: {{ $purchase->invoice_number }} |
                                            {{ $purchase->supplier->supplier_name ?? 'N/A' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label small fw-bold">Product Description *</label>
                                    <input type="text" name="product_name" id="item_product_input"
                                        class="form-control" list="product_list"
                                        placeholder="Search or type product name..." required>
                                    <datalist id="product_list">
                                        @foreach ($products as $product)
                                            <option value="{{ $product->product_name }}"
                                                data-id="{{ $product->product_ID }}">
                                        @endforeach
                                    </datalist>
                                    <input type="hidden" name="product_id" id="hidden_product_id">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">Expiry Date</label>
                                    <input type="date" name="expiry_date" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">Quantity *</label>
                                    <input type="number" name="quantity" id="item_qty" class="form-control"
                                        value="1" min="1" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">Unit (UOM) *</label>
                                    <select name="uom_id" id="item_uom" class="form-select" required>
                                        <option value="">Select Unit...</option>
                                        @foreach ($uoms as $uom)
                                            <option value="{{ $uom->uom_ID }}">{{ $uom->uom_title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">Unit Per Quantity *</label>
                                    <input type="number" name="uom_per_quantity" id="uom_per_quantity"
                                        class="form-control" step="0.01" value="1" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Unit Price *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₱</span>
                                        <input type="number" name="unit_price" id="item_uprice" class="form-control"
                                            step="0.01" placeholder="0.00" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-primary">Total Price (Subtotal)</label>
                                    <input type="text" id="total_price" class="form-control bg-light" readonly
                                        placeholder="0.00">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-success">Total Amount</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-success text-white">₱</span>
                                        <input type="text" id="total_amount"
                                            class="form-control bg-light fw-bold text-success" readonly
                                            placeholder="0.00">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer bg-light border-0">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success px-4 fw-bold" id="saveItemsBtn">
                                <i class="fas fa-save me-2"></i>Save Item
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- View item --}}
        <div class="modal fade" id="viewItemsModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-dark text-white py-3">
                        <h5 class="modal-title d-flex align-items-center">
                            <i class="fas fa-file-invoice me-2 text-info"></i>
                            Invoice Detail: <span id="view_invoice_no" class="ms-2">---</span>
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body p-4">
                        <div class="row mb-4 border-bottom pb-3">
                            <div class="col-6">
                                <small class="text-muted d-block text-uppercase fw-bold small">Supplier</small>
                                <span id="view_supplier_name" class="fw-bold h6">---</span>
                            </div>
                            <div class="col-6 text-end">
                                <small class="text-muted d-block text-uppercase fw-bold small">Date Received</small>
                                <span id="view_invoice_date">---</span>
                            </div>
                        </div>

                        <div class="table-responsive rounded border shadow-sm">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light text-uppercase small fw-bold">
                                    <tr>
                                        <th style="width: 25%">Quantity</th>
                                        <th style="width: 23%">UOM</th>
                                        <th style="width: 23%">Description</th>
                                        <th style="width: 23%">Exp. Date</th>
                                        <th style="width: 23%">U. Price</th>
                                        <th style="width: 23%">Price</th>
                                        <th style="width: 23%">Amount</th>


                                    </tr>
                                </thead>
                                <tbody id="itemRows">
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="modal-footer bg-light p-3">
                        <div class="me-auto">
                            <small class="text-muted text-uppercase small d-block">Net Amount</small>
                            <span class="h4 fw-bold text-primary">₱<span id="view_net_amount">0.00</span></span>
                        </div>
                        <button type="button" class="btn btn-secondary px-4 shadow-sm"
                            data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

    @endsection

    @section('tables')
        <script>
            $(document).ready(function() {
                // 1. Initialize DataTable
                var table = $('#example2').DataTable({
                    "paging": true,
                    "searching": true,
                    "ordering": true,
                    "responsive": true,
                    "destroy": true, // Corrected: keep it as a property
                    "ajax": {
                        "url": "{{ route('purchase_invoice') }}",
                        "dataSrc": "data"
                    },
                    "columns": [{
                            "data": "invoice_number"
                        },
                        {
                            "data": "supplier.supplier_name",
                            "defaultContent": "N/A"
                        },
                        {
                            "data": "invoice_date",
                            "render": function(data) {
                                return data ? data.split('T')[0] : '---';
                            }
                        },
                        {
                            "data": "due_date",
                            "render": function(data) {
                                return data ? data.split('T')[0] : '---';
                            }
                        },
                        {
                            "data": "gross_amount",
                            "defaultContent": "0.00"
                        },
                        {
                            "data": "vat_amount",
                            "defaultContent": "0.00"
                        },
                        {
                            "data": "net_amount",
                            "render": function(data) {
                                return '₱' + parseFloat(data || 0).toLocaleString(undefined, {
                                    minimumFractionDigits: 2
                                });
                            }
                        },
                        {
                            "data": "total_paid_sum",
                            "render": function(data) {
                                return '₱' + parseFloat(data || 0).toLocaleString(undefined, {
                                    minimumFractionDigits: 2
                                });
                            }
                        },
                        {
                            "data": null,
                            "render": function(data, type, row) {
                                let net = parseFloat(row.net_amount || 0);
                                let paid = parseFloat(row.total_paid_sum || 0);
                                let remaining = net - paid;
                                return '<span class="text-danger">₱' + remaining.toLocaleString(
                                    undefined, {
                                        minimumFractionDigits: 2
                                    }) + '</span>';
                            }
                        },
                        {
                            "data": "status"
                        },
                        {
                            "data": null,
                            "orderable": false,
                            "render": function(data, type, row) {
                                let net = parseFloat(row.net_amount || 0);
                                let paid = parseFloat(row.total_paid_sum || 0);
                                let remaining = net - paid;

                                // Using a d-flex wrapper with gap for perfect alignment
                                let html = `<div class="d-flex gap-1 justify-content-center">`;

                                // View Button (Teal/Info)
                                // View Button (Teal/Info) - Updated to trigger Modal
                                html += `<button type="button" class="btn btn-sm btn-info text-white view-items-btn" data-id="${row.purchase_id}">
                                    View Item
                                </button>`;

                                // Pay Button (Blue/Primary) - Only shows if there is a balance
                                if (remaining > 0.01) {
                                    html += `<button class="btn btn-sm btn-primary pay-btn" 
                                    data-id="${row.purchase_id}" 
                                    data-invoice="${row.invoice_number}" 
                                    data-remaining="${remaining.toFixed(2)}">
                                    Pay
                                </button>`;
                                }

                                html += `</div>`;
                                return html;
                            }
                        }
                    ]
                });

                // 2. Click Handler - Fixed for dynamic elements
                $(document).on('click', '.pay-btn', function() {
                    // Use $(this) to get the specific button clicked
                    var btn = $(this);
                    var purchaseId = btn.data('id');
                    var invoiceNum = btn.data('invoice');
                    var remaining = btn.data('remaining');

                    // Fill Modal Fields
                    $('#modalInvoiceNumber').text(invoiceNum);
                    $('#modalRemainingBalance').text('₱' + parseFloat(remaining).toLocaleString(undefined, {
                        minimumFractionDigits: 2
                    }));

                    // Ensure these IDs match your HTML precisely
                    $('#modal_purchase_id').val(purchaseId);
                    $('#amountPaid').val(remaining);

                    // Show Modal
                    if ($.fn.modal) {
                        $('#paymentModal').modal('show');
                    } else {
                        $('#paymentModal').addClass('show').css('display', 'block');
                    }
                });

                // 3. Save Payment - AJAX Fix
                $('#savePaymentBtn').off('click').on('click', function(e) {
                    e.preventDefault();

                    let formData = {
                        _token: "{{ csrf_token() }}",
                        purchase_id: $('#modal_purchase_id').val(),
                        amount_paid: $('#amountPaid').val(),
                        payment_date: $('#paymentDate').val(),
                        payment_method: $('#paymentMethod').val(),
                        reference_number: $('#referenceNumber').val()
                    };

                    $.ajax({
                        url: "/admin/storePayment",
                        method: "POST",
                        data: formData,
                        success: function(res) {
                            // 1. Try the standard way
                            if ($.fn.modal) {
                                $('#paymentModal').modal('hide');
                            }

                            // 2. The "Force Close" (Works even if Bootstrap is broken)
                            $('#paymentModal').removeClass('show');
                            $('body').removeClass('modal-open');
                            $('.modal-backdrop').remove(); // This removes the dark grey background
                            $('#paymentModal').hide();

                            table.ajax.reload(null, false);
                            alert('Payment recorded successfully!');
                        },
                        error: function(xhr) {
                            alert('Error: ' + xhr.responseText);
                        }
                    });
                });

                $(document).ready(function() {
                    // Listen to Gross Amount (the Total)
                    $('#new_gross').on('input', function() {
                        let totalVal = $(this).val();

                        if (totalVal !== "") {
                            let total = parseFloat(totalVal);

                            // Math to match your receipt
                            let vatableSales = total / 1.12;
                            let vatAmount = total - vatableSales;

                            // Update the other fields
                            // We use .val() so it fills up automatically
                            $('#new_vat').val(vatAmount.toFixed(2));

                            // On your receipt, Net Amount is the same as Gross Amount
                            $('#new_net').val(total.toFixed(2));
                        } else {
                            $('#new_vat').val('');
                            $('#new_net').val('');
                        }
                    });

                    // Formatting only on blur to prevent cursor jumping
                    $('#new_gross').on('blur', function() {
                        let val = parseFloat($(this).val());
                        if (!isNaN(val)) {
                            $(this).val(val.toFixed(2));
                        }
                    });
                });

                // 2. Formatting (Only when you finish typing)
                $('#new_net').on('blur', function() {
                    let val = parseFloat($(this).val());
                    if (!isNaN(val)) {
                        // Now it's safe to add the .00 without breaking your typing
                        $(this).val(val.toFixed(2));
                    }
                });

                $('#saveInvoiceForm').on('click', function(e) {
                    e.preventDefault();

                    // 1. Collect the data from the form
                    let formData = {
                        _token: "{{ csrf_token() }}", // Required by Laravel for security
                        invoice_number: $('#invoiceNumber').val(),
                        supplier_id: $('#supplier').val(),
                        invoice_date: $('input[name="invoice_date"]').val(),
                        due_date: $('input[name="due_date"]').val(),
                        gross_amount: $('#new_gross').val(), // Vatable Sales
                        vat_amount: $('#new_vat').val(), // VAT 12%
                        net_amount: $('#new_net').val() // Total Sales
                    };

                    // 2. Send to Laravel via AJAX
                    $.ajax({
                        url: "{{ route('save_invoice') }}", // Make sure this route exists!
                        type: "POST",
                        data: formData,
                        success: function(response) {
                            // Close the modal and refresh the table
                            $('#addInvoiceModal').modal('hide');
                            alert("Invoice Saved Successfully!");
                            location.reload(); // Or use table.ajax.reload() if using DataTables
                        },
                        error: function(xhr) {
                            // Show errors (like duplicate invoice numbers)
                            alert("Error: " + xhr.responseText);
                        }
                    });
                });

                $(document).ready(function() {
                    // This makes the "Add Row" button actually do something
                    $('#addRow').on('click', function() {
                        const newRow = getProfessionalRow();
                        $('#itemRows').append(newRow);
                    });

                    // Remove row logic
                    $(document).on('click', '.remove-row', function() {
                        $(this).closest('tr').remove();
                        calculateGrandTotal(); // Recalculate if a row is deleted
                    });
                });


                function getProfessionalRow() {
                    // We convert the PHP arrays to JS objects safely
                    const products = {!! json_encode($products) !!};
                    const uoms = {!! json_encode($uoms) !!};

                    let productOptions = '<option value="">Select Product...</option>';
                    products.forEach(p => {
                        productOptions += `<option value="${p.product_ID}">${p.product_name}</option>`;
                    });

                    let uomOptions = '<option value="">Unit...</option>';
                    uoms.forEach(u => {
                        uomOptions += `<option value="${u.uom_ID}">${u.uom_name}</option>`;
                    });

                    return `
                        <tr class="item-row">
                            <td><select name="product_id[]" class="form-select border-0 bg-transparent shadow-none" required>${productOptions}</select></td>
                            <td><select name="uom_id[]" class="form-select border-0 bg-transparent shadow-none" required>${uomOptions}</select></td>
                            <td><input type="number" name="quantity_per_uom[]" class="form-control qty-input border-0 bg-transparent shadow-none" value="1" min="1" required></td>
                            <td>
                                <div class="input-group input-group-sm border-0">
                                    <span class="input-group-text bg-transparent border-0">₱</span>
                                    <input type="number" name="unit_price[]" class="form-control price-input border-0 bg-transparent shadow-none" step="0.01" placeholder="0.00" required>
                                </div>
                            </td>
                            <td><input type="text" class="form-control bg-light fw-bold border-0 row-total" readonly value="0.00"></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-link text-danger remove-row p-0 shadow-none"><i class="fas fa-trash-alt"></i></button>
                            </td>
                        </tr>`;
                }

                $(document).ready(function() {
                    // 1. Datalist ID Handler (Prevents "Zero" or "Null" Product IDs)
                    $('#item_product_input').on('input', function() {
                        let val = $(this).val();
                        let option = $('#product_list option').filter(function() {
                            return $(this).val() === val;
                        });
                        // If found, set hidden ID; if not, leave empty so Controller creates new product
                        $('#hidden_product_id').val(option.length ? option.data('id') : '');
                    });

                    // 2. Automated 
                    $(document).on('input', '#uom_per_quantity, #item_uprice, #item_qty', function() {
                        let uomPer = parseFloat($('#uom_per_quantity').val()) || 0;
                        let uPrice = parseFloat($('#item_uprice').val()) || 0;
                        let qty = parseFloat($('#item_qty').val()) || 0;

                        // Formula 1: Total Price = UOM Per Unit * Unit Price
                        let totalPrice = uomPer * uPrice;
                        $('#total_price').val(totalPrice.toFixed(2));

                        // Formula 2: Total Amount = Total Price * Quantity
                        let totalAmount = totalPrice * qty;
                        $('#total_amount').val(totalAmount.toFixed(2));
                    });

                    // 3. Save Item Ajax
                    $('#saveItemsBtn').on('click', function(e) {
                        e.preventDefault();

                        if (!$('#select_purchase_id').val() || !$('#item_product_input').val()) {
                            alert("Please fill in the Invoice and Product name!");
                            return;
                        }

                        $.ajax({
                            url: "{{ route('storePurchaseItems') }}",
                            method: "POST",
                            data: $('#purchaseItemsForm').serialize(),
                            success: function(response) {
                                alert('Item added to invoice!');
                                $('#addItemModal').modal('hide');
                                location.reload();
                            },
                            error: function(xhr) {
                                let errorMsg = xhr.responseJSON ? xhr.responseJSON.message :
                                    "Something went wrong";
                                alert("Error: " + errorMsg);
                            }
                        });
                    });
                });


                $(document).on('click', '.view-items-btn', function() {
                    // 1. Get the row data from DataTables
                    let data = table.row($(this).parents('tr')).data();

                    // 2. Fill the "Header" parts of the modal instantly
                    $('#view_invoice_no').text(data.invoice_number);
                    $('#view_supplier_name').text(data.supplier ? data.supplier.supplier_name : '---');
                    $('#view_invoice_date').text(data.invoice_date ? data.invoice_date.split('T')[0] : '---');
                    $('#view_net_amount').text(parseFloat(data.net_amount).toLocaleString(undefined, {
                        minimumFractionDigits: 2
                    }));


                    // 3. Clear the table and show a "Loading" row
                    $('#viewItemRows').html(`
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                            <span class="ms-2">Fetching items for Invoice #${data.invoice_number}...</span>
                        </td>
                    </tr>
                `);

                    // 4. Trigger the Modal
                    $('#viewItemsModal').modal('show');
                });
            });
        </script>


    @endsection
