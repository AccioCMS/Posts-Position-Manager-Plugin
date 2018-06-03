<?php

namespace Plugins\Accio\PostPositionManager\Controllers;

use App\Http\Controllers\MainPluginsController;
use Plugins\Accio\PostPositionManager\Models\PositionManager;

class PositionManagerController extends MainPluginsController{

    // Get positions of a post
    public function details(string $lang, int $postID){
        $positionManager = new PositionManager();
        $positionManager->select(['positionKey']);
        $getPosition = $positionManager->where('postID', $postID)->get()->first();
        if($getPosition){
            return $getPosition;
        }

        return ['positionKey' => null];
    }
}