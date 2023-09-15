@extends('layouts.modern')
@section('content')
  <div class="container">
    <div class="row justify-content-center mt-5">
      <div class="col-md-6">
        <div class="card">
          <div class="card-body text-center">
            <h1 class="card-title">Hello, World!</h1>
            <p class="card-text">Nexus Framework, light, safe and powerful.</p>
            <img src="{{ asset('images/logo.jpg') }}" style="width:300px;" />
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection