@php
    $items = old('items', $itemsData ?? []);
    $supplierProductMap = collect($suppliers)
        ->mapWithKeys(function ($supplier) {
            return [
                (string) $supplier->id => $supplier->resolvedProducts()
                    ->pluck('product_name')
                    ->filter()
                    ->values()
                    ->all(),
            ];
        })
        ->all();
    $supplierOptionsData = collect($suppliers)
        ->map(function ($supplier) {
            $approvalLabel = str($supplier->approval_status ?? 'approved')->replace('_', ' ')->title()->toString();
            $statusLabel = str($supplier->status ?? 'active')->replace('_', ' ')->title()->toString();

            return [
                'id' => $supplier->id,
                'label' => "{$supplier->supplier_name} ({$supplier->supplier_code})",
                'meta' => "{$approvalLabel} Approval | {$statusLabel}",
            ];
        })
        ->values()
        ->all();
    if (empty($items)) {
        $items = [[
            'supplier_id' => null,
            'product_name' => '',
            'specification' => '',
            'quantity_kg' => null,
            'selling_price' => null,
            'buying_price' => null,
        ]];
    }
@endphp

@csrf
@if ($formMethod !== 'POST')
    @method($formMethod)
@endif

<div class="row">
    <div class="col-12 col-lg-4">
        <div class="mb-3">
            <label for="client_id" class="form-label">Client</label>
            <select id="client_id" name="client_id" class="form-select @error('client_id') is-invalid @enderror" required>
                <option value="">Select client</option>
                @foreach ($clients as $clientOption)
                    <option value="{{ $clientOption->id }}" @selected((string) old('client_id', $order->client_id) === (string) $clientOption->id)>
                        {{ $clientOption->company_name }} ({{ $clientOption->client_code }})
                    </option>
                @endforeach
            </select>
            @error('client_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="mb-3">
            <label for="destination_country" class="form-label">Destination Country</label>
            <input type="text" id="destination_country" name="destination_country" class="form-control @error('destination_country') is-invalid @enderror" value="{{ old('destination_country', $order->destination_country) }}" placeholder="United States">
            @error('destination_country')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="mb-3">
            <label for="destination_port" class="form-label">Destination Port</label>
            <input type="text" id="destination_port" name="destination_port" class="form-control @error('destination_port') is-invalid @enderror" value="{{ old('destination_port', $order->destination_port) }}" placeholder="Los Angeles, New York">
            @error('destination_port')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="mb-3">
            <label for="shipment_mode" class="form-label">Shipment Mode</label>
            <input type="text" id="shipment_mode" name="shipment_mode" class="form-control @error('shipment_mode') is-invalid @enderror" value="{{ old('shipment_mode', $order->shipment_mode) }}" placeholder="FCL, LCL, Air">
            @error('shipment_mode')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-12 col-lg-4">
        <div class="mb-3">
            <label for="order_date" class="form-label">Order Date</label>
            <input type="date" id="order_date" name="order_date" class="form-control @error('order_date') is-invalid @enderror" value="{{ old('order_date', optional($order->order_date)->format('Y-m-d') ?: $order->order_date) }}" required>
            @error('order_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="mb-3">
            <label for="delivery_date" class="form-label">Delivery Date</label>
            <input type="date" id="delivery_date" name="delivery_date" class="form-control @error('delivery_date') is-invalid @enderror" value="{{ old('delivery_date', optional($order->delivery_date)->format('Y-m-d') ?: $order->delivery_date) }}">
            @error('delivery_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-12 col-lg-3">
        <div class="mb-3">
            <label for="po_number" class="form-label">PO Number</label>
            <input type="text" id="po_number" name="po_number" class="form-control @error('po_number') is-invalid @enderror" value="{{ old('po_number', $order->po_number) }}">
            @error('po_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-12 col-lg-3">
        <div class="mb-3">
            <label for="currency" class="form-label">Currency</label>
            <input type="text" id="currency" name="currency" class="form-control @error('currency') is-invalid @enderror" value="{{ old('currency', $order->currency ?: 'USD') }}" placeholder="USD">
            @error('currency')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-12 col-lg-3">
        <div class="mb-3">
            <label for="incoterm" class="form-label">Incoterm</label>
            <input type="text" id="incoterm" name="incoterm" class="form-control @error('incoterm') is-invalid @enderror" value="{{ old('incoterm', $order->incoterm) }}" placeholder="FOB, CIF, EXW">
            @error('incoterm')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-12 col-lg-3">
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                @foreach ($statusOptions as $option)
                    <option value="{{ $option['value'] }}" @selected(old('status', $order->status) === $option['value'])>{{ $option['label'] }}</option>
                @endforeach
            </select>
            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-12">
        <div class="mb-3">
            <label for="payment_term" class="form-label">Payment Term</label>
            <input type="text" id="payment_term" name="payment_term" class="form-control @error('payment_term') is-invalid @enderror" value="{{ old('payment_term', $order->payment_term) }}" placeholder="T/T 30% DP 70%, LC at sight">
            @error('payment_term')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <div>
                <h5 class="mb-1">Order Items</h5>
                <small class="text-muted">Supplier hanya menampilkan yang sudah owner-approved. Status operasional tetap ditampilkan sebagai konteks, dan product mengikuti supplier yang dipilih.</small>
            </div>
            <button type="button" class="btn btn-light-primary" id="add-item-row">Add Item Row</button>
        </div>

        @error('items')<div class="text-danger small mb-2">{{ $message }}</div>@enderror

        <div class="table-responsive">
            <table class="table table-bordered align-middle" id="order-items-table">
                <thead>
                    <tr>
                        <th style="min-width: 220px;">Supplier</th>
                        <th style="min-width: 180px;">Product</th>
                        <th style="min-width: 180px;">Specification</th>
                        <th style="min-width: 130px;">Qty (kg)</th>
                        <th style="min-width: 140px;">Selling Price</th>
                        <th style="min-width: 160px;">Product Cost / kg</th>
                        <th style="min-width: 220px;">Product Margin</th>
                        <th style="width: 70px;" class="text-center">Act</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $index => $item)
                        @php
                            $selectedSupplierId = (string) data_get($item, 'supplier_id');
                            $selectedProductName = data_get($item, 'product_name');
                            $productOptions = $supplierProductMap[$selectedSupplierId] ?? [];
                            if ($selectedProductName && ! in_array($selectedProductName, $productOptions, true)) {
                                $productOptions[] = $selectedProductName;
                            }
                        @endphp
                        <tr class="order-item-row">
                            <td>
                                <select name="items[{{ $index }}][supplier_id]" class="form-select js-supplier-select" required>
                                    <option value="">Select supplier</option>
                                    @foreach ($supplierOptionsData as $supplierOption)
                                        <option value="{{ $supplierOption['id'] }}" @selected((string) data_get($item, 'supplier_id') === (string) $supplierOption['id'])>
                                            {{ $supplierOption['label'] }} - {{ $supplierOption['meta'] }}
                                        </option>
                                    @endforeach
                                </select>
                                @error("items.$index.supplier_id")<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </td>
                            <td>
                                <select name="items[{{ $index }}][product_name]" class="form-select js-product-select" data-selected-product="{{ $selectedProductName }}" @disabled($selectedSupplierId === '') required>
                                    <option value="">{{ $selectedSupplierId !== '' ? 'Select product' : 'Select supplier first' }}</option>
                                    @foreach ($productOptions as $productOption)
                                        <option value="{{ $productOption }}" @selected($selectedProductName === $productOption)>{{ $productOption }}</option>
                                    @endforeach
                                </select>
                                @error("items.$index.product_name")<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </td>
                            <td>
                                <textarea name="items[{{ $index }}][specification]" rows="2" class="form-control">{{ data_get($item, 'specification') }}</textarea>
                                @error("items.$index.specification")<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </td>
                            <td>
                                <input type="text" inputmode="decimal" name="items[{{ $index }}][quantity_kg]" class="form-control js-qty js-numeric-input" value="{{ data_get($item, 'quantity_kg') }}" required>
                                @error("items.$index.quantity_kg")<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </td>
                            <td>
                                <input type="text" inputmode="decimal" name="items[{{ $index }}][selling_price]" class="form-control js-selling-price js-numeric-input" value="{{ data_get($item, 'selling_price') }}" required>
                                @error("items.$index.selling_price")<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </td>
                            <td>
                                <input type="text" inputmode="decimal" name="items[{{ $index }}][buying_price]" class="form-control js-buying-price js-numeric-input" value="{{ data_get($item, 'buying_price') }}" required>
                                @error("items.$index.buying_price")<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </td>
                            <td>
                                <div class="fw-semibold js-line-profit">0</div>
                                <small class="text-muted js-line-total-sales">Sales Total: 0</small><br>
                                <small class="text-muted js-line-total-buying">Product Cost Total: 0</small><br>
                                <small class="text-muted js-line-total-profit">Gross Line Profit: 0</small>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-light-danger js-remove-row">Remove</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-12 col-xl-6">
        <div class="card border mb-4">
            <div class="card-body">
                <h6 class="mb-3">Export Cost Sheet</h6>
                <div class="row g-3">
                    <div class="col-12 col-lg-6">
                        <label for="local_logistics_cost" class="form-label">Local Logistics Cost</label>
                        <input type="text" inputmode="decimal" id="local_logistics_cost" name="local_logistics_cost" class="form-control js-extra-cost js-numeric-input @error('local_logistics_cost') is-invalid @enderror" value="{{ old('local_logistics_cost', $order->local_logistics_cost ?? 0) }}">
                        @error('local_logistics_cost')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-lg-6">
                        <label for="export_document_cost" class="form-label">Export Document Cost</label>
                        <input type="text" inputmode="decimal" id="export_document_cost" name="export_document_cost" class="form-control js-extra-cost js-numeric-input @error('export_document_cost') is-invalid @enderror" value="{{ old('export_document_cost', $order->export_document_cost ?? 0) }}">
                        @error('export_document_cost')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-lg-6">
                        <label for="forwarding_cost" class="form-label">Forwarding Cost</label>
                        <input type="text" inputmode="decimal" id="forwarding_cost" name="forwarding_cost" class="form-control js-extra-cost js-numeric-input @error('forwarding_cost') is-invalid @enderror" value="{{ old('forwarding_cost', $order->forwarding_cost ?? 0) }}">
                        @error('forwarding_cost')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-lg-6">
                        <label for="freight_cost" class="form-label">Freight Cost</label>
                        <input type="text" inputmode="decimal" id="freight_cost" name="freight_cost" class="form-control js-extra-cost js-numeric-input @error('freight_cost') is-invalid @enderror" value="{{ old('freight_cost', $order->freight_cost ?? 0) }}">
                        @error('freight_cost')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-lg-6">
                        <label for="insurance_cost" class="form-label">Insurance Cost</label>
                        <input type="text" inputmode="decimal" id="insurance_cost" name="insurance_cost" class="form-control js-extra-cost js-numeric-input @error('insurance_cost') is-invalid @enderror" value="{{ old('insurance_cost', $order->insurance_cost ?? 0) }}">
                        @error('insurance_cost')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-lg-6">
                        <label for="compliance_cost" class="form-label">Compliance Cost</label>
                        <input type="text" inputmode="decimal" id="compliance_cost" name="compliance_cost" class="form-control js-extra-cost js-numeric-input @error('compliance_cost') is-invalid @enderror" value="{{ old('compliance_cost', $order->compliance_cost ?? 0) }}" placeholder="FDA test, lab, phytosanitary">
                        @error('compliance_cost')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-lg-6">
                        <label for="destination_cost" class="form-label">Destination Cost</label>
                        <input type="text" inputmode="decimal" id="destination_cost" name="destination_cost" class="form-control js-extra-cost js-numeric-input @error('destination_cost') is-invalid @enderror" value="{{ old('destination_cost', $order->destination_cost ?? 0) }}" placeholder="Broker, inland, inspection">
                        @error('destination_cost')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-lg-6">
                        <label for="misc_cost" class="form-label">Misc Cost</label>
                        <input type="text" inputmode="decimal" id="misc_cost" name="misc_cost" class="form-control js-extra-cost js-numeric-input @error('misc_cost') is-invalid @enderror" value="{{ old('misc_cost', $order->misc_cost ?? 0) }}">
                        @error('misc_cost')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-6">
        <div class="card bg-light mb-4">
            <div class="card-body">
                <h6 class="mb-3">Export Profit Summary</h6>
                <div class="d-flex justify-content-between mb-2">
                    <span>Sales Total</span>
                    <strong id="summary-sales">0</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Product Cost Total</span>
                    <strong id="summary-buying">0</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Gross Profit</span>
                    <strong id="summary-gross-profit">0</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Gross Margin</span>
                    <strong id="summary-gross-margin">0%</strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-2">
                    <span>Total Export Costs</span>
                    <strong id="summary-extra-cost">0</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Net Profit</span>
                    <strong id="summary-net-profit">0</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Net Margin</span>
                    <strong id="summary-net-margin">0%</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="mb-3">
            <label for="notes" class="form-label">Notes</label>
            <textarea id="notes" name="notes" rows="4" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $order->notes) }}</textarea>
            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<div class="d-flex justify-content-end gap-2">
    <a href="{{ route('orders.index') }}" class="btn btn-light">Cancel</a>
    <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tableBody = document.querySelector('#order-items-table tbody');
            const addButton = document.getElementById('add-item-row');
            const currencyInput = document.getElementById('currency');
            const summarySales = document.getElementById('summary-sales');
            const summaryBuying = document.getElementById('summary-buying');
            const summaryGrossProfit = document.getElementById('summary-gross-profit');
            const summaryGrossMargin = document.getElementById('summary-gross-margin');
            const summaryExtraCost = document.getElementById('summary-extra-cost');
            const summaryNetProfit = document.getElementById('summary-net-profit');
            const summaryNetMargin = document.getElementById('summary-net-margin');
            const extraCostInputs = [...document.querySelectorAll('.js-extra-cost')];
            const supplierOptionsHtml = @json(
                '<option value="">Select supplier</option>' .
                collect($supplierOptionsData)->map(fn ($supplierOption) => '<option value="' . $supplierOption['id'] . '">' . e($supplierOption['label'] . ' - ' . $supplierOption['meta']) . '</option>')->implode('')
            );
            const supplierProductsMap = @json($supplierProductMap);
            const numericFieldsSelector = '.js-numeric-input';

            const escapeHtml = (value) => String(value)
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');

            const renumberRows = () => {
                [...tableBody.querySelectorAll('.order-item-row')].forEach((row, index) => {
                    row.querySelectorAll('input, select, textarea').forEach((field) => {
                        field.name = field.name.replace(/items\[\d+\]/, `items[${index}]`);
                    });
                });
            };

            const sanitizeNumericValue = (value) => {
                let sanitized = String(value || '').replace(',', '.').replace(/[^0-9.]/g, '');
                const firstDotIndex = sanitized.indexOf('.');

                if (firstDotIndex !== -1) {
                    sanitized = sanitized.slice(0, firstDotIndex + 1) + sanitized.slice(firstDotIndex + 1).replaceAll('.', '');
                }

                if (sanitized === '') {
                    return '';
                }

                if (sanitized.startsWith('.')) {
                    sanitized = `0${sanitized}`;
                }

                const [integerPartRaw, decimalPart] = sanitized.split('.');
                const normalizedIntegerPart = integerPartRaw.replace(/^0+(?=\d)/, '') || '0';

                if (typeof decimalPart === 'string') {
                    return `${normalizedIntegerPart}.${decimalPart}`;
                }

                return normalizedIntegerPart;
            };

            const bindNumericGuards = (root = document) => {
                root.querySelectorAll(numericFieldsSelector).forEach((field) => {
                    if (field.dataset.numericGuardBound === 'true') {
                        return;
                    }

                    field.dataset.numericGuardBound = 'true';
                    field.setAttribute('autocomplete', 'off');

                    field.addEventListener('keydown', (event) => {
                        if (['e', 'E', '+', '-'].includes(event.key)) {
                            event.preventDefault();
                        }
                    });

                    field.addEventListener('beforeinput', (event) => {
                        if (event.data && /[^0-9.,]/.test(event.data)) {
                            event.preventDefault();
                            return;
                        }

                        if (
                            event.inputType === 'insertText' &&
                            /^[1-9]$/.test(event.data || '') &&
                            (field.value === '0' || field.value === '0.00')
                        ) {
                            event.preventDefault();
                            field.value = event.data;
                            field.dispatchEvent(new Event('input', { bubbles: true }));
                        }
                    });

                    field.addEventListener('input', () => {
                        const sanitized = sanitizeNumericValue(field.value);

                        if (field.value !== sanitized) {
                            field.value = sanitized;
                        }
                    });

                    field.addEventListener('paste', (event) => {
                        event.preventDefault();
                        const pastedText = event.clipboardData?.getData('text') ?? '';
                        const sanitized = sanitizeNumericValue(pastedText);
                        const start = field.selectionStart ?? field.value.length;
                        const end = field.selectionEnd ?? field.value.length;
                        const nextValue = field.value.slice(0, start) + sanitized + field.value.slice(end);
                        field.value = sanitizeNumericValue(nextValue);
                        field.dispatchEvent(new Event('input', { bubbles: true }));
                    });
                });
            };

            const buildProductOptionsHtml = (supplierId, selectedProduct = '') => {
                const normalizedSupplierId = String(supplierId || '');
                const products = supplierProductsMap[normalizedSupplierId] || [];
                const selectedValue = String(selectedProduct || '');

                if (products.length === 0 && selectedValue) {
                    return `<option value="">Select product</option><option value="${escapeHtml(selectedValue)}" selected>${escapeHtml(selectedValue)}</option>`;
                }

                if (products.length === 0) {
                    return `<option value="">${normalizedSupplierId ? 'No product available' : 'Select supplier first'}</option>`;
                }

                const fallbackProducts = selectedValue && !products.includes(selectedValue)
                    ? [...products, selectedValue]
                    : products;

                return '<option value="">Select product</option>' + fallbackProducts.map((product) => {
                    const isSelected = product === selectedValue ? ' selected' : '';
                    return `<option value="${escapeHtml(product)}"${isSelected}>${escapeHtml(product)}</option>`;
                }).join('');
            };

            const syncProductSelect = (row, selectedProduct = '') => {
                const supplierSelect = row.querySelector('.js-supplier-select');
                const productSelect = row.querySelector('.js-product-select');

                if (!supplierSelect || !productSelect) {
                    return;
                }

                const supplierId = supplierSelect.value;
                productSelect.innerHTML = buildProductOptionsHtml(supplierId, selectedProduct);
                productSelect.disabled = !supplierId || (!(supplierProductsMap[String(supplierId)] || []).length && !selectedProduct);

                if (selectedProduct) {
                    productSelect.value = selectedProduct;
                }
            };

            const initializeProductSelects = () => {
                [...tableBody.querySelectorAll('.order-item-row')].forEach((row) => {
                    const productSelect = row.querySelector('.js-product-select');
                    syncProductSelect(row, productSelect?.dataset.selectedProduct || productSelect?.value || '');
                });
            };

            const formatMoney = (value) => {
                const currency = (currencyInput.value || 'USD').toUpperCase();
                const numericValue = Number(value || 0);
                const formatted = new Intl.NumberFormat('en-US', {
                    minimumFractionDigits: Number.isInteger(numericValue) ? 0 : 2,
                    maximumFractionDigits: 2,
                }).format(numericValue);

                return `${currency} ${formatted}`;
            };

            const formatPercent = (value) => {
                const numericValue = Number(value || 0);
                const formatted = new Intl.NumberFormat('en-US', {
                    minimumFractionDigits: Number.isInteger(numericValue) ? 0 : 2,
                    maximumFractionDigits: 2,
                }).format(numericValue);

                return `${formatted}%`;
            };

            const refreshSummary = () => {
                let totalSales = 0;
                let totalBuying = 0;

                [...tableBody.querySelectorAll('.order-item-row')].forEach((row) => {
                    const qty = Number(row.querySelector('.js-qty')?.value || 0);
                    const sellingPrice = Number(row.querySelector('.js-selling-price')?.value || 0);
                    const buyingPrice = Number(row.querySelector('.js-buying-price')?.value || 0);
                    const unitProfit = sellingPrice - buyingPrice;
                    const lineSales = qty * sellingPrice;
                    const lineBuying = qty * buyingPrice;
                    const lineProfit = lineSales - lineBuying;

                    totalSales += lineSales;
                    totalBuying += lineBuying;

                    row.querySelector('.js-line-profit').textContent = formatMoney(unitProfit);
                    row.querySelector('.js-line-total-sales').textContent = `Sales Total: ${formatMoney(lineSales)}`;
                    row.querySelector('.js-line-total-buying').textContent = `Product Cost Total: ${formatMoney(lineBuying)}`;
                    row.querySelector('.js-line-total-profit').textContent = `Gross Line Profit: ${formatMoney(lineProfit)}`;
                });

                const grossProfit = totalSales - totalBuying;
                const grossMargin = totalSales > 0 ? (grossProfit / totalSales) * 100 : 0;
                const extraCost = extraCostInputs.reduce((sum, input) => sum + Number(input.value || 0), 0);
                const netProfit = grossProfit - extraCost;
                const netMargin = totalSales > 0 ? (netProfit / totalSales) * 100 : 0;

                summarySales.textContent = formatMoney(totalSales);
                summaryBuying.textContent = formatMoney(totalBuying);
                summaryGrossProfit.textContent = formatMoney(grossProfit);
                summaryGrossMargin.textContent = formatPercent(grossMargin);
                summaryExtraCost.textContent = formatMoney(extraCost);
                summaryNetProfit.textContent = formatMoney(netProfit);
                summaryNetMargin.textContent = formatPercent(netMargin);
            };

            addButton.addEventListener('click', () => {
                const index = tableBody.querySelectorAll('.order-item-row').length;
                const row = document.createElement('tr');
                row.className = 'order-item-row';
                row.innerHTML = `
                    <td>
                        <select name="items[${index}][supplier_id]" class="form-select js-supplier-select" required>${supplierOptionsHtml}</select>
                    </td>
                    <td>
                        <select name="items[${index}][product_name]" class="form-select js-product-select" disabled required>
                            <option value="">Select supplier first</option>
                        </select>
                    </td>
                    <td>
                        <textarea name="items[${index}][specification]" rows="2" class="form-control"></textarea>
                    </td>
                    <td>
                        <input type="text" inputmode="decimal" name="items[${index}][quantity_kg]" class="form-control js-qty js-numeric-input" required>
                    </td>
                    <td>
                        <input type="text" inputmode="decimal" name="items[${index}][selling_price]" class="form-control js-selling-price js-numeric-input" required>
                    </td>
                    <td>
                        <input type="text" inputmode="decimal" name="items[${index}][buying_price]" class="form-control js-buying-price js-numeric-input" required>
                    </td>
                    <td>
                        <div class="fw-semibold js-line-profit">0</div>
                        <small class="text-muted js-line-total-sales">Sales Total: 0</small><br>
                        <small class="text-muted js-line-total-buying">Product Cost Total: 0</small><br>
                        <small class="text-muted js-line-total-profit">Gross Line Profit: 0</small>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-light-danger js-remove-row">Remove</button>
                    </td>
                `;

                tableBody.appendChild(row);
                bindNumericGuards(row);
                syncProductSelect(row);
                refreshSummary();
            });

            tableBody.addEventListener('click', (event) => {
                const removeButton = event.target.closest('.js-remove-row');
                if (!removeButton) {
                    return;
                }

                if (tableBody.querySelectorAll('.order-item-row').length === 1) {
                    removeButton.closest('.order-item-row').querySelectorAll('input, textarea, select').forEach((field) => {
                        field.value = '';
                    });
                    syncProductSelect(removeButton.closest('.order-item-row'));
                } else {
                    removeButton.closest('.order-item-row').remove();
                    renumberRows();
                }

                refreshSummary();
            });

            tableBody.addEventListener('change', (event) => {
                if (event.target.matches('.js-supplier-select')) {
                    syncProductSelect(event.target.closest('.order-item-row'));
                }
            });

            tableBody.addEventListener('input', (event) => {
                if (event.target.matches('.js-qty, .js-selling-price, .js-buying-price')) {
                    refreshSummary();
                }
            });

            extraCostInputs.forEach((input) => input.addEventListener('input', refreshSummary));
            currencyInput.addEventListener('input', refreshSummary);
            bindNumericGuards();
            initializeProductSelects();
            refreshSummary();
        });
    </script>
@endpush
