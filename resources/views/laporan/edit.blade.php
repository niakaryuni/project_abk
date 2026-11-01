@extends('layouts.app')

@section('title', 'Edit Laporan')
@section('page-title', 'Edit Laporan')

@section('content')
<div class="card" style="max-width:620px;margin:0 auto">
    <div class="card-header">
        <h3><i class="fas fa-edit" style="color:var(--primary);margin-right:8px"></i>Edit Laporan</h3>
        <a href="{{ route('laporan.index') }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
    <div class="card-body">

        @if($errors->any())
        <div style="background:#fde8e8;border:1px solid var(--danger);border-radius:8px;padding:12px 16px;margin-bottom:20px">
            <ul style="margin:0;padding-left:20px">
                @foreach($errors->all() as $e)
                    <li style="font-size:13px">{{ $e }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="alert-info" style="background:#eaf7ee;border:1px solid #34c77b;color:#1b7a4b;padding:12px 14px;border-radius:8px;margin-bottom:20px;font-size:13.5px">
            <i class="fas fa-magic" style="margin-right:6px"></i>
            Ringkasan laporan akan <strong>otomatis dibuat ulang</strong> berdasarkan data Nilai Perkembangan &amp; Nilai Akademik terbaru saat laporan ini disimpan.
        </div>