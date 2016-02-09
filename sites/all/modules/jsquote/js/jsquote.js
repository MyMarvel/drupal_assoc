(function ($) {

  Drupal.behaviors.jsquote = {
    attach: function (context, settings) {
      $('ul.links .quote a', context).attr('href', '#').click(function(){
        var name;
        var text;
        
        //Creating text for insert
        
        if ($(this).data('name'))  name = '=' + $(this).data('name');
        else name = '';

        if (window.getSelection && !window.opera) text = window.getSelection();
        else if (document.getSelection) text = document.getSelection();
        else if (document.selection) text = document.selection.createRange().text;
        
        if (text=='') {
          //Getting full message
          var $parent = $(this).parents(".forum-post");
          if ($(this).parents("#forum-comments").size() > 0) {
            text = $parent.find(".field-name-comment-body .field-item").html();
          }
          else {
            // It is a button under the node itself
            text = $parent.find(".field-name-body .field-item").html();
          }
          text = text.replace(/<p><\/p>/g, '');
        }
        
        text='[quote' + name + ']' + text + '[/quote]';
        
        var wysiwyg_type = 'no_wysiwyg';
        if ($("table.cke_editor").size() > 0) wysiwyg_type = 'ckeditor3';
        if ($("textarea.ckeditor-mod").size() > 0) wysiwyg_type = 'ckeditor4';
        
        //Insert text
        switch (wysiwyg_type) {
           case 'ckeditor3':
              //$comment_field = $("table.cke_editor iframe").contents().find("body");
              for(var instanceName in CKEDITOR.instances) {
                if (instanceName.substr(0, 17)=='edit-comment-body') {
                    CKEDITOR.instances[instanceName].insertHtml(text);
                    CKEDITOR.instances[instanceName].insertText("\n\r");
                }
              }
              break;
           case 'ckeditor4':
              //console.log(CKEDITOR);
              //$comment_field = $("table.cke_editor iframe").contents().find("body");
              for(var instanceName in CKEDITOR.instances) {
                if (instanceName.substr(0, 17)=='edit-comment-body') {
                    CKEDITOR.instances[instanceName].insertHtml(text);
                    CKEDITOR.instances[instanceName].insertText("\n\r");
                }
              }
              break;
           case 'no_wysiwyg':
           default:
              //For simple textarea we will use getCursorPosition() function
              //Finding the textarea
              var $comment_field = $("form.comment-form .form-textarea");
              //Inserting new text in the textarea
              var position = $comment_field.getCursorPosition();
              var content = $comment_field.val();
              var newContent = content.substr(0, position) + text + content.substr(position);
              $comment_field.val(newContent);
              break;
        }      
        return false;
      });

      // 'Reply' button will lead to the same page instead of a new one.
      //$(".forum-post .links .comment-add a, .comment-wrapper .comment-reply a").click(function() {
        //$('body').scrollTop($("form.comment-form").offset().top);
        //return false;
      //});

    } //attach
  }; //Drupal.behaviors

  $.fn.getCursorPosition = function () {
      var el = $(this).get(0);
      var pos = 0;
      if ('selectionStart' in el) {
          pos = el.selectionStart;
      } else if ('selection' in document) {
          el.focus();
          var Sel = document.selection.createRange();
          var SelLength = document.selection.createRange().text.length;
          Sel.moveStart('character', -el.value.length);
          pos = Sel.text.length - SelLength;
      }
      return pos;
  }
})(jQuery);
