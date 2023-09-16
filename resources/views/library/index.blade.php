@extends('layouts.modern')
@section('content')
    <div class="container">
        <nexus:tablefy :class="App\Http\Nexus\Views\LibraryTableView" :route="library.tablefy" />
        <nexus:tablefy :class="App\Http\Nexus\Views\LibraryTableView" :route="library.tablefy">
        </nexus:tablefy>
    </div>
@endsection
