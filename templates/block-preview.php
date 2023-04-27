<?php get_header(); ?>
<style>
    html{
        margin-top: 0 !important;
    }    
    .wsu-wrapper-content{
        padding: 0 !important;
    }
    .wsu-wrapper-content::before{
        background-color: transparent !important;
    }
</style>
<div class="wsu-wrapper-content ">
    <main role="main" id="wsu-content" class="wsu-wrapper-main" tabindex="-1">
        <div id="wsu-gutenberg-snippet-preview">
            <?php the_content(); ?>
        </div>
    </main>
</div>
<?php get_footer(); ?>
