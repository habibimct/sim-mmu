@extends('adminlte::page')

@section('title', 'Transaksi Baru')

@section('content_header')

    <h1>
        <i class="bi bi-plus-circle me-1"></i>
        Transaksi Baru
    </h1>

@stop

@section('content')

    <div class="card">

        <div class="card-header">
            <h3 class="card-title">
                Input Transaksi Keuangan
            </h3>
        </div>

        <form
            method="POST"
            action="{{ route('admin.finance.transactions.store') }}"
        >

            @csrf

            <div class="card-body">

                <div class="row g-3">

                    {{-- Unit --}}
                    <div class="col-md-6">

                        <label class="form-label">
                            Unit
                        </label>

                        <select
                            name="organization_id"
                            class="form-select @error('organization_id') is-invalid @enderror"
                            required
                        >

                            <option value="">
                                -- Pilih Unit --
                            </option>

                            @foreach ($organizations as $organization)

                                <option
                                    value="{{ $organization->id }}"
                                    @selected(
                                        old('organization_id') == $organization->id
                                    )
                                >
                                    {{ $organization->name }}
                                </option>

                            @endforeach

                        </select>

                        @error('organization_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- Tanggal --}}
                    <div class="col-md-6">

                        <label class="form-label">
                            Tanggal
                        </label>

                        <input
                            type="date"
                            name="transaction_date"
                            value="{{ old('transaction_date', now()->format('Y-m-d')) }}"
                            class="form-control @error('transaction_date') is-invalid @enderror"
                            required
                        >

                        @error('transaction_date')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- Jenis --}}
                    <div class="col-md-6">

                        <label class="form-label">
                            Jenis Transaksi
                        </label>

                        <select
                            name="type"
                            class="form-select @error('type') is-invalid @enderror"
                            required
                        >

                            <option value="">
                                -- Pilih Jenis --
                            </option>

                            <option
                                value="income"
                                @selected(old('type') === 'income')
                            >
                                Pemasukan
                            </option>

                            <option
                                value="expense"
                                @selected(old('type') === 'expense')
                            >
                                Pengeluaran
                            </option>

                        </select>

                        @error('type')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- Jumlah --}}
                    <div class="col-md-6">

                        <label class="form-label">
                            Jumlah
                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                Rp
                            </span>

                            <input
                                type="number"
                                name="amount"
                                value="{{ old('amount') }}"
                                min="1"
                                step="0.01"
                                class="form-control @error('amount') is-invalid @enderror"
                                required
                            >

                        </div>

                        @error('amount')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- Metode --}}
                    <div class="col-md-6">

                        <label class="form-label">
                            Metode Pembayaran
                        </label>

                        <select
                            name="payment_method"
                            class="form-select @error('payment_method') is-invalid @enderror"
                            required
                        >

                            <option value="">
                                -- Pilih Metode --
                            </option>

                            <option
                                value="cash"
                                @selected(old('payment_method') === 'cash')
                            >
                                Tunai / Offline
                            </option>

                            <option
                                value="bank_transfer"
                                @selected(old('payment_method') === 'bank_transfer')
                            >
                                Transfer Bank
                            </option>

                            <option
                                value="online"
                                @selected(old('payment_method') === 'online')
                            >
                                Online Lainnya
                            </option>

                        </select>

                        @error('payment_method')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- Kategori --}}
                    <div class="col-md-6">

                        <label class="form-label">
                            Kategori
                        </label>

                        <input
                            type="text"
                            name="category"
                            value="{{ old('category') }}"
                            class="form-control @error('category') is-invalid @enderror"
                            placeholder="Contoh: Operasional"
                            required
                        >

                        @error('category')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- Keterangan --}}
                    <div class="col-12">

                        <label class="form-label">
                            Keterangan
                        </label>

                        <textarea
                            name="description"
                            rows="3"
                            class="form-control @error('description') is-invalid @enderror"
                            placeholder="Keterangan transaksi..."
                        >{{ old('description') }}</textarea>

                        @error('description')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>

            </div>


            <div class="card-footer text-end">

                <a
                    href="{{ route('admin.finance.transactions.index') }}"
                    class="btn btn-secondary"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    <i class="bi bi-save me-1"></i>
                    Simpan Transaksi
                </button>

            </div>

        </form>

    </div>

@stop
