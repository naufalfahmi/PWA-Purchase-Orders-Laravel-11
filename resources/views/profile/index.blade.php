@extends('layouts.app')

@section('title', 'Profile - Admin PWA')
@section('page-title', 'Profile')

@section('content')
<div class="p-4 space-y-6">
    <!-- Loading Skeleton (shown on initial load) -->
    <div id="skeletonLoader" class="space-y-6">
        <!-- Profile Header Skeleton -->
        <div class="card p-6 animate-pulse">
            <div class="text-center">
                <div class="w-20 h-20 bg-gray-200 rounded-full mx-auto mb-4"></div>
                <div class="h-6 bg-gray-200 rounded w-48 mx-auto mb-2"></div>
                <div class="h-4 bg-gray-200 rounded w-64 mx-auto mb-2"></div>
                <div class="h-4 bg-gray-200 rounded w-32 mx-auto"></div>
            </div>
        </div>

        <!-- Profile Form Skeleton -->
        <div class="card p-6 animate-pulse">
            <div class="h-6 bg-gray-200 rounded w-32 mb-4"></div>
            <div class="space-y-4">
                <div>
                    <div class="h-4 bg-gray-200 rounded w-24 mb-2"></div>
                    <div class="h-10 bg-gray-200 rounded"></div>
                </div>
                <div>
                    <div class="h-4 bg-gray-200 rounded w-16 mb-2"></div>
                    <div class="h-10 bg-gray-200 rounded"></div>
                </div>
                <div>
                    <div class="h-4 bg-gray-200 rounded w-32 mb-2"></div>
                    <div class="h-10 bg-gray-200 rounded"></div>
                </div>
                <div>
                    <div class="h-4 bg-gray-200 rounded w-28 mb-2"></div>
                    <div class="h-10 bg-gray-200 rounded"></div>
                </div>
                <div>
                    <div class="h-4 bg-gray-200 rounded w-40 mb-2"></div>
                    <div class="h-10 bg-gray-200 rounded"></div>
                </div>
                <div class="pt-4">
                    <div class="h-10 bg-gray-200 rounded w-full"></div>
                </div>
            </div>
        </div>

        <!-- App Information Skeleton -->
        <div class="card p-6 animate-pulse">
            <div class="h-6 bg-gray-200 rounded w-40 mb-4"></div>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <div class="h-4 bg-gray-200 rounded w-32"></div>
                    <div class="h-4 bg-gray-200 rounded w-16"></div>
                </div>
                <div class="flex justify-between">
                    <div class="h-4 bg-gray-200 rounded w-20"></div>
                    <div class="h-4 bg-gray-200 rounded w-24"></div>
                </div>
                <div class="flex justify-between">
                    <div class="h-4 bg-gray-200 rounded w-24"></div>
                    <div class="h-4 bg-gray-200 rounded w-32"></div>
                </div>
            </div>
        </div>

        <!-- Logout Button Skeleton -->
        <div class="card p-6 animate-pulse">
            <div class="h-10 bg-gray-200 rounded w-full"></div>
        </div>
    </div>

    <!-- Actual Content (hidden initially) -->
    <div id="profileContent" class="hidden">
    <!-- Profile Header -->
    <div class="card p-6 text-center">
        <div class="w-20 h-20 bg-blue-600 rounded-full mx-auto mb-4 flex items-center justify-center">
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
        </div>
        <h2 class="text-xl font-semibold text-gray-900">{{ $user->name }}</h2>
        <p class="text-gray-600">{{ $user->email }}</p>
        <p class="text-sm text-gray-500 mt-2">Member sejak {{ $user->created_at->format('d M Y') }}</p>
    </div>

    <!-- Profile Form -->
    <div class="card p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Update Profile</h3>
        
        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PATCH')
            
            <div class="space-y-4">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="{{ old('name', $user->name) }}"
                        class="input-field @error('name') border-red-500 @enderror"
                        placeholder="Masukkan nama lengkap"
                        required
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="{{ old('email', $user->email) }}"
                        class="input-field @error('email') border-red-500 @enderror"
                        placeholder="Masukkan email"
                        required
                    >
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Current Password -->
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password Saat Ini
                    </label>
                    <input 
                        type="password" 
                        id="current_password" 
                        name="current_password"
                        class="input-field @error('current_password') border-red-500 @enderror"
                        placeholder="Masukkan password saat ini (untuk mengubah password)"
                    >
                    @error('current_password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- New Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password Baru
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password"
                        class="input-field @error('password') border-red-500 @enderror"
                        placeholder="Masukkan password baru"
                    >
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        Konfirmasi Password Baru
                    </label>
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation"
                        class="input-field @error('password_confirmation') border-red-500 @enderror"
                        placeholder="Konfirmasi password baru"
                    >
                    @error('password_confirmation')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <button type="submit" class="w-full btn-primary">
                        Update Profile
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- App Information -->
    <div class="card p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Aplikasi</h3>
        <div class="space-y-3 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-600">Versi Aplikasi</span>
                <span class="text-gray-900">1.0.0</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Platform</span>
                <span class="text-gray-900">Web Application</span>
            </div>
        </div>
    </div>

    <!-- Logout Button -->
    <div class="card p-6">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full btn-secondary bg-red-100 hover:bg-red-200 text-red-700 border-red-200">
                Logout
            </button>
        </form>
    </div>
    </div> <!-- Close profileContent div -->
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const skeletonLoader = document.getElementById('skeletonLoader');
    const profileContent = document.getElementById('profileContent');

    // Show skeleton loading on initial page load with 1-second delay
    setTimeout(() => {
        skeletonLoader.classList.add('hidden');
        profileContent.classList.remove('hidden');
    }, 1000);
});
</script>
@endsection
