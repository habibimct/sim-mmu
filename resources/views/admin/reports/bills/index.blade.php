{{-- resources/views/admin/reports/bills/index.blade.php --}}

@extends('adminlte::page')

@section('title', 'Laporan Tagihan')

@section('content')
<div class="container-fluid">

    @include('admin.reports.bills.partials.filter')

    @include('admin.reports.bills.partials.summary')

    @include('admin.reports.bills.partials.organization-summary')

    @include('admin.reports.bills.partials.table')

    @include('admin.reports.bills.partials.student-summary')

</div>
@endsection
