
    {{-- ==========================================================
    MODAL TRANSAKSI BARU
    ========================================================== --}}

    <div class="modal fade" id="modalTransaksiBaru" tabindex="-1" aria-labelledby="modalTransaksiBaruLabel"
        aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-centered">

            <div class="modal-content">

                <form method="POST" action="{{ route('admin.finance.transactions.store') }}"
                    enctype="multipart/form-data" id="formTransaksiBaru">

                    @csrf

                    <div class="modal-header">

                        <h5 class="modal-title" id="modalTransaksiBaruLabel">

                            <i class="bi bi-cash-stack me-1"></i>

                            Transaksi Baru

                        </h5>

                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                    </div>


                    <div class="modal-body">

                        @if ($errors->any())

                            <div class="alert alert-danger">

                                <strong>Terdapat kesalahan:</strong>

                                <ul class="mb-0 mt-2">

                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach

                                </ul>

                            </div>

                        @endif

                        <div class="row g-3">

                            {{-- ==================================================
                        JENIS INPUT
                        =================================================== --}}

                            <div class="col-12">

                                <label class="form-label">
                                    Jenis Input
                                </label>

                                <select name="transaction_kind" id="transactionKind" class="form-select" required>

                                    <option value="normal" @selected(old('transaction_kind', 'normal') === 'normal')>
                                        Transaksi Biasa
                                    </option>

                                    <option value="deposit" @selected(old('transaction_kind') === 'deposit')>
                                        Setoran Unit ke Parent
                                    </option>

                                </select>

                                <div class="form-text">

                                    <span id="normalKindHelp">

                                        Digunakan untuk mencatat pemasukan
                                        atau pengeluaran langsung.

                                    </span>

                                    <span id="depositKindHelp" class="d-none">

                                        Digunakan untuk menyetorkan uang
                                        dari unit kepada organisasi parent.
                                        Setoran harus dikonfirmasi terlebih dahulu.

                                    </span>

                                </div>

                            </div>


                            {{-- ==================================================
                            UNIT
                            ================================================== --}}

                            <div class="col-md-6">

                                <label class="form-label">
                                    Unit
                                </label>

                                @php
                                    $userOrganizations = auth()
                                        ->user()
                                        ->organizations()
                                        ->where('is_active', true)
                                        ->get();
                                @endphp

                                @if ($userOrganizations->count() === 1)

                                    @php
                                        $currentOrganization = $userOrganizations->first();
                                    @endphp

                                    <input type="hidden" name="organization_id" value="{{ $currentOrganization->id }}">

                                    <input type="text" class="form-control" value="{{ $currentOrganization->name }}"
                                        readonly>
                                @else
                                    <select name="organization_id" class="form-select" required>

                                        <option value="">
                                            -- Pilih Unit --
                                        </option>

                                        @foreach ($userOrganizations as $organization)
                                            <option value="{{ $organization->id }}" @selected(old('organization_id') == $organization->id)>
                                                {{ $organization->name }}
                                            </option>
                                        @endforeach

                                    </select>

                                @endif

                            </div>


                            {{-- ==================================================
                        TANGGAL
                        =================================================== --}}

                            <div class="col-md-6">

                                <label class="form-label">
                                    Tanggal
                                </label>

                                <input type="date" name="transaction_date"
                                    value="{{ old('transaction_date', now()->format('Y-m-d')) }}"
                                    max="{{ now()->format('Y-m-d') }}" class="form-control" required>

                            </div>


                            {{-- ==================================================
                        JENIS TRANSAKSI BIASA
                        =================================================== --}}

                            <div class="col-md-6" id="normalTypeField">

                                <label class="form-label">
                                    Jenis Transaksi
                                </label>

                                <select name="type" id="transactionType" class="form-select">

                                    <option value="">
                                        -- Pilih Jenis --
                                    </option>

                                    <option value="income" @selected(old('type') === 'income')>
                                        Pemasukan
                                    </option>

                                    <option value="expense" @selected(old('type') === 'expense')>
                                        Pengeluaran
                                    </option>

                                </select>

                            </div>


                            {{-- ==================================================
                        JUMLAH
                        =================================================== --}}

                            <div class="col-md-6">

                                <label class="form-label">
                                    Jumlah
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text">
                                        Rp
                                    </span>

                                    <input type="number" name="amount" value="{{ old('amount') }}" min="1"
                                        step="0.01" class="form-control" required>

                                </div>

                            </div>


                            {{-- ==================================================
                        METODE
                        =================================================== --}}

                            <div class="col-md-6">

                                <label class="form-label">
                                    Metode Pembayaran
                                </label>

                                <select name="payment_method" class="form-select" required>

                                    <option value="">
                                        -- Pilih Metode --
                                    </option>

                                    <option value="cash" @selected(old('payment_method') === 'cash')>
                                        Tunai / Offline
                                    </option>

                                    <option value="bank_transfer" @selected(old('payment_method') === 'bank_transfer')>
                                        Transfer Bank
                                    </option>

                                    <option value="online" @selected(old('payment_method') === 'online')>
                                        Online Lainnya
                                    </option>

                                </select>

                            </div>


                            {{-- ==================================================
                        KATEGORI
                        =================================================== --}}

                            <div class="col-md-6" id="categoryField">

                                <label class="form-label">
                                    Kategori
                                </label>

                                <input type="text" name="category" value="{{ old('category') }}"
                                    class="form-control" id="transactionCategory" placeholder="Contoh: Operasional">

                            </div>


                            {{-- ==================================================
                        BUKTI SETORAN
                        =================================================== --}}

                            <div class="col-12 d-none" id="depositProofField">

                                <div class="alert alert-info mb-3">

                                    <i class="bi bi-info-circle me-1"></i>

                                    <strong>Setoran Unit</strong>

                                    <div class="mt-1">

                                        Uang akan disetorkan kepada
                                        organisasi parent dari unit yang dipilih.
                                        Parent akan melakukan konfirmasi sebelum
                                        transaksi keuangan dibuat.

                                    </div>

                                </div>


                                <label class="form-label">

                                    Bukti Setoran

                                </label>

                                <input type="file" name="proof" id="depositProof" class="form-control"
                                    accept="image/jpeg,image/png,image/webp">

                                <div class="form-text">

                                    Upload gambar slip setoran atau bukti uang
                                    masuk. Maksimal 5 MB.

                                </div>

                            </div>


                            {{-- ==================================================
                        KETERANGAN
                        =================================================== --}}

                            <div class="col-12">

                                <label class="form-label">
                                    Keterangan
                                </label>

                                <textarea name="description" rows="3" class="form-control" placeholder="Keterangan transaksi...">{{ old('description') }}</textarea>

                            </div>

                        </div>

                    </div>


                    <div class="modal-footer">

                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">

                            Batal

                        </button>
                        <button type="submit" id="btnSimpanTransaksi" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>
                            Simpan Transaksi
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>
