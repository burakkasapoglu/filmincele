<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use App\Models\Watchlist;
use App\Models\User;
use App\Models\Post;
use App\Services\SocialMediaService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard', [
            'userCount' => User::count(),
            'ratingCount' => Rating::count(),
            'watchlistCount' => Watchlist::count(),
            'postCount' => Post::count(),
            'recentUsers' => User::latest()->take(5)->get(),
            'recentRatings' => Rating::with(['user', 'movie'])->latest()->take(10)->get(),
            'topRated' => Rating::selectRaw('movie_id, AVG(rating) as avg, COUNT(*) as count')
                ->with('movie')
                ->groupBy('movie_id')
                ->orderByDesc('count')
                ->take(10)
                ->get(),
            'popularGenres' => $this->getPopularGenres(),
        ]);
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

    public function createPost()
    {
        return view('admin.post-form', ['post' => null]);
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
