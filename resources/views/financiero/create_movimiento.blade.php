@extends('layouts.ajax')
@section('content')

<div class="card">
  <div class="card-body">
    {{ $form->render() }}
  </div>
</div>

@endsection
