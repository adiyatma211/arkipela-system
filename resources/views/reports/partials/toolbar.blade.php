<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route($reportRouteName) }}" class="row g-3 align-items-end">
            <div class="col-12 col-md-4 col-xl-3">
                <label for="start_date" class="form-label">Start Date</label>
                <input type="date" id="start_date" name="start_date" class="form-control" value="{{ $filters['start_date'] }}">
            </div>
            <div class="col-12 col-md-4 col-xl-3">
                <label for="end_date" class="form-label">End Date</label>
                <input type="date" id="end_date" name="end_date" class="form-control" value="{{ $filters['end_date'] }}">
            </div>
            <div class="col-12 col-md-4 col-xl-2">
                <button type="submit" class="btn btn-primary w-100">Apply Filter</button>
            </div>
            <div class="col-12 col-xl-4">
                <div class="d-flex flex-wrap gap-2 justify-content-xl-end">
                    <a href="{{ route($reportRouteName) }}" class="btn btn-light-secondary">Reset</a>
                    <a href="{{ route($reportRouteName, ['start_date' => $filters['start_date'], 'end_date' => $filters['end_date'], 'export' => 'excel']) }}"
                        class="btn btn-light-success">
                        Export Excel
                    </a>
                    <a href="{{ route($reportRouteName, ['start_date' => $filters['start_date'], 'end_date' => $filters['end_date'], 'export' => 'html']) }}"
                        class="btn btn-light-primary">
                        Export HTML
                    </a>
                </div>
            </div>
        </form>

        <div class="mt-3 text-muted small">
            Active period: <strong>{{ $filters['period_label'] }}</strong>
        </div>
    </div>
</div>
