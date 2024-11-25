@extends('layouts.app')

@section('content')
  <div class="py-4">
    <nav-header :page="{{json_encode($page)}}"/>
  </div>
  <div class="py-4">
    <base-layout>
      <task-lists data-type="{{$dataType}}"/>
    </base-layout>
  </div>
@endsection
