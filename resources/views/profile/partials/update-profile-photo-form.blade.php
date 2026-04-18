<section>
    <header>
        <h2 class="text-lg font-medium text-white">Profile Photo</h2>
        <p class="mt-1 text-sm text-gray-400">Upload a photo that will appear next to your messages.</p>
    </header>

    <form method="POST" action="{{ route('profile.photo') }}" enctype="multipart/form-data" class="mt-6 space-y-4">
        @csrf

        @if ($user->profile_photo)
            <img src="{{ Storage::url($user->profile_photo) }}" class="w-16 h-16 rounded-full object-cover ring-2 ring-white/10">
        @else
            <p class="text-sm text-gray-500">No photo uploaded yet.</p>
        @endif

        <div>
            <x-input-label for="photo" value="Choose a new photo" />
            <input type="file" id="photo" name="photo" accept="image/*" class="mt-1 block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-medium file:bg-white/10 file:text-gray-300 hover:file:bg-white/20 transition-all duration-200">
            <x-input-error :messages="$errors->get('photo')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>Save Photo</x-primary-button>

            @if (session('status') === 'photo-updated')
                <p class="text-sm text-emerald-400">Saved.</p>
            @endif
        </div>
    </form>
</section>
