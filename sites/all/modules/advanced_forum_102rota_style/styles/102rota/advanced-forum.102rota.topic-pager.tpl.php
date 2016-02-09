<?php

/* topic pager template
 *
 * $pages: Array of linked numbers for first set of pages
 * $last_page_text: Linked text "Last page" (translatable)
 * $last_page_number: Linked number pointing to the last page.
 * $last_page: defined in advanced_forum_preprocess_topic_pager(). Left for backward compatibility.
 */
?>
<?php if (!empty($pages)): ?>
  <div class="topic-pager clearfix"><span><?php print implode('</span></span>', $pages) . '</span>' . $last_page_text; ?></div>
<?php endif; ?>
