
<?php
    $img = $img ?? fn (string $file) => asset('img/lasles/'.$file);
    $variant = $variant ?? 'person'; // person | features | map | tools
?>
<?php if($variant === 'person'): ?>
  <div class="lasles-page-art lasles-page-art--person" aria-hidden="true">
    <img src="<?php echo e($img('hero-illustration.svg')); ?>" width="611" height="382" alt="" decoding="async" fetchpriority="high">
  </div>
<?php elseif($variant === 'features'): ?>
  <div class="lasles-page-art lasles-page-art--features" aria-hidden="true">
    <img src="<?php echo e($img('features-illustration.png')); ?>" width="508" height="414" alt="" decoding="async" fetchpriority="high">
  </div>
<?php elseif($variant === 'map'): ?>
  <div class="lasles-page-art lasles-page-art--map" aria-hidden="true">
    <img src="<?php echo e($img('map-global.svg')); ?>" width="508" height="280" alt="" loading="lazy" decoding="async">
  </div>
<?php elseif($variant === 'tools'): ?>
  
  <div class="lasles-page-art lasles-page-art--tools" aria-hidden="true">
    <img src="<?php echo e($img('features-illustration.png')); ?>" width="508" height="414" alt="" decoding="async" fetchpriority="high">
  </div>
<?php else: ?>
  <div class="lasles-page-art lasles-page-art--person" aria-hidden="true">
    <img src="<?php echo e($img('hero-illustration.svg')); ?>" width="611" height="382" alt="" decoding="async">
  </div>
<?php endif; ?>
<?php /**PATH /Users/cityphone/Documents/tadris lab/resources/views/partials/landing/lasles/page-art.blade.php ENDPATH**/ ?>