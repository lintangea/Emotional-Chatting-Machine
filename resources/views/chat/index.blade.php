<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Chat') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="mb-4 text-lg font-semibold">Users</h3>
                    <div class="space-y-2">
                        @foreach ($users as $user)
                            <a href="{{ route('chat.show', $user->id) }}" 
                               class="block p-3 rounded-lg hover:bg-gray-50">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            {{ $user->name }}
                                        </p>
                                        <p class="text-sm text-gray-500 truncate">
                                            {{ $user->email }}
                                        </p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>