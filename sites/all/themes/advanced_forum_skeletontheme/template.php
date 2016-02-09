<?php

function advanced_forum_skeletontheme_filter_tips() {
  return '';
}

function advanced_forum_skeletontheme_filter_tips_more_info() {
  return '';
}

function advanced_forum_skeletontheme_form_alter(&$form, $form_state, $form_id) {
  if (isset($form['actions']['submit'])) {
    $path = base_path() . drupal_get_path('theme', 'advanced_forum_skeletontheme') . '/images/ajax-loader.gif';
    if (!isset($form['actions']['submit']['#suffix'])) $form['actions']['submit']['#suffix'] = '';
    $loading = '<img src="' . $path . '" alt="loading..."/>';
    $form['actions']['submit']['#suffix'] .= '<script>jQuery("#edit-submit").click(function() {
      jQuery(this).hide();
      jQuery(this).parent().append(\'<div class="throbber_comment_save">' . t('Loading') . '...</div>\');
    });</script>';
  }
}

function advanced_forum_skeletontheme_preprocess_comment(&$variables) {
  //Если это ответ на коммент
  if ($variables['comment']->pid > 0) {
    $parent_comment = comment_load($variables['comment']->pid);
    //Эта библиотека нужна для склонения в Дательный падеж
    include_once(drupal_get_path('theme', 'advanced_forum_skeletontheme') . '/libs/names.php');
    $parent_author_name = strip_tags(theme('username', array('account' => user_load($parent_comment->uid))));
    $name = new RussianNameProcessor($parent_author_name);
    //Заменяем перевод "(Ответ на пост #!post_position)" (т.е. вам обязательно надо переводить в админке именно так)
    $variables['in_reply_to'] = str_replace("(Ответ", "(Ответ " . $name->fullName($name->gcaseDat), $variables['in_reply_to']); 
    $parent_comment_body = $parent_comment->comment_body['und'][0]['value'];
    if (strlen($parent_comment_body) > 600) {
      $parent_comment_body = substr($parent_comment_body, 0, 600) . '...';
    }
    $variables['content']['comment_body'][0]['#markup'] = check_markup('[quote=' . $parent_author_name . ']' . $parent_comment_body . '[/quote]', 'filtered_html') .  $variables['content']['comment_body'][0]['#markup'];
  }
}

/**
 * Theme function to show list of types that can be posted in forum.
 */
function advanced_forum_skeletontheme_advanced_forum_node_type_create_list(&$variables) {
  $forum_id = $variables['forum_id'];

  // Get the list of node types to display links for.
  $type_list = advanced_forum_node_type_create_list($forum_id);

  $output = '';
  if (is_array($type_list)) {
    foreach ($type_list as $type => $item) {
      $output .= '<div class="forum-add-node forum-add-' . $type . '">';
      if ($item['name']=='Опрос') $name = 'Новый Опрос';
      else $name = t('New @node_type', array('@node_type' => $item['name']));
      $output .= theme('advanced_forum_l', array(
        'text' => $name,
        'path' => $item['href'],
        'options' => NULL,
        'button_class' => 'large'
          ));
      $output .= '</div>';
    }
  }
  else {
    // User did not have access to create any node types in this fourm so
    // we just return the denial text / login prompt.
    $output = $type_list;
  }

  return $output;
}

function advanced_forum_skeletontheme_process_node(&$variables) {
  if (arg(0)=='forum' && arg(1)=='search') {
    $node = $variables['node'];
    $variables['search_title'] = '<span class="search-title"><b>Тема: </b>' . l($node->title . ' →', 'node/' . $node->nid) . '</span>';
  }
}

