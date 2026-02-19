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
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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

        thead {
            background: #cc0c19;
            color: white;
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
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
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
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-content {
            background-color: white;
            margin: 3% auto;
            padding: 0;
            border-radius: 10px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.3);
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

            th, td {
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
            <button class="btn btn-add" id="addInvoiceBtn">Add New Invoice</button>
        </div>

        <!-- Filters -->
        <div class="filters">
            <div class="filter-group">
                <label for="filterSupplier">Supplier</label>
                <select id="filterSupplier">
                    <option value="">All Suppliers</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="filterStatus">Status</label>
                <select id="filterStatus">
                    <option value="">All Status</option>
                    <option value="unpaid">Unpaid</option>
                    <option value="partial">Partial</option>
                    <option value="paid">Paid</option>
                    <option value="overdue">Overdue</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="filterDateFrom">Date From</label>
                <input type="date" id="filterDateFrom">
            </div>
            <div class="filter-group">
                <label for="filterDateTo">Date To</label>
                <input type="date" id="filterDateTo">
            </div>
        </div>

        <!-- Invoice Table -->
        <div class="table-container">
            <table id="invoiceTable">
                <thead>
                    <tr>
                        <th>Invoice #</th>
                        <th>Supplier</th>
                        <th>Invoice Date</th>
                        <th>Due Date</th>
                        <th>Net Amount</th>
                        <th>Total Paid</th>
                        <th>Remaining Balance</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="invoiceTableBody">
                    <!-- Rows will be populated by JavaScript -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Invoice Modal -->
    <div id="addInvoiceModal" class="modal">
        <div class="modal-content">
            <div class="modal-header add-invoice">
                <h2>Add New Invoice</h2>
                <span class="close" id="closeAddInvoice">&times;</span>
            </div>
            <div class="modal-body">
                <div class="info-box">
                    📝 Fill in the invoice details below. Fields marked with * are required.
                </div>
                <form id="addInvoiceForm">
                    <div class="form-group">
                        <label for="invoiceNumber" class="required">Invoice Number</label>
                        <input type="text" id="invoiceNumber" name="invoice_number" placeholder="e.g., 10700003440" required>
                    </div>

                    <div class="form-group">
                        <label for="supplier" class="required">Supplier</label>
                        <input 
                            type="text" 
                            id="supplier" 
                            name="supplier_name" 
                            list="supplierList" 
                            placeholder="Select supplier" 
                            autocomplete="off"
                            required>
                        <input type="hidden" id="supplierId" name="supplier_id">
                        <datalist id="supplierList">
                            @foreach($suppliers as $supplier)
                                <option 
                                    value="{{ $supplier->supplier_name }}" 
                                    data-id="{{ $supplier->supplier_id }}">
                                </option>
                            @endforeach
                        </datalist>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="invoiceDate" class="required">Invoice Date</label>
                            <input type="date" id="invoiceDate" name="invoice_date" required>
                        </div>
                        <div class="form-group">
                            <label for="dueDate" class="required">Due Date</label>
                            <input type="date" id="dueDate" name="due_date" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="grossAmount" class="required">Gross Amount</label>
                        <input type="number" id="grossAmount" name="gross_amount" step="0.01" min="0.01" placeholder="0.00" required>
                    </div>

                    <div class="form-group">
                        <label for="vatAmount" class="required">VAT Amount</label>
                        <input type="number" id="vatAmount" name="vat_amount" step="0.01" min="0" placeholder="0.00" required>
                    </div>

                    <div class="form-group">
                        <label for="netAmount" class="required">Net Amount</label>
                        <input type="number" id="netAmount" name="net_amount" step="0.01" min="0.01" placeholder="0.00" required readonly>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cancelAddInvoice">Cancel</button>
                <button type="button" class="btn btn-success" id="saveNewInvoice">Save Invoice</button>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div id="paymentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Record Payment</h2>
                <span class="close" id="closePayment">&times;</span>
            </div>
            <div class="modal-body">
                <div class="invoice-details">
                    <h3>Invoice Details</h3>
                    <div class="detail-row">
                        <span class="detail-label">Invoice Number:</span>
                        <span class="detail-value" id="modalInvoiceNumber"></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Supplier:</span>
                        <span class="detail-value" id="modalSupplier"></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Invoice Date:</span>
                        <span class="detail-value" id="modalInvoiceDate"></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Due Date:</span>
                        <span class="detail-value" id="modalDueDate"></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Net Amount:</span>
                        <span class="detail-value amount-highlight" id="modalNetAmount"></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Total Paid:</span>
                        <span class="detail-value" id="modalTotalPaid"></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Remaining Balance:</span>
                        <span class="detail-value amount-highlight" id="modalRemainingBalance"></span>
                    </div>
                </div>

                <form id="paymentForm">
                    <div class="form-group">
                        <label for="paymentDate" class="required">Payment Date</label>
                        <input type="date" id="paymentDate" required>
                    </div>
                    <div class="form-group">
                        <label for="amountPaid" class="required">Amount Paid</label>
                        <input type="number" id="amountPaid" step="0.01" min="0" required>
                        <span class="error-message" id="amountError">Amount cannot exceed remaining balance!</span>
                    </div>
                    <div class="form-group">
                        <label for="paymentMethod" class="required">Payment Method</label>
                        <select id="paymentMethod" required>
                            <option value="">Select Method</option>
                            <option value="Cash">Cash</option>
                            <option value="Check">Check</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Credit Card">Credit Card</option>
                            <option value="Mobile Payment">Mobile Payment</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="referenceNumber">Reference Number</label>
                        <input type="text" id="referenceNumber" placeholder="e.g., Check #, Transaction ID">
                    </div>
                    {{-- <div class="form-group">
                        <label for="paymentNotes">Notes</label>
                        <textarea id="paymentNotes" placeholder="Additional notes about this payment..."></textarea>
                    </div> --}}
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cancelPayment">Cancel</button>
                <button type="button" class="btn btn-success" id="savePaymentBtn">Save Payment</button>
            </div>
        </div>
    </div>

    {{-- <script>
        // Sample invoice data
        let invoices = [
            {
                id: 1,
                invoiceNumber: 'INV-2026-001',
                supplier: 'ABC Electronics Ltd.',
                invoiceDate: '2026-01-15',
                dueDate: '2026-02-14',
                netAmount: 15000.00,
                totalPaid: 0,
                status: 'OVERDUE'
            },
            {
                id: 2,
                invoiceNumber: 'INV-2026-002',
                supplier: 'XYZ Supplies Co.',
                invoiceDate: '2026-01-20',
                dueDate: '2026-02-19',
                netAmount: 8500.50,
                totalPaid: 5000.00,
                status: 'PARTIAL'
            },
            {
                id: 3,
                invoiceNumber: 'INV-2026-003',
                supplier: 'Global Trade Inc.',
                invoiceDate: '2026-02-01',
                dueDate: '2026-03-03',
                netAmount: 22000.00,
                totalPaid: 22000.00,
                status: 'PAID'
            },
            {
                id: 4,
                invoiceNumber: 'INV-2026-004',
                supplier: 'ABC Electronics Ltd.',
                invoiceDate: '2026-02-05',
                dueDate: '2026-03-07',
                netAmount: 12500.00,
                totalPaid: 0,
                status: 'UNPAID'
            },
            {
                id: 5,
                invoiceNumber: 'INV-2026-005',
                supplier: 'TechParts Distribution',
                invoiceDate: '2026-02-10',
                dueDate: '2026-03-12',
                netAmount: 18750.75,
                totalPaid: 0,
                status: 'UNPAID'
            }
        ];

        let currentInvoiceId = null;
        let nextInvoiceId = 6;

        // Initialize on DOM ready
        (function() {
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initApp);
            } else {
                initApp();
            }
        })();

        function initApp() {
            console.log('Initializing app...');
            populateSupplierFilter();
            populateSupplierDatalist();
            renderInvoices();
            setupEventListeners();
            setDefaultDate();
        }

        // Populate supplier filter dropdown
        function populateSupplierFilter() {
            const suppliers = [...new Set(invoices.map(inv => inv.supplier))].sort();
            const select = document.getElementById('filterSupplier');
            select.innerHTML = '<option value="">All Suppliers</option>';
            suppliers.forEach(supplier => {
                const option = document.createElement('option');
                option.value = supplier;
                option.textContent = supplier;
                select.appendChild(option);
            });
        }

        // Populate supplier datalist for add invoice form
        function populateSupplierDatalist() {
            const suppliers = [...new Set(invoices.map(inv => inv.supplier))].sort();
            const datalist = document.getElementById('supplierList');
            datalist.innerHTML = '';
            suppliers.forEach(supplier => {
                const option = document.createElement('option');
                option.value = supplier;
                datalist.appendChild(option);
            });
        }

        // Calculate remaining balance
        function calculateRemainingBalance(invoice) {
            return invoice.netAmount - invoice.totalPaid;
        }

        // Update invoice status
        function updateInvoiceStatus(invoice) {
            const remaining = calculateRemainingBalance(invoice);
            const today = new Date().toISOString().split('T')[0];
            
            if (remaining === 0) {
                invoice.status = 'PAID';
            } else if (remaining < invoice.netAmount) {
                invoice.status = 'PARTIAL';
            } else if (invoice.dueDate < today) {
                invoice.status = 'OVERDUE';
            } else {
                invoice.status = 'UNPAID';
            }
        }

        // Format currency
        function formatCurrency(amount) {
            return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD'
            }).format(amount);
        }

        // Format date
        function formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }

        // Render invoices
        function renderInvoices() {
            const tbody = document.getElementById('invoiceTableBody');
            const filteredInvoices = getFilteredInvoices();
            
            tbody.innerHTML = '';
            
            filteredInvoices.forEach(invoice => {
                const remaining = calculateRemainingBalance(invoice);
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td><strong>${invoice.invoiceNumber}</strong></td>
                    <td>${invoice.supplier}</td>
                    <td>${formatDate(invoice.invoiceDate)}</td>
                    <td>${formatDate(invoice.dueDate)}</td>
                    <td>${formatCurrency(invoice.netAmount)}</td>
                    <td>${formatCurrency(invoice.totalPaid)}</td>
                    <td><strong>${formatCurrency(remaining)}</strong></td>
                    <td><span class="status-badge status-${invoice.status.toLowerCase()}">${invoice.status}</span></td>
                    <td>
                        ${invoice.status !== 'PAID' ? 
                            `<button class="btn btn-primary" data-invoice-id="${invoice.id}">Record Payment</button>` :
                            '<span style="color: #27ae60;">✓ Completed</span>'
                        }
                    </td>
                `;
                tbody.appendChild(row);
            });

            // Add event listeners to dynamically created buttons
            document.querySelectorAll('[data-invoice-id]').forEach(btn => {
                btn.addEventListener('click', function() {
                    openPaymentModal(parseInt(this.getAttribute('data-invoice-id')));
                });
            });

            if (filteredInvoices.length === 0) {
                tbody.innerHTML = '<tr><td colspan="9" style="text-align: center; padding: 30px; color: #95a5a6;">No invoices found</td></tr>';
            }
        }

        // Get filtered invoices
        function getFilteredInvoices() {
            const supplier = document.getElementById('filterSupplier').value;
            const status = document.getElementById('filterStatus').value;
            const dateFrom = document.getElementById('filterDateFrom').value;
            const dateTo = document.getElementById('filterDateTo').value;

            return invoices.filter(invoice => {
                if (supplier && invoice.supplier !== supplier) return false;
                if (status && invoice.status !== status) return false;
                if (dateFrom && invoice.invoiceDate < dateFrom) return false;
                if (dateTo && invoice.invoiceDate > dateTo) return false;
                return true;
            });
        }

        // Open add invoice modal
        function openAddInvoiceModal() {
            console.log('Opening add invoice modal...');
            const modal = document.getElementById('addInvoiceModal');
            modal.classList.add('show');
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
            
            // Set default dates
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('invoiceDate').value = today;
            
            // Set due date to 30 days from today
            const dueDate = new Date();
            dueDate.setDate(dueDate.getDate() + 30);
            document.getElementById('dueDate').value = dueDate.toISOString().split('T')[0];
        }

        // Close add invoice modal
        function closeAddInvoiceModal() {
            console.log('Closing add invoice modal...');
            const modal = document.getElementById('addInvoiceModal');
            modal.classList.remove('show');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
            document.getElementById('addInvoiceForm').reset();
        }

        // Save new invoice
        function saveNewInvoice() {
            const form = document.getElementById('addInvoiceForm');
            
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            // Check for duplicate invoice number
            const invoiceNumber = document.getElementById('invoiceNumber').value.trim();
            if (invoices.some(inv => inv.invoiceNumber === invoiceNumber)) {
                alert('⚠️ Invoice number already exists! Please use a unique invoice number.');
                return;
            }

            // Create new invoice
            const newInvoice = {
                id: nextInvoiceId++,
                invoiceNumber: invoiceNumber,
                supplier: document.getElementById('supplier').value.trim(),
                invoiceDate: document.getElementById('invoiceDate').value,
                dueDate: document.getElementById('dueDate').value,
                netAmount: parseFloat(document.getElementById('netAmount').value),
                totalPaid: 0,
                status: 'UNPAID'
            };

            // Update status based on due date
            updateInvoiceStatus(newInvoice);

            // Add to invoices array
            invoices.push(newInvoice);

            // Update UI
            populateSupplierFilter();
            populateSupplierDatalist();
            renderInvoices();

            // Show success message
            alert(`✓ Invoice ${newInvoice.invoiceNumber} added successfully!\n\nSupplier: ${newInvoice.supplier}\nAmount: ${formatCurrency(newInvoice.netAmount)}\nStatus: ${newInvoice.status}`);

            // Close modal
            closeAddInvoiceModal();
        }

        // Open payment modal
        function openPaymentModal(invoiceId) {
            console.log('Opening payment modal for invoice:', invoiceId);
            currentInvoiceId = invoiceId;
            const invoice = invoices.find(inv => inv.id === invoiceId);
            
            if (!invoice) {
                console.error('Invoice not found:', invoiceId);
                return;
            }

            const remaining = calculateRemainingBalance(invoice);

            // Populate modal
            document.getElementById('modalInvoiceNumber').textContent = invoice.invoiceNumber;
            document.getElementById('modalSupplier').textContent = invoice.supplier;
            document.getElementById('modalInvoiceDate').textContent = formatDate(invoice.invoiceDate);
            document.getElementById('modalDueDate').textContent = formatDate(invoice.dueDate);
            document.getElementById('modalNetAmount').textContent = formatCurrency(invoice.netAmount);
            document.getElementById('modalTotalPaid').textContent = formatCurrency(invoice.totalPaid);
            document.getElementById('modalRemainingBalance').textContent = formatCurrency(remaining);

            // Set max amount
            document.getElementById('amountPaid').max = remaining;

            // Show modal
            const modal = document.getElementById('paymentModal');
            modal.classList.add('show');
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        // Close payment modal
        function closePaymentModal() {
            console.log('Closing payment modal...');
            const modal = document.getElementById('paymentModal');
            modal.classList.remove('show');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
            document.getElementById('paymentForm').reset();
            document.getElementById('amountError').style.display = 'none';
            currentInvoiceId = null;
            setDefaultDate();
        }

        // Validate payment amount
        function validatePaymentAmount() {
            const invoice = invoices.find(inv => inv.id === currentInvoiceId);
            if (!invoice) return false;
            
            const remaining = calculateRemainingBalance(invoice);
            const amountPaid = parseFloat(document.getElementById('amountPaid').value) || 0;
            const errorMsg = document.getElementById('amountError');

            if (amountPaid > remaining) {
                errorMsg.style.display = 'block';
                return false;
            } else {
                errorMsg.style.display = 'none';
                return true;
            }
        }

        // Save payment
        function savePayment() {
            const form = document.getElementById('paymentForm');
            
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            if (!validatePaymentAmount()) {
                return;
            }

            const invoice = invoices.find(inv => inv.id === currentInvoiceId);
            const amountPaid = parseFloat(document.getElementById('amountPaid').value);

            // Update invoice
            invoice.totalPaid += amountPaid;
            updateInvoiceStatus(invoice);

            // Show success message
            alert(`✓ Payment of ${formatCurrency(amountPaid)} recorded successfully!\n\nInvoice: ${invoice.invoiceNumber}\nNew Status: ${invoice.status}`);

            // Close modal and refresh table
            closePaymentModal();
            renderInvoices();
        }

        // Set default date to today
        function setDefaultDate() {
            const today = new Date().toISOString().split('T')[0];
            const paymentDateInput = document.getElementById('paymentDate');
            if (paymentDateInput) {
                paymentDateInput.value = today;
            }
        }

        // Setup event listeners
        function setupEventListeners() {
            console.log('Setting up event listeners...');
            
            // Add Invoice Button
            const addBtn = document.getElementById('addInvoiceBtn');
            if (addBtn) {
                addBtn.addEventListener('click', openAddInvoiceModal);
                console.log('Add invoice button listener attached');
            }

            // Close buttons
            const closeAddInvoice = document.getElementById('closeAddInvoice');
            if (closeAddInvoice) {
                closeAddInvoice.addEventListener('click', closeAddInvoiceModal);
            }

            const cancelAddInvoice = document.getElementById('cancelAddInvoice');
            if (cancelAddInvoice) {
                cancelAddInvoice.addEventListener('click', closeAddInvoiceModal);
            }

            const closePayment = document.getElementById('closePayment');
            if (closePayment) {
                closePayment.addEventListener('click', closePaymentModal);
            }

            const cancelPayment = document.getElementById('cancelPayment');
            if (cancelPayment) {
                cancelPayment.addEventListener('click', closePaymentModal);
            }

            // Save buttons
            const saveInvoiceBtn = document.getElementById('saveNewInvoice');
            if (saveInvoiceBtn) {
                saveInvoiceBtn.addEventListener('click', saveNewInvoice);
            }

            const savePaymentBtn = document.getElementById('savePaymentBtn');
            if (savePaymentBtn) {
                savePaymentBtn.addEventListener('click', savePayment);
            }

            // Filter changes
            document.getElementById('filterSupplier').addEventListener('change', renderInvoices);
            document.getElementById('filterStatus').addEventListener('change', renderInvoices);
            document.getElementById('filterDateFrom').addEventListener('change', renderInvoices);
            document.getElementById('filterDateTo').addEventListener('change', renderInvoices);

            // Modal close on outside click
            window.addEventListener('click', function(event) {
                const addModal = document.getElementById('addInvoiceModal');
                const paymentModal = document.getElementById('paymentModal');
                if (event.target === addModal) {
                    closeAddInvoiceModal();
                }
                if (event.target === paymentModal) {
                    closePaymentModal();
                }
            });

            // Amount validation
            const amountPaidInput = document.getElementById('amountPaid');
            if (amountPaidInput) {
                amountPaidInput.addEventListener('input', validatePaymentAmount);
            }

            // Escape key to close modals
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closeAddInvoiceModal();
                    closePaymentModal();
                }
            });
        }
    </script> --}}
    <script>
        let invoices = @json($purchases);
        let currentInvoiceId = null;
        let nextInvoiceId = invoices.length + 1;

        document.addEventListener('DOMContentLoaded', function () {
            populateSupplierFilter();
            renderInvoices();
            setupEventListeners();
            setDefaultDate();
        });

        // -----------------------------
        // Utility Functions
        // -----------------------------

        function calculateRemainingBalance(invoice) {
            return parseFloat(invoice.netAmount) - parseFloat(invoice.totalPaid);
        }

        function formatCurrency(amount) {
            return new Intl.NumberFormat('en-PH', {
                style: 'currency',
                currency: 'PHP'
            }).format(amount);
        }

        function formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }

        // -----------------------------
// Render Invoices
// -----------------------------
function renderInvoices() {
    const tbody = document.getElementById('invoiceTableBody');
    tbody.innerHTML = '';

    const filtered = getFilteredInvoices();

    if (filtered.length === 0) {
        tbody.innerHTML = '<tr><td colspan="9" style="text-align:center; padding:30px; color:#95a5a6;">No invoices found</td></tr>';
        return;
    }

    filtered.forEach(invoice => {
        const remaining = calculateRemainingBalance(invoice);

        const row = document.createElement('tr');
        row.innerHTML = `
            <td><strong>${invoice.invoiceNumber}</strong></td>
            <td>${invoice.supplier}</td>
            <td>${formatDate(invoice.invoiceDate)}</td>
            <td>${formatDate(invoice.dueDate)}</td>
            <td>${formatCurrency(invoice.netAmount)}</td>
            <td>${formatCurrency(invoice.totalPaid)}</td>
            <td><strong>${formatCurrency(remaining)}</strong></td>
            <td><span class="status-badge status-${invoice.status.toLowerCase()}">${invoice.status}</span></td>
            <td>
                ${
                    remaining <= 0 || invoice.status.toUpperCase() === 'PAID'
                    ? `<span style="color:green">✓ Completed</span>`
                    : `<button class="btn btn-primary" data-id="${invoice.id}">Record Payment</button>`
                }
            </td>
        `;
        tbody.appendChild(row);
    });

    document.querySelectorAll('[data-id]').forEach(btn => {
        btn.addEventListener('click', function () {
            openPaymentModal(parseInt(this.dataset.id));
        });
    });
}

        // -----------------------------
        // Filter Functions
        // -----------------------------
        function getFilteredInvoices() {
            const supplier = document.getElementById('filterSupplier').value;
            const status = document.getElementById('filterStatus').value;
            const dateFrom = document.getElementById('filterDateFrom').value;
            const dateTo = document.getElementById('filterDateTo').value;

            return invoices.filter(inv => {
                if (supplier && inv.supplier !== supplier) return false;
                if (status && inv.status !== status) return false;
                if (dateFrom && inv.invoiceDate < dateFrom) return false;
                if (dateTo && inv.invoiceDate > dateTo) return false;
                return true;
            });
        }

        function populateSupplierFilter() {
            const suppliers = [...new Set(invoices.map(i => i.supplier))].sort();
            const select = document.getElementById('filterSupplier');
            select.innerHTML = '<option value="">All Suppliers</option>';
            suppliers.forEach(s => {
                const option = document.createElement('option');
                option.value = s;
                option.textContent = s;
                select.appendChild(option);
            });
        }

        // -----------------------------
        // Add Invoice Modal
        // -----------------------------
        // -----------------------------
        // Add Invoice Modal
        // -----------------------------
        function openAddInvoiceModal() {
            const modal = document.getElementById('addInvoiceModal');
            modal.classList.add('show');
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';

            const today = new Date().toISOString().split('T')[0];
            document.getElementById('invoiceDate').value = today;

            const due = new Date();
            due.setDate(due.getDate() + 30);
            document.getElementById('dueDate').value = due.toISOString().split('T')[0];
        }

        function closeAddInvoiceModal() {
            const modal = document.getElementById('addInvoiceModal');
            modal.classList.remove('show');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
            document.getElementById('addInvoiceForm').reset();
        }

        // -----------------------------
        // Add Invoice Modal
        // -----------------------------
        function openAddInvoiceModal() {
            const modal = document.getElementById('addInvoiceModal');
            modal.classList.add('show');
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';

            const today = new Date().toISOString().split('T')[0];
            document.getElementById('invoiceDate').value = today;

            const due = new Date();
            due.setDate(due.getDate() + 30);
            document.getElementById('dueDate').value = due.toISOString().split('T')[0];
        }

        function closeAddInvoiceModal() {
            const modal = document.getElementById('addInvoiceModal');
            modal.classList.remove('show');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
            document.getElementById('addInvoiceForm').reset();
            document.getElementById('supplierId').value = ''; // Clear hidden field
        }

        // Auto-calculate net amount and handle supplier selection
        document.addEventListener('DOMContentLoaded', function() {
            const grossAmountInput = document.getElementById('grossAmount');
            const vatAmountInput = document.getElementById('vatAmount');
            const netAmountInput = document.getElementById('netAmount');

            function calculateNetAmount() {
                const gross = parseFloat(grossAmountInput.value) || 0;
                const vat = parseFloat(vatAmountInput.value) || 0;
                netAmountInput.value = (gross + vat).toFixed(2);
            }

            if (grossAmountInput && vatAmountInput) {
                grossAmountInput.addEventListener('input', calculateNetAmount);
                vatAmountInput.addEventListener('input', calculateNetAmount);
            }

            // Handle supplier selection from datalist
            const supplierInput = document.getElementById('supplier');
            const supplierIdInput = document.getElementById('supplierId');
            
            if (supplierInput && supplierIdInput) {
                supplierInput.addEventListener('input', function() {
                    const supplierName = this.value.trim();
                    const options = document.querySelectorAll('#supplierList option');
                    let foundId = null;
                    
                    options.forEach(option => {
                        if (option.value === supplierName) {
                            foundId = option.dataset.id;
                        }
                    });
                    
                    // Update hidden field with supplier_id
                    supplierIdInput.value = foundId || '';
                });

                // Also handle on blur to ensure selection
                supplierInput.addEventListener('blur', function() {
                    const supplierName = this.value.trim();
                    const options = document.querySelectorAll('#supplierList option');
                    let found = false;
                    
                    options.forEach(option => {
                        if (option.value === supplierName) {
                            found = true;
                            supplierIdInput.value = option.dataset.id;
                        }
                    });
                    
                    if (!found) {
                        supplierIdInput.value = '';
                    }
                });
            }
        });

        function saveNewInvoice() {
            const form = document.getElementById('addInvoiceForm');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const supplierId = document.getElementById('supplierId').value;

            if (!supplierId) {
                alert('⚠️ Please select a valid supplier from the list!');
                return;
            }

            // Prepare form data
            const formData = {
                invoice_number: document.getElementById('invoiceNumber').value.trim(),
                supplier_id: parseInt(supplierId),
                invoice_date: document.getElementById('invoiceDate').value,
                due_date: document.getElementById('dueDate').value,
                gross_amount: parseFloat(document.getElementById('grossAmount').value),
                vat_amount: parseFloat(document.getElementById('vatAmount').value),
                net_amount: parseFloat(document.getElementById('netAmount').value)
            };

            console.log('Sending data:', formData); // Debug log

            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Send AJAX request
            fetch('/purchases', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    closeAddInvoiceModal();
                    
                    // Reload the page or update the table dynamically
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                
                if (error.errors) {
                    // Display validation errors
                    let errorMessage = 'Validation errors:\n';
                    for (let field in error.errors) {
                        errorMessage += `${field}: ${error.errors[field].join(', ')}\n`;
                    }
                    alert(errorMessage);
                } else {
                    alert('⚠️ An error occurred while saving the invoice. Please try again.');
                }
            });
        }

        // Event listeners
        document.getElementById('closeAddInvoice')?.addEventListener('click', closeAddInvoiceModal);
        document.getElementById('cancelAddInvoice')?.addEventListener('click', closeAddInvoiceModal);
        document.getElementById('saveNewInvoice')?.addEventListener('click', saveNewInvoice);

        // -----------------------------
        // Payment Modal
        // -----------------------------
        function openPaymentModal(id) {
            currentInvoiceId = id;
            const invoice = invoices.find(i => i.id === id);
            if (!invoice) return;

            const remaining = calculateRemainingBalance(invoice);

            document.getElementById('modalInvoiceNumber').textContent = invoice.invoiceNumber;
            document.getElementById('modalSupplier').textContent = invoice.supplier;
            document.getElementById('modalInvoiceDate').textContent = formatDate(invoice.invoiceDate);
            document.getElementById('modalDueDate').textContent = formatDate(invoice.dueDate);
            document.getElementById('modalNetAmount').textContent = formatCurrency(invoice.netAmount);
            document.getElementById('modalTotalPaid').textContent = formatCurrency(invoice.totalPaid);
            document.getElementById('modalRemainingBalance').textContent = formatCurrency(remaining);

            document.getElementById('amountPaid').max = remaining;
            document.getElementById('paymentModal').classList.add('show');
        }

        function closePaymentModal() {
            document.getElementById('paymentModal').classList.remove('show');
            document.getElementById('paymentForm').reset();
            document.getElementById('amountError').style.display = 'none';
            currentInvoiceId = null;
            setDefaultDate();
        }

        function validatePaymentAmount() {
            const invoice = invoices.find(i => i.id === currentInvoiceId);
            if (!invoice) return false;

            const remaining = calculateRemainingBalance(invoice);
            const amountPaid = parseFloat(document.getElementById('amountPaid').value) || 0;
            const error = document.getElementById('amountError');

            if (amountPaid > remaining) {
                error.style.display = 'block';
                return false;
            }
            error.style.display = 'none';
            return true;
        }

        function savePayment() {
            const form = document.getElementById('paymentForm');
            
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            if (!validatePaymentAmount()) {
                return;
            }

            const invoice = invoices.find(i => i.id === currentInvoiceId);
            const amountPaid = parseFloat(document.getElementById('amountPaid').value);

            // Prepare payment data
            const paymentData = {
                purchase_id: invoice.id,
                payment_date: document.getElementById('paymentDate').value,
                amount_paid: amountPaid,
                payment_method: document.getElementById('paymentMethod').value,
                reference_number: document.getElementById('referenceNumber').value || null,
                // notes: document.getElementById('paymentNotes').value || null
            };

            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Disable save button to prevent double submission
            const saveBtn = document.getElementById('savePaymentBtn');
            saveBtn.disabled = true;
            saveBtn.textContent = 'Saving...';

            // Send AJAX request
            fetch('/payments', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(paymentData)
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert(`✓ ${data.message}\n\nAmount: ${formatCurrency(amountPaid)}\nNew Status: ${data.data.purchase.status}`);
                    
                    // Update the invoice in the local array
                    const invoiceIndex = invoices.findIndex(i => i.id === currentInvoiceId);
                    if (invoiceIndex !== -1) {
                        invoices[invoiceIndex].totalPaid = data.data.purchase.totalPaid;
                        invoices[invoiceIndex].status = data.data.purchase.status;
                    }
                    
                    closePaymentModal();
                    renderInvoices();
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                
                if (error.errors) {
                    let errorMessage = 'Validation errors:\n';
                    for (let field in error.errors) {
                        errorMessage += `${field}: ${error.errors[field].join(', ')}\n`;
                    }
                    alert(errorMessage);
                } else {
                    alert(error.message || '⚠️ An error occurred while recording the payment. Please try again.');
                }
            })
            .finally(() => {
                // Re-enable save button
                saveBtn.disabled = false;
                saveBtn.textContent = 'Save Payment';
            });
        }

        // -----------------------------
        // Default Date
        // -----------------------------
        function setDefaultDate() {
            const today = new Date().toISOString().split('T')[0];
            const paymentDateInput = document.getElementById('paymentDate');
            if (paymentDateInput) paymentDateInput.value = today;
        }

        // -----------------------------
        // Event Listeners
        // -----------------------------
        function setupEventListeners() {
            // Add Invoice modal
            document.getElementById('addInvoiceBtn').addEventListener('click', openAddInvoiceModal);
            document.getElementById('closeAddInvoice').addEventListener('click', closeAddInvoiceModal);
            document.getElementById('cancelAddInvoice').addEventListener('click', closeAddInvoiceModal);
            document.getElementById('saveNewInvoice').addEventListener('click', saveNewInvoice);

            // Payment modal
            document.getElementById('savePaymentBtn').addEventListener('click', savePayment);
            document.getElementById('cancelPayment').addEventListener('click', closePaymentModal);
            document.getElementById('closePayment').addEventListener('click', closePaymentModal);
            document.getElementById('amountPaid').addEventListener('input', validatePaymentAmount);

            // Filters
            document.getElementById('filterSupplier').addEventListener('change', renderInvoices);
            document.getElementById('filterStatus').addEventListener('change', renderInvoices);
            document.getElementById('filterDateFrom').addEventListener('change', renderInvoices);
            document.getElementById('filterDateTo').addEventListener('change', renderInvoices);

            // Close modals on outside click
            window.addEventListener('click', function(event) {
                if (event.target === document.getElementById('addInvoiceModal')) closeAddInvoiceModal();
                if (event.target === document.getElementById('paymentModal')) closePaymentModal();
            });

            // Escape key
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closeAddInvoiceModal();
                    closePaymentModal();
                }
            });
        }
    </script>


@endsection

