<?php

namespace Plugins\Accio\PostPositionManager;

use App\Models\Post;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Accio\App\Interfaces\PluginInterface;
use Accio\App\Traits\PluginTrait;
use Plugins\Accio\PostPositionManager\Models\PositionManager;
use Symfony\Component\Console\Command\Command;

class Plugin implements PluginInterface {
    use PluginTrait;

    /**
     * Saves post data
     * @var object $modelMetaData
     */
    private $modelMetaData;

    /**
     * SEO Settings
     * @var array $settings
     */
    private $settings;

    /**
     * The model where we will get meta data from 
     * @var object $model
     */
    private $model;

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register(){
        // Saved Event
        Event::listen('post:stored', function ($data, $model) {
            $this->store($data, $model);
        });

        $this->eventAppendListColumns();
    }

    private function store($data, $post){
        if(isset($data['pluginsData']['Accio_PostPositionManager_post'])){
            $position = $data['pluginsData']['Accio_PostPositionManager_post']['position'];
            $postID = $post->postID;

            // remove posts that belongs to this position
            PositionManager::where('postID', $postID)->delete();

            if($position){
                $postPosition = new PositionManager();
                $postPosition->positionKey = $position;
                $postPosition->postID = $postID;
                $postPosition->published_at = $post->published_at->format('Y-m-d H:i:s');
                $postPosition->save();
            }
        }
    }

    /**
     *  Do something after all plugins have been loaded,
     *
     * @return void
     */
    public function boot(){}

    /**
     * @param Command $command
     * @return bool
     */
    public function install(Command $command){
        if(!Schema::hasTable('accio_post_position_manager')){
            Schema::create('accio_post_position_manager', function ($table)  {
                $table->increments("positionManagerID");
                $table->integer("postID");
                $table->string("positionKey", 10);
                $table->timestamps();
            });
        }

        return true;
    }

    /**
     * addDefaultListColumns
     * @return $this
     */
    private function eventAppendListColumns(){
        $currentPositions = PositionManager::getCurrentPositions()->keyBy('postID')->toArray();

        // add position column to post lists
        Event::listen('post:table_list_columns', function ($postType) {
            if($postType == 'post_articles') {
                return [
                  'position' => __('Accio.PostPositionManager::base.position')
                ];
            }
        });

        // add position rows to post lists
        Event::listen('post:table_list_rows', function ($results, $postType) use ($currentPositions) {
            if($postType == 'post_articles') {
                $rows = [];
                foreach ($results as $key => $item) {
                    $post = (new Post())->setRawAttributes((array)$item);
                    if (isset($currentPositions[$post->postID])) {
                        $rows[$key]['position'] = $currentPositions[$post->postID]['positionKey'];
                    }
                }
                return $rows;
            }
        });

        return $this;
    }

    public function update(){
        return true;
    }
}