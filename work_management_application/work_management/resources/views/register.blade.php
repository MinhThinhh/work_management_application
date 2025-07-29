@extends('layouts.auth')

@section('title', 'Đăng ký - Work Management')

@section('content')
<div class="bg-white p-8 rounded shadow-md w-96">
    <h2 class="text-2xl font-bold mb-6 text-center">Đăng ký tài khoản</h2>

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        {{ session('error') }}
    </div>
    @endif

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                Họ và tên
            </label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name') }}"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('name') border-red-500 @enderror"
                pattern="^[a-zA-ZÀ-ỹ\s]+$"
                title="Họ và tên chỉ được chứa chữ cái và khoảng trắng, không được chứa số"
                required />
            <!-- <small class="text-gray-600 text-xs">Chỉ được chứa chữ cái và khoảng trắng, không được chứa số</small> -->
            @error('name')
            <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                Email
            </label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('email') border-red-500 @enderror"
                required />
            @error('email')
            <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                Mật khẩu
            </label>
            <input
                type="password"
                id="password"
                name="password"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('password') border-red-500 @enderror"
                minlength="8"
                pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$"
                title="Mật khẩu phải có ít nhất 8 ký tự, bao gồm 1 chữ thường, 1 chữ hoa và 1 số"
                required />
            <!-- <small class="text-gray-600 text-xs">Mật khẩu phải có ít nhất 8 ký tự, bao gồm 1 chữ thường, 1 chữ hoa và 1 số</small> -->
            @error('password')
            <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="password_confirmation">
                Xác nhận mật khẩu
            </label>
            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                minlength="8"
                required />
        </div>

        <div class="flex items-center justify-between">
            <button
                type="submit"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full">
                Đăng ký
            </button>
        </div>

        <div class="text-center mt-4">
            <p class="text-sm">
                Đã có tài khoản?
                <a href="{{ route('login') }}" class="text-blue-500 hover:text-blue-700">
                    Đăng nhập
                </a>
            </p>
        </div>
    </form>
</div>
@endsection