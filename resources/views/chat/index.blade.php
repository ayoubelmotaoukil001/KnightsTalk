<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Chat
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                @php
                    $colors = ['bg-red-500', 'bg-blue-500', 'bg-green-500', 'bg-yellow-500', 'bg-purple-500', 'bg-pink-500', 'bg-indigo-500'];
                @endphp

                <div id="messages" class="space-y-4 mb-6 max-h-96 overflow-y-auto">

                    @forelse ($messages as $message)
                    @php $color = $colors[$message->user_id % count($colors)]; @endphp
                    <div id="message-{{ $message->id }}" class="flex items-start gap-3">

                        @if ($message->user->profile_photo)
                            <img src="{{ Storage::url($message->user->profile_photo) }}" class="w-9 h-9 rounded-full object-cover flex-shrink-0">
                        @else
                            <div class="w-9 h-9 rounded-full {{ $color }} flex items-center justify-center text-white font-bold flex-shrink-0">
                                {{ strtoupper(substr($message->user->name, 0, 1)) }}
                            </div>
                        @endif

                        <div>
                            <p class="text-sm font-semibold text-gray-800">
                                {{ $message->user->name }}
                                <span class="text-xs text-gray-400 font-normal ml-1">
                                    {{ $message->created_at->diffForHumans() }}
                                </span>
                            </p>
                            <p class="text-gray-700 mt-0.5">{{ $message->content }}</p>
                        </div>

                        @if (auth()->id() === $message->user_id)
                        <button
                            class="ml-auto text-xs text-red-400 hover:text-red-600"
                            onclick="deleteMessage({{ $message->id }}, '{{ route('chat.destroy', $message) }}')">
                            Delete
                        </button>
                        @endif

                    </div>
                    @empty
                    <p class="text-gray-400 text-center">No messages yet. Say something!</p>
                    @endforelse

                </div>

                <form id="chat_form" method="POST" action="{{ route('chat.store') }}" class="flex gap-2">
                    @csrf
                    <input id="chat_input"
                        type="text"
                        name="content"
                        placeholder="Type a message..."
                        autocomplete="off"
                        class="flex-1 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" />
                    <button type="submit" class="bg-indigo-500 hover:bg-indigo-600 text-white text-sm px-4 py-2 rounded-lg">
                        Send
                    </button>
                </form>

                @error('content')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror

            </div>
        </div>
    </div>

    <script>
        function deleteMessage(id, url) {
            axios.delete(url)
                .then(function () {
                    var row = document.getElementById('message-' + id);
                    if (row) row.remove();
                });
        }

        document.addEventListener('DOMContentLoaded', function () {

            var colors = ['bg-red-500', 'bg-blue-500', 'bg-green-500', 'bg-yellow-500', 'bg-purple-500', 'bg-pink-500', 'bg-indigo-500'];

            var form       = document.getElementById('chat_form');
            var input      = document.getElementById('chat_input');
            var messageBox = document.getElementById('messages');

            messageBox.scrollTop = messageBox.scrollHeight;

            function buildAvatar(name, userId, photoUrl, color) {
                if (photoUrl) {
                    return '<img src="' + photoUrl + '" class="w-9 h-9 rounded-full object-cover flex-shrink-0">';
                }
                return '<div class="w-9 h-9 rounded-full ' + color + ' flex items-center justify-center text-white font-bold flex-shrink-0">' + name.charAt(0).toUpperCase() + '</div>';
            }

            form.addEventListener('submit', function (e) {
                e.preventDefault();

                var content = input.value.trim();
                if (content === '') return;

                axios.post('{{ route("chat.store") }}', { content: content })
                    .then(function (response) {
                        var myId      = {{ auth()->id() }};
                        var myName    = '{{ auth()->user()->name }}';
                        var myPhoto   = '{{ auth()->user()->profile_photo ? Storage::url(auth()->user()->profile_photo) : '' }}';
                        var myColor   = colors[myId % colors.length];
                        var msgId     = response.data.id;
                        var deleteUrl = '{{ url("chat") }}/' + msgId;

                        var div = document.createElement('div');
                        div.id = 'message-' + msgId;
                        div.className = 'flex items-start gap-3';
                        div.innerHTML = buildAvatar(myName, myId, myPhoto, myColor)
                                      + '<div>'
                                      + '<p class="text-sm font-semibold text-gray-800">' + myName + ' <span class="text-xs text-gray-400 font-normal">Just now</span></p>'
                                      + '<p class="text-gray-700 mt-0.5">' + content + '</p>'
                                      + '</div>'
                                      + '<button class="ml-auto text-xs text-red-400 hover:text-red-600" onclick="deleteMessage(' + msgId + ', \'' + deleteUrl + '\')">Delete</button>';

                        messageBox.appendChild(div);
                        messageBox.scrollTop = messageBox.scrollHeight;
                        input.value = '';
                    });
            });

            window.Echo.channel('knighttsTalk')
                .listen('MessageSent', function (e) {
                    var color = colors[e.message.user_id % colors.length];

                    var div = document.createElement('div');
                    div.id = 'message-' + e.message.id;
                    div.className = 'flex items-start gap-3';
                    div.innerHTML = buildAvatar(e.message.user.name, e.message.user_id, e.message.photo_url, color)
                                  + '<div>'
                                  + '<p class="text-sm font-semibold text-gray-800">' + e.message.user.name + ' <span class="text-xs text-gray-400 font-normal">Just now</span></p>'
                                  + '<p class="text-gray-700 mt-0.5">' + e.message.content + '</p>'
                                  + '</div>';

                    messageBox.appendChild(div);
                    messageBox.scrollTop = messageBox.scrollHeight;
                })
                .listen('MessageDeleted', function (e) {
                    var row = document.getElementById('message-' + e.message_id);
                    if (row) row.remove();
                });

        });
    </script>

</x-app-layout>
