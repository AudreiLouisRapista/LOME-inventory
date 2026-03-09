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

        body.modal-open {
            overflow: hidden;
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

        h1 {
            color: #2c3e50;
            font-size: 28px;
        }

        .search-bar {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .search-bar input {
            flex: 1;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .search-bar input:focus {
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
            background: #34495e;
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

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
            margin-right: 5px;
        }

        .btn-secondary {
            background: #95a5a6;
            color: white;
        }

        .btn-secondary:hover {
            background: #7f8c8d;
        }

        .btn-add {
            background: #9b59b6;
            color: white;
            padding: 12px 24px;
            font-size: 15px;
        }

        .btn-add:hover {
            background: #8e44ad;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(155, 89, 182, 0.3);
        }

        .btn-add::before {
            content: "+ ";
            font-size: 18px;
        }

        .btn-success {
            background: #27ae60;
            color: white;
        }

        .btn-success:hover {
            background: #229954;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 4000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            padding: 24px;
            background: rgba(29, 29, 30, 0.7);
            animation: fadeIn 0.25s;
            overflow-y: auto;
            align-items: center;
            justify-content: center;
        }

        .modal.is-active {
            display: flex;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-content {
            background-color: white;
            margin: 2% auto;
            padding: 0;
            border-radius: 10px;
            width: 90%;
            max-width: 600px;
            max-height: 95vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 5px 20px rgba(0,0,0,0.3);
            animation: slideDown 0.3s;
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
            background: #9b59b6;
            color: white;
            border-radius: 10px 10px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
        }

        .modal-header h2 {
            font-size: 22px;
        }

        .close {
            color: white;
            font-size: 32px;
            font-weight: bold;
            cursor: pointer;
            line-height: 20px;
        }

        .close:hover {
            color: #ecf0f1;
        }

        .modal-body {
            padding: 30px;
            overflow-y: auto;
            flex: 1;
            min-height: 0;
        }

        .modal-footer {
            padding: 20px 30px;
            background: #f8f9fa;
            border-radius: 0 0 10px 10px;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            flex-shrink: 0;
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

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            font-family: inherit;
        }

        .form-group input:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
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

        .no-data {
            text-align: center;
            padding: 50px;
            color: #95a5a6;
            font-size: 16px;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-success {
            background: #eafaf1;
            border-left: 4px solid #27ae60;
            color: #1e8449;
        }

        .alert-error {
            background: #fdecea;
            border-left: 4px solid #e74c3c;
            color: #943126;
        }

        .alert-error ul {
            margin-top: 10px;
            padding-left: 20px;
        }

        .error-text {
            color: #e74c3c;
            font-size: 13px;
            margin-top: 6px;
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
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
            <h1>🏢 Supplier Management</h1>
            <button type="button" class="btn btn-add" onclick="openAddSupplierModal()">Add New Supplier</button>
        </div>

        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">
                <strong>There were some problems with your submission:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="🔍 Search by supplier name, contact number, or address...">
        </div>

        <div class="table-container">
            <table id="supplierTable">
                <thead>
                    <tr>
                        <th>Supplier ID</th>
                        <th>Supplier Name</th>
                        <th>Contact Number</th>
                        <th>Address</th>
                        <th>Added On</th>
                    </tr>
                </thead>
                <tbody id="supplierTableBody">
                    @forelse ($suppliers as $supplier)
                        <tr class="supplier-row" data-search="{{ strtolower($supplier->supplier_name . ' ' . $supplier->contact_no . ' ' . $supplier->address) }}">
                            <td><strong>{{ $supplier->supplier_id }}</strong></td>
                            <td>{{ $supplier->supplier_name }}</td>
                            <td>{{ $supplier->contact_no }}</td>
                            <td>{{ $supplier->address }}</td>
                            <td>{{ optional($supplier->created_at)->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="no-data">No suppliers found yet.</td>
                        </tr>
                    @endforelse
                    <tr id="noMatchesRow" style="display:none;">
                        <td colspan="5" class="no-data">No suppliers match your search.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Supplier Modal -->
    <div id="supplierModal" class="modal" aria-hidden="true">
        <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
            <div class="modal-header" id="modalHeader">
                <h2 id="modalTitle">Add New Supplier</h2>
                <span class="close" role="button" tabindex="0" onclick="closeSupplierModal()" aria-label="Close">&times;</span>
            </div>
            <div class="modal-body">
                <div class="info-box">
                    📝 Fill in the supplier details below. Fields marked with * are required.
                </div>
                <form id="supplierForm" method="POST" action="{{ route('supplier.store') }}">
                    @csrf
                    <div class="form-group">
                        <label for="supplier_name" class="required">Supplier Name</label>
                        <input type="text" id="supplier_name" name="supplier_name" value="{{ old('supplier_name') }}" placeholder="e.g., ABC Electronics Ltd." required>
                        @error('supplier_name')
                            <p class="error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="contact_no" class="required">Contact Number</label>
                        <input type="text" id="contact_no" name="contact_no" value="{{ old('contact_no') }}" placeholder="e.g., +1 (555) 123-4567" required>
                        @error('contact_no')
                            <p class="error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="address" class="required">Address</label>
                        <input type="text" id="address" name="address" value="{{ old('address') }}" placeholder="Street address" required>
                        @error('address')
                            <p class="error-text">{{ $message }}</p>
                        @enderror
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeSupplierModal()">Cancel</button>
                <button type="submit" class="btn btn-success" form="supplierForm">Save Supplier</button>
            </div>
        </div>
    </div>

    <script>
        const supplierModal = document.getElementById('supplierModal');
        const supplierForm = document.getElementById('supplierForm');
        const searchInput = document.getElementById('searchInput');

        function openAddSupplierModal() {
            supplierModal.classList.add('is-active');
            supplierModal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('modal-open');
            setTimeout(() => {
                const firstField = document.getElementById('supplier_name');
                if (firstField) {
                    firstField.focus();
                }
            }, 50);
        }

        function closeSupplierModal() {
            supplierModal.classList.remove('is-active');
            supplierModal.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('modal-open');
            if (supplierForm) {
                supplierForm.reset();
            }
        }

        function filterSuppliers() {
            if (!searchInput) {
                return;
            }

            const term = searchInput.value.trim().toLowerCase();
            const rows = document.querySelectorAll('#supplierTableBody tr.supplier-row');
            const noMatchesRow = document.getElementById('noMatchesRow');
            let visibleCount = 0;

            rows.forEach((row) => {
                const searchValue = row.dataset.search || '';
                const isMatch = !term || searchValue.includes(term);
                row.style.display = isMatch ? '' : 'none';
                if (isMatch) {
                    visibleCount += 1;
                }
            });

            if (noMatchesRow) {
                const shouldShow = rows.length > 0 && visibleCount === 0;
                noMatchesRow.style.display = shouldShow ? '' : 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            if (searchInput) {
                searchInput.addEventListener('input', filterSuppliers);
            }

            const shouldOpenModal = Boolean(@json($errors->any() || session('showSupplierModal')));
            if (shouldOpenModal) {
                openAddSupplierModal();
            }
        });

        window.addEventListener('click', (event) => {
            if (event.target === supplierModal) {
                closeSupplierModal();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && supplierModal?.classList.contains('is-active')) {
                closeSupplierModal();
            }
        });
    </script>

@endsection
