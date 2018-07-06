<?php

namespace Plugins\Accio\PostPositionManager\Models;


use Accio\App\Traits\CacheTrait;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PositionManager extends Model{
    use CacheTrait;

    /**
     * @var string table name
     */
    protected $table = "accio_post_position_manager";

    /**
     * @var string primary ID name
     */
    protected $primaryKey = "positionManagerID";

    /**
     * Get posts with positions
     *
     * @return Collection|static
     * @throws \Exception
     */
    public static function getPostsWithPosition(){
        $positions = self::cache()->getItems()->where('published_at', "<=", Carbon::now())->sortBy('published_at', SORT_REGULAR, true);
        if (!$positions){
            return collect();
        }

        $takenPositions = [];
        $positionIDsToBeDeleted = [];

        // Get only the latest post in a position
        foreach($positions as $key => $position){
            if (in_array($position->positionKey, $takenPositions)){
                array_push($positionIDsToBeDeleted, $position->positionManagerID);
                unset($positions[$key]);
            }else{
                array_push($takenPositions, $position->positionKey);
            }
        }

        // delete outdated positions
        if ($positionIDsToBeDeleted){
            $positionsToBeDeleted = PositionManager::whereIn('positionManagerID', $positionIDsToBeDeleted);
            if($positionsToBeDeleted){
                $positionsToBeDeleted->delete();
            }
        }

        $postIDs = $positions->pluck("postID");
        $articlesCache = Post::cache()->getItems()->whereIn("postID", $postIDs);
        if(!$articlesCache){
            return collect();
        }

        // Get post object of each position
        $positions = $positions->map(function ($item, $key) use ($articlesCache){
            $single_post = $articlesCache->where('postID',$item->postID)->first();
            $single_post->positionKey = $item->positionKey;
            return $single_post;
        });

        return $positions->sortBy('positionKey');
    }

    /**
     * Get current positions
     * @return \Illuminate\Database\Eloquent\Collection|PositionManager[]
     */
    public static function getCurrentPositions(){
        return self::all()->keyBy("postID");
    }

}