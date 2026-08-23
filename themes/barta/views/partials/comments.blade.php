{{-- Comment thread + submission form. Params: $post --}}
<section id="comments" class="mt-10">
    @php($comments = $post->approvedComments ?? collect())
    <h2 class="mb-4 flex items-center gap-2 text-xl font-black">
        <span class="h-6 w-1.5 rounded bg-brand-600"></span>
        {{ __('Comments') }}
        <span class="text-base font-semibold text-ink-400">({{ localized_number($comments->count()) }})</span>
    </h2>

    @if (session('comment_status'))
        <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-sm font-semibold text-green-700">{{ session('comment_status') }}</div>
    @endif

    {{-- Existing comments --}}
    <div class="space-y-5">
        @forelse ($comments as $comment)
            <article class="rounded-xl border border-ink-100 bg-white p-4">
                <div class="flex items-center gap-2">
                    <div class="grid h-8 w-8 place-items-center rounded-full bg-brand-100 text-sm font-bold text-brand-700">
                        {{ mb_substr($comment->authorName(), 0, 1) }}
                    </div>
                    <div>
                        <p class="text-sm font-bold">{{ $comment->authorName() }}</p>
                        <p class="text-xs text-ink-400">{{ localized_number(optional($comment->created_at)->translatedFormat('j F Y, g:i a')) }}</p>
                    </div>
                </div>
                <p class="mt-3 whitespace-pre-line text-sm text-ink-700">{{ $comment->body }}</p>

                {{-- One level of replies --}}
                @if ($comment->replies->isNotEmpty())
                    <div class="mt-4 space-y-3 border-l-2 border-ink-100 pl-4">
                        @foreach ($comment->replies as $reply)
                            <div>
                                <p class="text-sm font-bold">{{ $reply->authorName() }}
                                    <span class="ml-1 text-xs font-normal text-ink-400">{{ localized_number(optional($reply->created_at)->translatedFormat('j F Y')) }}</span>
                                </p>
                                <p class="mt-1 whitespace-pre-line text-sm text-ink-700">{{ $reply->body }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </article>
        @empty
            <p class="rounded-xl border border-dashed border-ink-200 py-8 text-center text-ink-400">{{ __('No comments yet.') }}</p>
        @endforelse
    </div>

    {{-- Comment form --}}
    @if ($post->allow_comments)
        <form method="POST" action="{{ route('comments.store', $post) }}" class="mt-6 rounded-xl border border-ink-100 bg-ink-50 p-5">
            @csrf
            <h3 class="mb-3 font-black">{{ app()->getLocale() === 'bn' ? 'মন্তব্য করুন' : 'Leave a comment' }}</h3>

            @guest
                <div class="grid gap-3 sm:grid-cols-2">
                    <div>
                        <input type="text" name="author_name" value="{{ old('author_name') }}" required placeholder="{{ __('Full name') }}"
                               class="w-full rounded-lg border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                        @error('author_name')<p class="mt-1 text-xs text-brand-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <input type="email" name="author_email" value="{{ old('author_email') }}" required placeholder="{{ __('Email') }}"
                               class="w-full rounded-lg border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                        @error('author_email')<p class="mt-1 text-xs text-brand-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            @endguest

            <div class="mt-3">
                <textarea name="body" rows="4" required placeholder="{{ app()->getLocale() === 'bn' ? 'আপনার মন্তব্য…' : 'Your comment…' }}"
                          class="w-full rounded-lg border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">{{ old('body') }}</textarea>
                @error('body')<p class="mt-1 text-xs text-brand-600">{{ $message }}</p>@enderror
            </div>

            <button type="submit" class="mt-3 rounded-lg bg-brand-600 px-5 py-2 text-sm font-bold text-white hover:bg-brand-700">
                {{ app()->getLocale() === 'bn' ? 'মন্তব্য পাঠান' : 'Post comment' }}
            </button>
        </form>
    @else
        <p class="mt-6 rounded-lg bg-ink-50 px-4 py-3 text-sm text-ink-500">{{ __('Comments are closed for this article.') }}</p>
    @endif
</section>
