@extends('adminlte::page')

@section('title', 'Dashboard Admin Sistem')

@section('content_header')
    <h1>Dashboard Admin Sistem</h1>
@stop

@section('content')

    <div class="row">

        <div class="col-lg-3 col-6">
            <div class="small-box text-bg-primary">
                <div class="inner">
                    <h3>0</h3>
                    <p>Pengguna</p>
                </div>
                <i class="fas fa-users small-box-icon"></i>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box text-bg-success">
                <div class="inner">
                    <h3>0</h3>
                    <p>Siswa</p>
                </div>
                <i class="fas fa-user-graduate small-box-icon"></i>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box text-bg-warning">
                <div class="inner">
                    <h3>0</h3>
                    <p>Guru</p>
                </div>
                <i class="fas fa-chalkboard-teacher small-box-icon"></i>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box text-bg-danger">
                <div class="inner">
                    <h3>0</h3>
                    <p>Unit</p>
                </div>
                <i class="fas fa-building small-box-icon"></i>
            </div>
        </div>

    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Selamat Datang</h3>
        </div>

        <div class="card-body">
            Selamat datang di Sistem Informasi INDUK.
        </div>
    </div>

@stop
