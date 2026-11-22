@extends('layouts.app')

@section('title', 'پروفایل من')

@section('content')
    <div class="mx-auto max-w-3xl space-y-6">
        <div class="card p-6 sm:p-8">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="card p-6 sm:p-8">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>
    </div>
@endsection
