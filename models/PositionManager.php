<?php

namespace Plugins\Accio\PostPositionManager\Models;

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
            $posts = DB::table("post_articles")
                ->join('accio_post_position_manager', 'post_articles.postID', '=', 'accio_post_position_manager.postID')
                ->orderBy("positionKey")
                ->get()
                ->keyBy("positionKey");

            Cache::forever('posts_with_position',$posts);
        }

        return Cache::get("posts_with_position");
    }

}