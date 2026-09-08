@extends('layouts.kepala-unit')

@section('content')

<div class="max-w-7xl mx-auto">

    {{-- ==========================================================
    HEADER
    =========================================================== --}}

    <div class="mb-6">

        <div class="flex flex-col sm:flex-row
                    sm:items-center sm:justify-between gap-3">

            <div>

                <h1 class="text-2xl font-bold text-gray-800">
                    Siswa
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    Daftar siswa pada unit Anda.
                </p>

            </div>

            <div class="text-sm text-gray-500">

                Total:

                <span class="font-semibold text-gray-800">
                    {{ $students->total() }}
                </span>

                siswa

            </div>

        </div>

    </div>


    {{-- ==========================================================
    FILTER
    =========================================================== --}}

    @include('kepala-unit.students.partials.filters')


    {{-- ==========================================================
    TABLE
    =========================================================== --}}

    <div
        class="mt-6
               bg-white
               rounded-xl
               border border-gray-200
               shadow-sm
               overflow-hidden"
    >

        <div class="overflow-x-auto">

            <table class="min-w-full divide-y divide-gray-200">

                <thead class="bg-gray-50">

                    <tr>

                        <th
                            class="px-6 py-3
                                   text-left
                                   text-xs
                                   font-semibold
                                   text-gray-500
                                   uppercase
                                   tracking-wider"
                        >
                            No
                        </th>

                        <th
                            class="px-6 py-3
                                   text-left
                                   text-xs
                                   font-semibold
                                   text-gray-500
                                   uppercase
                                   tracking-wider"
                        >
                            NIS
                        </th>

                        <th
                            class="px-6 py-3
                                   text-left
                                   text-xs
                                   font-semibold
                                   text-gray-500
                                   uppercase
                                   tracking-wider"
                        >
                            Nama Siswa
                        </th>

                        <th
                            class="px-6 py-3
                                   text-left
                                   text-xs
                                   font-semibold
                                   text-gray-500
                                   uppercase
                                   tracking-wider"
                        >
                            Jenis Kelamin
                        </th>

                        <th
                            class="px-6 py-3
                                   text-left
                                   text-xs
                                   font-semibold
                                   text-gray-500
                                   uppercase
                                   tracking-wider"
                        >
                            Kelas
                        </th>

                        <th
                            class="px-6 py-3
                                   text-left
                                   text-xs
                                   font-semibold
                                   text-gray-500
                                   uppercase
                                   tracking-wider"
                        >
                            Status
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-gray-200">

                    @forelse ($students as $student)

                        <tr class="hover:bg-gray-50">

                            {{-- No --}}
                            <td
                                class="px-6 py-4
                                       text-sm
                                       text-gray-500
                                       whitespace-nowrap"
                            >
                                {{ $students->firstItem() + $loop->index }}
                            </td>


                            {{-- NIS --}}
                            <td
                                class="px-6 py-4
                                       text-sm
                                       text-gray-700
                                       whitespace-nowrap"
                            >
                                {{ $student->nis ?: '-' }}
                            </td>


                            {{-- Nama --}}
                            <td class="px-6 py-4">

                                <div
                                    class="font-medium
                                           text-gray-900"
                                >
                                    {{ $student->name }}
                                </div>

                            </td>


                            {{-- Jenis Kelamin --}}
                            <td
                                class="px-6 py-4
                                       text-sm
                                       text-gray-700
                                       whitespace-nowrap"
                            >
                                {{ $student->gender ?: '-' }}
                            </td>


                            {{-- Kelas --}}
                            <td
                                class="px-6 py-4
                                       text-sm
                                       text-gray-700
                                       whitespace-nowrap"
                            >
                                {{ $student->displayAcademicRecord?->schoolClass?->name ?? '-' }}
                            </td>


                            {{-- Status --}}
                            <td class="px-6 py-4">

                                @if ($student->is_active)

                                    <span
                                        class="inline-flex
                                               items-center
                                               px-2.5 py-1
                                               rounded-full
                                               text-xs
                                               font-medium
                                               bg-green-100
                                               text-green-700"
                                    >
                                        Aktif
                                    </span>

                                @else

                                    <span
                                        class="inline-flex
                                               items-center
                                               px-2.5 py-1
                                               rounded-full
                                               text-xs
                                               font-medium
                                               bg-gray-100
                                               text-gray-600"
                                    >
                                        Tidak Aktif
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="px-6 py-12
                                       text-center
                                       text-sm
                                       text-gray-400"
                            >
                                Belum ada data siswa.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- ======================================================
        PAGINATION
        ======================================================= --}}

        @if ($students->hasPages())

            <div
                class="px-6 py-4
                       border-t border-gray-200"
            >

                {{ $students->links() }}

            </div>

        @endif

    </div>

</div>

@endsection
