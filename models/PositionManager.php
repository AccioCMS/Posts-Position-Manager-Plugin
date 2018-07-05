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
     * Get posts with position.
     *
     * @param array $with
     * @return Collection of posts
     */
    public static function getPostsWithPosition( $with = []){
        $data = Cache::get("posts_with_position");

        $cacheInstance = Post::initializeCache(Post::class, 'posts_with_position', []);

        if(!$data){
            $postObj = (new Post());
            $data = $postObj->join('accio_post_position_manager', 'post_articles.postID', '=', 'accio_post_position_manager.postID');

            if($with){
                $data = $data->with($with);
            }else{
                $data = $data->with($postObj->getDefaultRelations(getPostType('post_articles')));
            }

            $data = $data->orderBy("positionKey")
              ->get()
              ->keyBy("positionKey")->toArray();

            Cache::forever('posts_with_position',$data);
        }

        return $cacheInstance->setCacheCollection($data);
    }

    /**
     * Get current positions
     * @return \Illuminate\Database\Eloquent\Collection|PositionManager[]
     */
    public static function getCurrentPositions(){
        return self::all()->keyBy("postID");
    }

}