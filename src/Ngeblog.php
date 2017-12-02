<?php

namespace Antoniputra\Ngeblog;

use Antoniputra\Ngeblog\Models\Blog;
use Antoniputra\Ngeblog\Models\Category;
use Closure;

class Ngeblog
{
    /**
     * The callback that should be used to authenticate Ngeblog Users.
     *
     * @var \Closure
     */
    public static $authUsing;

    /**
     * Determine if the given request can access the Ngeblog Admin
     *
     * @param  \Illuminate\Http\Request $request
     * @return bool
     */
    public static function check($request)
    {
        return (static::$authUsing ?: function () {
            return app()->environment('local') || app()->environment('testing');
        })($request);
    }

    /**
     * Set the callback that should be used to authenticate Ngeblog users.
     *
     * @param  \Closure  $callback
     * @return static
     */
    public static function auth(Closure $callback)
    {
        static::$authUsing = $callback;

        return new static;
    }

    public function totalCategory()
    {
        return Category::count();
    }

    public function getLatestCategory($limit = 10)
    {
        return Category::with('blogs')
            ->withCount('blogs')
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
    }

    public function getDropdownCategory($value = 'id', $display = 'title')
    {
        $cats = Category::orderBy('title', 'asc')->get()->pluck($display, $value)->toArray();
        return array_merge([0 => '<< select category >>'], $cats);
    }

    public function totalBlog()
    {
        return Blog::count();
    }
}
