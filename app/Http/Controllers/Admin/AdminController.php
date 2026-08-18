<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageView;
use App\Models\Rating;
use App\Models\Watchlist;
use App\Models\User;
use App\Models\Post;
use App\Services\AiBlogService;
use App\Services\SocialMediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $today = now()->format('Y-m-d');

        return view('admin.dashboard', [
            'userCount' => User::count(),
            'ratingCount' => Rating::count(),
            'watchlistCount' => Watchlist::count(),
            'postCount' => Post::count(),
            'todayViews' => PageView::whereDate('created_at', $today)->count(),
            'weekViews' => PageView::where('created_at', '>=', now()->subDays(7))->count(),
            'totalViews' => PageView::count(),
            'uniqueVisitors' => PageView::whereDate('created_at', $today)->distinct('session_id')->count('session_id'),
            'recentUsers' => User::latest()->take(5)->get(),
            'recentRatings' => Rating::with(['user', 'movie'])->latest()->take(10)->get(),
            'recentVisitors' => PageView::with('user')->latest()->take(20)->get(),
            'hourlyViews' => PageView::selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
                ->whereDate('created_at', $today)->groupBy('hour')->orderBy('hour')->get(),
            'dailyViews' => PageView::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->where('created_at', '>=', now()->subDays(14))
                ->groupBy('date')->orderBy('date', 'desc')->get(),
            'topActiveUsers' => PageView::selectRaw('user_id, COUNT(*) as count')
                ->whereNotNull('user_id')->where('created_at', '>=', now()->subDays(30))
                ->groupBy('user_id')->orderByDesc('count')->take(10)->get()
                ->map(function ($v) {
                    $v->user = User::find($v->user_id);
                    return $v;
                }),
            'topMovies' => PageView::selectRaw('movie_id, COUNT(*) as count')
                ->whereNotNull('movie_id')->where('url', 'like', '/film/%')
                ->groupBy('movie_id')->orderByDesc('count')->take(10)->get(),
            'topBlogPosts' => PageView::selectRaw('url, COUNT(*) as count')
                ->where('url', 'like', '/blog/%')->where('url', 'not like', '/blog?%')
                ->groupBy('url')->orderByDesc('count')->take(10)->get(),
            'topPages' => PageView::selectRaw('url, COUNT(*) as count')
                ->where('created_at', '>=', now()->subDays(7))
                ->whereNotIn('url', ['/livewire/update', '/'])
                ->groupBy('url')->orderByDesc('count')->take(15)->get(),
            'topRated' => Rating::selectRaw('movie_id, AVG(rating) as avg, COUNT(*) as count')
                ->with('movie')->groupBy('movie_id')->orderByDesc('count')->take(10)->get(),
            'popularGenres' => $this->getPopularGenres(),
        ]);
    }

    public function contentIdeas(Request $request)
    {
        $tab = $request->query('tab', 'suggestions');
        $service = app(\App\Services\ContentIdeaService::class);

        if ($tab === 'planned' || $tab === 'published') {
            $status = $tab === 'planned' ? 'planned' : 'published';

            $rows = \App\Models\ContentIdea::with('post')
                ->where('status', $status)
                ->orderByRaw('event_date IS NULL, event_date ASC')
                ->orderByDesc('updated_at')
                ->get()
                ->map(function ($row) {
                    return [
                        'type' => $row->type,
                        'icon' => match ($row->type) {
                            'birthday' => '🎂',
                            'anniversary' => '🎬',
                            'trend' => '🔥',
                            default => '💡',
                        },
                        'title' => $row->title,
                        'suggestion' => $row->suggestion,
                        'event_date' => $row->event_date?->toDateString(),
                        'tmdb_ref' => $row->tmdb_ref,
                        'status' => $row->status,
                        'post' => $row->post,
                        'script' => $row->script,
                        'when_label' => $row->event_date
                            ? $row->event_date->format('d.m.Y') . ' (' . $row->event_date->locale('tr')->translatedFormat('l') . ')'
                            : 'Esnek',
                    ];
                });

            $ideas = $rows;
        } else {
            $ideas = $service->getUpcoming(21);

            $existing = \App\Models\ContentIdea::whereIn('tmdb_ref', $ideas->pluck('tmdb_ref'))
                ->with('post')
                ->get()
                ->keyBy(fn ($e) => $e->type . ':' . $e->tmdb_ref);

            $ideas = $ideas->map(function ($idea) use ($existing) {
                $key = $idea['type'] . ':' . $idea['tmdb_ref'];
                $row = $existing->get($key);
                $idea['status'] = $row?->status ?? 'new';
                $idea['post'] = $row?->post;
                $idea['notes'] = $row?->notes;
                $idea['script'] = $row?->script;
                return $idea;
            })->values();
        }

        return view('admin.content-ideas', [
            'ideas' => $ideas,
            'tab' => $tab,
            'counts' => [
                'suggestions' => \App\Models\ContentIdea::where('status', 'new')->count(),
                'planned' => \App\Models\ContentIdea::where('status', 'planned')->count(),
                'published' => \App\Models\ContentIdea::where('status', 'published')->count(),
            ],
        ]);
    }

    public function updateContentIdeaStatus(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'tmdb_ref' => 'required|string|max:100',
            'status' => 'required|in:new,planned,published,dismissed',
            'suggestion' => 'nullable|string',
            'event_date' => 'nullable|date',
            'notes' => 'nullable|string|max:2000',
        ]);

        \App\Models\ContentIdea::updateOrCreate(
            ['type' => $data['type'], 'tmdb_ref' => $data['tmdb_ref']],
            [
                'title' => $data['title'],
                'suggestion' => $data['suggestion'] ?? null,
                'event_date' => $data['event_date'] ?? null,
                'status' => $data['status'],
                'notes' => $data['notes'] ?? null,
            ]
        );

        return back()->with('success', 'Fikir güncellendi: ' . $data['title']);
    }

    public function generateFromIdea(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'type' => 'required|string|max:50',
            'tmdb_ref' => 'required|string|max:100',
            'suggestion' => 'nullable|string',
            'event_date' => 'nullable|date',
        ]);

        $video = app(\App\Services\VideoScriptService::class);

        if (!$video->isConfigured()) {
            return back()->with('idea_error', 'Gemini API anahtarı tanımlı değil.');
        }

        $topic = $data['suggestion'] ?: $data['title'];

        try {
            $script = $video->generateScript($data['title'] . '. ' . $topic);

            if (!$script) {
                return back()->with('idea_error', 'Video metni üretilemedi, tekrar deneyin.');
            }

            \App\Models\ContentIdea::updateOrCreate(
                ['type' => $data['type'], 'tmdb_ref' => $data['tmdb_ref']],
                [
                    'title' => $data['title'],
                    'suggestion' => $data['suggestion'] ?? null,
                    'event_date' => $data['event_date'] ?? null,
                    'script' => json_encode($script, JSON_UNESCAPED_UNICODE),
                    'status' => 'planned',
                ]
            );

            return back()->with('success', '🎬 Video metni üretildi: ' . $script['video_title']);
        } catch (\Exception $e) {
            return back()->with('idea_error', 'Hata: ' . $e->getMessage());
        }
    }

    public function generateBlogFromIdea(Request $request)
    {
        $request->validate(['ai_topic' => 'required|string|max:500']);

        $ai = app(\App\Services\AiBlogService::class);

        if (!$ai->isConfigured()) {
            return back()->with('idea_error', 'Gemini API anahtarı tanımlı değil.');
        }

        try {
            $result = $ai->generateBlogPost($request->ai_topic);

            if (!$result) {
                return back()->with('idea_error', 'Blog yazısı üretilemedi, tekrar deneyin.');
            }

            $imageUrl = $ai->searchImage($result['image_query'] ?? $request->ai_topic);

            return redirect()->route('admin.posts.create')->with([
                'ai_title' => $result['title'],
                'ai_excerpt' => $result['excerpt'] ?? '',
                'ai_body' => $result['body'],
                'ai_category' => $result['category'] ?? 'Liste',
                'ai_image_url' => $imageUrl,
                'ai_topic' => $request->ai_topic,
            ]);
        } catch (\Exception $e) {
            return back()->with('idea_error', 'Hata: ' . $e->getMessage());
        }
    }

    public function users()
    {
        return view('admin.users', [
            'users' => User::withCount(['ratings', 'watchlists'])->latest()->paginate(20),
        ]);
    }

    public function userDetail(User $user)
    {
        return view('admin.user-detail', [
            'profile' => $user->loadCount(['ratings', 'watchlists']),
            'ratings' => $user->ratings()->with('movie')->latest()->take(20)->get(),
            'favoriteGenres' => $user->favoriteGenres()->get(),
            'avgRating' => round($user->ratings()->avg('rating') ?? 0, 1),
        ]);
    }

    public function toggleAdmin(User $user)
    {
        $user->update(['is_admin' => !$user->is_admin]);
        return back()->with('success', $user->name . ' için admin durumu güncellendi.');
    }

    public function posts()
    {
        return view('admin.posts', [
            'posts' => Post::latest()->paginate(20),
        ]);
    }

    public function createPost(Request $request)
    {
        $data = ['post' => null];

        if ($request->has('ai_topic') && !empty($request->ai_topic)) {
            try {
                $ai = app(\App\Services\AiBlogService::class);
                $result = $ai->generateBlogPost($request->ai_topic);
                if ($result) {
                    $data['ai_title'] = $result['title'];
                    $data['ai_excerpt'] = $result['excerpt'] ?? '';
                    $data['ai_body'] = $result['body'];
                    $data['ai_category'] = $result['category'] ?? 'Liste';
                    $data['ai_image_url'] = $ai->searchImage($result['image_query'] ?? $request->ai_topic);
                } else {
                    $data['ai_error'] = 'İçerik oluşturulamadı. Lütfen tekrar deneyin.';
                }
            } catch (\Exception $e) {
                $data['ai_error'] = 'Hata: ' . $e->getMessage();
            }
        }

        return view('admin.post-form', $data);
    }

    public function editPost(Post $post)
    {
        return view('admin.post-form', ['post' => $post]);
    }

    public function storePost(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'excerpt' => 'nullable|string',
            'body' => 'required|string',
            'image_url' => 'nullable|url',
            'image_file' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'read_time' => 'integer|min:1',
            'is_published' => 'boolean',
        ]);

        if ($request->hasFile('image_file')) {
            $data['image_url'] = asset('storage/' . $request->file('image_file')->store('posts', 'public'));
        }

        Post::create($data + ['published_at' => now()]);
        return redirect('/admin/blog')->with('success', 'Yazı oluşturuldu.');
    }

    public function updatePost(Request $request, Post $post)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'excerpt' => 'nullable|string',
            'body' => 'required|string',
            'image_url' => 'nullable|url',
            'image_file' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'read_time' => 'integer|min:1',
            'is_published' => 'boolean',
        ]);

        if ($request->hasFile('image_file')) {
            if ($post->image_url && str_contains($post->image_url, '/storage/posts/')) {
                \Storage::disk('public')->delete(str_replace('/storage/', '', parse_url($post->image_url, PHP_URL_PATH)));
            }
            $data['image_url'] = asset('storage/' . $request->file('image_file')->store('posts', 'public'));
        }

        $post->update($data);
        return redirect('/admin/blog')->with('success', 'Yazı güncellendi.');
    }

    public function deletePost(Post $post)
    {
        $post->delete();
        return back()->with('success', 'Yazı silindi.');
    }

    public function sharePost(Post $post, SocialMediaService $social)
    {
        $results = $social->publishPost($post);

        $messages = [];
        foreach ($results as $platform => $result) {
            $messages[] = $result['success'] ?? false
                ? "✓ {$platform} paylaşıldı"
                : "✗ {$platform}: " . ($result['error'] ?? 'Hata');
        }

        return back()->with('success', implode(' | ', $messages));
    }

    public function uploadImage(Request $request)
    {
        $request->validate(['image' => 'required|image|max:2048']);
        $path = $request->file('image')->store('blog-images', 'public');
        return response()->json(['data' => ['filePath' => asset('storage/' . $path)]]);
    }

    public function aiGenerate(Request $request)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            return back()->with('ai_error', 'Yetkisiz erişim.');
        }

        $request->validate(['topic' => 'required|string|max:200']);

        try {
            $ai = app(AiBlogService::class);

            if (!$ai->isConfigured()) {
                return back()->with('ai_error', 'Gemini API anahtarı tanımlanmamış.')->withInput();
            }

            $result = $ai->generateBlogPost($request->topic);

            if (!$result) {
                return back()->with('ai_error', 'İçerik oluşturulamadı. Lütfen tekrar deneyin.')->withInput();
            }

            $imageUrl = $ai->searchImage($result['image_query'] ?? $request->topic);

            return redirect()->route('admin.posts.create')->with([
                'ai_title' => $result['title'],
                'ai_excerpt' => $result['excerpt'] ?? '',
                'ai_body' => $result['body'],
                'ai_category' => $result['category'] ?? 'Liste',
                'ai_image_url' => $imageUrl,
                'ai_topic' => $request->topic,
            ]);
        } catch (\Exception $e) {
            return back()->with('ai_error', 'Sunucu hatası: ' . $e->getMessage())->withInput();
        }
    }

    private function getPopularGenres(): array
    {
        $data = \DB::table('user_favorite_genres')
            ->join('genres', 'genres.id', '=', 'user_favorite_genres.genre_id')
            ->selectRaw('genres.name, COUNT(*) as count')
            ->groupBy('genres.id', 'genres.name')
            ->orderByDesc('count')
            ->take(5)
            ->get()
            ->toArray();

        return $data;
    }
}
