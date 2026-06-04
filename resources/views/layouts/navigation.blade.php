@component('layouts.app')
    <div class="flex items-center justify-between p-4">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-gray-600 hover:text-red-600">
                <i class="bi bi-box-arrow-right"></i>
                <span class="ml-2">Logout</span>
            </button>
        </form>

        <div class="flex items-center space-x-4">
            <div class="relative">
                <i class="bi bi-bell text-gray-600"></i>
                <span class="absolute -top-1 -right-1 w-2 h-2 bg-red-500 rounded-full"></span>
            </div>
            <div class="text-right hidden lg:block">
                <p class="font-bold text-gray-800">{{ auth()->user()->name }}</p>
                <p class="text-sm text-gray-500">{{ auth()->user()->roles->pluck('name')->implode(', ') }}</p>
            </div>
            <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                Masuk
            </button>
        </div>
    </div>
@endcomponent
