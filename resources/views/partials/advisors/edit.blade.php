@extends('layouts.app')
@section('title','افزودن مشاور')
@section('content')
<div class="container mt-4">
    <h4>افزودن مشاور جدید</h4>

    <form action="{{ route('advisors.update', $advisor) }}" method="POST">
        @csrf
        @method('PUT')
        <input type="text" name="name" value="{{ old('name', $advisor->name) }}">
        <input type="text" name="phone" value="{{ old('phone', $advisor->phone) }}">
    </form>

</div>
@endsection