@extends('layouts.app')
@section('content')
@endsection
@section('content2')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <nav class="nav nav-sm header-block-flex">
                        <a href="/home" class="nav-link text-xs text-uppercase ">Urejanje knjig</a><br />
                        <a href="/viewResRen" class="nav-link text-xs text-uppercase ">Pregled / urejanje rezervacij in izposoj</a><br />
                        <nav>
                </div>
                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif
                    <div id="admin_home">
                        <admin_home>
                        </admin_home>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection