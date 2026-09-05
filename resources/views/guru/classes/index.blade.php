@extends('layouts.guru')

@section('content')

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            Kelas Saya
        </h1>

        <p class="mt-1 text-sm text-gray-500">
            Daftar kelas dan mata pelajaran yang Anda ampu.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

        @forelse ($assignments as $assignment)

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">

                <div class="flex items-start justify-between gap-4">

                    <div>
                        <h2 class="text-xl font-bold text-gray-800">
                            {{ $assignment->schoolClass->name }}
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            {{ $assignment->schoolClass->organization->name }}
                        </p>
                    </div>

                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                        Aktif
                    </span>

                </div>

                <div class="mt-5 pt-4 border-t border-gray-100">

                    <p class="text-sm text-gray-500">
                        Mata Pelajaran
                    </p>

                    <p class="mt-1 font-semibold text-gray-800">
                        {{ $assignment->subject->name }}
                    </p>

                    <p class="mt-4 text-sm text-gray-500">
                        Tahun Ajaran
                    </p>

                    <p class="mt-1 font-semibold text-gray-800">
                        {{ $assignment->schoolClass->academicYear->name }}
                    </p>

                </div>

            </div>

        @empty

            <div class="col-span-full">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">

                    <p class="text-gray-500">
                        Belum ada kelas yang ditugaskan kepada Anda.
                    </p>

                </div>
            </div>

        @endforelse

    </div>

@endsection
