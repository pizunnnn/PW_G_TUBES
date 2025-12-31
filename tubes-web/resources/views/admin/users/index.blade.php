@extends('layouts.app')

@section('title', 'Users Management')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">
                <i class="fas fa-users text-purple-600"></i> Users Management
            </h1>
            <p class="text-gray-600 mt-1">Manage all registered users</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition inline-flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Users</p>
                    <p class="text-3xl font-bold text-purple-600">{{ $users->count() }}</p>
                </div>
                <div class="bg-purple-100 rounded-full p-4">
                    <i class="fas fa-users text-3xl text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Admin Users</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $users->where('is_admin', true)->count() }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-4">
                    <i class="fas fa-user-shield text-3xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Regular Users</p>
                    <p class="text-3xl font-bold text-green-600">{{ $users->where('is_admin', false)->count() }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-4">
                    <i class="fas fa-user text-3xl text-green-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            User
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Email
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Phone
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Role
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Transactions
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Total Spent
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Registered
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                                            <span class="text-purple-600 font-bold text-lg">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $user->name }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            ID: {{ $user->id }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center text-sm text-gray-900">
                                    <i class="fas fa-envelope text-gray-400 mr-2"></i>
                                    {{ $user->email }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center text-sm text-gray-900">
                                    @if($user->phone)
                                        <i class="fas fa-phone text-gray-400 mr-2"></i>
                                        {{ $user->phone }}
                                    @else
                                        <span class="text-gray-400 italic">No phone</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($user->is_admin)
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        <i class="fas fa-crown mr-1"></i> Admin
                                    </span>
                                @else
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        <i class="fas fa-user mr-1"></i> User
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="flex items-center">
                                    <i class="fas fa-shopping-cart text-gray-400 mr-2"></i>
                                    {{ $user->transactions_count }} orders
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-purple-600">
                                {{ 'Rp ' . number_format($user->transactions->sum('total_price'), 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar text-gray-400 mr-2"></i>
                                    {{ $user->created_at->format('d M Y') }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <i class="fas fa-users text-6xl text-gray-300 mb-4"></i>
                                <p class="text-gray-500 text-lg">No users found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Additional Info -->
    <div class="mt-6 bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-400 text-xl"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    <strong>Note:</strong> This page shows all registered users. Admins have full access to manage the system, while regular users can only make purchases.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection