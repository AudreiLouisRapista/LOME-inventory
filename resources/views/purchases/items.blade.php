@extends('themes.main')

@section('title', 'Purchase Items')

@section('content_header')
	<div class="content-header">
		<h1>Purchase Items</h1>
		<p>Add items included in the purchase</p>
	</div>
@endsection

@section('content')
	<style>
		.purchase-items-page {
			margin: 0;
			padding: 20px;
			box-sizing: border-box;
		}

		.purchase-items-page .container {
			max-width: 1400px;
			margin: 0 auto;
			background: white;
			border-radius: 10px;
			box-shadow: 0 2px 10px rgba(0,0,0,0.1);
			padding: 30px;
		}

		.purchase-items-page .page-header {
			display: flex;
			justify-content: space-between;
			align-items: center;
			margin-bottom: 30px;
			flex-wrap: wrap;
			gap: 15px;
		}

		.purchase-items-page .page-header h1 {
			color: #2c3e50;
			font-size: 28px;
			margin: 0;
		}

		.purchase-items-page .btn {
			padding: 8px 16px;
			border: none;
			border-radius: 5px;
			cursor: pointer;
			font-size: 14px;
			font-weight: 600;
			transition: all 0.3s;
			text-decoration: none;
			display: inline-flex;
			align-items: center;
			gap: 6px;
		}

		.purchase-items-page .btn-secondary {
			background: #95a5a6;
			color: white;
		}

		.purchase-items-page .btn-secondary:hover {
			background: #7f8c8d;
			color: white;
		}

		.purchase-items-page .btn-add {
			background: #4caf50;
			color: white;
			padding: 12px 24px;
			font-size: 15px;
		}

		.purchase-items-page .btn-add:hover {
			background: #3e8e41;
			color: white;
			transform: translateY(-2px);
			box-shadow: 0 4px 8px rgba(155, 89, 182, 0.3);
		}

		.purchase-items-page .btn-add::before {
			content: "+ ";
			font-size: 18px;
		}

		.purchase-items-page table {
			width: 100%;
			border-collapse: collapse;
			margin-bottom: 20px;
		}

		.purchase-items-page thead {
			background: #cc0c19;
			color: white;
		}

		.purchase-items-page th {
			padding: 15px;
			text-align: left;
			font-weight: 600;
			font-size: 14px;
		}

		.purchase-items-page td {
			padding: 12px 15px;
			border-bottom: 1px solid #ecf0f1;
			font-size: 14px;
		}

		.purchase-items-page tbody tr {
			transition: background-color 0.2s;
		}

		.purchase-items-page tbody tr:hover {
			background-color: #f8f9fa;
		}

		.purchase-items-page .status-badge {
			display: inline-block;
			padding: 5px 12px;
			border-radius: 20px;
			font-size: 12px;
			font-weight: 600;
			text-transform: uppercase;
			background: #95a5a6;
			color: white;
		}

		/* Modal styling to match purchases index */
		.purchase-items-page #addPurchaseItemModal .modal-header {
			background: #cc0c19;
			color: white;
		}

		@media (max-width: 768px) {
			.purchase-items-page .container { padding: 15px; }
			.purchase-items-page .btn-add { width: 100%; justify-content: center; }
			.purchase-items-page .page-header { flex-direction: column; align-items: stretch; }
		}
	</style>

	<div class="purchase-items-page">
		<div class="container">
			<div class="page-header">
				<h1>📦 Purchase Items</h1>
				<div class="d-flex gap-2" style="flex-wrap: wrap;">
					<a href="{{ route('purchases.index') }}" class="btn btn-secondary">
						Back
					</a>
					<button type="button" class="btn btn-add" data-bs-toggle="modal" data-bs-target="#addPurchaseItemModal">
						Add Item
					</button>
				</div>
			</div>

			<div class="table-container">
				<table>
					<thead>
						<tr>
							<th style="width: 40%;">Product</th>
							<th style="width: 15%;">Quantity</th>
							<th style="width: 15%;">Unit Price</th>
							<th style="width: 15%;">Total</th>
							<th style="width: 15%; text-align:right;">Status</th>
						</tr>
					</thead>
					<tbody>
						@php
							$purchaseItems = $purchaseItems ?? $items ?? [];
						@endphp

						@forelse ($purchaseItems as $item)
							<tr>
								<td>
									<div style="font-weight: 700; color:#2c3e50;">{{ $item->product_name ?? $item->product->product_name ?? '—' }}</div>
									<div style="color:#7f8c8d; font-size: 12px;">ID: {{ $item->product_id ?? '—' }}</div>
								</td>
								<td>{{ $item->quantity ?? '—' }}</td>
								<td>{{ isset($item->unit_price) ? number_format((float) $item->unit_price, 2) : '—' }}</td>
								<td>{{ isset($item->total_price) ? number_format((float) $item->total_price, 2) : '—' }}</td>
								<td style="text-align:right;"><span class="status-badge">Saved</span></td>
							</tr>
						@empty
							<tr>
								<td colspan="5" style="text-align:center; padding:30px; color:#95a5a6;">
									No items yet. Click “Add Item” to include products.
								</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<!-- Add Purchase Item Modal (Front-end only) -->
	<div class="modal fade" id="addPurchaseItemModal" tabindex="-1" aria-labelledby="addPurchaseItemModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-lg">
			<div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">

				<div class="modal-header bg-dark text-white py-3">
					<h5 class="modal-title fw-bold" id="addPurchaseItemModalLabel">
						<i class="fas fa-receipt me-2"></i> Add Purchase Item
					</h5>
					<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>

				<div class="modal-body p-4">
					@include('layout.partials.alerts')

					<form id="purchaseItemForm" action="#" method="POST" onsubmit="return false;">
						@csrf

						<input type="hidden" name="purchase_id" value="{{ $purchase->purchase_id ?? $purchase_id ?? request('purchase_id') ?? '' }}">

						<p class="text-muted small fw-bold text-uppercase mb-3 border-bottom pb-1">Item Details</p>

						<div class="row g-3 mb-4">
							<div class="col-md-5">
								<label for="purchase_item_category_id" class="form-label fw-semibold">Category</label>
								<div class="input-group">
									<span class="input-group-text bg-light"><i class="fas fa-tag"></i></span>
									<select name="category_id" id="purchase_item_category_id" class="form-select border-start-0" required>
										<option value="" selected disabled>Select Category</option>
										@isset($categories)
											@foreach ($categories as $category)
												<option value="{{ $category->category_ID ?? $category->id ?? '' }}">
													{{ $category->category_name ?? $category->name ?? 'Unnamed category' }}
												</option>
											@endforeach
										@endisset
									</select>
								</div>
								{{-- <div class="form-text">Select category first to filter products.</div> --}}
							</div>

							<div class="col-md-7">
								<label for="purchase_item_product_id" class="form-label fw-semibold">Product</label>
								<div class="input-group">
									<span class="input-group-text bg-light"><i class="fas fa-box"></i></span>
									{{-- <select name="product_id" id="purchase_item_product_id" class="form-select border-start-0" required>
										<option value="" selected disabled>Select Product</option>
										@isset($products)
											@foreach ($products as $product)
												<option
													value="{{ $product->product_ID ?? $product->product_id ?? '' }}"
													data-category-id="{{ $product->category_ID ?? $product->category_id ?? '' }}">
													{{ $product->product_name ?? 'Unnamed product' }}
												</option>
											@endforeach
										@endisset
									</select> --}}
									<input type="text" name="product_name" id="purchase_item_product_name" class="form-control border-start-0" placeholder="Enter product name">
								</div>
								{{-- <div class="form-text">Products will show based on selected category.</div> --}}
							</div>

							<div class="col-md-5">
								<label for="purchase_item_quantity" class="form-label fw-semibold">Quantity</label>
								<input type="number" name="quantity" id="purchase_item_quantity" class="form-control" min="1" step="1" placeholder="e.g. 10" required>
								<div class="form-text">Whole number quantity.</div>
							</div>
						</div>

						<p class="text-muted small fw-bold text-uppercase mb-3 border-bottom pb-1">Pricing</p>

						<div class="row g-3 mb-4">
							<div class="col-md-4">
								<label for="purchase_item_cost_price" class="form-label fw-semibold">Cost Price</label>
								<div class="input-group">
									<span class="input-group-text">₱</span>
									<input type="number" name="cost_price" id="purchase_item_cost_price" class="form-control" min="0" step="0.01" placeholder="0.00" required>
								</div>
							</div>

							<div class="col-md-4">
								<label for="purchase_item_selling_price" class="form-label fw-semibold">Selling Price</label>
								<div class="input-group">
									<span class="input-group-text">₱</span>
									<input type="number" name="selling_price" id="purchase_item_selling_price" class="form-control" min="0" step="0.01" placeholder="0.00" required>
								</div>
							</div>

							<div class="col-md-4">
								<label for="purchase_item_unit_price" class="form-label fw-semibold">Unit Price</label>
								<div class="input-group">
									<span class="input-group-text">₱</span>
									<input type="number" name="unit_price" id="purchase_item_unit_price" class="form-control" min="0" step="0.01" placeholder="0.00" required>
								</div>
							</div>
						</div>

						<div class="row g-3 mb-4">
							<div class="col-md-4">
								<label for="purchase_item_total_price" class="form-label fw-semibold">Total Price</label>
								<div class="input-group">
									<span class="input-group-text">₱</span>
									<input type="number" name="total_price" id="purchase_item_total_price" class="form-control" step="0.01" placeholder="0.00" readonly>
								</div>
							</div>
							<div class="col-md-4 d-flex align-items-end">
								<button type="button" class="btn btn-outline-secondary w-100" id="purchase_item_clear_btn">
									Clear
								</button>
							</div>
						</div>

						<div class="d-flex justify-content-end gap-2">
							<button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
							<button type="button" class="btn btn-dark" id="purchase_item_submit_btn">Add Item</button>
						</div>
					</form>
				</div>

			</div>
		</div>
	</div>

	<script>
		document.addEventListener('DOMContentLoaded', function () {
			const categoryEl = document.getElementById('purchase_item_category_id');
			const qtyEl = document.getElementById('purchase_item_quantity');
			const unitPriceEl = document.getElementById('purchase_item_unit_price');
			const totalEl = document.getElementById('purchase_item_total_price');
			const clearBtn = document.getElementById('purchase_item_clear_btn');
			const submitBtn = document.getElementById('purchase_item_submit_btn');
			const productEl = document.getElementById('purchase_item_product_id');
			const costPriceEl = document.getElementById('purchase_item_cost_price');
			const sellingPriceEl = document.getElementById('purchase_item_selling_price');

			function toNumber(value) {
				const n = Number.parseFloat(value);
				return Number.isFinite(n) ? n : 0;
			}

			function recalcTotal() {
				const qty = Math.max(0, Math.floor(toNumber(qtyEl.value)));
				const unitPrice = Math.max(0, toNumber(unitPriceEl.value));
				const total = qty * unitPrice;
				totalEl.value = total ? total.toFixed(2) : '';
			}

			function clearForm() {
				if (categoryEl) categoryEl.value = '';
				if (productEl) productEl.value = '';
				qtyEl.value = '';
				unitPriceEl.value = '';
				if (costPriceEl) costPriceEl.value = '';
				if (sellingPriceEl) sellingPriceEl.value = '';
				totalEl.value = '';
			}

			function filterProductsByCategory() {
				if (!categoryEl || !productEl) return;
				const selectedCategory = categoryEl.value;
				// Reset product selection whenever category changes
				productEl.value = '';
				Array.from(productEl.options).forEach(opt => {
					if (!opt.value) return; // keep placeholder
					const optCat = opt.getAttribute('data-category-id') || '';
					const isMatch = selectedCategory && optCat === selectedCategory;
					opt.hidden = !isMatch;
					opt.disabled = !isMatch;
				});
			}

			qtyEl.addEventListener('input', recalcTotal);
			unitPriceEl.addEventListener('input', recalcTotal);
			clearBtn.addEventListener('click', clearForm);
			if (categoryEl) {
				categoryEl.addEventListener('change', filterProductsByCategory);
				filterProductsByCategory();
			}

			// Front-end only: no backend submission in this view.
			submitBtn.addEventListener('click', function () {
				recalcTotal();
			});
		});
	</script>
@endsection

