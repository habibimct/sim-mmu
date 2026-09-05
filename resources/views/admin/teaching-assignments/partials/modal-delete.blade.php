<div
    class="modal fade"
    id="modalDeleteTeachingAssignment"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog">

        <div class="modal-content">

            <form
                method="POST"
                id="formDeleteTeachingAssignment"
            >

                @csrf
                @method('DELETE')

                <div class="modal-header">

                    <h5 class="modal-title text-danger">

                        <i class="bi bi-trash me-1"></i>

                        Hapus Penugasan

                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                    ></button>

                </div>


                <div class="modal-body">

                    <p class="mb-2">

                        Apakah Anda yakin ingin menghapus penugasan berikut?

                    </p>

                    <div
                        class="alert alert-warning mb-0"
                        id="deleteTeachingAssignmentLabel"
                    ></div>

                </div>


                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-light"
                        data-bs-dismiss="modal"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="btn btn-danger"
                    >

                        <i class="bi bi-trash me-1"></i>

                        Hapus

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>
