@extends('themes.main')

{{-- 1. DEFINE PAGE TITLE --}}
@section('title', 'Invoice Encoder')

{{-- 2. DEFINE CONTENT HEADER (Breadcrumbs) --}}
@section('content_header')
    <div class="px-4 pt-3">
        <h1 class="m-0 text-dark">Invoice Encoder</h1>
        <p class="text-muted">Manage supplier invoices</p>
    </div>
@endsection




@section('content')

    <div class="container py-5">
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="card-header bg-danger bg-gradient p-4 d-flex justify-content-between align-items-center border-0">
                <div class="d-flex align-items-center">
                    <div class="bg-white bg-opacity-25 rounded-3 p-2 me-3">
                        <i class="fas fa-file-invoice-dollar text-danger fs-3"></i>
                    </div>
                    <div>
                        <h4 class="text-white mb-0 fw-bold">Invoice Encoder</h4>
                        <p class="text-white text-opacity-75 mb-0 small">Create and log new purchase records</p>
                    </div>
                </div>
                <button type="submit" form="invoiceEncoderForm"
                    class="btn btn-light text-success fw-bold px-4 rounded-3 shadow-sm">
                    <i class="fas fa-check-circle me-2"></i>Save Invoice
                </button>
            </div>

            <div class="card-body p-4 p-lg-5">
                @include('layout.partials.alerts')

                {{-- HELP FOR DEBUGGING --}}
                {{-- @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif --}}

                @if (session('errorMessage'))
                    <div class="alert alert-warning">{{ session('errorMessage') }}</div>
                @endif
                <form id="invoiceEncoderForm" action="{{ route('saveInvoiceAndItem') }}" method="POST">
                    @csrf
                    <div class="mb-5">

                        <h5 class="text-dark fw-bold mb-4 d-flex align-items-center">
                            <span class="badge bg-danger me-2 p-2"><i class="fas fa-hashtag"></i></span> Invoice
                            Information
                        </h5>
                        <div class="row g-4">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">Invoice Number</label>
                                <input type="text" name="invoice_number"
                                    class="form-control form-control-lg bg-light border-0 shadow-none"
                                    placeholder="e.g. INV-10023" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">Invoice Date</label>
                                <input type="date" name="invoice_date"
                                    class="form-control form-control-lg bg-light border-0 shadow-none"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">Due Date</label>
                                <input type="date" name="due_date"
                                    class="form-control form-control-lg bg-light border-0 shadow-none">
                            </div>

                        </div>
                    </div>

                    <div class="mb-5">
                        <h5 class="text-dark fw-bold mb-4 d-flex align-items-center">
                            <span class="badge bg-danger me-2 p-2"><i class="fas fa-truck"></i></span> Supplier
                            Information
                        </h5>
                        <div class="row g-4">
                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-muted">Select Supplier</label>
                                <select name="supplier_id" class="form-select form-select-lg bg-light border-0 shadow-none"
                                    required>
                                    <option value="">Choose a supplier...</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->supplier_id }}">{{ $supplier->supplier_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-5">
                        <h5 class="text-dark fw-bold mb-4 d-flex align-items-center">
                            <span class="badge bg-danger me-2 p-2"><i class="bi bi-box-seam-fill"></i></span> Batch
                            Information
                        </h5>
                        <div class="row g-4">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">Batch Number</label>
                                <input type="number" name="batch_number"
                                    class="form-control form-control-lg bg-light border-0 shadow-none" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">Mfg. Date</label>
                                <input type="date" name="mfg_date"
                                    class="form-control form-control-lg bg-light border-0 shadow-none" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">Expiration Date</label>
                                <input type="date" name="exp_date"
                                    class="form-control form-control-lg bg-light border-0 shadow-none" required>
                            </div>

                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="text-dark fw-bold mb-0">Invoice Items</h5>
                            <button type="button" id="addRow" class="btn btn-outline-primary btn-sm fw-bold px-3">
                                <i class="fas fa-plus me-1"></i> Add Item
                            </button>
                        </div>
                        <div class="table-responsive">

                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr class="small text-uppercase text-muted">
                                        <th style="width: 7%;">Qty</th>
                                        <th style="width: 12%;">UOM</th>
                                        <th style="width: 8%;">Tie #</th>
                                        <th style="width: 10%;">Qty. per Tie</th>
                                        <th style="width: 30%;">Description (Product)</th>
                                        <th style="width: 12%;">Unit Price</th>
                                        <th style="width: 10%;">Price</th>
                                        <th style="width: 10%; text-align: right;">Amount</th>
                                        <th style="width: 3%;"></th>
                                    </tr>
                                </thead>
                                <tbody id="itemRows">
                                    <tr class="item-row">
                                        <td><input type="number" name="quantity[]"
                                                class="form-control border-0 bg-light shadow-none qty" value="1"
                                                min="1"></td>
                                        <td>
                                            <select name="uom[]" class="form-select border-0 bg-light shadow-none uom"
                                                required>
                                                <option value="">Select</option>
                                                @foreach ($uoms as $uom)
                                                    <option value="{{ $uom->uom_ID }}">{{ $uom->uom_title }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="number" name="quantity_per_unit[]"
                                                class="form-control border-0 bg-light shadow-none qty_per_unit"
                                                value="1" min="1"></td>
                                        <td><input type="text" name="tie_number[]"
                                                class="form-control border-0 bg-light shadow-none">
                                        </td>
                                        <td>
                                            <input type="text" name="product_name[]" list="productData"
                                                class="form-control border-0 bg-light shadow-none"
                                                placeholder="Enter product name..." required>
                                        </td>
                                        <td>
                                            <div class="input-group bg-light rounded">
                                                <span
                                                    class="input-group-text border-0 bg-transparent text-muted small">₱</span>
                                                <input type="number" name="unit_price[]" id="unit_price"
                                                    class="form-control border-0 bg-transparent shadow-none unit_price"
                                                    step="0.01" value="0.00">
                                            </div>
                                        </td>

                                        <td class="text-end fw-bold pe-3 totalPrice">0.00</td>
                                        <td class="text-end fw-bold pe-3 row-total">0.00</td>

                                        <td><button type="button" class="btn btn-link text-danger p-0 remove-row"><i
                                                    class="fas fa-times-circle"></i></button></td>
                                    </tr>
                                </tbody>
                            </table>

                            {{-- This list provides the "Autocomplete" for all rows --}}
                            <datalist id="productData">
                                @foreach ($products as $product)
                                    <option value="{{ $product->product_name }}">
                                @endforeach
                            </datalist>
                        </div>
                    </div>

                    <div class="row justify-content-end">
                        <div class="col-md-4 p-4 bg-light rounded-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Gross Amount:</span>
                                <span id="gross_total" name="gross_total" class="fw-bold text-dark">₱0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Vatable Sales:</span>
                                <span id="vatable_sales" name="vatable_sales" class="fw-bold text-dark">₱0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">VAT (12%):</span>
                                <span id="vat_amount" name="vat_amount" class="fw-bold text-dark">₱0.00</span>
                            </div>
                            <div
                                class="d-flex justify-content-between align-items-center pt-3 border-top border-secondary border-opacity-10">
                                <span class="h6 fw-bold mb-0">Grand Total (Net):</span>
                                <span id="grand_total" name="grand_total"
                                    class="h4 fw-bold text-primary mb-0">₱0.00</span>
                            </div>
                            <input type="hidden" name="gross_total_raw" id="gross_total_raw">
                            <input type="hidden" name="vat_amount_raw" id="vat_amount_raw">
                            <input type="hidden" name="grand_total_raw" id="grand_total_raw">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <style>
        /* Container for better targeting */
        .mb-5 .form-control {
            transition: all 0.3s ease-in-out;
            border: 1px solid transparent;
            /* Prevents layout jump on hover */
        }

        /* Hover State */
        .mb-5 .form-control:hover {
            background-color: #ffffff !important;
            border-color: #dee2e6;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05) !important;
            transform: translateY(-1px);
        }

        /* Focus State (Optional but recommended for accessibility) */
        .mb-5 .form-control:focus {
            background-color: #ffffff !important;
            border-color: #dc3545;
            /* Matches your bg-danger badge color */
            box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.1) !important;
        }
        }
    </style>
@endsection

@section('tables')
    <script>
        $(document).ready(function() {
            // 1. Build UOM options
            let uomOptions = '<option value="">Select</option>';
            @foreach ($uoms as $uom)
                uomOptions += `<option value="{{ $uom->uom_ID }}">{{ $uom->uom_title }}</option>`;
            @endforeach

            // 2. Add Row
            $('#addRow').on('click', function() {
                let newRow = `
                    <tr class="item-row">
                        <td><input type="number" name="quantity[]" class="form-control border-0 bg-light shadow-none qty" value="1" min="1"></td>
                        <td>
                            <select name="uom[]" class="form-select border-0 bg-light shadow-none uom" required>
                                ${uomOptions}
                            </select>
                        </td>
                        <td><input type="number" name="quantity_per_unit[]" class="form-control border-0 bg-light shadow-none qty_per_unit" value="1" min="1"></td>
                        <td><input type="number" name="tie_number[]" class="form-control border-0 bg-light shadow-none tie_number" value="1"></td>
                        <td>
                            <input type="text" name="product_name[]" list="productData" class="form-control border-0 bg-light shadow-none" placeholder="Enter product name..." required>
                        </td>
                        <td>
                            <div class="input-group bg-light rounded">
                                <span class="input-group-text border-0 bg-transparent text-muted small">₱</span>
                                <input type="number" name="unit_price[]" class="form-control border-0 bg-transparent shadow-none unit_price" step="0.01" value="0.00">
                            </div>
                        </td>
                        <td class="text-end fw-bold pe-3 totalPrice">0.00</td>
                        <td class="text-end fw-bold pe-3 row-total">0.00</td>
                        <td><button type="button" class="btn btn-link text-danger p-0 remove-row"><i class="fas fa-times-circle"></i></button></td>
                    </tr>`;
                $('#itemRows').append(newRow);
            });

            // 3. Remove Row
            $(document).on('click', '.remove-row', function() {
                if ($('.item-row').length > 1) {
                    $(this).closest('tr').remove();
                    calculateTotals();
                }
            });

            // 4. Trigger Calculation
            $(document).on('input', '.qty, .unit_price, .qty_per_unit, .tie_number', function() {
                calculateTotals();
            });

            function calculateTotals() {
                let grossTotal = 0;

                $('.item-row').each(function() {
                    let qty = parseFloat($(this).find('.qty').val()) || 0;
                    let unit_price = parseFloat($(this).find('.unit_price').val()) || 0;
                    let qty_per_unit = parseFloat($(this).find('.qty_per_unit').val()) || 0;
                    let tie_number = parseFloat($(this).find('.tie_number').val()) || 1;

                    // Price = Qty/Unit * Tie * Unit Price
                    let priceCalculated = qty_per_unit * tie_number * unit_price;
                    $(this).find('.totalPrice').text(priceCalculated.toLocaleString(undefined, {
                        minimumFractionDigits: 2
                    }));

                    // Amount = Qty * Price
                    let rowTotal = qty * priceCalculated;
                    $(this).find('.row-total').text(rowTotal.toLocaleString(undefined, {
                        minimumFractionDigits: 2
                    }));

                    grossTotal += rowTotal;
                });

                // VAT Calculations (Assuming 12% is included in Gross)
                // If Gross is the sum of items, Net = Gross / 1.12
                let vatableSales = grossTotal / 1.12;
                let vatAmount = grossTotal - vatableSales;

                // Display Results
                $('#gross_total').text('₱' + grossTotal.toLocaleString(undefined, {
                    minimumFractionDigits: 2
                }));
                $('#vatable_sales').text('₱' + vatableSales.toLocaleString(undefined, {
                    minimumFractionDigits: 2
                }));
                $('#vat_amount').text('₱' + vatAmount.toLocaleString(undefined, {
                    minimumFractionDigits: 2
                }));
                $('#grand_total').text('₱' + grossTotal.toLocaleString(undefined, {
                    minimumFractionDigits: 2
                }));

                // Add this at the very bottom of your calculateTotals() function
                $('#gross_total_raw').val(grossTotal.toFixed(2));
                $('#vat_amount_raw').val(vatAmount.toFixed(2));
                $('#grand_total_raw').val(grossTotal.toFixed(2));
            }
        });
    </script>
@endsection
