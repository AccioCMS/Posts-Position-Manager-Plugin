<?php

namespace Plugins\Accio\PostPositionManager\Models;


use App\Models\Post;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PositionManager extends Model{
    /**
     * @var string table name
     */
    protected $table = "accio_post_position_manager";

    /**
     * @var string primary ID name
     */
    protected $primaryKey = "positionManagerID";

    /**
     * Get posts with position
     * @return Collection of posts
     */
    public static function getPostsWithPosition(){
        $cache = Cache::has("posts_with_position");
        if(!$cache || !count($cache)){
            $postsTmp = DB::table("post_articles")
                ->join('accio_post_position_manager', 'post_articles.postID', '=', 'accio_post_position_manager.postID')
                ->orderBy("positionKey")
                ->get()
                ->keyBy("positionKey");

            $posts = [];

            foreach ($postsTmp as $key => $postTmp){
                $post = new Post();
                $post->setRawAttributes((array) $postTmp);
                $posts[$key] = $post;
            }

            $posts = Collection::make($posts);

            Cache::forever('posts_with_position',$posts);
        }

        return Cache::get("posts_with_position");
    }

    /**
     * Get current positions
     * @return \Illuminate\Database\Eloquent\Collection|PositionManager[]
     */
    public static function getCurrentPositions(){
        return self::all()->keyBy("postID");
    }

}