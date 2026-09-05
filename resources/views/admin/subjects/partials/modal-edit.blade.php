<div class="modal fade" id="modalEditSubject" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog">

        <form method="POST" id="formEditSubject" class="modal-content">

            @csrf

            @method('PUT')


            <div class="modal-header">

                <h5 class="modal-title">

                    <i class="bi bi-pencil-square me-1"></i>

                    Edit Mata Pelajaran

                </h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

            </div>
    {{-- Pesan error --}}
    @if ($errors->any())

        <div class="alert alert-danger alert-dismissible fade show">

            <strong>
                Periksa kembali data yang dimasukkan.
            </strong>

            <ul class="mb-0 mt-2">

                @foreach ($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

    @endif

            <div class="modal-body">

                <div class="mb-3">

                    <label class="form-label">
                        Kode
                    </label>

                    <input type="text" name="code" id="editSubjectCode" class="form-control" maxlength="50"
                        value="{{ old('code') }}" required>

                </div>


                <div class="mb-3">

                    <label class="form-label">
                        Nama Mata Pelajaran
                    </label>

                    <input type="text" name="name" id="editSubjectName" class="form-control" maxlength="150"
                        value="{{ old('name') }}" required>

                </div>


                <div class="form-check">

                    <input type="checkbox" name="is_active" value="1" class="form-check-input"
                        id="editSubjectActive">

                    <label class="form-check-label" for="editSubjectActive">
                        Mata pelajaran aktif
                    </label>

                </div>

            </div>


            <div class="modal-footer">

                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Batal
                </button>

                <button type="submit" class="btn btn-primary">

                    <i class="bi bi-save me-1"></i>

                    Simpan Perubahan

                </button>

            </div>

        </form>

    </div>

</div>
