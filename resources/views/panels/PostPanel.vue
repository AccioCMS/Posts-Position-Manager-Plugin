<template>
    <div class="col-md-12 col-sm-12 col-xs-12 ppmPanelWrapper">
        <form class="form-horizontal form-label-left" id="store">
            <br>
            <!-- title -->
            <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" id="positionKey">Post Position: </label>
                <div class="col-md-10 col-sm-10 col-xs-12" v-if="!isLoading">
                    <select name="positionKey" class="form-control" v-model="pluginData.position">
                        <option v-for="key in positionKeyOptions" :value="key">{{ key }}</option>
                    </select>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
    export default{
        props: ['pluginData', 'languages', 'activeLang', 'panel'],
        data() {
            return {
                positionKeyOptions: [null,'A1','A2','A3','A4','A5','A6'],
                isLoading: true
            }
        },
        created(){
            if(this.$route.params.id !== undefined){
                this.$http.get(this.basePath+'/'+this.$route.params.adminPrefix+'/'+this.activeLang+'/plugins/accio/post-position-manager/details/'+this.$route.params.id)
                    .then((resp) => {
                        if(resp.body.positionKey !== undefined){
                            this.pluginData['position'] = resp.body.positionKey;
                        }

                        this.isLoading = false;
                });
            }else{
                this.pluginData['position'] = null;
                this.isLoading = false;
            }
        },
        computed:{
            // get base path
            basePath(){
                return this.$store.getters.get_base_path;
            }
        }
    }
</script>