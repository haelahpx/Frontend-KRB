<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo e($title ?? 'App'); ?></title>
    <link rel="icon" type="image/png" href="<?php echo e(asset('images/logo/kebun-raya-bogor.png')); ?>" />
    <?php echo app('Illuminate\Foundation\Vite')('resources/css/app.css'); ?>
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

    <?php echo app('flux')->fluxAppearance(); ?>

</head>

<body class="bg-white min-h-screen">

    
    

    <main class="">
        <?php echo e($slot); ?>

    </main>

    

    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

    <?php echo app('Illuminate\Foundation\Vite')('resources/js/app.js'); ?>
    <?php app('livewire')->forceAssetInjection(); ?>
<?php echo app('flux')->scripts(); ?>  </body>

</html><?php /**PATH /home/adomancer/Documents/GitHub/KRB-System/resources/views/layouts/auth.blade.php ENDPATH**/ ?>