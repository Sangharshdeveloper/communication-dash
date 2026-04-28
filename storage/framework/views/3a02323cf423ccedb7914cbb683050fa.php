<?php
  $sideClass = $m['type'] === 'system' ? 'system' : ($m['mine'] ? 'mine' : 'theirs');
  $isAgent   = isset($m['sender_role']) && $m['sender_role'] === 'agent';
?>
<div class="msg <?php echo e($sideClass); ?>" id="m-<?php echo e($m['id']); ?>" data-id="<?php echo e($m['id']); ?>" data-date="<?php echo e($m['date']); ?>">

  
  <?php if($sideClass === 'theirs' && isset($m['sender_name'])): ?>
    <div class="msg-sender-label"><?php echo e($m['sender_name']); ?><?php if($isAgent): ?> · Agent <?php endif; ?></div>
  <?php endif; ?>

  <div class="bubble">
    <?php if(!empty($m['body'])): ?>
      <?php echo nl2br(e($m['body'])); ?>

    <?php endif; ?>
    <?php $__currentLoopData = $m['attachments'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <?php if($att['is_image']): ?>
        <a href="<?php echo e($att['url']); ?>" target="_blank" rel="noopener">
          <img class="att-img" src="<?php echo e($att['url']); ?>" alt="<?php echo e($att['name']); ?>">
        </a>
      <?php else: ?>
        <a class="att" href="<?php echo e($att['url']); ?>" target="_blank" rel="noopener">
          <span class="ico">📄</span>
          <span class="name"><?php echo e($att['name']); ?></span>
          <span class="sz"><?php echo e($att['size']); ?></span>
        </a>
      <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>
  <div class="msg-meta">
    <?php echo e($m['time']); ?>

    <?php if($m['mine']): ?>
      · <span class="tick <?php echo e($m['is_read'] ? 'read' : ''); ?>"><?php echo e($m['is_read'] ? '✓✓' : '✓'); ?></span>
    <?php endif; ?>
  </div>
</div><?php /**PATH /home/u804993635/domains/imagespark.in/public_html/a/resources/views/chat/partials/message.blade.php ENDPATH**/ ?>