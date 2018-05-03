<template>
    <div class="right_col" role="main">
        <div class="col-md-12 col-sm-12 col-xs-12">
            POST POSITION MANAGER
        </div>
    </div>
</template>
<script>
    export default{
        data(){
            return{
            }
        },

        created(){},

        methods:{},

        computed:{
            // get base path
            basePath(){
                return this.$store.getters.get_base_path;
            },
        }
    }
</script>