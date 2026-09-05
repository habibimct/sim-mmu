    {{-- ==========================================================
    MODAL TOLAK SETORAN
    ========================================================== --}}

    @foreach ($pendingDeposits as $deposit)
        @can('reject', $deposit)
            <div class="modal fade" id="modalTolakSetoran{{ $deposit->id }}" tabindex="-1"
                aria-labelledby="modalTolakSetoranLabel{{ $deposit->id }}" aria-hidden="true">

                <div class="modal-dialog modal-dialog-centered">

                    <div class="modal-content">

                        <form method="POST" action="{{ route('admin.finance.deposits.reject', $deposit) }}">

                            @csrf

                            <div class="modal-header">

                                <h5 class="modal-title" id="modalTolakSetoranLabel{{ $deposit->id }}">

                                    <i class="bi bi-x-circle me-1"></i>

                                    Tolak Setoran

                                </h5>

                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

                            </div>


                            <div class="modal-body">

                                <div class="alert alert-warning">

                                    Setoran dari

                                    <strong>
                                        {{ $deposit->organization->name }}
                                    </strong>

                                    sebesar

                                    <strong>
                                        Rp {{ number_format($deposit->amount, 0, ',', '.') }}
                                    </strong>

                                    akan ditolak.

                                </div>


                                <div class="mb-3">

                                    <label class="form-label">

                                        Alasan Penolakan

                                    </label>

                                    <textarea name="rejection_reason" class="form-control" rows="4" maxlength="1000" required
                                        placeholder="Tuliskan alasan penolakan..."></textarea>

                                </div>

                            </div>


                            <div class="modal-footer">

                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">

                                    Batal

                                </button>

                                <button type="submit" class="btn btn-danger">

                                    <i class="bi bi-x-lg me-1"></i>

                                    Tolak Setoran

                                </button>

                            </div>

                        </form>

                    </div>

                </div>

            </div>
        @endcan
    @endforeach
