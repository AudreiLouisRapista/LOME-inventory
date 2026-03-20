@extends('themes.main')

@section('title', 'Invoice Encoder')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/pages/add_invoice.css') }}">
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
                <form id="invoiceEncoderForm" action="{{ route('saveInvoiceAndItem') }}" method="POST">
                    @csrf

                    <div class="mb-5">
                        <h5 class="text-dark fw-bold mb-4 d-flex align-items-center">
                            <span class="badge bg-danger me-2 p-2"><i class="fas fa-hashtag"></i></span> Invoice Information
                        </h5>
                        <div class="row g-4">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">Invoice Number</label>
                                <input type="text" name="invoice_number" class="form-control form-control-lg bg-light"
                                    placeholder="e.g. 10023" maxlength="12" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">Invoice Date</label>
                                <input type="date" name="invoice_date" class="form-control form-control-lg bg-light"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">Due Date</label>
                                <input type="date" name="due_date" class="form-control form-control-lg bg-light"
                                    required>
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
                                <select name="supplier_id" class="form-select form-select-lg bg-light" required>
                                    <option value="">Choose a supplier...</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->supplier_id }}">{{ $supplier->supplier_name }}
                                        </option>
                                    @endforeach
                                </select>
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
                                        <th style="width: 8%;">QTY</th>
                                        <th>DESCRIPTION (PRODUCT)</th>
                                        <th style="width: 8%;">Quantity</th>
                                        <th style="width: 10%;">Pack Size</th>
                                        <th style="width: 12%;">TYPE</th>
                                        <th class="expiry-column" style="width: 15%; display: none;">EXPIRY DATE
                                        </th>
                                        <th style="width: 10%;">UNIT PRICE</th>
                                        <th style="width: 8%;">PRICE</th>
                                        <th style="width: 10%;">AMOUNT</th>
                                        <th style="width: 5%;"></th>
                                    </tr>
                                </thead>
                                <tbody id="itemRows">
                                    <tr class="item-row">
                                        <td><input type="number" name="quantity[]" class="form-control qty" value="1">
                                        </td>
                                        <td><input type="text" name="product_name[]" list="productData"
                                                class="form-control product-input" required></td>
                                        <td><input type="number" name="tie_number[]" class="form-control tie_number"
                                                value="0" readonly></td>
                                        <td><input type="number" name="tie_qty[]" class="form-control tie_qty"
                                                value="1" readonly></td>
                                        <td>
                                            <input type="text" name="perishable_type[]"
                                                class="form-control type-input bg-light" readonly placeholder="-">
                                        </td>
                                        <td class="expiry-column" style="display: none;">
                                            <div class="expiry-wrapper" style="display: none;">
                                                <input type="date" name="exp_date[]" class="form-control expiry-input">
                                            </div>
                                        </td>
                                        <td><input type="number" name="unit_price[]" class="form-control unit_price"
                                                step="0.01"></td>
                                        <td class="totalPrice fw-bold">0.00</td>
                                        <td class="row-total fw-bold text-primary">0.00</td>
                                        <td>
                                            <button type="button"
                                                class="btn btn-outline-danger btn-sm border-0 remove-row">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <datalist id="productData">
                                {{-- Dynamically populated by JS to show only top 10 --}}
                            </datalist>
                        </div>
                    </div>

                    <div class="row justify-content-end">
                        <div class="col-md-4 p-4 bg-light rounded-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Gross Amount:</span>
                                <span id="gross_total" class="fw-bold text-dark">₱0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Vatable Sales:</span>
                                <span id="vatable_sales" class="fw-bold text-dark">₱0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">VAT (12%):</span>
                                <span id="vat_amount" class="fw-bold text-dark">₱0.00</span>
                            </div>
                            <div
                                class="d-flex justify-content-between align-items-center pt-3 border-top border-secondary border-opacity-10">
                                <span class="h6 fw-bold mb-0">Grand Total (Net):</span>
                                <span id="grand_total" class="h4 fw-bold text-primary mb-0">₱0.00</span>
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
@endsection

@section('scripts src')
    <script>
        $(document).ready(function() {
            // Load products once from server
            const allProducts = @json($products);

            // 1. Add Row Logic
            $('#addRow').on('click', function() {
                let newRow = `
                <tr class="item-row">
                    <td><input type="number" name="quantity[]" class="form-control qty" value="1"></td>
                    <td><input type="text" name="product_name[]" list="productData" class="form-control product-input" required></td>
                    <td><input type="number" name="tie_number[]" class="form-control tie_number" value="0" readonly></td>
                    <td><input type="number" name="tie_qty[]" class="form-control tie_qty" value="1" readonly></td>
                    <td><input type="text" name="perishable_type[]" class="form-control type-input bg-light" readonly placeholder="-"></td>
                    <td class="expiry-column" style="display: none;">
                        <div class="expiry-wrapper" style="display: none;">
                            <input type="date" name="exp_date[]" class="form-control expiry-input">
                        </div>
                    </td>
                    <td><input type="number" name="unit_price[]" class="form-control unit_price" step="0.01"></td>
                    <td class="totalPrice fw-bold">0.00</td>
                    <td class="row-total fw-bold text-primary">0.00</td>
                    <td><button type="button" class="btn btn-outline-danger btn-sm border-0 remove-row"><i class="fas fa-trash-alt"></i></button></td>
                </tr>`;
                $('#itemRows').append(newRow);
                toggleExpiryHeader();
            });

            // 2. Dynamic Datalist (Shows only Top 10)
            $(document).on('input', '.product-input', function() {
                let inputVal = $(this).val().toLowerCase();
                let $datalist = $('#productData');
                $datalist.empty();

                if (inputVal.length > 0) {
                    let matches = allProducts.filter(p =>
                        p.product_name.toLowerCase().includes(inputVal)
                    ).slice(0, 10);

                    matches.forEach(p => {
                        $datalist.append(`<option value="${p.product_name}">`);
                    });
                }
            });

            // 3. Selection Logic (Triggered on change/select)
            $(document).on('change', '.product-input', function() {
                let val = $(this).val();
                let $row = $(this).closest('tr');
                let product = allProducts.find(p => p.product_name === val);

                if (product) {
                    let typeValue = product.perishable_title ? product.perishable_title.toLowerCase() :
                        'non-perishable';
                    $row.find('.type-input').val(typeValue);
                    $row.find('.tie_number').val(product.tie_number || 0);
                    $row.find('.tie_qty').val(product.tie_qty || 0);

                    handleExpiryVisibility($row, typeValue);
                    calculateTotals();
                }
            });

            // 4. Expiry Visibility Logic
            function handleExpiryVisibility($row, typeValue) {
                let $wrapper = $row.find('.expiry-wrapper');
                let $input = $row.find('.expiry-input');

                // Shows date only if 'perishable' is found but not 'non-perishable'
                if (typeValue.includes('perishable') && !typeValue.includes('non')) {
                    $wrapper.show();
                    $input.attr('required', 'required');
                } else {
                    $wrapper.hide();
                    $input.removeAttr('required').val('');
                }
                toggleExpiryHeader();
            }

            // 5. Global Table Header Visibility
            function toggleExpiryHeader() {
                let anyVisible = false;
                $('.type-input').each(function() {
                    let val = $(this).val().toLowerCase();
                    if (val.includes('perishable') && !val.includes('non')) {
                        anyVisible = true;
                        return false;
                    }
                });
                $('.expiry-column').toggle(anyVisible);
            }

            // 6. Calculations & Removal
            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
                calculateTotals();
                toggleExpiryHeader();
            });

            $(document).on('input', '.qty, .unit_price, .tie_qty, .tie_number', function() {
                calculateTotals();
            });

            function calculateTotals() {
                let grossTotal = 0;
                $('.item-row').each(function() {
                    let qty = parseFloat($(this).find('.qty').val()) || 0;
                    let up = parseFloat($(this).find('.unit_price').val()) || 0;
                    let qpu = parseFloat($(this).find('.tie_qty').val()) || 0;
                    let tie = parseFloat($(this).find('.tie_number').val()) || 0;

                    let priceCalculated = qpu * tie * up;
                    $(this).find('.totalPrice').text(priceCalculated.toFixed(2));

                    let rowTotal = priceCalculated * qty;
                    $(this).find('.row-total').text(rowTotal.toFixed(2));
                    grossTotal += rowTotal;
                });

                let vatableSales = grossTotal / 1.12;
                let vatAmount = grossTotal - vatableSales;

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

                $('#gross_total_raw').val(grossTotal.toFixed(2));
                $('#vat_amount_raw').val(vatAmount.toFixed(2));
                $('#grand_total_raw').val(grossTotal.toFixed(2));
            }

            toggleExpiryHeader();
        });
    </script>
@endsection
