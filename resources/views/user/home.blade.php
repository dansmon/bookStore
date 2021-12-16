@extends('layouts.app')
@section('content2')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <nav class="nav nav-sm header-block-flex">
                        <a href="/home" class="nav-link text-xs text-uppercase ">Izposoja / rezervacija knjig</a><br />
                        <a href="/viewResRen" class="nav-link text-xs text-uppercase ">Urejanje rezervacij in izposoj</a><br />
                        <nav>
                </div>
                <div class="card-body" style="background: linear-gradient(0deg, rgba(255, 255, 255, 0.7), rgba(255, 255, 255, 0.7)), url('/backGrounds/bookStoreBookListBackground.webp');">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif
                    <div id="user_home">
                        <user_home :auth_user="{{Auth::user()}}">
                        </user_home>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection