<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold leading-tight text-slate-900 dark:text-white">Chat</h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">

            <div class="rounded-2xl border border-slate-200/90 bg-white p-6 shadow-sm backdrop-blur-md dark:border-white/10 dark:bg-white/5">

                @php
                    $colors = ['bg-red-500', 'bg-blue-500', 'bg-amber-500', 'bg-yellow-500', 'bg-purple-500', 'bg-pink-500', 'bg-indigo-500'];
                @endphp

                <div id="messages" class="mb-6 max-h-96 space-y-3 overflow-y-auto">

                    @forelse ($messages as $message)
                    @php
                        $color = $colors[$message->user_id % count($colors)];
                        $mine = auth()->id() === $message->user_id;
                    @endphp
                    <div id="message-{{ $message->id }}" class="flex w-full {{ $mine ? 'justify-end' : 'justify-start' }}">

                        <div class="flex max-w-[min(85%,28rem)] items-end gap-2 {{ $mine ? 'flex-row-reverse' : 'flex-row' }}">

                            @if ($message->user->profile_photo)
                                <img src="{{ Storage::url($message->user->profile_photo) }}" class="h-9 w-9 flex-shrink-0 rounded-full object-cover ring-1 ring-slate-200 dark:ring-white/10">
                            @else
                                <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full {{ $color }} text-sm font-bold text-white">
                                    {{ strtoupper(substr($message->user->name, 0, 1)) }}
                                </div>
                            @endif

                            <div class="{{ $mine ? 'text-right' : 'text-left' }} min-w-0">
                                <p class="text-xs font-medium text-slate-500 dark:text-gray-500">
                                    @if ($mine)
                                        You
                                    @else
                                        {{ $message->user->name }}
                                    @endif
                                </p>
                                <div class="mt-0.5 inline-block rounded-2xl px-3.5 py-2 text-sm text-left
                                    {{ $mine
                                        ? 'rounded-tr-sm bg-red-600 text-white dark:bg-red-600'
                                        : 'rounded-tl-sm bg-slate-100 text-slate-800 dark:bg-white/10 dark:text-slate-200' }}">
                                    {{ $message->content }}
                                </div>
                                @if ($mine)
                                    <div class="mt-1">
                                        <button type="button"
                                            class="text-xs text-red-600 transition-colors hover:text-red-500 dark:text-red-400 dark:hover:text-red-300"
                                            onclick="deleteMessage({{ $message->id }}, '{{ route('chat.destroy', $message) }}')">
                                            Delete
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-center text-slate-500 dark:text-gray-500">No messages yet. Say something!</p>
                    @endforelse

                </div>

                <form id="chat_form" method="POST" action="{{ route('chat.store') }}" class="flex gap-2">
                    @csrf
                    <input id="chat_input"
                        type="text"
                        name="content"
                        placeholder="Type a message..."
                        autocomplete="off"
                        class="flex-1 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 transition-all duration-200 focus:border-red-400 focus:outline-none focus:ring-1 focus:ring-red-500/30 dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-gray-500 dark:focus:border-red-500 dark:focus:ring-red-500/40" />
                    <button id="chat_send" type="submit" class="rounded-xl border border-red-500/50 bg-transparent px-5 py-2.5 text-sm font-medium text-red-600 transition-all duration-200 hover:border-red-500 hover:bg-red-500 hover:text-white hover:shadow-md dark:text-red-400 dark:hover:shadow-[0_0_16px_rgba(239,68,68,0.25)]">
                        Send
                    </button>
                </form>

                @error('content')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
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

            var colors = ['bg-red-500', 'bg-blue-500', 'bg-amber-500', 'bg-yellow-500', 'bg-purple-500', 'bg-pink-500', 'bg-indigo-500'];

            var form       = document.getElementById('chat_form');
            var input      = document.getElementById('chat_input');
            var messageBox = document.getElementById('messages');
            var myUserId   = {{ auth()->id() }};

            messageBox.scrollTop = messageBox.scrollHeight;

            function buildAvatar(name, userId, photoUrl, color) {
                if (photoUrl) {
                    return '<img src="' + photoUrl + '" class="h-9 w-9 flex-shrink-0 rounded-full object-cover ring-1 ring-slate-200 dark:ring-white/10">';
                }
                return '<div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full ' + color + ' text-sm font-bold text-white">' + name.charAt(0).toUpperCase() + '</div>';
            }

            function appendMessageRow(id, userId, userName, photoUrl, content, showDelete, deleteUrl) {
                var mine = userId === myUserId;
                var color = colors[userId % colors.length];
                var div = document.createElement('div');
                div.id = 'message-' + id;
                div.className = 'flex w-full ' + (mine ? 'justify-end' : 'justify-start');

                var inner = '<div class="flex max-w-[min(85%,28rem)] items-end gap-2 ' + (mine ? 'flex-row-reverse' : 'flex-row') + '">';
                inner += buildAvatar(userName, userId, photoUrl, color);
                inner += '<div class="' + (mine ? 'text-right' : 'text-left') + ' min-w-0">';
                inner += '<p class="text-xs font-medium text-slate-500 dark:text-gray-500">' + (mine ? 'You' : escapeHtml(userName)) + '</p>';
                inner += '<div class="mt-0.5 inline-block rounded-2xl px-3.5 py-2 text-left text-sm ' + (mine ? 'rounded-tr-sm bg-red-600 text-white dark:bg-red-600' : 'rounded-tl-sm bg-slate-100 text-slate-800 dark:bg-white/10 dark:text-slate-200') + '">' + escapeHtml(content) + '</div>';
                if (mine && showDelete) {
                    inner += '<div class="mt-1"><button type="button" class="text-xs text-red-600 hover:text-red-500 dark:text-red-400 dark:hover:text-red-300" onclick="deleteMessage(' + id + ', \'' + deleteUrl + '\')">Delete</button></div>';
                }
                inner += '</div></div>';

                div.innerHTML = inner;
                messageBox.appendChild(div);
                messageBox.scrollTop = messageBox.scrollHeight;
            }

            function escapeHtml(s) {
                if (!s) return '';
                return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
            }

            var sendBtn = document.getElementById('chat_send');

            function lockForm() {
                input.disabled = true;
                sendBtn.disabled = true;
                sendBtn.style.opacity = '0.5';
                sendBtn.textContent = 'Sending...';
            }

            function unlockForm() {
                input.disabled = false;
                sendBtn.disabled = false;
                sendBtn.style.opacity = '';
                sendBtn.textContent = 'Send';
                input.focus();
            }

            form.addEventListener('submit', function (e) {
                e.preventDefault();

                var content = input.value.trim();
                if (content === '') return;

                lockForm();
                input.value = '';

                axios.post('{{ route("chat.store") }}', { content: content })
                    .then(function (response) {
                        var myName    = '{{ auth()->user()->name }}';
                        var myPhoto   = '{{ auth()->user()->profile_photo ? Storage::url(auth()->user()->profile_photo) : '' }}';
                        var msgId     = response.data.id;
                        var deleteUrl = '{{ url("chat") }}/' + msgId;

                        if (document.getElementById('message-' + msgId)) return;

                        appendMessageRow(msgId, myUserId, myName, myPhoto, content, true, deleteUrl);
                    })
                    .finally(function () {
                        unlockForm();
                    });
            });

            window.Echo.channel('knighttsTalk')
                .listen('MessageSent', function (e) {
                    if (document.getElementById('message-' + e.message.id)) return;

                    var uid = e.message.user_id;
                    var mine = uid === myUserId;
                    var delUrl = mine ? ('{{ url("chat") }}/' + e.message.id) : '';
                    appendMessageRow(
                        e.message.id,
                        uid,
                        e.message.user.name,
                        e.message.photo_url || '',
                        e.message.content,
                        mine,
                        delUrl
                    );
                })
                .listen('MessageDeleted', function (e) {
                    var row = document.getElementById('message-' + e.message_id);
                    if (row) row.remove();
                });

        });
    </script>

</x-app-layout>
