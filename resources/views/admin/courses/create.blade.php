<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold leading-tight text-slate-900 dark:text-white">Create Course</h2>
    </x-slot>

    <div class="mx-auto max-w-2xl px-4 py-8">

        <a href="{{ route('admin.courses.index') }}" class="mb-4 inline-block text-sm font-medium text-red-600 transition-colors hover:text-red-500 dark:text-red-400 dark:hover:text-red-300">&larr; Back to Courses</a>

        <form method="POST" action="{{ route('admin.courses.store') }}" class="space-y-5 rounded-2xl border border-slate-200/90 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-white/[0.05] dark:shadow-none">
            @csrf

            <div>
                <label for="title" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Title</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required
                       class="w-full rounded-xl border border-slate-200/90 bg-white px-4 py-3 text-slate-900 transition-all duration-200 placeholder:text-slate-400 focus:border-red-500 focus:ring-1 focus:ring-red-500 dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder:text-slate-500 dark:focus:border-red-400 dark:focus:ring-red-400">
                @error('title') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="description" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Description</label>
                <textarea name="description" id="description" rows="3"
                          class="w-full rounded-xl border border-slate-200/90 bg-white px-4 py-3 text-slate-900 transition-all duration-200 placeholder:text-slate-400 focus:border-red-500 focus:ring-1 focus:ring-red-500 dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder:text-slate-500 dark:focus:border-red-400 dark:focus:ring-red-400">{{ old('description') }}</textarea>
                @error('description') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="level" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Level</label>
                <select name="level" id="level"
                        class="w-full rounded-xl border border-slate-200/90 bg-white px-4 py-3 text-slate-900 transition-all duration-200 focus:border-red-500 focus:ring-1 focus:ring-red-500 dark:border-white/10 dark:bg-white/5 dark:text-white dark:focus:border-red-400 dark:focus:ring-red-400">
                    <option value="beginner" {{ old('level') === 'beginner' ? 'selected' : '' }}>Beginner</option>
                    <option value="intermediate" {{ old('level') === 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                    <option value="pro" {{ old('level') === 'pro' ? 'selected' : '' }}>Pro</option>
                </select>
                @error('level') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <button type="submit"
                    class="rounded-xl border border-red-500/50 bg-gradient-to-r from-red-500 to-red-600 px-5 py-2.5 text-sm font-medium text-white shadow-md transition-all duration-200 hover:from-red-400 hover:to-red-500 dark:shadow-lg dark:shadow-red-500/20">
                Create Course
            </button>
        </form>
    </div>
</x-app-layout>
