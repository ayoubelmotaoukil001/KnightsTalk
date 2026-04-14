<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">Profile Photo</h2>
        <p class="mt-1 text-sm text-gray-600">Upload a photo that will appear next to your messages.</p>
    </header>

    <form method="POST" action="{{ route('profile.photo') }}" enctype="multipart/form-data" class="mt-6 space-y-4">
        @csrf

        @if ($user->profile_photo)
            <img src="{{ Storage::url($user->profile_photo) }}" class="w-16 h-16 rounded-full object-cover">
        @else
            <p class="text-sm text-gray-400">No photo uploaded yet.</p>
        @endif

        <div>
            <x-input-label for="photo" value="Choose a new photo" />
            <input type="file" id="photo" name="photo" accept="image/*" class="mt-1 block w-full text-sm text-gray-600">
            <x-input-error :messages="$errors->get('photo')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>Save Photo</x-primary-button>

            @if (session('status') === 'photo-updated')
                <p class="text-sm text-gray-600">Saved.</p>
            @endif
        </div>
    </form>
</section>
