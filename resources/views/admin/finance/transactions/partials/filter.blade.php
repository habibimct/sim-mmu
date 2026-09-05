    {{-- ==========================================================
    FILTER
    =========================================================== --}}

    <div class="card">

        <div class="card-header">

            <h3 class="card-title">

                <i class="bi bi-funnel me-1"></i>

                Filter Transaksi

            </h3>

        </div>


        <div class="card-body">

            <form method="GET" action="{{ route('admin.finance.transactions.index') }}" class="row g-3">

                {{-- Pencarian --}}
                <div class="col-md-4">

                    <label class="form-label">
                        Cari
                    </label>

                    <input type="text" name="search" class="form-control" value="{{ request('search') }}"
                        placeholder="Kategori atau keterangan">

                </div>


                {{-- Jenis --}}
                <div class="col-md-2">

                    <label class="form-label">
                        Jenis
                    </label>

                    <select name="type" class="form-select">

                        <option value="">
                            Semua
                        </option>

                        <option value="income" @selected(request('type') === 'income')>
                            Pemasukan
                        </option>

                        <option value="expense" @selected(request('type') === 'expense')>
                            Pengeluaran
                        </option>

                    </select>

                </div>


                {{-- Unit --}}
                <div class="col-md-3">

                    <label class="form-label">
                        Unit
                    </label>

                    <select name="organization_id" class="form-select">

                        <option value="">
                            Semua Unit
                        </option>

                        @foreach ($organizations as $organization)
                            <option value="{{ $organization->id }}" @selected(request('organization_id') == $organization->id)>
                                {{ $organization->name }}
                            </option>
                        @endforeach

                    </select>

                </div>


                {{-- Tombol --}}
                <div class="col-md-3 d-flex align-items-end">

                    <button type="submit" class="btn btn-primary me-2">

                        <i class="bi bi-search me-1"></i>

                    </button>


                    <a href="{{ route('admin.finance.transactions.index') }}" class="btn btn-secondary">

                        <i class="bi bi-arrow-counterclockwise"></i>

                    </a>

                </div>

            </form>

        </div>

    </div>
