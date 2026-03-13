@extends('themes.main')

{{-- 1. DEFINE PAGE TITLE --}}
@section('title', 'POS History')

@section('content')
    <div class="container-fluid py-4">
        {{-- Header Section --}}
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="fw-bold mb-0">POS Import History</h4>
                <p class="text-muted small">Track and locate previously imported POS data files</p>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm border-start border-primary border-4">
                    <div class="card-body">
                        <p class="text-muted small fw-bold mb-1">TOTAL IMPORTS</p>
                        <h5 class="mb-0">{{ number_format($totalImports) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm border-start border-success border-4">
                    <div class="card-body">
                        <p class="text-muted small fw-bold mb-1">SUCCESSFUL</p>
                        <h5 class="mb-0">{{ number_format($successImports) }}</h5>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Logs Table --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <h6 class="mb-0 fw-bold text-secondary">Import Logs</h6>
                    <div class="input-group" style="width: 300px;">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control bg-light border-0" id="logSearch"
                            placeholder="Search file name...">
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary small fw-bold">
                        <tr>
                            <th class="ps-4">FILE NAME</th>
                            <th>IMPORT DATE</th>
                            <th>RECORDS</th>
                            <th>USER</th>
                            <th>STATUS</th>
                            <th class="text-end pe-4">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        {{-- Dynamic Icon based on File Extension --}}
                                        @if (str_contains($log->FileName, '.csv'))
                                            <i class="bi bi-filetype-csv fs-4 text-success me-3"></i>
                                        @else
                                            <i class="bi bi-filetype-xlsx fs-4 text-primary me-3"></i>
                                        @endif
                                        <div>
                                            <span class="fw-bold d-block">{{ $log->FileName }}</span>
                                            <span class="text-muted extra-small">Ref: #{{ $log->Import_logs_ID }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    {{ date('M d, Y', strtotime($log->Uploaded_At)) }}
                                    <br><small class="text-muted">{{ date('h:i A', strtotime($log->Uploaded_At)) }}</small>
                                </td>
                                <td>{{ number_format($log->row_count) }} Rows</td>
                                <td>{{ $log->name ?? 'System' }}</td>
                                <td>
                                    <span
                                        class="badge {{ $log->Status == 'Success' ? 'bg-success-subtle text-success border border-success' : 'bg-danger-subtle text-danger border border-danger' }}">
                                        {{ $log->Status }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    {{-- Locate Button (Filtered View) --}}
                                    <a href="#" class="btn btn-sm btn-outline-primary" title="Locate Data">
                                        <i class="bi bi-geo-alt-fill"></i> Locate
                                    </a>
                                    {{-- Download Button (Physical Repository) --}}
                                    <a href="{{ route('download_importedFile', $log->Import_logs_ID) }}"
                                        class="btn btn-sm btn-light border ms-1" title="Download Original">
                                        <i class="bi bi-download"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-folder2-open fs-1 d-block mb-2"></i>
                                    No import logs found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="card-footer bg-white py-3 border-0">
                <div class="d-flex justify-content-end">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

<link rel="stylesheet" href="{{ asset('css/pages/pos_history_style.css') }}">
