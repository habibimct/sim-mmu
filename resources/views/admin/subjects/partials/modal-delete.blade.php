<div
    class="modal fade"
    id="modalDeleteSubject"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog modal-dialog-centered">

        <form
            method="POST"
            id="formDeleteSubject"
            class="modal-content"
        >

            @csrf

            @method('DELETE')


            <div class="modal-header">

                <h5 class="modal-title text-danger">

                    <i class="bi bi-exclamation-triangle me-1"></i>

                    Hapus Mata Pelajaran

                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>


            <div class="modal-body">

                <p class="mb-0">

                    Apakah Anda yakin ingin menghapus mata pelajaran

                    <strong id="deleteSubjectName"></strong>?

                </p>

                <div class="alert alert-warning mt-3 mb-0">

                    <i class="bi bi-info-circle me-1"></i>

                    Data yang sudah dihapus tidak dapat dikembalikan.

                </div>

            </div>


            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="btn btn-danger"
                >

                    <i class="bi bi-trash me-1"></i>

                    Ya, Hapus

                </button>

            </div>

        </form>

    </div>

</div>
