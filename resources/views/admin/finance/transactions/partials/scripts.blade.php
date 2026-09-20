@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const modalElement =
                document.getElementById('modalTransaksiBaru');

            if (modalElement) {

                const modal =
                    new bootstrap.Modal(modalElement);

                modal.show();

            }

        });
    </script>
@endif



<script>
    document.addEventListener('DOMContentLoaded', function() {

        const kind =
            document.getElementById('transactionKind');

        const normalTypeField =
            document.getElementById('normalTypeField');

        const transactionType =
            document.getElementById('transactionType');

        const categoryField =
            document.getElementById('categoryField');

        const transactionCategory =
            document.getElementById('transactionCategory');

        const depositProofField =
            document.getElementById('depositProofField');

        const depositProof =
            document.getElementById('depositProof');

        const normalKindHelp =
            document.getElementById('normalKindHelp');

        const depositKindHelp =
            document.getElementById('depositKindHelp');


        /*
        |--------------------------------------------------------------------------
        | Tampilkan form sesuai jenis input
        |--------------------------------------------------------------------------
        */

        function updateTransactionForm() {

            if (!kind) {
                return;
            }

            const isDeposit =
                kind.value === 'deposit';


            /*
            |--------------------------------------------------------------------------
            | SETORAN UNIT
            |--------------------------------------------------------------------------
            */

            if (isDeposit) {

                normalTypeField.classList.add('d-none');

                categoryField.classList.add('d-none');

                depositProofField.classList.remove('d-none');

                normalKindHelp.classList.add('d-none');

                depositKindHelp.classList.remove('d-none');


                /*
                | Field transaksi biasa tidak wajib
                */

                transactionType.required = false;

                transactionCategory.required = false;


                /*
                | Kosongkan nilai transaksi biasa
                */

                transactionType.value = '';

                transactionCategory.value = '';


                /*
                | Bukti setoran wajib
                */

                depositProof.required = true;

            }


            /*
            |--------------------------------------------------------------------------
            | TRANSAKSI BIASA
            |--------------------------------------------------------------------------
            */
            else {

                normalTypeField.classList.remove('d-none');

                categoryField.classList.remove('d-none');

                depositProofField.classList.add('d-none');

                normalKindHelp.classList.remove('d-none');

                depositKindHelp.classList.add('d-none');


                /*
                | Field transaksi biasa wajib
                */

                transactionType.required = true;

                transactionCategory.required = true;


                /*
                | Bukti setoran tidak wajib
                */

                depositProof.required = false;

                /*
                | Reset input file
                */

                depositProof.value = '';

            }

        }


        /*
        |--------------------------------------------------------------------------
        | Ketika jenis input berubah
        |--------------------------------------------------------------------------
        */

        if (kind) {
            kind.addEventListener(
                'change',
                updateTransactionForm
            );

            updateTransactionForm();
        }

    });
</script>


{{-- <script>
    document.addEventListener('DOMContentLoaded', function() {

        const form = document.querySelector(
            '#modalTransaksiBaru form'
        );
        const button = document.getElementById(
            'btnSimpanTransaksi'
        );

        if (!form || !button) {
            return;
        }

        let submitted = false;

        form.addEventListener('submit', function(event) {

            /*
            |--------------------------------------------------------------------------
            | Cegah submit kedua dari halaman yang sama
            |--------------------------------------------------------------------------
            */

            if (submitted) {
                event.preventDefault();
                return;
            }

            submitted = true;


            /*
            |--------------------------------------------------------------------------
            | Kunci tombol
            |--------------------------------------------------------------------------
            */

            button.disabled = true;

            button.innerHTML = `
            <span
                class="spinner-border spinner-border-sm me-1"
                role="status"
                aria-hidden="true"
            ></span>
            Menyimpan...
        `;

        });

    });
</script> --}}

<script>
    document.addEventListener('DOMContentLoaded', function() {

        /*
        |--------------------------------------------------------------------------
        | Pastikan handler hanya dipasang satu kali
        |--------------------------------------------------------------------------
        */

        if (window.__simMmuOfflineSubmitHandlerInstalled) {
            return;
        }

        window.__simMmuOfflineSubmitHandlerInstalled = true;


        const form =
            document.getElementById('formTransaksiBaru');

        const button =
            document.getElementById('btnSimpanTransaksi');

        if (!form || !button) {
            return;
        }


        let processingOffline = false;


        form.addEventListener('submit', async function(event) {

            /*
            |--------------------------------------------------------------------------
            | ONLINE
            |--------------------------------------------------------------------------
            | Biarkan proses Laravel berjalan normal.
            */

            if (navigator.onLine) {
                return;
            }


            /*
            |--------------------------------------------------------------------------
            | OFFLINE
            |--------------------------------------------------------------------------
            | Untuk tahap ini hanya Transaksi Biasa.
            */

            const transactionKind =
                document.getElementById('transactionKind')?.value;

            if (transactionKind === 'deposit') {
                event.preventDefault();
                event.stopImmediatePropagation();

                alert(
                    'Setoran Unit harus dilakukan saat terhubung ke internet.\n\n' +
                    'Silakan sambungkan internet terlebih dahulu, kemudian ulangi penyimpanan setoran.'
                );

                return;
            }

            if (transactionKind !== 'normal') {
                return;
            }


            /*
            |--------------------------------------------------------------------------
            | Hentikan event handler lain
            |--------------------------------------------------------------------------
            */

            event.preventDefault();
            event.stopImmediatePropagation();


            /*
            |--------------------------------------------------------------------------
            | Cegah pemrosesan dua kali
            |--------------------------------------------------------------------------
            */

            if (processingOffline) {
                return;
            }

            processingOffline = true;


            button.disabled = true;

            button.innerHTML = `
                <span
                    class="spinner-border spinner-border-sm me-1"
                    role="status"
                    aria-hidden="true"
                ></span>
                Menyimpan Offline...
            `;


            try {

                const formData =
                    new FormData(form);


                const organizationId =
                    Number(
                        formData.get('organization_id')
                    );

                const transactionDate =
                    formData.get('transaction_date');

                const type =
                    formData.get('type');

                const amount =
                    Number(
                        formData.get('amount')
                    );

                const paymentMethod =
                    formData.get('payment_method');

                const category =
                    formData.get('category');

                const description =
                    formData.get('description') || null;


                /*
                |--------------------------------------------------------------------------
                | Validasi dasar
                |--------------------------------------------------------------------------
                */

                if (
                    !organizationId ||
                    !transactionDate ||
                    !type ||
                    !amount ||
                    !paymentMethod ||
                    !category
                ) {
                    throw new Error(
                        'Data transaksi belum lengkap.'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | Buat satu transaksi offline
                |--------------------------------------------------------------------------
                */

                const offlineTransaction = {

                    sync_id: crypto.randomUUID(),

                    transaction_kind: 'normal',

                    organization_id: organizationId,

                    transaction_date: transactionDate,

                    type: type,

                    amount: amount,

                    payment_method: paymentMethod,

                    category: category,

                    description: description,

                    status: 'pending_sync',

                    created_at: new Date().toISOString(),

                };


                /*
                |--------------------------------------------------------------------------
                | Simpan SATU kali ke IndexedDB
                |--------------------------------------------------------------------------
                */

                await SimMmuOffline.addToQueue(
                    offlineTransaction
                );


                /*
                |--------------------------------------------------------------------------
                | Update indikator antrean
                |--------------------------------------------------------------------------
                */

                const pendingCount =
                    await SimMmuOffline.getPendingCount();


                const countElement =
                    document.getElementById(
                        'pendingSyncCount'
                    );

                const statusElement =
                    document.getElementById(
                        'pendingSyncStatus'
                    );


                if (countElement) {
                    countElement.textContent =
                        pendingCount;
                }


                if (statusElement) {
                    statusElement.classList.remove(
                        'd-none'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | Informasi kepada pengguna
                |--------------------------------------------------------------------------
                */

                alert(
                    'Transaksi berhasil disimpan secara offline.\n\n' +
                    'Transaksi akan otomatis disinkronkan ' +
                    'ketika koneksi internet kembali.'
                );


                /*
                |--------------------------------------------------------------------------
                | Tutup modal
                |--------------------------------------------------------------------------
                */

                const modalElement =
                    document.getElementById(
                        'modalTransaksiBaru'
                    );


                if (modalElement) {

                    const modal =
                        bootstrap.Modal.getInstance(
                            modalElement
                        );

                    if (modal) {
                        modal.hide();
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | Reset form
                |--------------------------------------------------------------------------
                */

                form.reset();


                const kind =
                    document.getElementById(
                        'transactionKind'
                    );


                if (kind) {

                    kind.value = 'normal';

                    kind.dispatchEvent(
                        new Event('change')
                    );
                }


            } catch (error) {

                console.error(
                    '[SIM-MMU Offline] Gagal menyimpan transaksi:',
                    error
                );


                alert(
                    'Transaksi offline gagal disimpan.\n\n' +
                    'Silakan coba lagi.'
                );

            } finally {

                processingOffline = false;

                button.disabled = false;

                button.innerHTML = `
                    <i class="bi bi-save me-1"></i>
                    Simpan Transaksi
                `;
            }

        });

    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const indicator = document.getElementById('connectionStatus');

        if (!indicator) {
            return;
        }

        function updateConnectionStatus() {
            if (navigator.onLine) {
                indicator.innerHTML =
                    '<i class="bi bi-wifi me-1"></i> Online';

                indicator.classList.remove('bg-danger');
                indicator.classList.add('bg-success');
            } else {
                indicator.innerHTML =
                    '<i class="bi bi-wifi-off me-1"></i> Offline';

                indicator.classList.remove('bg-success');
                indicator.classList.add('bg-danger');
            }
        }

        async function updatePendingSyncStatus() {
            const status = document.getElementById('pendingSyncStatus');
            const countElement = document.getElementById('pendingSyncCount');

            if (!status || !countElement) {
                return;
            }

            try {
                const count = await SimMmuOffline.getPendingCount();

                countElement.textContent = count;

                if (count > 0) {
                    status.classList.remove('d-none');
                } else {
                    status.classList.add('d-none');
                }
            } catch (error) {
                console.error(
                    'Gagal membaca antrean offline:',
                    error
                );
            }
        }

        updateConnectionStatus();
        updatePendingSyncStatus();

        window.addEventListener('online', updateConnectionStatus);
        window.addEventListener('offline', updateConnectionStatus);
    });
</script>


<script>
    document.addEventListener('DOMContentLoaded', function() {

        const form = document.getElementById('formTransaksiBaru');
        const button = document.getElementById('btnSimpanTransaksi');

        if (!form || !button) {
            return;
        }

        form.addEventListener('submit', async function(event) {

            /*
            |--------------------------------------------------------------------------
            | Jika online → biarkan Laravel menangani seperti biasa
            |--------------------------------------------------------------------------
            */

            if (navigator.onLine) {
                return;
            }

            /*
            |--------------------------------------------------------------------------
            | Offline hanya untuk Transaksi Biasa
            |--------------------------------------------------------------------------
            */

            const transactionKind =
                document.getElementById('transactionKind')?.value;

            if (transactionKind !== 'normal') {
                return;
            }

            /*
            |--------------------------------------------------------------------------
            | Cegah submit HTTP
            |--------------------------------------------------------------------------
            */

            event.preventDefault();

            try {

                const formData = new FormData(form);

                const syncId = crypto.randomUUID();

                const offlineTransaction = {
                    sync_id: syncId,

                    transaction_kind: 'normal',

                    organization_id: Number(formData.get('organization_id')),

                    transaction_date: formData.get('transaction_date'),

                    type: formData.get('type'),

                    amount: Number(formData.get('amount')),

                    payment_method: formData.get('payment_method'),

                    category: formData.get('category'),

                    description: formData.get('description') || null,

                    status: 'pending_sync',

                    created_at: new Date().toISOString(),
                };

                /*
                |--------------------------------------------------------------------------
                | Simpan ke IndexedDB
                |--------------------------------------------------------------------------
                */

                await SimMmuOffline.addToQueue(
                    offlineTransaction
                );

                /*
                |--------------------------------------------------------------------------
                | Update tombol
                |--------------------------------------------------------------------------
                */

                button.disabled = true;

                button.innerHTML = `
                    <i class="bi bi-check-circle me-1"></i>
                    Tersimpan Offline
                `;

                /*
                |--------------------------------------------------------------------------
                | Update indikator antrean
                |--------------------------------------------------------------------------
                */

                const countElement =
                    document.getElementById('pendingSyncCount');

                const statusElement =
                    document.getElementById('pendingSyncStatus');

                if (countElement) {
                    const count =
                        await SimMmuOffline.getPendingCount();

                    countElement.textContent = count;
                }

                if (statusElement) {
                    statusElement.classList.remove('d-none');
                }

                /*
                |--------------------------------------------------------------------------
                | Beri tahu pengguna
                |--------------------------------------------------------------------------
                */

                alert(
                    'Transaksi berhasil disimpan secara offline. ' +
                    'Transaksi akan otomatis disinkronkan ketika koneksi internet kembali.'
                );

                /*
                |--------------------------------------------------------------------------
                | Tutup modal
                |--------------------------------------------------------------------------
                */

                const modalElement =
                    document.getElementById('modalTransaksiBaru');

                if (modalElement) {

                    const modal =
                        bootstrap.Modal.getInstance(modalElement);

                    if (modal) {
                        modal.hide();
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Reset form
                |--------------------------------------------------------------------------
                */

                form.reset();

            } catch (error) {

                console.error(
                    '[SIM-MMU Offline] Gagal menyimpan transaksi:',
                    error
                );

                alert(
                    'Transaksi offline gagal disimpan. ' +
                    'Silakan coba lagi.'
                );

                button.disabled = false;

                button.innerHTML = `
                    <i class="bi bi-save me-1"></i>
                    Simpan Transaksi
                `;
            }

        });

    });
</script>
