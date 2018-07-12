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
        $positions = self::cache()->getItems();
        if($positions->isEmpty()){
            return collect();
        }

//        $takenPositions = [];
//        $positionIDsToBeDeleted = [];
//
//        // Get only the latest post in a position
//        foreach($positions as $key => $position){
//            if (in_array($position->positionKey, $takenPositions)){
//                array_push($positionIDsToBeDeleted, $position->positionManagerID);
//                unset($positions[$key]);
//            }else{
//                array_push($takenPositions, $position->positionKey);
//            }
//        }
//
//        // Delete outdated positions
//        if ($positionIDsToBeDeleted){
//            $positionsToBeDeleted = PositionManager::whereIn('positionManagerID', $positionIDsToBeDeleted);
//            if($positionsToBeDeleted){
//                $positionsToBeDeleted->delete();
//            }
//        }

        $postIDs = $positions->pluck("postID");
        $articlesCache = Post::cache("post_articles")->getItems()->published()->whereIn("postID", $postIDs);
        if($articlesCache->isEmpty()){
            return collect();
        }

        $articlesCache = $articlesCache->map(function ($item, $key) use ($positions){
            $pos = $positions->where('postID',$item->postID)->first();
            $item->positionKey = $pos->positionKey;
            return $item;
        });

        return $articlesCache->sortBy('positionKey');
    }

    /**
     * Get current positions
     * @return \Illuminate\Database\Eloquent\Collection|PositionManager[]
     */
    public static function getCurrentPositions(){
        return self::all()->keyBy("postID");
    }

}