<?php
    $kind = $kind ?? 'link';
?>
<?php switch($kind):
  case ('facebook'): ?>
    <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" fill="currentColor"><path d="M14.5 8.5V6.8c0-.7.1-1.1 1.2-1.1H17V3h-2.3C11.9 3 11 4.6 11 6.6v1.9H9v2.9h2V21h3.5v-9.6h2.3l.4-2.9h-2.7Z"/></svg>
    <?php break; ?>
  <?php case ('instagram'): ?>
    <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" fill="none"><rect x="3.5" y="3.5" width="17" height="17" rx="5" stroke="currentColor" stroke-width="1.7"/><circle cx="12" cy="12" r="4.1" stroke="currentColor" stroke-width="1.7"/><circle cx="17.2" cy="6.8" r="1.1" fill="currentColor"/></svg>
    <?php break; ?>
  <?php case ('x'): ?>
    <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" fill="currentColor"><path d="M4.5 4h4.1l3.5 5.1L16.4 4H19.5l-5.5 7.1L19.8 20h-4.1l-3.8-5.5L7.6 20H4.5l5.8-7.5L4.5 4Zm2.7 1.5 8.9 13H16.8L7.9 5.5H7.2Z"/></svg>
    <?php break; ?>
  <?php case ('whatsapp'): ?>
    <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" fill="currentColor"><path d="M12.04 2.01A9.94 9.94 0 0 0 2.1 11.95a9.86 9.86 0 0 0 1.4 5.08L2 22l5.14-1.45a9.95 9.95 0 0 0 14.9-8.6 9.94 9.94 0 0 0-10-9.94Zm0 18.18a8.2 8.2 0 0 1-4.18-1.15l-.3-.18-3.05.86.82-2.97-.2-.31a8.23 8.23 0 1 1 6.91 3.75Zm4.5-6.16c-.25-.12-1.46-.72-1.69-.8-.23-.09-.39-.12-.56.12-.16.25-.64.8-.78.96-.14.16-.29.18-.54.06-.25-.12-1.05-.39-2-1.23-.74-.66-1.24-1.47-1.39-1.72-.14-.25-.02-.38.11-.5.11-.11.25-.29.37-.43.12-.14.16-.25.25-.41.08-.16.04-.31-.02-.43-.06-.12-.56-1.35-.77-1.85-.2-.48-.41-.41-.56-.42h-.48c-.16 0-.43.06-.65.31-.23.25-.86.84-.86 2.05s.88 2.38 1 2.54c.12.16 1.74 2.66 4.22 3.73 2.48 1.07 2.48.71 2.93.67.45-.04 1.46-.6 1.66-1.17.21-.58.21-1.07.14-1.17-.06-.1-.23-.16-.48-.29Z"/></svg>
    <?php break; ?>
  <?php case ('youtube'): ?>
    <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" fill="currentColor"><path d="M21.6 8.2a2.7 2.7 0 0 0-1.9-1.9C18 5.9 12 5.9 12 5.9s-6 0-7.7.4A2.7 2.7 0 0 0 2.4 8.2 28 28 0 0 0 2 12a28 28 0 0 0 .4 3.8 2.7 2.7 0 0 0 1.9 1.9c1.7.4 7.7.4 7.7.4s6 0 7.7-.4a2.7 2.7 0 0 0 1.9-1.9A28 28 0 0 0 22 12a28 28 0 0 0-.4-3.8ZM10 15.1V8.9l5.2 3.1L10 15.1Z"/></svg>
    <?php break; ?>
  <?php case ('linkedin'): ?>
    <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" fill="currentColor"><path d="M6.4 9.2H3.7V20h2.7V9.2ZM5 3.8a1.6 1.6 0 1 0 0 3.2 1.6 1.6 0 0 0 0-3.2ZM20.3 13c0-3-1.6-4.4-3.8-4.4-1.7 0-2.5.9-3 1.6V9.2h-2.7c0 .9 0 10.8 0 10.8h2.7v-6c0-.3 0-.6.1-.9.3-.6.9-1.3 1.9-1.3 1.4 0 1.9 1 1.9 2.5V20h2.7v-7Z"/></svg>
    <?php break; ?>
  <?php case ('mail'): ?>
    <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" fill="none"><path d="M4 7.5A2.5 2.5 0 0 1 6.5 5h11A2.5 2.5 0 0 1 20 7.5v9a2.5 2.5 0 0 1-2.5 2.5h-11A2.5 2.5 0 0 1 4 16.5v-9Z" stroke="currentColor" stroke-width="1.7"/><path d="m5.5 8 6.5 5L18.5 8" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
    <?php break; ?>
  <?php default: ?>
    <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" fill="none"><circle cx="12" cy="12" r="8.2" stroke="currentColor" stroke-width="1.7"/><path d="M4.5 12h15M12 4.5c2.2 2.4 3.3 4.9 3.3 7.5S14.2 17.1 12 19.5C9.8 17.1 8.7 14.6 8.7 12S9.8 6.9 12 4.5Z" stroke="currentColor" stroke-width="1.7"/></svg>
<?php endswitch; ?>
<?php /**PATH /Users/cityphone/Documents/tadris lab/resources/views/partials/landing/lasles/social-icon.blade.php ENDPATH**/ ?>