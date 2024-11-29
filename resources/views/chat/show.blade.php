<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Chat with ') . $otherUser->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div id="messages" class="space-y-4 h-96 overflow-y-auto mb-4">
                        @foreach ($messages as $message)
                            <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                                <div class="{{ $message->sender_id === auth()->id() ? 'bg-blue-500 text-white' : 'bg-gray-200' }} rounded-lg px-4 py-2 max-w-sm">
                                    <p class="text-sm">{{ $message->message }}</p>
                                    <p class="text-xs {{ $message->sender_id === auth()->id() ? 'text-blue-100' : 'text-gray-500' }}">
                                        {{ $message->created_at->format('H:i') }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <form id="messageForm" class="space-y-4">
                        <input type="hidden" name="receiver_id" value="{{ $otherUser->id }}">
                        <div>
                            <label for="message" class="sr-only">Message</label>
                            <textarea id="message" name="message" rows="3"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="Type your message..."></textarea>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Send
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script>
        const pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
            cluster: '{{ env('PUSHER_APP_CLUSTER') }}'
        });

        const channel = pusher.subscribe('chat.{{ auth()->id() }}');
        channel.bind('App\\Events\\MessageSent', function(data) {
            const message = data.message;
            const messagesDiv = document.getElementById('messages');
            
            const messageHtml = `
                <div class="flex justify-start">
                    <div class="bg-gray-200 rounded-lg px-4 py-2 max-w-sm">
                        <p class="text-sm">${message.message}</p>
                        <p class="text-xs text-gray-500">${new Date(message.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</p>
                    </div>
                </div>
            `;
            
            messagesDiv.insertAdjacentHTML('beforeend', messageHtml);
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        });

        const messageForm = document.getElementById('messageForm');
        messageForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(messageForm);
            const response = await fetch('{{ route('chat.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    message: formData.get('message'),
                    receiver_id: formData.get('receiver_id')
                })
            });

            if (response.ok) {
                const message = await response.json();
                const messagesDiv = document.getElementById('messages');
                
                const messageHtml = `
                    <div class="flex justify-end">
                        <div class="bg-blue-500 text-white rounded-lg px-4 py-2 max-w-sm">
                            <p class="text-sm">${message.message}</p>
                            <p class="text-xs text-blue-100">${new Date(message.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</p>
                        </div>
                    </div>
                `;
                
                messagesDiv.insertAdjacentHTML('beforeend', messageHtml);
                messagesDiv.scrollTop = messagesDiv.scrollHeight;
                messageForm.reset();
            }
        });
    </script>
    @endpush
</x-app-layout>