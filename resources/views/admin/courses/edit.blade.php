<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-white leading-tight">Edit Course: {{ $course->title }}</h2>
    </x-slot>

    <div class="py-8 px-4 max-w-2xl mx-auto">

        <a href="{{ route('admin.courses.show', $course) }}" class="text-emerald-400 hover:text-emerald-300 text-sm mb-4 inline-block transition-colors">&larr; Back to Course</a>

        <form method="POST" action="{{ route('admin.courses.update', $course) }}" class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-6 space-y-5">
            @csrf @method('PUT')

            <div>
                <label for="title" class="block text-sm font-medium text-gray-300 mb-1">Title</label>
                <input type="text" name="title" id="title" value="{{ old('title', $course->title) }}" required
                       class="w-full bg-white/5 border border-white/10 text-white rounded-xl px-4 py-3 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 placeholder-gray-500 transition-all duration-200">
                @error('title') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-300 mb-1">Description</label>
                <textarea name="description" id="description" rows="3"
                          class="w-full bg-white/5 border border-white/10 text-white rounded-xl px-4 py-3 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 placeholder-gray-500 transition-all duration-200">{{ old('description', $course->description) }}</textarea>
                @error('description') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="level" class="block text-sm font-medium text-gray-300 mb-1">Level</label>
                <select name="level" id="level"
                        class="w-full bg-white/5 border border-white/10 text-white rounded-xl px-4 py-3 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all duration-200">
                    <option value="beginner" {{ old('level', $course->level) === 'beginner' ? 'selected' : '' }}>Beginner</option>
                    <option value="intermediate" {{ old('level', $course->level) === 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                    <option value="pro" {{ old('level', $course->level) === 'pro' ? 'selected' : '' }}>Pro</option>
                </select>
                @error('level') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <button type="submit"
                    class="px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-emerald-700 text-white rounded-xl hover:from-emerald-400 hover:to-emerald-600 text-sm font-medium transition-all duration-200 shadow-lg shadow-emerald-500/20">
                Update Course
            </button>
        </form>
    </div>
</x-app-layout>
