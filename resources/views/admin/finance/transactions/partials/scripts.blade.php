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


<script>
    document.addEventListener('DOMContentLoaded', function() {

        const form = document.getElementById(
            'modalTransaksiBaru'
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
</script>
