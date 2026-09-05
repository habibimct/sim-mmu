{{-- =========================================================
MODAL CREATE PAYMENT
========================================================= --}}

<div class="modal fade" id="modalCreatePayment" tabindex="-1" aria-labelledby="modalCreatePaymentLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">

        <div class="modal-content">

            {{-- =====================================================
            HEADER
            ====================================================== --}}

            <div class="modal-header">

                <div>

                    <h5 class="modal-title" id="modalCreatePaymentLabel">
                        Tambah Pembayaran
                    </h5>

                    <p class="text-muted small mb-0 mt-1">
                        Catat pembayaran dan alokasikan ke tagihan siswa.
                    </p>

                </div>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>

            </div>


            {{-- =====================================================
            FORM
            ====================================================== --}}

            <form method="POST" action="{{ route('admin.finance.payments.store') }}" id="formCreatePayment">

                @csrf

                <div class="modal-body">

                    {{-- =================================================
                    SISWA
                    ================================================== --}}

                    <div class="mb-4">

                        <label for="payment_student_academic_year_id" class="form-label">
                            Siswa
                            <span class="text-danger">*</span>
                        </label>

                        <select id="payment_student_academic_year_id" name="student_academic_year_id"
                            class="form-select" required>

                            <option value="">
                                -- Pilih Siswa --
                            </option>

                            @foreach ($studentAcademicYears as $studentAcademicYear)
                                <option value="{{ $studentAcademicYear->id }}">
                                    {{ $studentAcademicYear->student->nis }}
                                    —
                                    {{ $studentAcademicYear->student->name }}

                                    @if ($studentAcademicYear->schoolClass)
                                        —
                                        {{ $studentAcademicYear->schoolClass->name }}
                                    @endif

                                </option>
                            @endforeach

                        </select>

                    </div>


                    {{-- =================================================
                    DAFTAR TAGIHAN
                    ================================================== --}}

                    <div class="mb-4">

                        <div class="d-flex justify-content-between align-items-center mb-2">

                            <label class="form-label mb-0">
                                Tagihan yang Dibayar
                            </label>

                            <span id="paymentBillsLoading" class="text-muted small d-none">
                                <span class="spinner-border spinner-border-sm me-1"></span>

                                Memuat tagihan...
                            </span>

                        </div>


                        <div id="paymentBillsContainer" class="border rounded">

                            <div id="paymentBillsEmpty" class="text-center text-muted py-4">

                                <i class="bi bi-receipt fs-2 d-block mb-2"></i>

                                Pilih siswa terlebih dahulu.

                            </div>


                            <div id="paymentBillsList" class="d-none">

                            </div>

                        </div>

                    </div>


                    {{-- =================================================
                    RINGKASAN ALOKASI
                    ================================================== --}}

                    <div id="paymentAllocationSummary" class="alert alert-light border d-none">

                        <div class="row g-3">

                            <div class="col-md-4">

                                <div class="small text-muted">
                                    Total Tagihan Dipilih
                                </div>

                                <div id="selectedBillsAmount" class="fw-bold">
                                    Rp 0
                                </div>

                            </div>

                            <div class="col-md-4">

                                <div class="small text-muted">
                                    Nominal Pembayaran
                                </div>

                                <div id="paymentAmountPreview" class="fw-bold">
                                    Rp 0
                                </div>

                            </div>

                            <div class="col-md-4">

                                <div class="small text-muted">
                                    Sisa Alokasi
                                </div>

                                <div id="paymentAllocationRemaining" class="fw-bold">
                                    Rp 0
                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- =================================================
                    NOMINAL
                    ================================================== --}}

                    <div class="row g-3">

                        <div class="col-md-6">

                            <label for="payment_amount" class="form-label">
                                Nominal Pembayaran
                                <span class="text-danger">*</span>
                            </label>

                            <input type="number" name="amount" id="payment_amount" class="form-control" min="0.01"
                                step="0.01" placeholder="500000" required>

                        </div>


                        {{-- =================================================
                        METODE
                        ================================================== --}}

                        <div class="col-md-6">

                            <label for="payment_method" class="form-label">
                                Metode Pembayaran
                                <span class="text-danger">*</span>
                            </label>

                            <select name="payment_method" id="payment_method" class="form-select" required>

                                <option value="">
                                    -- Pilih Metode --
                                </option>

                                <option value="cash">
                                    Tunai
                                </option>

                                <option value="bank_transfer">
                                    Transfer Bank
                                </option>

                                <option value="online">
                                    Online
                                </option>

                            </select>

                        </div>

                    </div>


                    {{-- =================================================
                    TANGGAL
                    ================================================== --}}

                    <div class="mt-3">

                        <label for="payment_date" class="form-label">
                            Tanggal Pembayaran
                            <span class="text-danger">*</span>
                        </label>

                        <input type="datetime-local" name="payment_date" id="payment_date" class="form-control"
                            required>

                    </div>


                    {{-- =================================================
                    KETERANGAN
                    ================================================== --}}

                    <div class="mt-3">

                        <label for="payment_description" class="form-label">
                            Keterangan
                        </label>

                        <textarea name="description" id="payment_description" rows="3" maxlength="1000" class="form-control"
                            placeholder="Keterangan pembayaran..."></textarea>

                    </div>

                </div>


                {{-- =====================================================
                FOOTER
                ====================================================== --}}

                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Batal
                    </button>

                    <button type="submit" class="btn btn-primary" id="btnCreatePayment">
                        <i class="bi bi-check-lg me-1"></i>
                        Simpan Pembayaran
                    </button>

                </div>

            </form>

        </div>

    </div>
</div>


{{-- =========================================================
JAVASCRIPT CREATE PAYMENT
========================================================= --}}

@push('js')
    <script>
        document.addEventListener(
            'DOMContentLoaded',
            function() {

                const studentSelect =
                    document.getElementById(
                        'payment_student_academic_year_id'
                    );

                const billsContainer =
                    document.getElementById(
                        'paymentBillsContainer'
                    );

                const billsList =
                    document.getElementById(
                        'paymentBillsList'
                    );

                const billsEmpty =
                    document.getElementById(
                        'paymentBillsEmpty'
                    );

                const billsLoading =
                    document.getElementById(
                        'paymentBillsLoading'
                    );

                const paymentAmount =
                    document.getElementById(
                        'payment_amount'
                    );

                const selectedBillsAmount =
                    document.getElementById(
                        'selectedBillsAmount'
                    );

                const paymentAmountPreview =
                    document.getElementById(
                        'paymentAmountPreview'
                    );

                const paymentAllocationRemaining =
                    document.getElementById(
                        'paymentAllocationRemaining'
                    );

                const allocationSummary =
                    document.getElementById(
                        'paymentAllocationSummary'
                    );

                const form =
                    document.getElementById(
                        'formCreatePayment'
                    );

                const submitButton =
                    document.getElementById(
                        'btnCreatePayment'
                    );


                if (
                    !studentSelect ||
                    !billsList ||
                    !form
                ) {
                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | Format Rupiah
                |--------------------------------------------------------------------------
                */

                function formatRupiah(
                    value
                ) {

                    return new Intl.NumberFormat(
                        'id-ID'
                    ).format(
                        Number(value) || 0
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | Reset daftar tagihan
                |--------------------------------------------------------------------------
                */

                function resetBills() {

                    billsList.innerHTML = '';

                    billsList.classList.add(
                        'd-none'
                    );

                    billsEmpty.classList.remove(
                        'd-none'
                    );

                    billsEmpty.innerHTML = `
                <i class="bi bi-receipt fs-2 d-block mb-2"></i>
                Pilih siswa terlebih dahulu.
            `;

                    allocationSummary.classList.add(
                        'd-none'
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | Ambil tagihan siswa
                |--------------------------------------------------------------------------
                */

                async function loadStudentBills() {

                    const studentAcademicYearId =
                        studentSelect.value;


                    resetBills();


                    if (!studentAcademicYearId) {
                        return;
                    }


                    billsEmpty.classList.add(
                        'd-none'
                    );

                    billsLoading.classList.remove(
                        'd-none'
                    );


                    const url =
                        `{{ url('admin/finance/payments/student') }}/${studentAcademicYearId}/bills`;


                    try {

                        const response =
                            await fetch(
                                url, {
                                    headers: {
                                        'Accept': 'application/json',

                                        'X-Requested-With': 'XMLHttpRequest',
                                    }
                                }
                            );


                        if (!response.ok) {

                            throw new Error(
                                'Gagal mengambil data tagihan.'
                            );

                        }


                        const bills =
                            await response.json();


                        if (!bills.length) {

                            billsEmpty.innerHTML = `
                        <i class="bi bi-check-circle fs-2 d-block mb-2"></i>
                        Siswa ini tidak memiliki tagihan yang dapat dibayar.
                    `;

                            billsEmpty.classList.remove(
                                'd-none'
                            );

                            return;
                        }


                        renderBills(
                            bills
                        );


                    } catch (error) {

                        billsEmpty.innerHTML = `
                    <div class="text-danger">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        ${error.message}
                    </div>
                `;

                        billsEmpty.classList.remove(
                            'd-none'
                        );

                    } finally {

                        billsLoading.classList.add(
                            'd-none'
                        );

                    }

                }


                /*
                |--------------------------------------------------------------------------
                | Render tagihan
                |--------------------------------------------------------------------------
                */

                function renderBills(bills) {

                    const container =
                        document.getElementById(
                            'paymentBillsContainer'
                        );

                    container.innerHTML = '';

                    if (!bills.length) {

                        container.innerHTML = `
            <div class="alert alert-info mb-0">
                <i class="bi bi-info-circle me-1"></i>
                Siswa ini belum memiliki tagihan
                yang dapat dibayar.
            </div>
        `;

                        return;
                    }


                    bills.forEach(function(bill) {

                        const amount =
                            Number(
                                bill.amount ?? 0
                            );

                        const paidAmount =
                            Number(
                                bill.paid_amount ?? 0
                            );

                        const pendingAmount =
                            Number(
                                bill.pending_amount ?? 0
                            );

                        const remainingAmount =
                            Number(
                                bill.remaining_amount ?? 0
                            );


                        const amountFormatted =
                            amount.toLocaleString(
                                'id-ID'
                            );

                        const paidFormatted =
                            paidAmount.toLocaleString(
                                'id-ID'
                            );

                        const pendingFormatted =
                            pendingAmount.toLocaleString(
                                'id-ID'
                            );

                        const remainingFormatted =
                            remainingAmount.toLocaleString(
                                'id-ID'
                            );


                        /*
                        |--------------------------------------------------------------------------
                        | Informasi pembayaran yang sedang pending
                        |--------------------------------------------------------------------------
                        */

                        const pendingInfo =
                            pendingAmount > 0 ?
                            `
                    <div class="text-warning small mt-1">
                        <i class="bi bi-clock me-1"></i>
                        Rp ${pendingFormatted}
                        sedang menunggu konfirmasi.
                    </div>
                ` :
                            '';


                        /*
                        |--------------------------------------------------------------------------
                        | Jika tidak ada sisa tagihan
                        |--------------------------------------------------------------------------
                        */

                        if (
                            remainingAmount <= 0
                        ) {

                            container.insertAdjacentHTML(
                                'beforeend',
                                `
                <div
                    class="border rounded p-3 mb-2 bg-light"
                >

                    <div
                        class="d-flex justify-content-between align-items-start"
                    >

                        <div>

                            <div class="fw-semibold">
                                ${bill.bill_type ?? '-'}
                            </div>

                            <div class="text-muted small">
                                Periode:
                                ${bill.period ?? '-'}
                            </div>

                        </div>

                        ${
                            pendingAmount > 0
                                ? `
                                                <span class="badge bg-warning text-dark">
                                                    <i class="bi bi-clock me-1"></i>
                                                    Menunggu
                                                </span>
                                              `
                                : `
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle me-1"></i>
                                                    Lunas
                                                </span>
                                              `
                        }

                    </div>


                    <div class="mt-2">

                        <div class="small text-muted">
                            Nominal tagihan
                        </div>

                        <div class="fw-semibold">
                            Rp ${amountFormatted}
                        </div>

                    </div>


                    ${
                        paidAmount > 0
                            ? `
                                            <div class="text-success small mt-1">
                                                <i class="bi bi-check-circle me-1"></i>
                                                Sudah dibayar:
                                                Rp ${paidFormatted}
                                            </div>
                                          `
                            : ''
                    }


                    ${pendingInfo}


                    ${
                        pendingAmount > 0
                            ? `
                                            <div class="text-muted small mt-2">
                                                Tagihan sedang menunggu
                                                konfirmasi pembayaran.
                                            </div>
                                          `
                            : ''
                    }

                </div>
                `
                            );

                            return;
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | Tagihan masih memiliki sisa
                        |--------------------------------------------------------------------------
                        |
                        | Checkbox tetap tersedia.
                        | Nominal yang digunakan adalah remaining_amount.
                        |
                        */

                        container.insertAdjacentHTML(
                            'beforeend',
                            `
            <div
                class="border rounded p-3 mb-2"
            >

                <div
                    class="d-flex align-items-start"
                >

                    <div class="form-check me-3">

                        <input
                            type="checkbox"
                            class="form-check-input payment-bill-checkbox"
                            name="bill_ids[]"
                            value="${bill.id}"
                            data-amount="${remainingAmount}"
                            data-remaining="${remainingAmount}"
                            data-bill-id="${bill.id}"
                            id="paymentBill${bill.id}"
                        >

                    </div>


                    <label
                        class="form-check-label flex-grow-1"
                        for="paymentBill${bill.id}"
                    >

                        <div
                            class="d-flex justify-content-between align-items-start"
                        >

                            <div>

                                <div class="fw-semibold">
                                    ${bill.bill_type ?? '-'}
                                </div>

                                <div class="text-muted small">
                                    Periode:
                                    ${bill.period ?? '-'}
                                </div>

                            </div>


                            <div class="text-end">

                                <div class="fw-semibold">
                                    Rp ${amountFormatted}
                                </div>

                            </div>

                        </div>


                        ${
                            paidAmount > 0
                                ? `
                                                <div class="text-success small mt-1">
                                                    <i class="bi bi-check-circle me-1"></i>
                                                    Sudah dibayar:
                                                    Rp ${paidFormatted}
                                                </div>
                                              `
                                : ''
                        }


                        ${pendingInfo}


                        <div class="text-primary small fw-semibold mt-1">
                            <i class="bi bi-wallet2 me-1"></i>
                            Sisa dapat dibayar:
                            Rp ${remainingFormatted}
                        </div>

                    </label>

                </div>

            </div>
            `
                        );

                    });


                    /*
                    |--------------------------------------------------------------------------
                    | Event checkbox
                    |--------------------------------------------------------------------------
                    */

                    document
                        .querySelectorAll(
                            '.payment-bill-checkbox'
                        )
                        .forEach(
                            function(checkbox) {

                                checkbox.addEventListener(
                                    'change',
                                    updatePaymentAmount
                                );

                            }
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | Hitung ulang nominal
                    |--------------------------------------------------------------------------
                    */

                    updatePaymentAmount();
                }


                /*
                |--------------------------------------------------------------------------
                | Hitung ringkasan
                |--------------------------------------------------------------------------
                */

                function updateSummary() {

                    const checked =
                        document.querySelectorAll(
                            '.payment-bill-checkbox:checked'
                        );


                    let totalSelected =
                        0;


                    checked.forEach(
                        function(checkbox) {

                            totalSelected +=
                                Number(
                                    checkbox.dataset.remaining
                                ) || 0;

                        }
                    );


                    const amount =
                        Number(
                            paymentAmount.value
                        ) || 0;


                    const remaining =
                        totalSelected -
                        amount;


                    selectedBillsAmount.textContent =
                        'Rp ' +
                        formatRupiah(
                            totalSelected
                        );


                    paymentAmountPreview.textContent =
                        'Rp ' +
                        formatRupiah(
                            amount
                        );


                    paymentAllocationRemaining.textContent =
                        'Rp ' +
                        formatRupiah(
                            Math.max(
                                0,
                                remaining
                            )
                        );


                    if (
                        checked.length > 0
                    ) {

                        allocationSummary.classList.remove(
                            'd-none'
                        );

                    } else {

                        allocationSummary.classList.add(
                            'd-none'
                        );

                    }

                }


                /*
                |--------------------------------------------------------------------------
                | Siswa berubah
                |--------------------------------------------------------------------------
                */

                studentSelect.addEventListener(
                    'change',
                    loadStudentBills
                );


                /*
                |--------------------------------------------------------------------------
                | Checkbox tagihan
                |--------------------------------------------------------------------------
                */

                billsContainer.addEventListener(
                    'change',
                    function(event) {

                        if (
                            event.target.classList.contains(
                                'payment-bill-checkbox'
                            )
                        ) {

                            updateSummary();

                        }

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | Nominal berubah
                |--------------------------------------------------------------------------
                */

                paymentAmount.addEventListener(
                    'input',
                    updateSummary
                );


                /*
                |--------------------------------------------------------------------------
                | Reset modal
                |--------------------------------------------------------------------------
                */

                const modal =
                    document.getElementById(
                        'modalCreatePayment'
                    );


                if (modal) {

                    modal.addEventListener(
                        'hidden.bs.modal',
                        function() {

                            form.reset();

                            resetBills();

                            paymentAmount.value =
                                '';

                            if (submitButton) {

                                submitButton.disabled =
                                    false;

                                submitButton.innerHTML = `
                            <i class="bi bi-check-lg me-1"></i>
                            Simpan Pembayaran
                        `;

                            }

                        }
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | Cegah double click
                |--------------------------------------------------------------------------
                */

                form.addEventListener(
                    'submit',
                    function(event) {

                        const checked =
                            document.querySelectorAll(
                                '.payment-bill-checkbox:checked'
                            );


                        /*
                        |--------------------------------------------------------------------------
                        | Harus memilih minimal satu tagihan
                        |--------------------------------------------------------------------------
                        */

                        if (
                            checked.length === 0
                        ) {

                            event.preventDefault();

                            alert(
                                'Silakan pilih minimal satu tagihan.'
                            );

                            return;

                        }


                        /*
                        |--------------------------------------------------------------------------
                        | Hitung total sisa tagihan yang dipilih
                        |--------------------------------------------------------------------------
                        */

                        let totalSelected = 0;


                        checked.forEach(
                            function(checkbox) {

                                totalSelected +=
                                    Number(
                                        checkbox.dataset.remaining
                                    ) || 0;

                            }
                        );


                        /*
                        |--------------------------------------------------------------------------
                        | Nominal pembayaran
                        |--------------------------------------------------------------------------
                        */

                        const amount =
                            Number(
                                paymentAmount.value
                            ) || 0;


                        /*
                        |--------------------------------------------------------------------------
                        | Nominal harus lebih dari 0
                        |--------------------------------------------------------------------------
                        */

                        if (
                            amount <= 0
                        ) {

                            event.preventDefault();

                            alert(
                                'Nominal pembayaran harus lebih dari Rp0.'
                            );

                            paymentAmount.focus();

                            return;

                        }


                        /*
                        |--------------------------------------------------------------------------
                        | Tidak boleh melebihi total sisa tagihan
                        |--------------------------------------------------------------------------
                        */

                        if (
                            amount > totalSelected
                        ) {

                            event.preventDefault();

                            alert(
                                'Nominal pembayaran melebihi total sisa tagihan yang dipilih.'
                            );

                            paymentAmount.focus();

                            return;

                        }


                        /*
                        |--------------------------------------------------------------------------
                        | Semua valid → cegah double click
                        |--------------------------------------------------------------------------
                        */

                        if (
                            submitButton
                        ) {

                            if (
                                submitButton.disabled
                            ) {

                                event.preventDefault();

                                return;

                            }


                            submitButton.disabled =
                                true;

                            submitButton.innerHTML = `
                <span
                    class="spinner-border spinner-border-sm me-1"
                    role="status"
                    aria-hidden="true"
                ></span>
                Menyimpan...
            `;

                        }

                    }
                );

            });
    </script>
@endpush
